<?php
/**
 * Server-side rendering of the BrightLocal Reviews block
 */

if (!defined('ABSPATH')) {
    exit;
}

$args = array(
    'post_type' => 'bl-reviews',
    'posts_per_page' => -1,
    'post_status' => 'publish'
);

// Handle item limit attribute
$limit_items = isset($attributes['limitItems']) ? (bool) $attributes['limitItems'] : false;
$items_per_page = isset($attributes['itemsPerPage']) ? intval($attributes['itemsPerPage']) : 3;

if ($limit_items && $items_per_page > 0) {
    $args['posts_per_page'] = $items_per_page;
}

// Add taxonomy query if a specific label is selected
if (isset($attributes['reviewLabel']) && $attributes['reviewLabel'] !== 'all') {
    $label_value = $attributes['reviewLabel'];
    $label_field = is_numeric($label_value) ? 'term_id' : 'slug';
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'bl_review_label',
            'field'    => $label_field,
            'terms'    => $label_value,
        )
    );
}

$reviews_query = new WP_Query($args);
$reviews = array();

if ($reviews_query->have_posts()) {
    while ($reviews_query->have_posts()) {
        $reviews_query->the_post();
        $post_id = get_the_ID();
        
        $review = array(
            'author' => get_the_title(),
            'reviewBody' => get_the_content(),
            'ratingValue' => get_post_meta($post_id, '_bl_rating', true),
            'datePublished' => get_post_meta($post_id, '_bl_date', true),
            'source' => get_post_meta($post_id, '_bl_source', true)
        );
        
        $reviews[] = $review;
    }
}
wp_reset_postdata();

if (empty($reviews)) {
    echo '<p>' . esc_html__('No reviews found.', 'brightlocal-reviews') . '</p>';
    return;
}

// Sort reviews by date
usort($reviews, function($a, $b) {
    return strtotime($b['datePublished']) - strtotime($a['datePublished']);
});

$display_type = isset($attributes['displayType']) ? $attributes['displayType'] : 'grid';
$show_author = isset($attributes['showAuthor']) ? $attributes['showAuthor'] : true;
$show_date = isset($attributes['showDate']) ? $attributes['showDate'] : true;
$show_source = isset($attributes['showSource']) ? $attributes['showSource'] : true;
$show_arrows = isset($attributes['showArrows']) ? $attributes['showArrows'] : true;

$wrapper_class = 'bl-reviews-wrapper bl-reviews-' . esc_attr($display_type);

// Source icons mapping
$source_icons = array(
    'google' => 'https://www.google.com/favicon.ico',
    'facebook' => 'https://www.facebook.com/favicon.ico',
    'yelp' => 'https://www.yelp.com/favicon.ico',
    'tripadvisor' => 'https://www.tripadvisor.com/favicon.ico',
    'yahoo' => 'https://www.yahoo.com/favicon.ico',
    'bing' => 'https://www.bing.com/favicon.ico',
    'trustpilot' => 'https://www.trustpilot.com/favicon.ico',
    'homeadvisor' => 'https://www.homeadvisor.com/favicon.ico',
    'angieslist' => 'https://www.angieslist.com/favicon.ico',
    'thumbtack' => 'https://www.thumbtack.com/favicon.ico',
    'houzz' => 'https://www.houzz.com/favicon.ico',
    'zillow' => 'https://www.zillow.com/favicon.ico',
    'realtor' => 'https://www.realtor.com/favicon.ico',
    'healthgrades' => 'https://www.healthgrades.com/favicon.ico',
    'zocdoc' => 'https://www.zocdoc.com/favicon.ico',
    'vitals' => 'https://www.vitals.com/favicon.ico',
    'opentable' => 'https://www.opentable.com/favicon.ico',
    'resy' => 'https://www.resy.com/favicon.ico',
    'default' => 'https://www.brightlocal.com/favicon.ico'
);
?>
<div class="<?php echo esc_attr($wrapper_class); ?>" data-arrows="<?php echo $show_arrows ? 'true' : 'false'; ?>">
    <?php foreach ($reviews as $review): 
        $source = strtolower($review['source']);
        $source_icon = isset($source_icons[$source]) ? $source_icons[$source] : $source_icons['default'];
    ?>
        <div class="bl-review-item">
            <div class="bl-review-header">
                <div class="bl-review-rating">
                    <?php
                    $rating = intval($review['ratingValue']);
                    for ($i = 1; $i <= 5; $i++) {
                        echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
                    }
                    ?>
                </div>
                
                <div class="bl-review-meta">
                    <?php if ($show_date && !empty($review['datePublished'])): ?>
                        <small class="bl-review-date"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($review['datePublished']))); ?></small>
                    <?php endif; ?>
                    <?php if ($show_source && !empty($review['source'])): ?>
                        <span class="bl-review-source">
                            <img src="<?php echo esc_url($source_icon); ?>" alt="<?php echo esc_attr($review['source']); ?>" />
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($show_author && !empty($review['author'])): ?>
                <h3 class="bl-review-author"><?php echo esc_html($review['author']); ?></h3>
            <?php endif; ?>
            
            <?php 
            $content = wp_kses_post($review['reviewBody']);
            $content_length = strlen(strip_tags($content));
            
            // Different handling for list vs grid mode
            if ($display_type === 'list') {
                // In list mode, only truncate if content is very long
                $needs_read_more = $content_length > 100;
                $truncated_content = $needs_read_more ? wp_trim_words($content, 20, '...') : $content;
            } else {
                // In grid mode, truncate more aggressively
                $needs_read_more = $content_length > 200;
                $truncated_content = $needs_read_more ? wp_trim_words($content, 30, '...') : $content;
            }
            ?>
            <div class="bl-review-content<?php echo $needs_read_more ? ' bl-review-content-truncated' : ''; ?>">
                <?php echo $content; ?>
            </div>
            
            <?php if ($needs_read_more): ?>
                <button type="button" class="bl-review-read-more" aria-expanded="false">
                    Read More
                </button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php
// Show Load More button if more reviews are available
if ($display_type !== 'carousel' && $limit_items && $reviews_query->found_posts > $items_per_page): ?>
    <button type="button" class="bl-reviews-load-more" data-offset="<?php echo esc_attr($items_per_page); ?>" data-per-page="<?php echo esc_attr($items_per_page); ?>" data-label="<?php echo esc_attr(isset($attributes['reviewLabel']) ? $attributes['reviewLabel'] : 'all'); ?>">
        <?php esc_html_e('Load More', 'brightlocal-reviews'); ?>
    </button>
<?php endif; ?> 