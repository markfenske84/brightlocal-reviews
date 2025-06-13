<?php

class BL_Reviews_Admin {
    private $base_url = 'https://www.local-marketing-reports.com/external/showcase-reviews/widgets/';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_bl_get_reviews', array($this, 'ajax_get_reviews'));
        add_action('wp_ajax_bl_delete_all_reviews', array($this, 'ajax_delete_all_reviews'));
        add_action('wp_ajax_bl_remove_all_widgets', array($this, 'ajax_remove_all_widgets'));
        add_action('wp_ajax_bl_save_widgets', array($this, 'ajax_save_widgets'));
        add_action('init', array($this, 'register_taxonomies'));
        add_filter('parent_file', array($this, 'set_current_menu'));
        add_filter('submenu_file', array($this, 'set_current_submenu'));
        add_filter('plugin_action_links_brightlocal-reviews/brightlocal-reviews.php', array($this, 'add_plugin_action_links'));
    }

    public function register_taxonomies() {
        // Register Source taxonomy
        register_taxonomy('bl_review_source', 'bl-reviews', array(
            'label' => __('Review Source', 'brightlocal-reviews'),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'review-source'),
        ));

        // Register Label taxonomy
        register_taxonomy('bl_review_label', 'bl-reviews', array(
            'label' => __('Review Label', 'brightlocal-reviews'),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'review-label'),
            'show_in_rest' => true,
            'rest_base' => 'bl_review_label',
            'rest_controller_class' => 'WP_REST_Terms_Controller'
        ));
    }

    public function add_settings_page() {
        // Add main menu page
        add_menu_page(
            __('BrightLocal Reviews', 'brightlocal-reviews'),
            __('BrightLocal Reviews', 'brightlocal-reviews'),
            'manage_options',
            'brightlocal-reviews',
            array($this, 'render_settings_page'),
            'dashicons-star-filled',
            30
        );

        // Add Settings submenu (first item)
        add_submenu_page(
            'brightlocal-reviews',
            __('Settings', 'brightlocal-reviews'),
            __('Settings', 'brightlocal-reviews'),
            'manage_options',
            'brightlocal-reviews',
            array($this, 'render_settings_page')
        );

        // Remove the default "Reviews" submenu that WordPress adds
        remove_submenu_page('brightlocal-reviews', 'edit.php?post_type=bl-reviews');
        
        // Add Reviews submenu (second item)
        add_submenu_page(
            'brightlocal-reviews',
            __('Reviews', 'brightlocal-reviews'),
            __('Reviews', 'brightlocal-reviews'),
            'edit_posts',
            'edit.php?post_type=bl-reviews'
        );

        // Add Review Sources submenu (third item)
        add_submenu_page(
            'brightlocal-reviews',
            __('Review Sources', 'brightlocal-reviews'),
            __('Review Sources', 'brightlocal-reviews'),
            'manage_options',
            'edit-tags.php?taxonomy=bl_review_source&post_type=bl-reviews'
        );

        // Add Review Labels submenu (fourth item)
        add_submenu_page(
            'brightlocal-reviews',
            __('Review Labels', 'brightlocal-reviews'),
            __('Review Labels', 'brightlocal-reviews'),
            'manage_options',
            'edit-tags.php?taxonomy=bl_review_label&post_type=bl-reviews'
        );
    }

    public function register_settings() {
        register_setting('bl_reviews_settings', 'bl_reviews_widgets', array(
            'type' => 'array',
            'default' => array(
                array(
                    'widget_id' => '',
                    'label' => ''
                )
            ),
            'sanitize_callback' => array($this, 'validate_widgets')
        ));
    }

    public function validate_widgets($widgets) {
        if (!is_array($widgets)) {
            return array();
        }

        $valid_widgets = array();
        $has_errors = false;

        // Get current widgets before update
        $current_widgets = get_option('bl_reviews_widgets', array());
        $current_labels = array_column($current_widgets, 'label');
        $current_widget_ids = array_column($current_widgets, 'widget_id');
        $new_labels = array();
        $new_widget_ids = array();
        $has_new_widgets = false;

        foreach ($widgets as $widget) {
            $widget_id = isset($widget['widget_id']) ? trim($widget['widget_id']) : '';
            $label = isset($widget['label']) ? trim($widget['label']) : '';

            // Skip empty rows
            if (empty($widget_id) && empty($label)) {
                continue;
            }

            // Check for required fields
            if (empty($widget_id) || empty($label)) {
                $has_errors = true;
                add_settings_error(
                    'bl_reviews_widgets',
                    'bl_reviews_widgets_error',
                    sprintf(
                        __('Widget ID and Label are required fields. Please fill in both fields for row %d.', 'brightlocal-reviews'),
                        count($valid_widgets) + 1
                    ),
                    'error'
                );
                // Keep the invalid data so it's not lost
                $valid_widgets[] = array(
                    'widget_id' => $widget_id,
                    'label' => $label
                );
                continue;
            }

            // Validate widget ID format (40 character hex string)
            if (!preg_match('/^[a-f0-9]{40}$/', $widget_id)) {
                $has_errors = true;
                add_settings_error(
                    'bl_reviews_widgets',
                    'bl_reviews_widgets_error',
                    sprintf(
                        __('Invalid Widget ID format in row %d. The ID should be a 40-character hexadecimal string.', 'brightlocal-reviews'),
                        count($valid_widgets) + 1
                    ),
                    'error'
                );
                // Keep the invalid data so it's not lost
                $valid_widgets[] = array(
                    'widget_id' => $widget_id,
                    'label' => $label
                );
                continue;
            }

            $valid_widgets[] = array(
                'widget_id' => $widget_id,
                'label' => $label
            );
            $new_labels[] = $label;
            $new_widget_ids[] = $widget_id;

            // Check if this is a new widget ID
            if (!in_array($widget_id, $current_widget_ids)) {
                $has_new_widgets = true;
            }
        }

        // If we have no valid widgets and no errors, add an empty row
        if (empty($valid_widgets) && !$has_errors) {
            $valid_widgets[] = array(
                'widget_id' => '',
                'label' => ''
            );
        }

        // Find removed labels
        $removed_labels = array_diff($current_labels, $new_labels);

        // Handle removed labels
        foreach ($removed_labels as $removed_label) {
            if (empty($removed_label)) {
                continue;
            }

            // Get the term
            $term = get_term_by('name', $removed_label, 'bl_review_label');
            if ($term) {
                // Get all posts with this label
                $posts = get_posts(array(
                    'post_type' => 'bl-reviews',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'bl_review_label',
                            'field' => 'term_id',
                            'terms' => $term->term_id
                        )
                    )
                ));

                // Delete all posts with this label
                foreach ($posts as $post) {
                    wp_delete_post($post->ID, true);
                }

                // Delete the term
                wp_delete_term($term->term_id, 'bl_review_label');
            }
        }

        // Schedule review update if new widgets were added
        if ($has_new_widgets && !$has_errors) {
            add_action('update_option_bl_reviews_widgets', array($this, 'schedule_review_update'), 10, 3);
        }

        return $valid_widgets;
    }

    public function schedule_review_update($old_value, $value, $option) {
        // Schedule the review update to run after the settings are saved
        add_action('admin_notices', array($this, 'trigger_review_update'));
    }

    public function trigger_review_update() {
        // Only show the notice on the BrightLocal Reviews settings page
        $screen = get_current_screen();
        if ($screen->id !== 'toplevel_page_brightlocal-reviews') {
            return;
        }

        // Trigger the review update via AJAX
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#bl_get_reviews').trigger('click');
        });
        </script>
        <?php
    }

    public function enqueue_scripts($hook) {
        if ('toplevel_page_brightlocal-reviews' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'bl-reviews-admin',
            BL_REVIEWS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            BL_REVIEWS_VERSION
        );

        wp_enqueue_script(
            'bl-reviews-admin',
            BL_REVIEWS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            BL_REVIEWS_VERSION,
            true
        );

        wp_localize_script('bl-reviews-admin', 'blReviewsAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bl_get_reviews_nonce'),
            'deleteNonce' => wp_create_nonce('bl_delete_reviews_nonce'),
            'confirmDelete' => __('Warning: Deleting all reviews cannot be undone. This will remove all reviews from your website. Are you sure you want to continue?', 'brightlocal-reviews'),
            'removeWidgetsNonce' => wp_create_nonce('bl_remove_widgets_nonce'),
            'confirmRemoveWidgets' => __('Warning: This will remove all widget IDs from your settings. Are you sure you want to continue?', 'brightlocal-reviews'),
            'saveWidgetsNonce' => wp_create_nonce('bl_save_widgets_nonce'),
            'baseUrl' => $this->base_url
        ));
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check if any reviews exist
        $has_reviews = get_posts(array(
            'post_type' => 'bl-reviews',
            'posts_per_page' => 1,
            'post_status' => 'publish'
        ));

        // Get saved widgets
        $widgets = get_option('bl_reviews_widgets', array(
            array(
                'widget_id' => '',
                'label' => ''
            )
        ));

        // Check if there is at least one saved widget ID (non-empty)
        $has_saved_widgets = false;
        foreach ($widgets as $w) {
            if (!empty($w['widget_id'])) {
                $has_saved_widgets = true;
                break;
            }
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php settings_errors('bl_reviews_widgets'); ?>
            
            <p>
                <?php _e('Enter your BrightLocal Showcase Review widget IDs and corresponding labels for each location.', 'brightlocal-reviews'); ?>
            </p>
                
            <form action="options.php" method="post">
                <?php
                settings_fields('bl_reviews_settings');
                do_settings_sections('bl_reviews_settings');
                ?>
                <table class="form-table" id="bl-widgets-table">
                    <thead>
                        <tr>
                            <th scope="col"><?php _e('Widget ID', 'brightlocal-reviews'); ?> <span class="required">*</span></th>
                            <th scope="col"><?php _e('Label', 'brightlocal-reviews'); ?> <span class="required">*</span></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($widgets as $index => $widget): ?>
                        <tr class="widget-row">
                            <td>
                                <input type="text" 
                                       name="bl_reviews_widgets[<?php echo $index; ?>][widget_id]" 
                                       value="<?php echo esc_attr($widget['widget_id']); ?>" 
                                       class="regular-text"
                                       placeholder="a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0">
                                <?php if (!empty($widget['widget_id'])): ?>
                                <div class="bl-widget-json-link">
                                    <a href="<?php echo esc_url($this->base_url . $widget['widget_id']); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php _e('View Raw JSON', 'brightlocal-reviews'); ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="text" 
                                       name="bl_reviews_widgets[<?php echo $index; ?>][label]" 
                                       value="<?php echo esc_attr($widget['label']); ?>" 
                                       class="regular-text"
                                       placeholder="<?php _e('e.g., Downtown Location', 'brightlocal-reviews'); ?>">
                            </td>
                            <td>
                                <?php if ($index > 0): ?>
                                <button type="button" class="button remove-widget"><?php _e('Remove', 'brightlocal-reviews'); ?></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p>
                    <button type="button" class="button" id="add-widget"><?php _e('Add Widget', 'brightlocal-reviews'); ?></button>
                </p>
                <p class="submit">
                    <?php submit_button(__('Save Settings', 'brightlocal-reviews'), 'primary', 'submit', false); ?>
                    <button type="button" class="button button-secondary" id="bl_get_reviews" data-has-reviews="<?php echo $has_reviews ? '1' : '0'; ?>" style="margin-left: 10px;">
                        <?php echo $has_reviews ? __('Update Reviews', 'brightlocal-reviews') : __('Get Reviews', 'brightlocal-reviews'); ?>
                    </button>
                    <br><br>
                        <?php _e('To get a widget ID, go to the Review Widgets link on the left sidebar in the BrightLocal dashboard for your location and create a new JSON Feed under the Review Widgets tab. After that\'s created, copy the widget ID from the URL provided.  For more instructions on creating these widgets, see the <a href="https://help.brightlocal.com/hc/en-us/articles/360013528499-How-to-create-Showcase-Review-widgets" target="_blank">How to create Showcase Review widgets</a> documentation from BrightLocal.', 'brightlocal-reviews'); ?>
                    <br><br>
                        <?php _e('Need a BrightLocal account? <a href="https://tools.brightlocal.com/seo-tools/admin/sign-up-v2/257/" target="_blank">Click here to get started</a> with a free trial.', 'brightlocal-reviews'); ?>
                    <br><br>
                    <?php if ($has_reviews): ?>
                    <button type="button" class="button button-link-delete" id="bl_delete_all_reviews" style="margin-right: 10px;">
                        <?php _e('Delete All Reviews', 'brightlocal-reviews'); ?>
                    </button>
                    <?php endif; ?>
                    <?php if ($has_saved_widgets): ?>
                    <button type="button" class="button button-link-delete" id="bl_remove_all_widgets">
                        <?php _e('Remove All Widgets', 'brightlocal-reviews'); ?>
                    </button>
                    <?php endif; ?>
                </p>
            </form>

       
        </div>
        <script>
        jQuery(document).ready(function($) {
            // Add new widget row
            $('#add-widget').on('click', function() {
                var index = $('.widget-row').length;
                var newRow = `
                    <tr class="widget-row">
                        <td>
                            <input type="text" 
                                   name="bl_reviews_widgets[${index}][widget_id]" 
                                   class="regular-text"
                                   placeholder="a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0">
                        </td>
                        <td>
                            <input type="text" 
                                   name="bl_reviews_widgets[${index}][label]" 
                                   class="regular-text"
                                   placeholder="<?php _e('e.g., Downtown Location', 'brightlocal-reviews'); ?>">
                        </td>
                        <td>
                            <button type="button" class="button remove-widget"><?php _e('Remove', 'brightlocal-reviews'); ?></button>
                        </td>
                    </tr>
                `;
                $('#bl-widgets-table tbody').append(newRow);
            });

            // Remove widget row
            $(document).on('click', '.remove-widget', function() {
                $(this).closest('tr').remove();
            });

            // Delete All Reviews button click handler
            $('#bl_delete_all_reviews').on('click', function() {
                if (!confirm(blReviewsAdmin.confirmDelete)) {
                    return;
                }

                var $button = $(this);
                $button.prop('disabled', true);

                $.ajax({
                    url: blReviewsAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'bl_delete_all_reviews',
                        nonce: blReviewsAdmin.deleteNonce
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert(response.data);
                        }
                    },
                    error: function() {
                        alert('<?php _e('An error occurred while deleting reviews.', 'brightlocal-reviews'); ?>');
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                    }
                });
            });

            // Remove All Widgets button click handler
            $('#bl_remove_all_widgets').on('click', function() {
                if (!confirm(blReviewsAdmin.confirmRemoveWidgets)) {
                    return;
                }

                var $button = $(this);
                $button.prop('disabled', true);

                $.ajax({
                    url: blReviewsAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'bl_remove_all_widgets',
                        nonce: blReviewsAdmin.removeWidgetsNonce
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert(response.data);
                        }
                    },
                    error: function() {
                        alert('<?php _e('An error occurred while removing all widgets.', 'brightlocal-reviews'); ?>');
                    },
                    complete: function() {
                        $button.prop('disabled', false);
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function ajax_delete_all_reviews() {
        check_ajax_referer('bl_delete_reviews_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        // Get all review posts
        $args = array(
            'post_type' => 'bl-reviews',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'fields' => 'ids'
        );

        $review_posts = get_posts($args);
        $deleted_count = 0;

        // Delete each post
        foreach ($review_posts as $post_id) {
            if (wp_delete_post($post_id, true)) {
                $deleted_count++;
            }
        }

        // Delete all terms in the source taxonomy
        $source_terms = get_terms(array(
            'taxonomy' => 'bl_review_source',
            'hide_empty' => false,
        ));

        foreach ($source_terms as $term) {
            wp_delete_term($term->term_id, 'bl_review_source');
        }

        // Delete all terms in the label taxonomy
        $label_terms = get_terms(array(
            'taxonomy' => 'bl_review_label',
            'hide_empty' => false,
        ));

        foreach ($label_terms as $term) {
            wp_delete_term($term->term_id, 'bl_review_label');
        }

        wp_send_json_success(array(
            'message' => sprintf(
                __('Successfully deleted %d reviews and all associated terms', 'brightlocal-reviews'),
                $deleted_count
            )
        ));
    }

    /**
     * AJAX callback to remove all stored widget IDs/labels.
     * This resets the bl_reviews_widgets option to an empty array.
     */
    public function ajax_remove_all_widgets() {
        check_ajax_referer('bl_remove_widgets_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'brightlocal-reviews'));
            return;
        }

        // 1. Clear stored widget IDs / labels
        update_option('bl_reviews_widgets', array());

        // 2. Remove all terms for the related taxonomies so they do not linger in the database / UI

        // Delete all Source terms
        $source_terms = get_terms(array(
            'taxonomy'   => 'bl_review_source',
            'hide_empty' => false,
        ));

        foreach ($source_terms as $term) {
            wp_delete_term($term->term_id, 'bl_review_source');
        }

        // Delete all Label terms (these are linked to widgets)
        $label_terms = get_terms(array(
            'taxonomy'   => 'bl_review_label',
            'hide_empty' => false,
        ));

        foreach ($label_terms as $term) {
            wp_delete_term($term->term_id, 'bl_review_label');
        }

        wp_send_json_success(array(
            'message' => __('Successfully removed all widgets and cleared associated taxonomies.', 'brightlocal-reviews')
        ));
    }

    /**
     * AJAX callback to save widgets settings (option bl_reviews_widgets) from the JS updater.
     * Accepts a JSON payload of widgets identical to the repeaters UI.
     */
    public function ajax_save_widgets() {
        check_ajax_referer('bl_save_widgets_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'brightlocal-reviews'));
            return;
        }

        $raw_widgets_payload = isset($_POST['widgets']) ? wp_unslash($_POST['widgets']) : '';
        if (empty($raw_widgets_payload)) {
            wp_send_json_error(__('No widgets supplied', 'brightlocal-reviews'));
            return;
        }

        $decoded_widgets = json_decode($raw_widgets_payload, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded_widgets)) {
            wp_send_json_error(__('Invalid widgets payload', 'brightlocal-reviews'));
            return;
        }

        // Re-use validate_widgets() to sanitize and validate.
        $validated = $this->validate_widgets($decoded_widgets);

        update_option('bl_reviews_widgets', $validated);

        // Return validated widgets so JS can decide what to do next if needed
        wp_send_json_success(array(
            'widgets' => $validated,
            'message' => __('Settings saved.', 'brightlocal-reviews'),
        ));
    }

    public function ajax_get_reviews() {
        check_ajax_referer('bl_get_reviews_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'brightlocal-reviews'));
            return;
        }

        // 1. Determine which widgets should be processed. If the request supplies a
        //    `widgets` payload (sent from the settings repeater without saving),
        //    use that. Otherwise, fall back to the widgets saved in the options.
        $raw_widgets_payload = isset($_POST['widgets']) ? wp_unslash($_POST['widgets']) : '';
        if (!empty($raw_widgets_payload)) {
            $decoded_widgets = json_decode($raw_widgets_payload, true);
            // Ensure decoded result is an array of arrays with widget_id/label keys
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_widgets)) {
                $widgets = array();
                foreach ($decoded_widgets as $w) {
                    $widget_id = isset($w['widget_id']) ? sanitize_text_field($w['widget_id']) : '';
                    $label     = isset($w['label']) ? sanitize_text_field($w['label'])     : '';

                    if ($widget_id !== '' && $label !== '') {
                        $widgets[] = array(
                            'widget_id' => $widget_id,
                            'label'     => $label,
                        );
                    }
                }
            }
        }

        // If no payload or invalid, fall back to stored option
        if (empty($widgets)) {
            $widgets = get_option('bl_reviews_widgets', array());
        }

        if (empty($widgets)) {
            wp_send_json_error(__('No widgets configured. Please add at least one widget ID and save settings.', 'brightlocal-reviews'));
            return;
        }

        // 2. Delete reviews that belong to labels that are no longer present when
        //    compared to the currently saved configuration. This handles the case
        //    where a repeater row has been removed but the settings have not yet
        //    been saved – the user expects those reviews to disappear when they
        //    click "Update Reviews".
        $total_removed   = 0; // keep track of how many reviews are deleted due to removed labels
        $saved_widgets   = get_option('bl_reviews_widgets', array());
        $saved_labels    = array_filter(array_column($saved_widgets, 'label'));
        $saved_widget_ids = array_filter(array_column($saved_widgets, 'widget_id'));
        $current_labels  = array_filter(array_column($widgets, 'label'));
        $removed_labels  = array_diff($saved_labels, $current_labels);

        foreach ($removed_labels as $removed_label) {
            // Get the corresponding term
            $term = get_term_by('name', $removed_label, 'bl_review_label');
            if (!$term) {
                continue;
            }

            // Fetch and permanently delete all review posts associated with the label
            $posts = get_posts(array(
                'post_type'      => 'bl-reviews',
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'bl_review_label',
                        'field'    => 'term_id',
                        'terms'    => $term->term_id,
                    ),
                ),
                'fields' => 'ids',
            ));

            foreach ($posts as $post_id) {
                wp_delete_post($post_id, true);
                $total_removed++;
            }

            // Delete the taxonomy term itself
            wp_delete_term($term->term_id, 'bl_review_label');
        }

        // 3. Proceed with normal review fetching for the widgets currently
        //    present in the repeater/UI (or saved settings).
        $total_created   = 0;
        $total_updated   = 0;
        $total_reviews   = 0;
        $processed_widgets = 0;

        foreach ($widgets as $widget) {
            $widget_id = isset($widget['widget_id']) ? sanitize_text_field($widget['widget_id']) : '';
            $label     = isset($widget['label'])     ? sanitize_text_field($widget['label'])     : '';

            if (empty($widget_id)) {
                continue;
            }

            // Skip processing of widgets that have not yet been saved
            if (!in_array($widget_id, $saved_widget_ids, true)) {
                continue;
            }

            // Validate widget ID format (40 character hex string)
            if (!preg_match('/^[a-f0-9]{40}$/', $widget_id)) {
                continue;
            }

            $processed_widgets++;

            // Construct full URL
            $widget_url = $this->base_url . $widget_id;

            // Fetch reviews
            $response = wp_remote_get($widget_url);

            if (is_wp_error($response)) {
                continue;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!$data || !isset($data['results'])) {
                continue;
            }

            $total_reviews    += count($data['results']);
            $created_count     = 0;
            $updated_count     = 0;
            $processed_reviews = array();

            foreach ($data['results'] as $review) {
                // Create a unique identifier for the review
                $review_identifier = md5($widget_id . $review['source'] . $review['sourceId'] . $review['author'] . $review['datePublished']);

                // Skip if we've already processed this review in this loop
                if (in_array($review_identifier, $processed_reviews, true)) {
                    continue;
                }
                $processed_reviews[] = $review_identifier;

                // Check if review already exists by the unique identifier
                $existing_posts = get_posts(array(
                    'post_type'      => 'bl-reviews',
                    'meta_key'       => '_bl_review_identifier',
                    'meta_value'     => $review_identifier,
                    'posts_per_page' => 1,
                ));

                $post_data = array(
                    'post_title'          => $review['author'],
                    'post_content'        => $review['reviewBody'],
                    'post_type'           => 'bl-reviews',
                    'post_status'         => 'publish',
                    'post_date'           => $review['datePublished'],
                    'post_date_gmt'       => get_gmt_from_date($review['datePublished']),
                    'post_modified'       => $review['datePublished'],
                    'post_modified_gmt'   => get_gmt_from_date($review['datePublished']),
                );

                if (!empty($existing_posts)) {
                    // Update existing post
                    $post_data['ID'] = $existing_posts[0]->ID;
                    wp_update_post($post_data);
                    $post_id = $post_data['ID'];
                    $updated_count++;
                } else {
                    // Create new post
                    $post_id = wp_insert_post($post_data);
                    $created_count++;
                }

                if ($post_id) {
                    // Update post meta
                    update_post_meta($post_id, '_bl_review_identifier', $review_identifier);
                    update_post_meta($post_id, '_bl_source', $review['source']);
                    update_post_meta($post_id, '_bl_source_id', $review['sourceId']);
                    update_post_meta($post_id, '_bl_rating', $review['ratingValue']);
                    update_post_meta($post_id, '_bl_date', $review['datePublished']);
                    if (!empty($review['reviewTitle'])) {
                        update_post_meta($post_id, '_bl_title', $review['reviewTitle']);
                    }

                    // Set source taxonomy
                    wp_set_object_terms($post_id, ucfirst($review['source']), 'bl_review_source');

                    // Set label taxonomy if provided
                    if (!empty($label)) {
                        wp_set_object_terms($post_id, $label, 'bl_review_label');
                    }
                }
            }

            $total_created += $created_count;
            $total_updated += $updated_count;
        }

        if ($processed_widgets === 0) {
            wp_send_json_error(__('No valid widget IDs found. Please check your widget IDs and try again.', 'brightlocal-reviews'));
            return;
        }

        wp_send_json_success(array(
            'message' => sprintf(
                __('Successfully processed reviews: %1$d created, %2$d updated, %3$d removed. Total reviews in API: %4$d', 'brightlocal-reviews'),
                $total_created,
                $total_updated,
                $total_removed,
                $total_reviews
            )
        ));
    }

    public function set_current_menu($parent_file) {
        global $current_screen;
        
        if ($current_screen->taxonomy === 'bl_review_source' || $current_screen->taxonomy === 'bl_review_label') {
            $parent_file = 'brightlocal-reviews';
        }
        
        return $parent_file;
    }

    public function set_current_submenu($submenu_file) {
        global $current_screen;
        
        if ($current_screen->taxonomy === 'bl_review_source') {
            $submenu_file = 'edit-tags.php?taxonomy=bl_review_source&post_type=bl-reviews';
        } elseif ($current_screen->taxonomy === 'bl_review_label') {
            $submenu_file = 'edit-tags.php?taxonomy=bl_review_label&post_type=bl-reviews';
        }
        
        return $submenu_file;
    }

    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=brightlocal-reviews') . '">' . __('Settings', 'brightlocal-reviews') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
} 