<?php get_header(); ?>
<main id="main" class="py-20">
  <div class="container">
    <div class="max-w-4xl mx-auto">
      <h1 class="section-title text-center mb-8"><?php the_title(); ?></h1>
      <div class="prose prose-lg max-w-none">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
          <?php the_content(); ?>
        <?php endwhile; endif; ?>
      </div>
    </div>
  </div>
</main>
<?php get_footer(); ?>
