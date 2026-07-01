-- Peptide site generation platform — core schema
-- Run with the Supabase CLI (`supabase db push`) or paste into the SQL editor.

-- ---------------------------------------------------------------------------
-- Pipeline status for a site request.
-- ---------------------------------------------------------------------------
do $$ begin
  create type site_status as enum (
    'submitted',    -- customer finished intake
    'in_review',    -- admin looking at it
    'approved',     -- staged, ready to build
    'building',     -- deploy worker running
    'deploying',    -- provisioner POST in flight
    'live',         -- site is up
    'failed'        -- deploy errored
  );
exception when duplicate_object then null; end $$;

-- ---------------------------------------------------------------------------
-- Admin allowlist (drives RLS + /admin gating).
-- ---------------------------------------------------------------------------
create table if not exists admins (
  email text primary key,
  created_at timestamptz not null default now()
);

create or replace function is_admin() returns boolean
language sql stable security definer set search_path = public as $$
  select exists (
    select 1 from admins
    where lower(email) = lower(coalesce(auth.jwt() ->> 'email', ''))
  );
$$;

-- ---------------------------------------------------------------------------
-- Design presets (token sets shown in intake + used at deploy).
-- ---------------------------------------------------------------------------
create table if not exists presets (
  key          text primary key,
  label        text not null,
  tokens       jsonb not null,          -- { "--ap-bg": "#...", "--ap-serif": "'X', serif", ... }
  fonts        jsonb not null,          -- { "url": "...", "serif": "...", "sans": "..." }
  default_copy jsonb not null default '{}'::jsonb,
  thumbnail    text,
  sort         int  not null default 0,
  created_at   timestamptz not null default now()
);

-- ---------------------------------------------------------------------------
-- Site requests — the intake submission + build/queue record (the CRM row).
-- ---------------------------------------------------------------------------
create table if not exists site_requests (
  id                     uuid primary key default gen_random_uuid(),
  created_at             timestamptz not null default now(),
  updated_at             timestamptz not null default now(),
  status                 site_status not null default 'submitted',

  -- Customer + intake answers
  customer_name          text,
  customer_email         text,
  business_name          text,
  positioning            text,
  answers                jsonb not null default '{}'::jsonb,
  emphasis_categories    jsonb not null default '[]'::jsonb,

  -- Chosen design (denormalized from the preset + any overrides)
  preset_key             text references presets(key),
  tokens                 jsonb not null default '{}'::jsonb,
  fonts                  jsonb not null default '{}'::jsonb,
  copy                   jsonb not null default '{}'::jsonb,
  logo_path              text,      -- Supabase Storage path in `logos`
  hero_image_path        text,      -- Supabase Storage path in `hero-images`

  -- Deploy target + result
  target_wp_url          text,
  target_wp_user         text,
  target_wp_app_password text,      -- write-only via RLS; never selected to client
  deploy_result          jsonb,
  deployed_url           text,

  submitted_at           timestamptz not null default now(),
  deployed_at            timestamptz
);

create index if not exists site_requests_status_idx on site_requests (status, created_at desc);

create or replace function touch_updated_at() returns trigger
language plpgsql as $$
begin new.updated_at = now(); return new; end $$;

drop trigger if exists site_requests_touch on site_requests;
create trigger site_requests_touch before update on site_requests
  for each row execute function touch_updated_at();

-- ---------------------------------------------------------------------------
-- RLS
-- ---------------------------------------------------------------------------
alter table site_requests enable row level security;
alter table presets       enable row level security;
alter table admins        enable row level security;

-- Presets are public-read (intake needs them); only admins write.
drop policy if exists presets_read on presets;
create policy presets_read on presets for select using (true);
drop policy if exists presets_admin_write on presets;
create policy presets_admin_write on presets for all using (is_admin()) with check (is_admin());

-- Intake submissions are written by the server route (/api/intake) using the
-- service-role key, which bypasses RLS. We deliberately grant NO anon insert
-- policy here, so the public cannot write rows directly (no spam / field
-- tampering) — all submissions flow through the validated server route.

-- Admins can read + update everything.
drop policy if exists site_requests_admin_read on site_requests;
create policy site_requests_admin_read on site_requests for select using (is_admin());
drop policy if exists site_requests_admin_update on site_requests;
create policy site_requests_admin_update on site_requests for update using (is_admin()) with check (is_admin());

-- Admins table: readable only by admins (used by is_admin(), which is
-- security-definer so it bypasses this for its own check).
drop policy if exists admins_admin_read on admins;
create policy admins_admin_read on admins for select using (is_admin());

-- ---------------------------------------------------------------------------
-- Storage buckets for uploaded/generated assets.
-- ---------------------------------------------------------------------------
insert into storage.buckets (id, name, public)
  values ('logos', 'logos', true), ('hero-images', 'hero-images', true)
  on conflict (id) do nothing;
