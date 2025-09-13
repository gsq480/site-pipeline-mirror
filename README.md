# SiteFuse Agency

A professional parent theme for a web design agency, built as a classic PHP theme using Tailwind CSS v4.

## Build
From the theme folder:
```bash
npm install
npm run build
```
This produces `dist/tailwind.css` which the theme enqueues.

## Activate
Upload to `wp-content/themes/` then activate in wp‑admin or via WP‑CLI:
```bash
wp theme activate sitefuse-agency
```

## Change Log – Gutenberg as Source of Truth

- Freeform pipelines now produce a `website.json` with real Gutenberg content. Each page includes either `content_html` (serialized Gutenberg block HTML) or `blocks_raw` (Gutenberg block JSON).
- Deploy never overwrites editor content with shortcodes. The child theme renders `the_content()` on homepage and pages.
- The deploy workflow publishes the exact `website.json` to the secure publisher endpoint and verifies the homepage contains Gutenberg markers (`<!-- wp:`).
- Optionally posts the exact `website.json` to the production n8n webhook when `N8N_WEBHOOK_URL` is set.

### Runbook – Freeform
1. Trigger the "Create Website Freeform" workflow with a prompt.
2. Confirm CI passes the jq validation step for `website.json` (freeform mode).
3. Confirm publisher returns HTTP 200.
4. Open `https://…cloudwaysapps.com/?sf_nocache=1` and view source; look for `<!-- wp:` markers.
5. In WP Admin → Pages → Home shows Gutenberg blocks (no shortcodes).
6. In n8n Executions, payload contains `{ site_url, repo, website }` and `website.pages[0]…` (no legacy root keys).
