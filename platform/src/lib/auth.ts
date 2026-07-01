import { createSupabaseServerClient } from './supabase/server';

/** Emails allowed into /admin (kept in sync with the `admins` DB table for RLS). */
export function adminEmails(): string[] {
  return (process.env.ADMIN_EMAILS ?? '')
    .split(',')
    .map((e) => e.trim().toLowerCase())
    .filter(Boolean);
}

export interface AdminUser {
  id: string;
  email: string;
}

/** Return the signed-in admin, or null. Use to gate admin pages + routes. */
export async function getAdmin(): Promise<AdminUser | null> {
  const supabase = createSupabaseServerClient();
  const { data } = await supabase.auth.getUser();
  const user = data.user;
  if (!user?.email) return null;
  if (!adminEmails().includes(user.email.toLowerCase())) return null;
  return { id: user.id, email: user.email };
}
