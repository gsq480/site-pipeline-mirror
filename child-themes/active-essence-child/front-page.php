<?php get_header(); ?>
<main id="main">
  <!-- Hero Section -->
  <section class="hero-section relative py-20 lg:py-32">
    <div class="container relative z-10">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="text-white">
          <h1 class="section-title text-white mb-6">
            <?php echo get_bloginfo('description') ?: 'Welcome to ' . get_bloginfo('name'); ?>
          </h1>
          <p class="text-xl text-white/90 mb-8 leading-relaxed">
            <?php
            $website_data = json_decode(file_get_contents(get_template_directory() . '/../../website.json'), true);
            echo esc_html($website_data['description'] ?? 'Discover excellence in every detail. We\'re committed to providing you with exceptional service and results that exceed your expectations.');
            ?>
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="#services" class="btn btn-secondary">Our Services</a>
            <a href="#contact" class="btn" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.3);">Get In Touch</a>
          </div>
        </div>
        <div class="relative">
          <div class="relative rounded-2xl overflow-hidden shadow-2xl">
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/hero.jpg'); ?>" 
                 alt="Hero Image" 
                 class="w-full h-96 lg:h-[500px] object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-20" style="background: white;">
    <div class="container">
      <div class="text-center mb-16">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">We offer comprehensive solutions tailored to your needs</p>
      </div>
      <div class="grid md:grid-cols-3 gap-8">
        <?php
        $website_data = json_decode(file_get_contents(get_template_directory() . '/../../website.json'), true);
        $services = $website_data['services'] ?? [
          ['title' => 'Strategic Planning', 'description' => 'Comprehensive planning and strategy development to help you achieve your goals efficiently and effectively.', 'icon' => '🎯'],
          ['title' => 'Expert Implementation', 'description' => 'Professional implementation of solutions with attention to detail and commitment to excellence in every project.', 'icon' => '⚡'],
          ['title' => 'Ongoing Support', 'description' => 'Continuous support and optimization to ensure your success and adapt to changing needs over time.', 'icon' => '🚀']
        ];
        foreach ($services as $service): ?>
          <div class="service-item text-center">
            <div class="service-icon mx-auto"><?php echo esc_html($service['icon']); ?></div>
            <h3 class="text-xl font-semibold mb-4"><?php echo esc_html($service['title']); ?></h3>
            <p class="text-gray-600 leading-relaxed"><?php echo esc_html($service['description']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-20" style="background: var(--color-cream);">
    <div class="container">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div>
          <h2 class="section-title mb-6">About <?php bloginfo('name'); ?></h2>
          <p class="text-lg text-gray-600 mb-6 leading-relaxed">
            We are passionate professionals dedicated to delivering exceptional results. With years of experience and a commitment to excellence, we work closely with our clients to understand their unique needs and provide tailored solutions.
          </p>
          <p class="text-gray-600 mb-8 leading-relaxed">
            Our team combines expertise, innovation, and personalized service to help you achieve your goals. We believe in building lasting relationships based on trust, quality, and results.
          </p>
          <div class="flex items-center space-x-8">
            <div class="text-center">
              <div class="text-3xl font-bold" style="color: var(--color-brand);">500+</div>
              <div class="text-sm text-gray-600">Happy Clients</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold" style="color: var(--color-brand);">10+</div>
              <div class="text-sm text-gray-600">Years Experience</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold" style="color: var(--color-brand);">98%</div>
              <div class="text-sm text-gray-600">Success Rate</div>
            </div>
          </div>
        </div>
        <div class="relative">
          <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/about.jpg'); ?>" 
               alt="About Us" 
               class="rounded-2xl shadow-xl w-full h-96 object-cover">
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-20" style="background: white;">
    <div class="container">
      <div class="text-center mb-16">
        <h2 class="section-title">What Our Clients Say</h2>
        <p class="section-subtitle">Don't just take our word for it - hear from our satisfied clients</p>
      </div>
      <div class="grid md:grid-cols-3 gap-8">
        <?php
        $testimonials = $website_data['testimonials'] ?? [
          ['name' => 'Sarah Johnson', 'role' => 'Business Owner', 'text' => 'Exceptional service and outstanding results. The team went above and beyond to ensure our project was a success. Highly recommended!', 'rating' => 5],
          ['name' => 'Michael Chen', 'role' => 'Marketing Director', 'text' => 'Professional, reliable, and innovative. They understood our vision and delivered exactly what we needed. Great communication throughout.', 'rating' => 5],
          ['name' => 'Lisa Rodriguez', 'role' => 'Startup Founder', 'text' => 'Amazing experience from start to finish. The attention to detail and commitment to quality is unmatched. Will definitely work with them again.', 'rating' => 5]
        ];
        foreach ($testimonials as $testimonial): ?>
          <div class="testimonial-card card">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-gray-300 rounded-full mr-4"></div>
              <div>
                <h4 class="font-semibold"><?php echo esc_html($testimonial['name']); ?></h4>
                <p class="text-sm text-gray-600"><?php echo esc_html($testimonial['role']); ?></p>
              </div>
            </div>
            <p class="text-gray-600 italic">"<?php echo esc_html($testimonial['text']); ?>"</p>
            <div class="flex mt-4" style="color: var(--color-accent);">
              <?php echo str_repeat('⭐', $testimonial['rating']); ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="py-20" style="background: var(--color-cream);">
    <div class="container">
      <div class="text-center mb-16">
        <h2 class="section-title">Get In Touch</h2>
        <p class="section-subtitle">Ready to start your project? We'd love to hear from you</p>
      </div>
      <div class="grid lg:grid-cols-2 gap-12">
        <div>
          <div class="space-y-6">
            <?php
            $contact = $website_data['contact'] ?? [
              'address' => '123 Business Street, City, State 12345',
              'phone' => '(555) 123-4567',
              'email' => 'hello@business.com'
            ];
            ?>
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--color-brand); color: white;">📍</div>
              <div>
                <h4 class="font-semibold mb-1">Visit Us</h4>
                <p class="text-gray-600"><?php echo esc_html($contact['address']); ?></p>
              </div>
            </div>
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--color-brand); color: white;">📞</div>
              <div>
                <h4 class="font-semibold mb-1">Call Us</h4>
                <p class="text-gray-600"><?php echo esc_html($contact['phone']); ?></p>
              </div>
            </div>
            <div class="flex items-start space-x-4">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--color-brand); color: white;">✉️</div>
              <div>
                <h4 class="font-semibold mb-1">Email Us</h4>
                <p class="text-gray-600"><?php echo esc_html($contact['email']); ?></p>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <?php echo do_shortcode('[contact_form]'); ?>
        </div>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
