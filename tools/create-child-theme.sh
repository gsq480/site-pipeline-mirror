#!/usr/bin/env bash
set -euo pipefail

PARENT="sitefuse-agency"
SLUG="${1:?Usage: create-child-theme.sh client-slug [Client Name]}"
NAME="${2:-Client Site}"

# this script runs from wp-content/themes/sitefuse-agency
THEMES=".."                          # all themes live here
DEST="$THEMES/${SLUG}-child"         # child sits alongside parent

# fresh copy of parent into child (minus build artefacts)
rm -rf "$DEST"
mkdir -p "$DEST"
rsync -a "$THEMES/$PARENT/" "$DEST/" \
  --exclude ".git" --exclude "node_modules" --exclude ".github" --exclude "dist/tailwind.css"

# child header
cat > "$DEST/style.css" <<CSS
/*
Theme Name: ${NAME} Child
Template: ${PARENT}
Text Domain: ${SLUG}-child
Version: 1.0.0
*/
CSS

# enqueue parent then child CSS
cat > "$DEST/functions.php" <<'PHP'
<?php
add_action('wp_enqueue_scripts', function () {
  $parent_css = get_template_directory_uri() . '/dist/tailwind.css';
  wp_enqueue_style('sitefuse-parent', $parent_css, [], null);

  $child_path = get_stylesheet_directory() . '/dist/tailwind.css';
  $ver = file_exists($child_path) ? filemtime($child_path) : null;
  wp_enqueue_style('sitefuse-child', get_stylesheet_directory_uri() . '/dist/tailwind.css', ['sitefuse-parent'], $ver);
});
PHP

# ensure child Tailwind entry
mkdir -p "$DEST/dist" "$DEST/assets/css"
cp "$THEMES/$PARENT/assets/css/tailwind.css" "$DEST/assets/css/tailwind.css"

# ensure parent has CLI, then expose it to the child
npm ci || npm install

# key fix: make Tailwind resolvable in the child by symlinking node_modules
ln -sfn "$THEMES/$PARENT/node_modules" "$DEST/node_modules"

# run parent's CLI but with CWD in child so @import "tailwindcss" resolves via symlink
"$THEMES/$PARENT/node_modules/.bin/tailwindcss" \
  --cwd "$DEST" \
  --input "./assets/css/tailwind.css" \
  --output "./dist/tailwind.css" \
  --minify

echo "✅ Child theme created at $DEST"
