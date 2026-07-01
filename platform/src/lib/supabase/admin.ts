import { createClient } from '@supabase/supabase-js';

/**
 * Service-role client — bypasses RLS. SERVER ONLY. Used by the deploy worker and
 * intake insert path (which need to read the app password / write status). Never
 * import this into a client component.
 */
export function createSupabaseAdminClient() {
  return createClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.SUPABASE_SERVICE_ROLE_KEY!,
    { auth: { persistSession: false, autoRefreshToken: false } },
  );
}
