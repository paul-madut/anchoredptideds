-- Per-slot image for each of the three selling-point ("bento") cards.
-- Holds the EFFECTIVE Storage path (in the `site-artifacts` bucket) for each
-- slot, whether AI-generated at approve or manually attached by an admin.
-- Index 0..2 maps 1:1 to selling_points[0..2]; a null/absent slot falls back to
-- generated art at render time.
alter table site_requests
  add column if not exists selling_point_image_paths jsonb not null default '[]'::jsonb;
