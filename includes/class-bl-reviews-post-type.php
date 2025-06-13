<?php

class BL_Reviews_Post_Type {
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_filter('manage_bl-reviews_posts_columns', array($this, 'add_review_score_column'));
        add_action('manage_bl-reviews_posts_custom_column', array($this, 'populate_review_score_column'), 10, 2);
        add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg'), 10, 2);
        add_filter('manage_edit-bl-reviews_sortable_columns', array($this, 'make_columns_sortable'));
        add_action('pre_get_posts', array($this, 'handle_sortable_columns'));
    }

    public function disable_gutenberg($use_block_editor, $post_type) {
        if ($post_type === 'bl-reviews') {
            return false;
        }
        return $use_block_editor;
    }

    public function register_post_type() {
        $labels = array(
            'name'               => _x('Reviews', 'post type general name', 'brightlocal-reviews'),
            'singular_name'      => _x('Review', 'post type singular name', 'brightlocal-reviews'),
            'menu_name'          => _x('Reviews', 'admin menu', 'brightlocal-reviews'),
            'add_new'            => _x('Add New', 'review', 'brightlocal-reviews'),
            'add_new_item'       => __('Add New Review', 'brightlocal-reviews'),
            'edit_item'          => __('Edit Review', 'brightlocal-reviews'),
            'new_item'           => __('New Review', 'brightlocal-reviews'),
            'view_item'          => __('View Review', 'brightlocal-reviews'),
            'search_items'       => __('Search Reviews', 'brightlocal-reviews'),
            'not_found'          => __('No reviews found', 'brightlocal-reviews'),
            'not_found_in_trash' => __('No reviews found in Trash', 'brightlocal-reviews'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => 'brightlocal-reviews',
            'query_var'           => true,
            'rewrite'             => array('slug' => 'bl-reviews'),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title', 'editor'),
            'menu_icon'           => 'dashicons-star-filled',
            'show_in_rest'        => true,
            'rest_base'           => 'bl-reviews',
        );

        register_post_type('bl-reviews', $args);

        // Register meta fields for REST API
        register_post_meta('bl-reviews', '_bl_rating', array(
            'type' => 'number',
            'single' => true,
            'show_in_rest' => true,
        ));

        register_post_meta('bl-reviews', '_bl_date', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));

        register_post_meta('bl-reviews', '_bl_source', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));

        register_post_meta('bl-reviews', '_bl_source_id', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));

        register_post_meta('bl-reviews', '_bl_title', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
        ));
    }

    public function enqueue_admin_styles($hook) {
        // Ensure we are on a screen related to the BrightLocal Reviews post type.
        $screen = get_current_screen();

        if ( ! $screen || 'bl-reviews' !== $screen->post_type ) {
            return;
        }

        wp_enqueue_style(
            'bl-reviews-admin',
            BL_REVIEWS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            BL_REVIEWS_VERSION
        );
    }

    public function add_meta_boxes() {
        add_meta_box(
            'bl_review_details',
            __('Review Details', 'brightlocal-reviews'),
            array($this, 'render_meta_box'),
            'bl-reviews',
            'side',
            'default'
        );
    }

    public function render_meta_box($post) {
        $rating = get_post_meta($post->ID, '_bl_rating', true);
        $date = get_post_meta($post->ID, '_bl_date', true);
        $source = get_post_meta($post->ID, '_bl_source', true);
        $source_id = get_post_meta($post->ID, '_bl_source_id', true);
        $review_title = get_post_meta($post->ID, '_bl_title', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Rating', 'brightlocal-reviews'); ?></th>
                <td>
                    <?php
                    $rating = intval($rating);
                    for ($i = 1; $i <= 5; $i++) {
                        echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
                    }
                    echo ' <span class="rating-value">(' . esc_html($rating) . '/5)</span>';
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Date', 'brightlocal-reviews'); ?></th>
                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($date))); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Source', 'brightlocal-reviews'); ?></th>
                <td><?php echo esc_html(ucfirst($source)); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Source ID', 'brightlocal-reviews'); ?></th>
                <td><?php echo esc_html($source_id); ?></td>
            </tr>
            <?php if ($review_title): ?>
            <tr>
                <th scope="row"><?php _e('Review Title', 'brightlocal-reviews'); ?></th>
                <td><?php echo esc_html($review_title); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php
    }

    public function save_meta_box($post_id) {
        if (!isset($_POST['bl_reviews_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['bl_reviews_meta_box_nonce'], 'bl_reviews_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    public function make_columns_sortable($columns) {
        $columns['bl_review_source'] = 'bl_review_source';
        
        // Only make Label column sortable if there are multiple label terms
        $label_terms = get_terms(array(
            'taxonomy' => 'bl_review_label',
            'hide_empty' => true,
            'fields' => 'ids'
        ));
        
        if (!is_wp_error($label_terms) && count($label_terms) > 1) {
            $columns['bl_review_label'] = 'bl_review_label';
        }
        
        return $columns;
    }

    public function handle_sortable_columns($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $orderby = $query->get('orderby');

        if ('bl_review_source' === $orderby) {
            $query->set('meta_key', '_bl_source');
            $query->set('orderby', 'meta_value');
        } elseif ('bl_review_label' === $orderby) {
            /*
             * Order by the term name of the first assigned "Review Label" so the
             * list table groups reviews by label. Because WordPress core does
             * not provide a built-in taxonomy orderby option for the post list
             * table, we hook into `posts_clauses` to join the term tables and
             * adjust the ORDER BY clause.
             */

            // Pass information downstream so the posts_clauses filter knows to run.
            $query->set('orderby', 'bl_review_label');

            // Ensure our custom clauses filter is attached just once per request.
            add_filter('posts_clauses', array($this, 'order_by_label_clauses'), 10, 2);
        }
    }

    /**
     * Modify SQL clauses so that ordering by "bl_review_label" sorts by the
     * associated taxonomy term name (ASC/DESC depending on list-table order).
     *
     * @param array    $clauses The pieces of the SQL query.
     * @param WP_Query $query   The current query instance.
     * @return array Modified clauses.
     */
    public function order_by_label_clauses($clauses, $query) {
        // Only affect the main admin query when explicitly ordering by label.
        if (!is_admin() || !$query->is_main_query()) {
            return $clauses;
        }

        if ('bl_review_label' !== $query->get('orderby')) {
            return $clauses;
        }

        global $wpdb;

        // Join the necessary term tables.
        $clauses['join']   .= " LEFT JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id "
                           .  " LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id "
                           .  " LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id ";

        // Restrict to our label taxonomy.
        $clauses['where']  .= $wpdb->prepare(" AND tt.taxonomy = %s ", 'bl_review_label');

        // Avoid duplicate rows caused by the joins.
        $clauses['groupby'] = "{$wpdb->posts}.ID";

        // Set order direction.
        $order = strtoupper($query->get('order')) === 'DESC' ? 'DESC' : 'ASC';
        $clauses['orderby'] = " t.name {$order} ";

        return $clauses;
    }

    public function add_review_score_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['bl_review_source'] = __('Source', 'brightlocal-reviews');
                $new_columns['bl_review_label'] = __('Label', 'brightlocal-reviews');
                $new_columns['review_score'] = __('Review Score', 'brightlocal-reviews');
            }
        }
        return $new_columns;
    }

    public function populate_review_score_column($column, $post_id) {
        if ($column === 'review_score') {
            $rating = get_post_meta($post_id, '_bl_rating', true);
            if ($rating) {
                echo '<div class="bl-review-rating">';
                for ($i = 1; $i <= 5; $i++) {
                    echo '<span class="star ' . ($i <= $rating ? 'filled' : '') . '">★</span>';
                }
                echo ' <span class="rating-value">(' . esc_html($rating) . '/5)</span>';
                echo '</div>';
            }
        } elseif ($column === 'bl_review_source') {
            $source = get_post_meta($post_id, '_bl_source', true);
            if ($source) {
                echo esc_html(ucfirst($source));
            }
        } elseif ($column === 'bl_review_label') {
            $terms = get_the_terms($post_id, 'bl_review_label');
            if ($terms && !is_wp_error($terms)) {
                $labels = array();
                foreach ($terms as $term) {
                    $labels[] = $term->name;
                }
                echo esc_html(implode(', ', $labels));
            }
        }
    }
} 