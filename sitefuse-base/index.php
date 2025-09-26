<?php get_header(); ?>

<main id="main" class="py-20">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-8">SiteFuse Base Theme</h1>
        
        <?php if (have_posts()) : ?>
            <div class="space-y-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-semibold mb-4">
                            <a href="<?php the_permalink(); ?>" class="text-blue-600 hover:text-blue-800">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <div class="text-gray-600 mb-4">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Read More
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="text-gray-600">No content found.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
