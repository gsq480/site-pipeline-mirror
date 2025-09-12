<?php
// SiteFuse Base Theme Functions

// Theme setup
add_action('after_setup_theme', function() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    
    // Register navigation menus
    register_nav_menus([
        'primary' => 'Primary Navigation',
        'footer' => 'Footer Navigation'
    ]);
});

// Enqueue parent theme styles and scripts
add_action('wp_enqueue_scripts', function() {
    // Enqueue Tailwind CSS
    wp_enqueue_style('sitefuse-tailwind', get_template_directory_uri() . '/dist/tailwind.css', [], '1.0.0');
    
    // Enqueue parent theme JS if it exists
    if (file_exists(get_template_directory() . '/assets/js/theme.js')) {
        $js_path = get_template_directory() . '/assets/js/theme.js';
        $js_ver  = file_exists($js_path) ? filemtime($js_path) : null;
        wp_enqueue_script('sitefuse-js', get_template_directory_uri() . '/assets/js/theme.js', [], $js_ver, true);
    }
});
