/**
 * Thin OpenAI helpers for the intake "Let AI choose" feature.
 * Text via chat completions (JSON mode); images via the images endpoint.
 * Configure with OPENAI_API_KEY (+ optional OPENAI_TEXT_MODEL / OPENAI_IMAGE_MODEL).
 */

export function hasOpenAI(): boolean {
  return Boolean(process.env.OPENAI_API_KEY);
}

const TEXT_MODEL = () => process.env.OPENAI_TEXT_MODEL || 'gpt-4o-mini';
const IMAGE_MODEL = () => process.env.OPENAI_IMAGE_MODEL || 'gpt-image-1';

/** Chat completion constrained to a JSON object. Returns the parsed object. */
export async function chatJSON(system: string, user: string): Promise<Record<string, unknown>> {
  const res = await fetch('https://api.openai.com/v1/chat/completions', {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${process.env.OPENAI_API_KEY}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      model: TEXT_MODEL(),
      temperature: 0.9,
      response_format: { type: 'json_object' },
      messages: [
        { role: 'system', content: system },
        { role: 'user', content: user },
      ],
    }),
  });
  if (!res.ok) throw new Error(`OpenAI text HTTP ${res.status}: ${(await res.text()).slice(0, 200)}`);
  const json = await res.json();
  const content = json?.choices?.[0]?.message?.content;
  if (!content) throw new Error('OpenAI returned no content');
  return JSON.parse(content);
}

/** Generate a single image and return raw PNG bytes. */
export async function generateImageBytes(prompt: string): Promise<Uint8Array> {
  const res = await fetch('https://api.openai.com/v1/images/generations', {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${process.env.OPENAI_API_KEY}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ model: IMAGE_MODEL(), prompt, size: '1024x1024', n: 1 }),
  });
  if (!res.ok) throw new Error(`OpenAI image HTTP ${res.status}: ${(await res.text()).slice(0, 200)}`);
  const json = await res.json();
  const item = json?.data?.[0];
  if (item?.b64_json) return Uint8Array.from(Buffer.from(item.b64_json, 'base64'));
  if (item?.url) return new Uint8Array(await (await fetch(item.url)).arrayBuffer());
  throw new Error('OpenAI image response missing data');
}
