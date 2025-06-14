jQuery(document).ready(function($) {
    // Track whether new, unsaved widget rows (or unsaved edits that introduce a new widget ID) exist
    let unsavedAdditions = false;

    // Capture the list of originally saved widget IDs on page load
    const initialWidgetIds = [];
    $('.widget-row').each(function() {
        const idVal = $(this).find('input[name$="[widget_id]"]').val().trim();
        if (idVal !== '') {
            initialWidgetIds.push(idVal);
        }
    });

    function markUnsavedAdditions() {
        unsavedAdditions = true;
    }

    // When a new widget row is added it's automatically an unsaved addition
    $('#add-widget').on('click', function() {
        markUnsavedAdditions();
    });

    // When widget ID inputs are changed to a value that wasn't originally present mark as unsaved addition
    $(document).on('input', 'input[name$="[widget_id]"]', function() {
        const val = $(this).val().trim();
        if (val !== '' && initialWidgetIds.indexOf(val) === -1) {
            markUnsavedAdditions();
        }
    });

    // Submitting the settings form clears the unsaved additions flag (as we assume the page will reload)
    $(document).on('submit', 'form', function() {
        unsavedAdditions = false;
    });

    // Warn if user attempts to navigate away with unsaved additions
    window.addEventListener('beforeunload', function (e) {
        if (!unsavedAdditions) {
            return undefined;
        }
        const confirmationMessage = 'You have unsaved changes to your Widget IDs. If you leave this page, the changes will be lost.';
        (e || window.event).returnValue = confirmationMessage; // Gecko + IE
        return confirmationMessage; // Gecko + Webkit, Safari, Chrome etc.
    });

    // Update visibility/creation of extra action buttons (Delete Reviews & Remove Widgets)
    function updateDeleteButtonVisibility() {
        const $submitContainer = $('#bl_get_reviews').parent(); // <p class="submit">

        // Ensure a single wrapper span exists to hold additional action buttons
        let $wrapper = $submitContainer.find('.bl-action-buttons-wrapper');
        if (!$wrapper.length) {
            $wrapper = $('<span class="bl-action-buttons-wrapper"></span>').appendTo($submitContainer);
        }

        const hasReviews       = $('#bl_get_reviews').attr('data-has-reviews') === '1';
        const hasSavedWidgets  = initialWidgetIds.length > 0;

        // ----- Delete All Reviews button -----
        if (hasReviews) {
            if (!$('#bl_delete_all_reviews').length) {
                $wrapper.append('<button type="button" class="button button-link-delete" id="bl_delete_all_reviews" style="margin-right: 10px;">Delete All Reviews</button>');
            }
        } else {
            $('#bl_delete_all_reviews').remove();
        }

        // ----- Remove All Widgets button -----
        if (hasSavedWidgets) {
            if (!$('#bl_remove_all_widgets').length) {
                $wrapper.append('<button type="button" class="button button-link-delete" id="bl_remove_all_widgets">Remove All Widgets</button>');
            }
        } else {
            $('#bl_remove_all_widgets').remove();
        }

        // If wrapper is empty (no children), remove it to keep DOM clean
        if ($wrapper.children().length === 0) {
            $wrapper.remove();
        }
    }

    // Initial check
    updateDeleteButtonVisibility();

    $('#bl_refresh_reviews').on('click', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const originalText = $button.text();
        
        $button.prop('disabled', true).text('Refreshing...');
        
        $.ajax({
            url: blReviewsAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bl_refresh_reviews',
                nonce: blReviewsAdmin.nonce,
                post_id: blReviewsAdmin.postId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Failed to refresh reviews. Please try again.');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $('#bl_get_reviews').on('click', function() {
        // Collect current widget rows so they can be saved and then used for review fetching
        const widgetsPayload = [];
        $('.widget-row').each(function() {
            const widgetId = $(this).find('input[name$="[widget_id]"]').val().trim();
            const label    = $(this).find('input[name$="[label]"]').val().trim();

            if (widgetId !== '' && label !== '') {
                widgetsPayload.push({ widget_id: widgetId, label: label });
            }
        });

        if (widgetsPayload.length === 0) {
            alert('Please enter at least one widget ID before getting or updating reviews');
            return;
        }

        const $button = $(this);
        const originalText = $button.text();

        // Helper to fetch reviews after saving
        function fetchReviews() {
            $button.text('Fetching reviews...');

            $.ajax({
                url: blReviewsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bl_get_reviews',
                    nonce: blReviewsAdmin.nonce,
                    widgets: JSON.stringify(widgetsPayload)
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        $button.text('Update Reviews').attr('data-has-reviews', '1');
                        updateDeleteButtonVisibility();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('Failed to fetch reviews. Please try again.');
                },
                complete: function() {
                    $button.prop('disabled', false);

                    // Ensure the button label reflects current state
                    if ($button.attr('data-has-reviews') === '1') {
                        $button.text('Update Reviews');
                    } else {
                        $button.text('Get Reviews');
                    }
                }
            });
        }

        // First save widget settings via AJAX, then fetch reviews
        $button.prop('disabled', true).text('Saving settings...');

        $.ajax({
            url: blReviewsAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bl_save_widgets',
                nonce: blReviewsAdmin.saveWidgetsNonce,
                widgets: JSON.stringify(widgetsPayload)
            },
            success: function(response) {
                if (response.success) {
                    // Clear unsaved flag as settings are now persisted
                    unsavedAdditions = false;
                    // Refresh initialWidgetIds to the just-saved list so further edits are tracked correctly
                    initialWidgetIds.length = 0;
                    widgetsPayload.forEach(function(w){ initialWidgetIds.push(w.widget_id); });
                    fetchReviews();
                } else {
                    alert('Error saving settings: ' + response.data);
                    $button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                alert('Failed to save settings. Please try again.');
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '#bl_delete_all_reviews', function() {
        if (!confirm(blReviewsAdmin.confirmDelete)) {
            return;
        }

        const $button = $(this);
        const $getReviewsButton = $('#bl_get_reviews');
        
        $button.prop('disabled', true).text('Deleting reviews...');

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
                    $getReviewsButton.text('Get Reviews').attr('data-has-reviews', '0');
                    updateDeleteButtonVisibility();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Failed to delete reviews. Please try again.');
            },
            complete: function() {
                $button.prop('disabled', false).text('Delete All Reviews');
            }
        });
    });

    // Use delegated handler so it works for dynamically inserted button
    $(document).on('click', '#bl_remove_all_widgets', function() {
        if (!confirm(blReviewsAdmin.confirmRemoveWidgets)) {
            return;
        }

        const $button = $(this);
        const $getReviewsButton = $('#bl_get_reviews');
        
        $button.prop('disabled', true).text('Removing widgets...');

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
                    $getReviewsButton.text('Get Reviews').attr('data-has-reviews', '0');
                    // Clear list of initial widget IDs since they are removed
                    initialWidgetIds.length = 0;
                    updateDeleteButtonVisibility();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Failed to remove widgets. Please try again.');
            },
            complete: function() {
                $button.prop('disabled', false).text('Remove All Widgets');
            }
        });
    });

    // Helper to create/update the View Raw JSON link when widget ID field has content
    function updateJsonLink($input) {
        const widgetId = $input.val().trim();
        const $cell = $input.closest('td');
        let $container = $cell.find('.bl-widget-json-link');

        if (widgetId !== '') {
            if ($container.length === 0) {
                $container = $('<div class="bl-widget-json-link"></div>').appendTo($cell);
            }

            const url = blReviewsAdmin.baseUrl + widgetId;

            if ($container.find('a').length) {
                $container.find('a').attr('href', url);
            } else {
                $container.html('<a href="' + url + '" target="_blank" rel="noopener noreferrer">View Raw JSON</a>');
            }
        } else {
            // Remove link if field becomes empty
            $container.remove();
        }
    }

    // Initial rendering of links for any pre-populated widget IDs
    $('input[name$="[widget_id]"]').each(function() {
        updateJsonLink($(this));
    });

    // Update link dynamically as user types
    $(document).on('input', 'input[name$="[widget_id]"]', function() {
        updateJsonLink($(this));
    });

    // Initialise WordPress colour pickers for any colour fields
    if ( typeof $.fn.wpColorPicker !== 'undefined' ) {
        $('.bl-color-field').wpColorPicker();
    }

    /* Border-radius corner linking ------------------------- */
    function syncLinkedRadius(value){
        if($('#bl_link_radius').is(':checked')){
            $('#radius_tr, #radius_br, #radius_bl').val(value);
        }
    }

    function updateRadiusUI() {
        if( $('#bl_link_radius').is(':checked') ) {
            $('#radius_tr, #radius_br, #radius_bl').attr('readonly', 'readonly').closest('label').addClass('hidden');
            $('#bl_link_radius_btn').attr('aria-pressed', 'true');
            $('#bl_link_radius_btn .dashicons').removeClass('dashicons-admin-links').addClass('dashicons-editor-unlink');
            syncLinkedRadius($('#radius_tl').val());
        } else {
            $('#radius_tr, #radius_br, #radius_bl').removeAttr('readonly').closest('label').removeClass('hidden');
            $('#bl_link_radius_btn').attr('aria-pressed', 'false');
            $('#bl_link_radius_btn .dashicons').removeClass('dashicons-editor-unlink').addClass('dashicons-admin-links');
        }
    }

    // Toggle via button
    $(document).on('click', '#bl_link_radius_btn', function(e){
        e.preventDefault();
        $('#bl_link_radius').prop('checked', !$('#bl_link_radius').is(':checked'));
        updateRadiusUI();
    });

    // Fallback: still react to manual checkbox change if happens
    $(document).on('change', '#bl_link_radius', updateRadiusUI);

    // Keep inputs in sync when user types in TL field while linked
    $(document).on('input', '#radius_tl', function(){
        syncLinkedRadius( $(this).val() );
    });

    // On initial load, reflect linked state
    updateRadiusUI();
}); 