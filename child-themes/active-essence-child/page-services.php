<?php get_header(); ?>
<main id="main" class="py-20">
  <div class="container">
    <h1 class="section-title text-center mb-16"><?php the_title(); ?></h1>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php
      $services = get_posts(['post_type' => 'service', 'numberposts' => -1]);
      foreach ($services as $service) : setup_postdata($service); ?>
        <div class="card">
          <?php if (has_post_thumbnail($service->ID)) : ?>
            <img src="<?php echo get_the_post_thumbnail_url($service->ID, 'medium'); ?>" 
                 alt="<?php echo get_the_title($service->ID); ?>" 
                 class="w-full h-48 object-cover rounded-lg mb-4">
          <?php endif; ?>
          <h3 class="text-xl font-semibold mb-3"><?php echo get_the_title($service->ID); ?></h3>
          <p class="text-gray-600"><?php echo get_the_excerpt($service->ID); ?></p>
        </div>
      <?php endforeach; wp_reset_postdata(); ?>
    </div>
  </div>
</main>
<?php get_footer(); ?>
