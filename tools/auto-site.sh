#!/usr/bin/env bash
set -euo pipefail
# === Inputs ==============================================================
SLUG="${1:?Usage: tools/auto-site.sh slug \"Site Name\" [#brand] [#accent] [hero_url]}"
NAME="${2:-Client Site}"
BRAND="${3:-#8B5E3C}"
ACCENT="${4:-#C9A66B}"
HERO_URL="${5:-https://picsum.photos/seed/coffee/1200/800}"

# === Local paths =========================================================
PARENT_DIR="$PWD"                                  # repo root is parent theme
THEMES_DIR="$PARENT_DIR/.."                        # wp-content/themes
CHILD_DIR="$THEMES_DIR/${SLUG}-child"

# === Cloudways deploy settings (edit once) ===============================
HOST="${CLOUDWAYS_HOST:-170.64.197.187}"
USER="${CLOUDWAYS_USER:-master_qyzkybrspk}"
KEY="${CLOUDWAYS_KEY:-$HOME/.ssh/id_rsa_cloudways_v2}"
APP_PATH="/home/1504475.cloudwaysapps.com/zhudbfctnw/public_html"

# === Prep child folder ===================================================
rm -rf "$CHILD_DIR"
mkdir -p "$CHILD_DIR/assets/css" "$CHILD_DIR/assets/img" "$CHILD_DIR/dist" "$CHILD_DIR/parts"

# Style header
cat > "$CHILD_DIR/style.css" <<CSS
/*
Theme Name: ${NAME} Child
Template: sitefuse-agency
Text Domain: ${SLUG}-child
Version: 1.0.0
*/
CSS

# Enqueue parent + child CSS
cat > "$CHILD_DIR/functions.php" <<'PHP'
<?php
add_action('wp_enqueue_scripts', function () {
  $parent = get_template_directory_uri() . '/dist/tailwind.css';
  wp_enqueue_style('sitefuse-parent', $parent, [], null);
  $child_path = get_stylesheet_directory() . '/dist/tailwind.css';
  $ver = file_exists($child_path) ? filemtime($child_path) : null;
  wp_enqueue_style('sitefuse-child', get_stylesheet_directory_uri() . '/dist/tailwind.css', ['sitefuse-parent'], $ver);
});
PHP

# Child Tailwind tokens + utilities
cat > "$CHILD_DIR/assets/css/tailwind.css" <<CSS
@import "tailwindcss" source("../..");
.btn { @apply inline-flex items-center justify-center rounded-lg px-5 py-2 font-semibold; }
/* Brand tokens */
.btn { @apply inline-flex items-center justify-center rounded-lg px-5 py-2 font-semibold; }@theme {
  --font-sans: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial;
  --color-brand: ${BRAND};
  --color-brand-600: color-mix(in oklab, ${BRAND} 85%, black);
  --color-accent: ${ACCENT};
  --color-cream: #FFF7EC;
}
.container { @apply mx-auto max-w-6xl; }
.btn { @apply inline-flex items-center justify-center rounded-lg px-5 py-2 font-semibold; }
.btn { @apply inline-flex items-center justify-center rounded-lg px-5 py-2 font-semibold; }
.btn-primary {  inline-flex items-center justify-center rounded-lg px-5 py-2 font-semibold bg-[--color-brand] text-white hover:bg-[--color-brand-600];  }
.card { @apply bg-white rounded-xl shadow p-8 border border-slate-100; }
.section-title { @apply text-2xl md:text-3xl font-extrabold tracking-tight; }
.lead { @apply text-lg text-slate-700; }
.nav-link { @apply hover:underline; }
CSS

# Header
cat > "$CHILD_DIR/header.php" <<'PHP'
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class('bg-[--color-cream] text-slate-900 font-sans'); ?>>
<a class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 p-2 bg-white rounded" href="#main">Skip to content</a>
<header class="bg-white/90 backdrop-blur border-b">
  <div class="container px-4 py-4 flex items-center justify-between">
    <a class="text-xl font-extrabold tracking-tight text-[--color-brand]" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <nav class="hidden md:block">
      <?php wp_nav_menu(['theme_location'=>'primary','menu_class'=>'md:flex md:items-center md:gap-6','container'=>false,'fallback_cb'=>'__return_empty_string','link_before'=>'<span class="nav-link">','link_after'=>'</span>']); ?>
    </nav>
  </div>
</header>
PHP

# Footer
cat > "$CHILD_DIR/footer.php" <<'PHP'
<footer class="mt-16 bg-slate-900 text-slate-200">
  <div class="container px-4 py-10 grid gap-6 md:grid-cols-3">
    <div><div class="text-lg font-semibold">About</div><p class="text-sm opacity-80 mt-2">Neighbourhood coffee, fresh bakes, warm service.</p></div>
    <div><div class="text-lg font-semibold">Hours</div><ul class="text-sm opacity-80 mt-2 space-y-1"><li>Mon to Fri 7am to 3pm</li><li>Sat to Sun 8am to 2pm</li></ul></div>
    <div><div class="text-lg font-semibold">Find us</div><p class="text-sm opacity-80 mt-2">123 Lonsdale Street, Canberra</p></div>
  </div>
  <div class="border-t border-slate-800"><div class="container px-4 py-4 text-sm opacity-70 flex justify-between"><span>© <?php echo date('Y'); ?> <?php bloginfo('name'); ?></span><span>Powered by WordPress</span></div></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
