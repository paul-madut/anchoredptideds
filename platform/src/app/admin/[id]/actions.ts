'use server';

import { revalidatePath } from 'next/cache';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';
import { siteConfigFor, setHtmlSource } from '@/lib/artifacts';
import { renderHomeHtml } from '@/lib/renderHome';
import type { SiteStatus, SiteRequest } from '@/lib/types';

const SP_BUCKET = 'site-artifacts';

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

/**
 * Set (or clear, when path is null) one selling-point card's image path, then
 * re-render the homepage if the site is already approved so the change shows
 * immediately. Both bento uploads and the auto-generated art live in the same
 * `selling_point_image_paths` field, so re-rendering never loses the others.
 */
async function setSellingPointImagePath(
  db: ReturnType<typeof createSupabaseAdminClient>, id: string, index: number, path: string | null,
) {
  const { data } = await db.from('site_requests').select('*').eq('id', id).single();
  if (!data) throw new Error('Request not found');
  const row = data as SiteRequest;
  const current = Array.isArray(row.selling_point_image_paths) ? row.selling_point_image_paths : [];
  const paths: (string | null)[] = [0, 1, 2].map((i) => current[i] ?? null);
  paths[index] = path;
  await db.from('site_requests').update({ selling_point_image_paths: paths }).eq('id', id);

  if (row.html_source) {
    const cfg = siteConfigFor({ ...row, selling_point_image_paths: paths });
    await setHtmlSource(id, renderHomeHtml(cfg));
  }
}

/** Attach (or replace) the image on selling-point card `index` (0..2). */
export async function saveSellingPointImage(id: string, index: number, formData: FormData) {
  const admin = await getAdmin();
  if (!admin) throw new Error('Unauthorized');
  if (!Number.isInteger(index) || index < 0 || index > 2) throw new Error('Invalid card');

  const file = formData.get('image');
  if (!(file instanceof File) || file.size === 0) throw new Error('Choose an image file.');
  if (!/^image\//.test(file.type)) throw new Error('That file is not an image.');
  if (file.size > 8 * 1024 * 1024) throw new Error('Image too large (max 8 MB).');

  const ext = file.type.includes('png') ? 'png' : file.type.includes('webp') ? 'webp'
    : file.type.includes('svg') ? 'svg' : file.type.includes('gif') ? 'gif' : 'jpg';
  const bytes = Buffer.from(await file.arrayBuffer());
  const path = `${id}/sp-${index + 1}-manual.${ext}`;

  const db = createSupabaseAdminClient();
  const up = await db.storage.from(SP_BUCKET).upload(path, bytes, { contentType: file.type, upsert: true, cacheControl: '0' });
  if (up.error) throw new Error(up.error.message);

  await setSellingPointImagePath(db, id, index, path);
  revalidatePath(`/admin/${id}`);
}

/** Remove card `index`'s image (reverts to generated art on next render/approve). */
export async function clearSellingPointImage(id: string, index: number) {
  const admin = await getAdmin();
  if (!admin) throw new Error('Unauthorized');
  if (!Number.isInteger(index) || index < 0 || index > 2) throw new Error('Invalid card');
  const db = createSupabaseAdminClient();
  await setSellingPointImagePath(db, id, index, null);
  revalidatePath(`/admin/${id}`);
}
