#!/usr/bin/env node
/**
 * Convert design-spec.json (exported from Figma plugin) into website.json.
 * Usage: node tools/from-figma-export.mjs --in design-spec.json --out website.json
 * - Writes a brief report to from-figma-report.txt
 */
import fs from 'node:fs';

function readJson(p) {
  try { return JSON.parse(fs.readFileSync(p, 'utf8')); } catch (e) {
    throw new Error(`Failed to read JSON '${p}': ${e.message}`);
  }
}
function writeJson(p, data) {
  fs.writeFileSync(p, JSON.stringify(data, null, 2) + '\n');
}
function parseArgs(argv) {
  const args = { in: 'design-spec.json', out: 'website.json' };
  for (let i = 2; i < argv.length; i++) {
    const k = argv[i];
    const v = argv[i + 1];
    if (k === '--in') args.in = v;
    if (k === '--out') args.out = v;
  }
  return args;
}
function toType(nameOrType) {
  if (!nameOrType) return '';
  const s = String(nameOrType);
  return s.startsWith('block/') ? s.replace(/^block\//, '') : s;
}

function convert(spec, prevSite) {
  const pages = Array.isArray(spec?.pages) ? spec.pages : [];
  const now = new Date().toISOString();

  // Index previous site by type (keep newest by updated_at)
  const prevIndex = new Map();
  const prevPageByType = new Map();
  for (const p of (prevSite?.pages || [])) {
    const pageTitle = p.title || p.slug || 'Page';
    for (const b of (p.blocks || [])) {
      const t = b?.type;
      if (!t) continue;
      const ts = Date.parse(b?.meta?.updated_at || 0) || 0;
      const cur = prevIndex.get(t);
      if (!cur || ts > cur.ts) {
        prevIndex.set(t, { block: b, ts });
        prevPageByType.set(t, pageTitle);
      }
    }
  }

  // Index new export by type (keep first, all have same now timestamp)
  const newIndex = new Map();
  const newPageByType = new Map();
  for (const page of pages) {
    const pageTitle = page.title || page.slug || 'Page';
    for (const b of (page.blocks || [])) {
      const type = toType(b?.name || b?.type || '');
      if (!type) continue;
      if (newIndex.has(type)) continue;
      const nb = { type, meta: { source: 'figma', updated_at: now } };
      if (b.title) nb.title = b.title;
      if (b.subtitle) nb.subtitle = b.subtitle;
      if (b.image) nb.image = b.image;
      if (b.cta) nb.cta = b.cta;
      if (Array.isArray(b.items)) nb.items = b.items;
      if (Array.isArray(b.members)) nb.members = b.members;
      if (Array.isArray(b.images)) nb.images = b.images;
      if (Array.isArray(b.tiers)) nb.tiers = b.tiers;
      newIndex.set(type, { block: nb, ts: Date.parse(now) });
      newPageByType.set(type, pageTitle);
    }
  }

  // Choose newer between prev and new per type
  const types = new Set([...prevIndex.keys(), ...newIndex.keys()]);
  const chosen = new Map(); // type -> {block, pageTitle, source}
  for (const t of types) {
    const prevRec = prevIndex.get(t);
    const newRec = newIndex.get(t);
    if (prevRec && newRec) {
      if (newRec.ts >= prevRec.ts) {
        chosen.set(t, { block: newRec.block, pageTitle: newPageByType.get(t), source: 'figma' });
      } else {
        chosen.set(t, { block: prevRec.block, pageTitle: prevPageByType.get(t), source: 'prev' });
      }
    } else if (newRec) {
      chosen.set(t, { block: newRec.block, pageTitle: newPageByType.get(t), source: 'figma' });
    } else if (prevRec) {
      chosen.set(t, { block: prevRec.block, pageTitle: prevPageByType.get(t), source: 'prev' });
    }
  }

  // Rebuild pages from chosen blocks grouped by page title
  const byPage = new Map();
  for (const [, rec] of chosen) {
    const title = rec.pageTitle || 'Blocks';
    if (!byPage.has(title)) byPage.set(title, []);
    // Ensure meta exists
    if (!rec.block.meta) rec.block.meta = { source: rec.source, updated_at: rec.block?.meta?.updated_at || now };
    byPage.get(title).push(rec.block);
  }
  const outPages = [...byPage.entries()].map(([title, blocks]) => ({ title, slug: (title||'').toLowerCase().replace(/\s+/g,'-'), layout: 'landing', blocks }));
  return { pages: outPages };
}

function diffTypes(prevSite, nextSite) {
  const prev = new Set();
  const next = new Set();
  (prevSite?.pages || []).forEach(p => (p.blocks || []).forEach(b => prev.add(b.type)));
  (nextSite?.pages || []).forEach(p => (p.blocks || []).forEach(b => next.add(b.type)));
  const added = [...next].filter(t => !prev.has(t)).sort();
  const removed = [...prev].filter(t => !next.has(t)).sort();
  return { added, removed };
}

function report(site) {
  const counts = {};
  let pages = 0, blocks = 0;
  for (const p of (site.pages || [])) {
    pages++;
    for (const b of (p.blocks || [])) {
      blocks++;
      counts[b.type] = (counts[b.type] || 0) + 1;
    }
  }
  const lines = [
    `Pages: ${pages}`,
    `Blocks: ${blocks}`,
    'Types:',
    ...Object.entries(counts).sort((a,b)=>b[1]-a[1]).map(([t,c]) => `- ${t}: ${c}`)
  ];
  return lines.join('\n') + '\n';
}

function main() {
  const { in: inPath, out: outPath } = parseArgs(process.argv);
  const spec = readJson(inPath);
  let prev = {};
  try { prev = readJson(outPath); } catch {}
  const out = convert(spec, prev);
  const prevTypes = new Set(); (prev?.pages||[]).forEach(p=> (p.blocks||[]).forEach(b=> prevTypes.add(b.type)));
  const nextTypes = new Set(); (out?.pages||[]).forEach(p=> (p.blocks||[]).forEach(b=> nextTypes.add(b.type)));
  const added = [...nextTypes].filter(t => !prevTypes.has(t)).sort();
  const removed = [...prevTypes].filter(t => !nextTypes.has(t)).sort();
  // Changed: types present in both where we chose figma version
  const chosenFromFigma = new Set();
  const tmpIndex = new Map();
  (out.pages||[]).forEach(p => (p.blocks||[]).forEach(b => tmpIndex.set(b.type, b)));
  // If meta.source === 'figma' and type existed in prev -> changed
  for (const t of nextTypes) {
    const b = tmpIndex.get(t);
    if (b?.meta?.source === 'figma' && prevTypes.has(t)) chosenFromFigma.add(t);
  }

  writeJson(outPath, out);
  let rep = report(out);
  if (added.length) rep += `Added types: ${added.join(', ')}\n`;
  if (chosenFromFigma.size) rep += `Changed types: ${[...chosenFromFigma].sort().join(', ')}\n`;
  if (removed.length) rep += `Removed types: ${removed.join(', ')}\n`;
  fs.writeFileSync('from-figma-report.txt', rep);
  console.log('[from-figma] Wrote website.json and from-figma-report.txt');
}

try { main(); } catch (err) { console.error('[from-figma] Error:', err?.message || err); process.exit(1); }
