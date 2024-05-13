<?php
/**
 * search-bar-theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package search-bar-theme
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function search_bar_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on search-bar-theme, use a find and replace
		* to change 'search-bar-theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'search-bar-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'search-bar-theme' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'search_bar_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'search_bar_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function search_bar_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'search_bar_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'search_bar_theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function search_bar_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'search-bar-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'search-bar-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'search_bar_theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function search_bar_theme_scripts() {
	wp_enqueue_style( 'search-bar-theme-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'search-bar-theme-style', 'rtl', 'replace' );

	wp_enqueue_script( 'search-bar-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	wp_enqueue_script( 'search-bar-theme-custom-page', get_template_directory_uri() . '/js/custom-page.js', array('jquery'), _S_VERSION, true );

	wp_localize_script('search-bar-theme-custom-page', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'search_bar_theme_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

add_action('wp_ajax_load_faq_post_titles', 'load_faq_post_titles');
add_action('wp_ajax_nopriv_load_faq_post_titles', 'load_faq_post_titles');

function load_faq_post_titles() {
    // Ensure search query is provided
    if (isset($_POST['search_query'])) {
        // Sanitize search query
        $search_query = sanitize_text_field($_POST['search_query']);

        // Query posts from "faq" post type within "ask-barry" taxonomy and search query
        $taxonomy_posts_query = new WP_Query(array(
            'post_type' => 'faq',
            'tax_query' => array(
                array(
                    'taxonomy' => 'ask-barry',
                    'field' => 'slug', // Field can also be 'term_id', 'name', or 'term_taxonomy_id'
                    'operator' => 'EXISTS', // Include posts that have any term from the taxonomy
                ),
            ),
            's' => $search_query, // Search query
            'posts_per_page' => -1,
        ));

        // Output the post titles with view button and links
        if ($taxonomy_posts_query->have_posts()) {
            while ($taxonomy_posts_query->have_posts()) {
                $taxonomy_posts_query->the_post();
                ?>
                <div class="post-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <a class="view-button" href="<?php the_permalink(); ?>">View</a>
                </div>
                <?php
            }
            wp_reset_postdata();
        } else {
            echo '<p>No results found for "' . esc_html($search_query) . '".</p>';
        }
    } else {
        echo '<p>Search query not provided.</p>';
    }

    // Always exit to avoid further execution
    wp_die();
}

