<?php
// Theme setup
add_action('wp_enqueue_scripts', function () {
  $parent = get_template_directory_uri() . '/dist/tailwind.css';
  wp_enqueue_style('sitefuse-parent', $parent, [], null);
  $child_path = get_stylesheet_directory() . '/dist/tailwind.css';
  $ver = file_exists($child_path) ? filemtime($child_path) : null;
  wp_enqueue_style('sitefuse-child', get_stylesheet_directory_uri() . '/dist/tailwind.css', ['sitefuse-parent'], $ver);
  $js_path = get_stylesheet_directory() . '/assets/js/theme.js';
  $js_ver  = file_exists($js_path) ? filemtime($js_path) : null;
  wp_enqueue_script('sitefuse-js', get_stylesheet_directory_uri() . '/assets/js/theme.js', [], $js_ver, true);
});

// Register navigation menus
register_nav_menus([
  'primary' => 'Primary Navigation',
  'footer' => 'Footer Navigation'
]);

// Add theme support
add_theme_support('post-thumbnails');
add_theme_support('custom-logo');
add_theme_support('title-tag');
add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

// Custom post types
add_action('init', function() {
  // Testimonials
  register_post_type('testimonial', [
    'labels' => [
      'name' => 'Testimonials',
      'singular_name' => 'Testimonial'
    ],
    'public' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'menu_icon' => 'dashicons-format-quote'
  ]);

  // Services
  register_post_type('service', [
    'labels' => [
      'name' => 'Services',
      'singular_name' => 'Service'
    ],
    'public' => true,
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    'menu_icon' => 'dashicons-admin-tools'
  ]);

  // Portfolio/Gallery
  register_post_type('portfolio', [
    'labels' => [
      'name' => 'Portfolio',
      'singular_name' => 'Portfolio Item'
    ],
    'public' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'menu_icon' => 'dashicons-portfolio'
  ]);
});

// Custom fields helper
function get_business_info($field) {
  $info = get_option('business_info', []);
  return $info[$field] ?? '';
}

// Contact form shortcode
add_shortcode('contact_form', function() {
  ob_start();
  ?>
  <form class="contact-form space-y-4" action="#" method="post">
    <div class="grid md:grid-cols-2 gap-4">
      <input type="text" name="name" placeholder="Your Name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand">
      <input type="email" name="email" placeholder="Your Email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand">
    </div>
    <input type="text" name="subject" placeholder="Subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand">
    <textarea name="message" rows="5" placeholder="Your Message" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand"></textarea>
    <button type="submit" class="btn btn-primary w-full">Send Message</button>
  </form>
  <?php
  return ob_get_clean();
});
