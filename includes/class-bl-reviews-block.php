<?php

class BL_Reviews_Block {
    public function __construct() {
        add_action('init', array($this, 'register_block'), 5);
        // Register shortcode so reviews can be embedded without the block editor.
        add_shortcode('brightlocal_reviews', array($this, 'shortcode_handler'));
    }

    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        $asset_file = include(BL_REVIEWS_PLUGIN_DIR . 'build/index.asset.php');

        register_block_type(
            BL_REVIEWS_PLUGIN_DIR . 'build',
            array(
                // Remove render_callback to let block.json handle the render file
                'editor_script' => 'brightlocal-reviews-editor',
                'editor_style' => 'brightlocal-reviews-editor',
                'style' => 'brightlocal-reviews-style',
                'script' => 'brightlocal-reviews-view',
                'dependencies' => $asset_file['dependencies']
            )
        ); 

        // Register editor script
        wp_register_script(
            'brightlocal-reviews-editor',
            BL_REVIEWS_PLUGIN_URL . 'build/index.js',
            $asset_file['dependencies'],
            $asset_file['version']
        );

        // Register editor style
        wp_register_style(
            'brightlocal-reviews-editor',
            BL_REVIEWS_PLUGIN_URL . 'build/index.css',
            array(),
            $asset_file['version']
        );

        // Register frontend style
        wp_register_style(
            'brightlocal-reviews-style',
            BL_REVIEWS_PLUGIN_URL . 'build/style-index.css',
            array(),
            $asset_file['version']
        );

        // Register frontend script
        wp_register_script(
            'brightlocal-reviews-view',
            BL_REVIEWS_PLUGIN_URL . 'build/view.js',
            array(),
            time(), // Use time() for development to prevent caching
            true
        );

        // Localize script with AJAX data
        wp_localize_script(
            'brightlocal-reviews-view',
            'blReviews',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('bl_reviews_nonce'),
            )
        );

        // Enqueue the script
        wp_enqueue_script('brightlocal-reviews-view');
    }

    public function render_block($attributes) {
        $args = array(
            'post_type' => 'bl-reviews',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        // Handle item limit attribute
        $limit_items   = isset($attributes['limitItems']) ? (bool) $attributes['limitItems'] : false;
        $items_per_page = isset($attributes['itemsPerPage']) ? intval($attributes['itemsPerPage']) : 3;
        
        if ( $limit_items && $items_per_page > 0 ) {
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
            return '<p>' . __('No reviews found.', 'brightlocal-reviews') . '</p>';
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
        
        ob_start();
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
        if ( $display_type !== 'carousel' && $limit_items && $reviews_query->found_posts > $items_per_page ) : ?>
            <button type="button" class="bl-reviews-load-more" data-offset="<?php echo esc_attr( $items_per_page ); ?>" data-per-page="<?php echo esc_attr( $items_per_page ); ?>" data-label="<?php echo esc_attr( isset( $attributes['reviewLabel'] ) ? $attributes['reviewLabel'] : 'all' ); ?>">
                <?php esc_html_e( 'Load More', 'brightlocal-reviews' ); ?>
            </button>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode handler – maps shortcode attributes to block attributes and
     * re-uses the same PHP renderer so the output & logic stay in one place.
     *
     * Example usage:
     * [brightlocal_reviews displayType="grid" showAuthor="true" showDate="false" limitItems="true" itemsPerPage="5" reviewLabel="23" showArrows="false"]
     *
     * @param array  $atts    Shortcode attributes supplied by the user.
     * @param string $content Not used (present for shortcode signature compatibility).
     * @return string Rendered HTML for the reviews list.
     */
    public function shortcode_handler($atts, $content = null) {
        // Default attribute values (use lowercase keys to match WordPress normalization)
        $defaults = array(
            'displaytype'  => 'grid',
            'showauthor'   => 'true',
            'showdate'     => 'true',
            'showsource'   => 'true',
            'showarrows'   => 'true',
            'limititems'   => 'false',
            'itemsperpage' => '3',
            'reviewlabel'  => 'all',
        );

        // Merge user attributes with defaults (WordPress handles case-insensitivity)
        $atts = shortcode_atts( $defaults, $atts, 'brightlocal_reviews' );

        // Cast to expected types (now that keys are lowercase)
        $atts['showauthor']   = filter_var($atts['showauthor'], FILTER_VALIDATE_BOOLEAN);
        $atts['showdate']     = filter_var($atts['showdate'], FILTER_VALIDATE_BOOLEAN);
        $atts['showsource']   = filter_var($atts['showsource'], FILTER_VALIDATE_BOOLEAN);
        $atts['showarrows']   = filter_var($atts['showarrows'], FILTER_VALIDATE_BOOLEAN);
        $atts['limititems']   = filter_var($atts['limititems'], FILTER_VALIDATE_BOOLEAN);
        $atts['itemsperpage'] = intval($atts['itemsperpage']);

        // Map to capitalized keys expected by render_block (if needed)
        $mapped_atts = array(
            'displayType'  => $atts['displaytype'],
            'showAuthor'   => $atts['showauthor'],
            'showDate'     => $atts['showdate'],
            'showSource'   => $atts['showsource'],
            'showArrows'   => $atts['showarrows'],
            'limitItems'   => $atts['limititems'],
            'itemsPerPage' => $atts['itemsperpage'],
            'reviewLabel'  => $atts['reviewlabel'],
        );

        // Re-use the existing render_block method
        return $this->render_block($mapped_atts);
    }
} 