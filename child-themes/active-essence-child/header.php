<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?php bloginfo('description'); ?>">
  <?php wp_head(); ?>
</head>
<body <?php body_class('font-sans antialiased'); ?> style="background-color: var(--color-cream); color: var(--color-dark);">
<a class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 btn btn-primary z-50" href="#main">Skip to content</a>

<header class="sticky top-0 z-40" style="background: rgba(255,255,255,.95); backdrop-filter: blur(12px); border-bottom: 1px solid #e5e7eb;">
  <div class="container">
    <div class="flex items-center justify-between h-20">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center space-x-2">
        <?php if (has_custom_logo()): ?>
          <?php the_custom_logo(); ?>
        <?php else: ?>
          <span style="font-size:1.5rem; font-weight:800; letter-spacing:-.025em; color:var(--color-brand);"><?php bloginfo('name'); ?></span>
        <?php endif; ?>
      </a>
      
      <nav class="hidden lg:flex items-center space-x-1">
        <?php wp_nav_menu([
          'theme_location' => 'primary',
          'menu_class' => 'flex items-center space-x-1',
          'container' => false,
          'link_class' => 'nav-link',
          'fallback_cb' => 'wp_page_menu'
        ]); ?>
      </nav>
      
      <button class="lg:hidden mobile-menu-btn p-2 rounded-lg" style="color: var(--color-brand);">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>
    
    <div class="mobile-menu hidden lg:hidden py-4 border-t border-gray-200">
      <?php wp_nav_menu([
        'theme_location' => 'primary',
        'menu_class' => 'flex flex-col space-y-2',
        'container' => false,
        'link_class' => 'nav-link block',
        'fallback_cb' => 'wp_page_menu'
      ]); ?>
    </div>
  </div>
</header>
