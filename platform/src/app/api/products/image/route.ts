import { NextRequest, NextResponse } from 'next/server';
import { getAdmin } from '@/lib/auth';
import { createSupabaseAdminClient } from '@/lib/supabase/admin';

export const maxDuration = 30;

/**
 * POST { id, image_data_url } — attach the designer's image to a master product.
 * Stored in the public `product-images` bucket; the per-site CSV export points
 * WooCommerce at this URL so the importer sideloads it into the media library.
 */
export async function POST(req: NextRequest) {
  const admin = await getAdmin();
  if (!admin) return NextResponse.json({ ok: false, error: 'Unauthorized' }, { status: 401 });

  const body = await req.json().catch(() => null);
  const id = body?.id;
  const dataUrl = body?.image_data_url;
  if (!id || typeof dataUrl !== 'string') {
    return NextResponse.json({ ok: false, error: 'id and image_data_url required' }, { status: 400 });
  }
  const m = dataUrl.match(/^data:(image\/(?:png|jpeg|webp));base64,(.+)$/);
  if (!m) return NextResponse.json({ ok: false, error: 'Use a PNG, JPG, or WebP image.' }, { status: 400 });
  const [, mime, b64] = m;
  const bytes = Buffer.from(b64, 'base64');
  if (bytes.length > 5 * 1024 * 1024) return NextResponse.json({ ok: false, error: 'Image over 5 MB.' }, { status: 400 });

  const db = createSupabaseAdminClient();
  const ext = mime.includes('webp') ? 'webp' : mime.includes('jpeg') ? 'jpg' : 'png';
  const path = `${id}.${ext}`;
  const up = await db.storage.from('product-images').upload(path, bytes, { contentType: mime, upsert: true, cacheControl: '0' });
  if (up.error) return NextResponse.json({ ok: false, error: up.error.message }, { status: 500 });

  const { error } = await db.from('products')
    .update({ image_path: path, updated_at: new Date().toISOString() })
    .eq('id', id);
  if (error) return NextResponse.json({ ok: false, error: error.message }, { status: 500 });

  const url = db.storage.from('product-images').getPublicUrl(path).data.publicUrl;
  return NextResponse.json({ ok: true, url: `${url}?v=${Date.now()}` });
}
