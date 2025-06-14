<?php
/**
 * Plugin Name: BrightLocal Reviews
 * Description: Display reviews from BrightLocal Showcase Review widget
 * Version: 1.0.9
 * Author: Mark Fenske
 * Update URI: https://github.com/markfenske84/brightlocal-reviews
 * Text Domain: brightlocal-reviews
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BL_REVIEWS_VERSION', '1.0.9');
define('BL_REVIEWS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BL_REVIEWS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once BL_REVIEWS_PLUGIN_DIR . 'includes/class-bl-reviews-post-type.php';
require_once BL_REVIEWS_PLUGIN_DIR . 'includes/class-bl-reviews-admin.php';
require_once BL_REVIEWS_PLUGIN_DIR . 'includes/class-bl-reviews-block.php';

// Load Composer autoloader so that external libraries are available.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Register GitHub-based automatic updates using Yahnis Elsts\' Plugin Update Checker.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$bl_reviews_update_checker = PucFactory::buildUpdateChecker(
    'https://github.com/markfenske84/brightlocal-reviews/', // GitHub repository
    __FILE__,                                              // Full path to main plugin file
    'brightlocal-reviews'                                  // Plugin slug
);
$bl_reviews_update_checker->setBranch( 'main' );
// Uncomment the next line if you attach custom release asset ZIPs instead of the automatically generated archive.
// $bl_reviews_update_checker->getVcsApi()->enableReleaseAssets();

// Initialize the plugin
function bl_reviews_init() {
    // Initialize post type
    new BL_Reviews_Post_Type();
    
    // Initialize admin
    new BL_Reviews_Admin();
    
    // Initialize block
    new BL_Reviews_Block(); 
}
add_action('plugins_loaded', 'bl_reviews_init');

// Activation hook
register_activation_hook(__FILE__, 'bl_reviews_activate');
function bl_reviews_activate() {
    // Flush rewrite rules after registering custom post type
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'bl_reviews_deactivate');
function bl_reviews_deactivate() {
    // Flush rewrite rules on deactivation
    flush_rewrite_rules();
}

/*
 * AJAX callback to load additional reviews (infinite scroll / load more)
 */
function bl_reviews_load_more_ajax() {
    check_ajax_referer( 'bl_reviews_nonce', 'nonce' );

    $per_page = isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 9;
    $offset   = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
    $label    = isset( $_POST['label'] ) ? sanitize_text_field( $_POST['label'] ) : 'all';

    $args = array(
        'post_type'      => 'bl-reviews',
        'posts_per_page' => $per_page,
        'offset'         => $offset,
        'post_status'    => 'publish',
    );

    if ( $label !== 'all' ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'bl_review_label',
                'field'    => 'slug',
                'terms'    => $label,
            ),
        );
    }

    $reviews_query = new WP_Query( $args );

    if ( ! $reviews_query->have_posts() ) {
        wp_die();
    }

    // Source icons mapping (keep in sync with main renderer)
    $source_icons = array(
        'google'      => 'https://www.google.com/favicon.ico',
        'facebook'    => 'https://www.facebook.com/favicon.ico',
        'yelp'        => 'https://www.yelp.com/favicon.ico',
        'tripadvisor' => 'https://www.tripadvisor.com/favicon.ico',
        'default'     => 'https://www.brightlocal.com/favicon.ico',
    );

    ob_start();
    while ( $reviews_query->have_posts() ) {
        $reviews_query->the_post();
        $post_id = get_the_ID();

        $author   = get_the_title();
        $body     = get_the_content();
        $rating   = intval( get_post_meta( $post_id, '_bl_rating', true ) );
        $date     = get_post_meta( $post_id, '_bl_date', true );
        $source   = strtolower( get_post_meta( $post_id, '_bl_source', true ) );
        $icon_url = isset( $source_icons[ $source ] ) ? $source_icons[ $source ] : $source_icons['default'];

        // Basic markup (mirror main template)
        ?>
        <div class="bl-review-item">
            <div class="bl-review-header">
                <div class="bl-review-rating">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <span class="star <?php echo ( $i <= $rating ) ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <?php if ( ! empty( $date ) || ! empty( $source ) ) : ?>
                    <div class="bl-review-meta-right">
                        <?php if ( ! empty( $date ) ) : ?>
                            <span class="bl-review-date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $source ) ) : ?>
                            <span class="bl-review-source"><img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $source ); ?>" /></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $author ) ) : ?>
                <div class="bl-review-author"><?php echo esc_html( $author ); ?></div>
            <?php endif; ?>
            <?php
                // Determine if this review needs the Read More toggle.
                // Using a similar heuristic to the block render logic
                $content_length   = strlen( wp_strip_all_tags( $body ) );
                $needs_read_more  = $content_length > 200; // simple threshold – aligns with grid mode
            ?>
            <div class="bl-review-content<?php echo $needs_read_more ? ' bl-review-content-truncated' : ''; ?>">
                <?php echo wp_kses_post( $body ); ?>
            </div>

            <?php if ( $needs_read_more ) : ?>
                <button type="button" class="bl-review-read-more" aria-expanded="false"><?php esc_html_e( 'Read More', 'brightlocal-reviews' ); ?></button>
            <?php endif; ?>
        </div>
        <?php
    }
    wp_reset_postdata();

    echo ob_get_clean();

    wp_die();
} 
add_action( 'wp_ajax_bl_load_more_reviews', 'bl_reviews_load_more_ajax' );
add_action( 'wp_ajax_nopriv_bl_load_more_reviews', 'bl_reviews_load_more_ajax' );