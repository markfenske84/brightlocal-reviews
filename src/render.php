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

// Add taxonomy query if a specific label is selected
if (isset($attributes['reviewLabel']) && $attributes['reviewLabel'] !== 'all') {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'bl_review_label',
            'field' => 'slug',
            'terms' => $attributes['reviewLabel']
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
$show_link = isset($attributes['showLink']) ? $attributes['showLink'] : true;
$show_arrows = isset($attributes['showArrows']) ? $attributes['showArrows'] : true;

$wrapper_class = 'bl-reviews-wrapper bl-reviews-' . esc_attr($display_type);

// Source icons mapping
$source_icons = array(
    'google' => 'https://www.google.com/favicon.ico',
    'facebook' => 'https://www.facebook.com/favicon.ico',
    'yelp' => 'https://www.yelp.com/favicon.ico',
    'tripadvisor' => 'https://www.tripadvisor.com/favicon.ico',
    'default' => 'https://www.brightlocal.com/favicon.ico'
);
?>
<div class="<?php echo esc_attr($wrapper_class); ?>" data-arrows="<?php echo $show_arrows ? 'true' : 'false'; ?>">
    <?php foreach ($reviews as $review): 
        $source = strtolower($review['source']);
        $source_icon = isset($source_icons[$source]) ? $source_icons[$source] : $source_icons['default'];
        $content_length = strlen(strip_tags($review['reviewBody']));
        $needs_read_more = $content_length > 200;
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
                
                <div class="bl-review-meta-right">
                    <?php if ($show_date && $review['datePublished']): ?>
                        <span class="bl-review-date"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($review['datePublished']))); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($show_source && $review['source']): ?>
                        <span class="bl-review-source">
                            <img src="<?php echo esc_url($source_icon); ?>" alt="<?php echo esc_attr($review['source']); ?>" />
                            <?php if (strtolower($review['source']) !== 'google'): ?>
                                <?php echo esc_html(ucfirst($review['source'])); ?>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($show_author && $review['author']): ?>
                <div class="bl-review-author"><?php echo esc_html($review['author']); ?></div>
            <?php endif; ?>
            
            <?php 
            $content = wp_kses_post($review['reviewBody']);
            $content_length = strlen(strip_tags($content));
            $needs_read_more = $content_length > 200;
            $truncated_content = $needs_read_more ? wp_trim_words($content, 30, '...') : $content;
            ?>
            <div class="bl-review-content<?php echo $needs_read_more ? ' collapsed' : ''; ?>">
                <div class="bl-review-content-full" style="display: none;">
                    <?php echo $content; ?>
                </div>
                <div class="bl-review-content-preview">
                    <?php echo $truncated_content; ?>
                </div>
            </div>
            
            <?php if ($needs_read_more): ?>
                <div class="bl-review-read-more">
                    Read More
                </div>
            <?php endif; ?>
            
            <?php if ($show_link && $review['reviewLink']): ?>
                <a href="<?php echo esc_url($review['reviewLink']); ?>" class="bl-review-link" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('View Original', 'brightlocal-reviews'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div> 