PHP

# Front page
cat > "$CHILD_DIR/front-page.php" <<'PHP'
<?php get_header(); ?>
<main id="main" class="container px-4 py-10 space-y-12">

  <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[--color-brand] to-[--color-accent] text-white">
    <div class="px-6 py-16 md:px-12 md:py-24 grid md:grid-cols-2 gap-8 items-center">
      <div>
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight">Coffee, done properly</h1>
        <p class="mt-4 lead text-white/90">Hand roasted beans, seasonal bakes and friendly faces in the heart of Canberra.</p>
        <div class="mt-8 flex gap-4">
          <a href="<?php echo esc_url(home_url('/menu')); ?>" class="btn-primary">View menu</a>
          <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn ring-1 ring-inset ring-white/60 text-white">Book a table</a>
        </div>
      </div>
      <div class="rounded-xl overflow-hidden shadow">
        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/hero.jpg' ); ?>" alt="Fresh coffee" class="w-full h-full object-cover">
      </div>
    </div>
  </section>

  <section class="grid md:grid-cols-3 gap-6">
    <article class="card"><h3 class="font-bold text-lg">House blend</h3><p class="mt-2 text-sm text-slate-600">Balanced chocolate notes with a bright citrus finish.</p></article>
    <article class="card"><h3 class="font-bold text-lg">Daily bakes</h3><p class="mt-2 text-sm text-slate-600">Croissants, banana bread, seasonal tarts baked every morning.</p></article>
    <article class="card"><h3 class="font-bold text-lg">Local first</h3><p class="mt-2 text-sm text-slate-600">We source dairy, flour and produce from Canberra suppliers.</p></article>
  </section>

  <section class="rounded-2xl bg-white p-8 shadow">
    <h2 class="section-title">Popular this week</h2>
    <ul class="mt-6 grid md:grid-cols-2 gap-4 text-slate-700">
      <li class="flex items-center justify-between border-b py-5 px-4 md:px-6"><span>Flat white</span><span class="font-semibold">$4.5</span></li>
      <li class="flex items-center justify-between border-b py-5 px-4 md:px-6"><span>Cappuccino</span><span class="font-semibold">$4.5</span></li>
      <li class="flex items-center justify-between border-b py-5 px-4 md:px-6"><span>Almond croissant</span><span class="font-semibold">$6.0</span></li>
      <li class="flex items-center justify-between border-b py-5 px-4 md:px-6"><span>Ham and cheese toastie</span><span class="font-semibold">$9.0</span></li>
    </ul>
    <a class="btn-primary mt-6 inline-block" href="<?php echo esc_url(home_url('/menu')); ?>">Full menu</a>
  </section>

  <section><h2 class="section-title">What customers say</h2>
    <div class="mt-6 grid md:grid-cols-3 gap-6">
      <blockquote class="card"><p>Best flat white in the city.</p><cite class="text-sm opacity-70">Alex</cite></blockquote>
      <blockquote class="card"><p>Lovely staff and a proper toastie.</p><cite class="text-sm opacity-70">Renee</cite></blockquote>
      <blockquote class="card"><p>My go to for mornings.</p><cite class="text-sm opacity-70">Sam</cite></blockquote>
    </div>
  </section>

</main>
<?php get_footer(); ?>
PHP

# Page template
cat > "$CHILD_DIR/page.php" <<'PHP'
<?php get_header(); ?>
<main id="main" class="container px-4 py-10">
  <article class="bg-white rounded-xl shadow p-6">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <h1 class="text-3xl font-bold mb-4"><?php the_title(); ?></h1>
      <div class="prose"><?php the_content(); ?></div>
    <?php endwhile; endif; ?>
  </article>
</main>
<?php get_footer(); ?>
PHP

# Assets
curl -fsSL "$HERO_URL" -o "$CHILD_DIR/assets/img/hero.jpg" >/dev/null || true

# Build Tailwind using parent's toolchain
ln -sfn "$PARENT_DIR/node_modules" "$CHILD_DIR/node_modules"
"$PARENT_DIR/node_modules/.bin/tailwindcss" --cwd "$CHILD_DIR" --input "./assets/css/tailwind.css" --output "./dist/tailwind.css" --minify

# Zip child for deployment
ZIP="/tmp/${SLUG}-child.zip"
(cd "$THEMES_DIR" && rm -f "$ZIP" && zip -qr "$ZIP" "${SLUG}-child")

# Deploy to Cloudways via SSH + wp-cli
ZIP_BASE="$(basename "$ZIP")"
scp -i "$KEY" "$ZIP" "$USER@$HOST:~/"
ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root"ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root" wp theme install ~/$ZIP_BASE --force --activate --allow-root ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root"ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root" rm -f ~/$ZIP_BASE ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root"ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root" wp cache flush --allow-root ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root"ssh -i "$KEY" "$USER@$HOST" bash -lc "cd "$APP_PATH" && mv ~/$ZIP_BASE . && wp theme install $ZIP_BASE --force --activate --allow-root && wp cache flush --allow-root && wp theme status --allow-root" wp theme status --allow-root"

echo "✅ Deployed ${SLUG}-child and activated on Cloudways."
