import { createSupabaseAdminClient } from './supabase/admin';
import { buildProvisionConfig } from './buildConfig';
import { generateBundle } from './artifacts';
import type { SiteRequest, DeployResult, SiteStatus } from './types';

const PROVISION_PATH = '/wp-json/ap-provision/v1/build';

/** Normalize a site URL and join the provisioner path. */
function provisionEndpoint(siteUrl: string): string {
  return siteUrl.replace(/\/+$/, '') + PROVISION_PATH;
}

/**
 * Deploy worker: builds the provisioner payload from a site_request row and
 * POSTs it to the target WordPress (authenticated with the stored Application
 * Password), advancing status building -> deploying -> live | failed.
 *
 * Runs with the service-role client so it can read the app password and write
 * status regardless of RLS. Long-running (image sideload + CSV import) — call
 * from a route handler with an extended timeout and let the CRM poll status.
 */
export async function runDeploy(requestId: string): Promise<DeployResult> {
  const db = createSupabaseAdminClient();

  const setStatus = async (status: SiteStatus, patch: Record<string, unknown> = {}) => {
    await db.from('site_requests').update({ status, ...patch }).eq('id', requestId);
  };

  const { data, error } = await db.from('site_requests').select('*').eq('id', requestId).single();
  if (error || !data) return { ok: false, error: error?.message ?? 'Request not found' };
  const row = data as SiteRequest;

  if (!row.target_wp_url || !row.target_wp_user || !row.target_wp_app_password) {
    const result: DeployResult = { ok: false, error: 'Target WordPress URL / credentials missing.' };
    await setStatus('failed', { deploy_result: result });
    return result;
  }

  await setStatus('building');

  // ACTIVATE also assembles the WordPress bundle from the reviewed HTML (best
  // effort — records a downloadable artifact; deploy proceeds via options).
  await generateBundle(requestId).catch(() => null);

  let config;
  try {
    config = buildProvisionConfig(row);
  } catch (e) {
    const result: DeployResult = { ok: false, error: (e as Error).message };
    await setStatus('failed', { deploy_result: result });
    return result;
  }

  await setStatus('deploying');

  const auth = Buffer.from(`${row.target_wp_user}:${row.target_wp_app_password}`).toString('base64');

  try {
    const res = await fetch(provisionEndpoint(row.target_wp_url), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Basic ${auth}`,
      },
      body: JSON.stringify(config),
      // Provisioning can take minutes (image sideload + CSV import).
      signal: AbortSignal.timeout(1000 * 60 * 8),
    });

    const text = await res.text();
    let payload: DeployResult;
    try {
      payload = JSON.parse(text);
    } catch {
      payload = { ok: false, error: `Non-JSON response (${res.status}): ${text.slice(0, 300)}` };
    }

    if (!res.ok || !payload.ok) {
      const result: DeployResult = { ok: false, error: payload.error ?? `HTTP ${res.status}`, warnings: payload.warnings };
      await setStatus('failed', { deploy_result: result });
      return result;
    }

    const liveUrl = payload.live_url ?? row.target_wp_url;
    await setStatus('live', {
      deploy_result: payload,
      deployed_url: liveUrl,
      deployed_at: new Date().toISOString(),
    });
    return payload;
  } catch (e) {
    const result: DeployResult = { ok: false, error: (e as Error).message };
    await setStatus('failed', { deploy_result: result });
    return result;
  }
}
