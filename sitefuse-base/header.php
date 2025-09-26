<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <h1 class="text-xl font-bold">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="text-gray-900">
                            <?php bloginfo('name'); ?>
                        </a>
                    </h1>
                <?php endif; ?>
            </div>
            
            <nav class="hidden md:block">
                <?php wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_class' => 'flex space-x-4',
                    'container' => false,
                    'fallback_cb' => 'wp_page_menu'
                ]); ?>
            </nav>
        </div>
    </div>
</header>
