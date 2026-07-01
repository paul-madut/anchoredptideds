'use server';

import { revalidatePath } from 'next/cache';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import type { SiteStatus } from '@/lib/types';

/** Save the deploy target (URL + Application Password) and mark approved. */
export async function saveTarget(id: string, formData: FormData) {
  const admin = await getAdmin();
  if (!admin) throw new Error('Unauthorized');

  const url = String(formData.get('target_wp_url') ?? '').trim();
  const user = String(formData.get('target_wp_user') ?? '').trim();
  const password = String(formData.get('target_wp_app_password') ?? '').trim();

  const db = createSupabaseAdminClient();
  const patch: Record<string, unknown> = { target_wp_url: url || null, target_wp_user: user || null };
  // Only overwrite the stored password when a new one is supplied.
  if (password) patch.target_wp_app_password = password;
  await db.from('site_requests').update(patch).eq('id', id);
  revalidatePath(`/admin/${id}`);
}

/** Move a request along the pipeline (in_review / approved). */
export async function setStatus(id: string, status: SiteStatus) {
  const admin = await getAdmin();
  if (!admin) throw new Error('Unauthorized');
  const db = createSupabaseAdminClient();
  await db.from('site_requests').update({ status }).eq('id', id);
  revalidatePath(`/admin/${id}`);
}
