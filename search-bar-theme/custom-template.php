<?php
/*
 * Template Name: Custom Template
 * Description: A custom template for displaying unique content.
 */
?>
<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <!-- Replace loop content with search form div -->
        <div class="search-trigger" id="search-trigger">
            <div class="search-form-container">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'textdomain' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="search-submit" disabled><?php echo esc_html_x( 'Search', 'submit button', 'textdomain' ); ?></button>
                </form>
                <div class="form-cover" id="form-cover"></div> <!-- Transparent div covering the form -->
            </div>
        </div>


<?php
// Specify the taxonomy slug
$taxonomy_slug = 'ask-barry'; // Replace 'your-taxonomy-slug' with your actual taxonomy slug

// Query posts associated with the specified taxonomy
$taxonomy_posts_query = new WP_Query(array(
    'post_type' => 'faq', // Adjust post type as needed
    'tax_query' => array(
        array(
            'taxonomy' => $taxonomy_slug,
            'field' => 'slug', // You can also use 'field' => 'taxonomy' if you want to query by taxonomy ID
            'operator' => 'EXISTS', // Include posts that have any term from the taxonomy
        ),
    ),
    'posts_per_page' => -1, // Retrieve all posts
));

// Output the post titles with view button and links
if ($taxonomy_posts_query->have_posts()) :
    while ($taxonomy_posts_query->have_posts()) : $taxonomy_posts_query->the_post();
        ?>
        <div class="post-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            <a class="view-button" href="<?php the_permalink(); ?>">View</a>
        </div>
        <?php
    endwhile;
    wp_reset_postdata();
else :
    echo '<p>No posts found.</p>';
endif;
?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

<!-- Search Popup -->
<div id="search-popup">
    <div class="search-content">
        <button class="close-btn">&times;</button>
        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'textdomain' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        </form>
        <div class="search-results" id="search-results"></div>
    </div>
</div>