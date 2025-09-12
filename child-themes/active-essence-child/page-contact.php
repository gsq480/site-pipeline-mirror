<?php get_header(); ?>
<main id="main" class="py-20">
  <div class="container">
    <h1 class="section-title text-center mb-16"><?php the_title(); ?></h1>
    <div class="grid lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
      <div class="card">
        <?php echo do_shortcode('[contact_form]'); ?>
      </div>
      <div class="space-y-8">
        <div>
          <h3 class="text-xl font-semibold mb-4">Contact Information</h3>
          <div class="space-y-4">
            <p class="flex items-center space-x-3">
              <span class="text-xl">📍</span>
              <span>123 Business Street, City, State 12345</span>
            </p>
            <p class="flex items-center space-x-3">
              <span class="text-xl">📞</span>
              <span>(555) 123-4567</span>
            </p>
            <p class="flex items-center space-x-3">
              <span class="text-xl">✉️</span>
              <span>hello@<?php echo str_replace(' ', '', strtolower(get_bloginfo('name'))); ?>.com</span>
            </p>
          </div>
        </div>
        <div>
          <h3 class="text-xl font-semibold mb-4">Business Hours</h3>
          <div class="space-y-2 text-gray-600">
            <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
            <p>Saturday: 10:00 AM - 4:00 PM</p>
            <p>Sunday: Closed</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php get_footer(); ?>
