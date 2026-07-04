import fs from 'node:fs';
import zlib from 'node:zlib';

const SRC = '/Users/paulmadut/Documents/anchoredptideds/ap/Anchored Peptides (standalone).html';
const OUT = process.argv[2] || './decoded-with-runtime.html';
const s = fs.readFileSync(SRC, 'utf8');

const man = JSON.parse(s.match(/<script type="__bundler\/manifest">\s*([\s\S]*?)\s*<\/script>/)[1]);
let tpl = JSON.parse(s.match(/<script type="__bundler\/template">\s*([\s\S]*?)\s*<\/script>/)[1]);

function decompress(buf) {
  for (const fn of [zlib.gunzipSync, zlib.inflateSync, zlib.inflateRawSync, zlib.brotliDecompressSync]) {
    try { return fn(buf); } catch { /* try next */ }
  }
  throw new Error('could not decompress');
}

let inlined = 0;
for (const [uuid, e] of Object.entries(man)) {
  if (!tpl.includes(uuid)) continue;
  let bytes = Buffer.from(e.data, 'base64');
  if (e.compressed) bytes = decompress(bytes);
  const dataUrl = `data:${e.mime};base64,${bytes.toString('base64')}`;
  tpl = tpl.split(uuid).join(dataUrl);
  inlined++;
}

fs.writeFileSync(OUT, tpl);
console.error(`inlined ${inlined} assets → ${OUT} (${(tpl.length / 1048576).toFixed(2)} MB)`);
