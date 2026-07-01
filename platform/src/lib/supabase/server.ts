import { cookies } from 'next/headers';
import { createServerClient, type CookieOptions } from '@supabase/ssr';

type CookieToSet = { name: string; value: string; options: CookieOptions };

/**
 * Request-scoped Supabase client for Server Components / Route Handlers.
 * Uses the anon key + the signed-in user's cookies, so RLS applies as that user
 * (admin gating for /admin flows through is_admin()).
 */
export function createSupabaseServerClient() {
  const cookieStore = cookies();
  return createServerClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY!,
    {
      cookies: {
        getAll: () => cookieStore.getAll(),
        setAll: (toSet: CookieToSet[]) => {
          try {
            toSet.forEach(({ name, value, options }) => cookieStore.set(name, value, options));
          } catch {
            // Called from a Server Component render — cookie writes are ignored;
            // the middleware/session refresh path handles persistence.
          }
        },
      },
    },
  );
}
