#!/usr/bin/env node
/**
 * Validate website.json against blocks-config.json and filter unknown blocks.
 * Usage: node tools/validate-website.mjs --in website.json --config blocks-config.json --out website.json
 */
import fs from 'node:fs';
import path from 'node:path';

function readJson(p) {
  try { return JSON.parse(fs.readFileSync(p, 'utf8')); } catch (e) { throw new Error(`Failed to read JSON '${p}': ${e.message}`); }
}

function writeJson(p, data) {
  fs.writeFileSync(p, JSON.stringify(data, null, 2) + '\n');
}

function parseArgs(argv) {
  const args = { in: 'website.json', config: 'blocks-config.json', out: 'website.json', strict: false };
  for (let i = 2; i < argv.length; i++) {
    const k = argv[i];
    const v = argv[i + 1];
    if (k === '--in') args.in = v;
    if (k === '--config') args.config = v;
    if (k === '--out') args.out = v;
    if (k === '--strict') args.strict = true;
  }
  return args;
}

function normalizePages(site) {
  // Accept {pages:[{blocks:[]}]}, {blocks:[]}, or legacy shapes
  if (Array.isArray(site?.pages)) return site.pages;
  if (Array.isArray(site?.home?.blocks)) return [{ slug: 'home', title: 'Home', blocks: site.home.blocks }];
  if (Array.isArray(site?.blocks)) return [{ slug: 'home', title: 'Home', blocks: site.blocks }];
  return [];
}

function detectType(b) {
  if (typeof b?.type === 'string' && b.type) return b.type;
  if (typeof b?.variant === 'string' && b.variant) return b.variant;
  if (typeof b?.name === 'string' && b.name.startsWith('block/')) return b.name.replace(/^block\//, '');
  return '';
}

function main() {
  const { in: inPath, config: cfgPath, out: outPath, strict } = parseArgs(process.argv);
  const site = readJson(inPath);
  const cfg = readJson(cfgPath);
  const allowed = new Set((cfg?.blocks || []).map(b => b.type).filter(Boolean));
  const pages = normalizePages(site);
  if (!pages.length) {
    console.log('[validate] No pages/blocks found; nothing to validate.');
    fs.writeFileSync('validate-report.txt', 'No pages/blocks found; nothing to validate.\n');
    return;
  }

  let removed = 0;
  const unknownTypes = {};
  let dedupRemoved = 0;
  const latestByType = new Map(); // type -> {ts, pageIndex, block}
  for (const page of pages) {
    const keep = [];
    for (const b of (page.blocks || [])) {
      const t = detectType(b);
      if (t && allowed.has(t)) {
        // ensure type field set for downstream use
        if (!b.type) b.type = t;
        // Deduplicate by type: keep newest by meta.updated_at
        const ts = Date.parse(b?.meta?.updated_at || 0) || 0;
        const prev = latestByType.get(t);
        if (!prev || ts > prev.ts) {
          latestByType.set(t, { ts, block: b });
        }
      } else {
        removed++;
        const key = t || '(missing)';
        unknownTypes[key] = (unknownTypes[key] || 0) + 1;
      }
    }
    // page.blocks temporarily not set; we'll rebuild after dedup
  }

  // Rebuild pages with deduplicated latest block per type, preserving original pages if possible
  const byPage = new Map(); // title -> blocks
  const pageTitles = pages.map(p => p.title || p.slug || 'Page');
  const pageTitleByType = new Map();
  // First pass to map types to first page they appeared in
  pages.forEach((p, i) => {
    const title = pageTitles[i];
    (p.blocks||[]).forEach(b => { const t = detectType(b); if (t && !pageTitleByType.has(t)) pageTitleByType.set(t, title); });
  });
  for (const [t, rec] of latestByType) {
    const title = pageTitleByType.get(t) || 'Blocks';
    if (!byPage.has(title)) byPage.set(title, []);
    byPage.get(title).push(rec.block);
  }
  const newPages = [...byPage.entries()].map(([title, blocks]) => ({ slug: (title||'').toLowerCase().replace(/\s+/g,'-'), title, blocks }));
  // Count duplicates removed: original kept count - dedup count
  const originalCount = pages.reduce((n,p)=> n + ((p.blocks||[]).filter(b => allowed.has(detectType(b))).length), 0);
  const dedupCount = [...latestByType.keys()].length;
  dedupRemoved = Math.max(0, originalCount - dedupCount);
  // Write back
  site.pages = newPages;

  // Write back to the same structure as input when possible
  let out = site;
  if (Array.isArray(site?.pages)) {
    out = { ...site, pages };
  } else if (Array.isArray(site?.home?.blocks)) {
    out = { ...site, home: { ...(site.home || {}), blocks: pages[0]?.blocks || [] } };
  } else if (Array.isArray(site?.blocks)) {
    out = { ...site, blocks: pages[0]?.blocks || [] };
  } else {
    // Fallback to normalized structure
    out = { ...site, pages };
  }

  writeJson(outPath, out);
  let report = '';
  if (removed) {
    report += `Filtered ${removed} unknown block(s).\n`;
    const entries = Object.entries(unknownTypes)
      .sort((a,b) => b[1]-a[1])
      .map(([t,c]) => `- ${t}: ${c}`)
      .join('\n');
    if (entries) report += `Unknown types:\n${entries}\n`;
    console.log(`[validate] ${report.trim()}`);
  } else {
    report += 'All blocks are valid.\n';
    console.log('[validate] All blocks are valid.');
  }
  if (dedupRemoved) {
    report += `Deduplicated ${dedupRemoved} older block instance(s) by type.\n`;
  }
  try { fs.writeFileSync('validate-report.txt', report); } catch {}
  if (strict && removed) {
    throw new Error(`Strict mode: ${removed} unknown block(s) detected`);
  }
}

try { main(); } catch (err) { console.error('[validate] Error:', err?.message || err); process.exit(1); }
