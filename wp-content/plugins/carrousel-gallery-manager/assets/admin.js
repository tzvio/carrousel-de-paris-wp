/**
 * Carrousel Gallery Manager Admin JavaScript
 */

jQuery(document).ready(function ($) {
    'use strict';

    let mediaFrame;
    let isLoading = false;

    // Initialize sortable functionality
    function initSortable() {
        $('#cgm-gallery-items').sortable({
            items: '.cgm-gallery-item',
            handle: '.cgm-drag-handle',
            placeholder: 'cgm-sortable-placeholder',
            tolerance: 'pointer',
            cursor: 'move',
            opacity: 0.8,
            revert: true,
            start: function (e, ui) {
                ui.placeholder.height(ui.item.height());
            },
            update: function (e, ui) {
                showMessage('Gallery order updated. Remember to save your changes.', 'info');
            }
        });
    }

    // Show loading state
    function showLoading(element) {
        if (element) {
            element.addClass('cgm-loading');
        }
        isLoading = true;
    }

    // Hide loading state
    function hideLoading(element) {
        if (element) {
            element.removeClass('cgm-loading');
        }
        isLoading = false;
    }

    // Show message
    function showMessage(message, type = 'info') {
        const messageEl = $('<div class="cgm-message ' + type + '">' + message + '</div>');
        $('.cgm-admin-container').prepend(messageEl);

        setTimeout(function () {
            messageEl.fadeOut(400, function () {
                $(this).remove();
            });
        }, 4000);
    }

    // Toggle empty state
    function toggleEmptyState() {
        const items = $('#cgm-gallery-items .cgm-gallery-item');
        const emptyState = $('#cgm-empty-state');

        if (items.length === 0) {
            emptyState.show();
        } else {
            emptyState.hide();
        }
    }

    // Add media button click
    $('#cgm-add-media').on('click', function (e) {
        e.preventDefault();

        if (isLoading) return;

        // Create media frame if it doesn't exist
        if (!mediaFrame) {
            mediaFrame = wp.media({
                title: 'Select Media for Gallery',
                button: {
                    text: 'Add to Gallery'
                },
                multiple: true,
                library: {
                    type: ['image', 'video']
                }
            });

            // Handle media selection
            mediaFrame.on('select', function () {
                const selection = mediaFrame.state().get('selection');
                const mediaIds = [];

                selection.map(function (attachment) {
                    mediaIds.push(attachment.get('id'));
                });

                if (mediaIds.length > 0) {
                    addMediaToGallery(mediaIds);
                }
            });
        }

        mediaFrame.open();
    });

    // Add media to gallery via AJAX
    function addMediaToGallery(mediaIds) {
        showLoading($('#cgm-gallery-items'));

        $.ajax({
            url: cgm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cgm_get_media',
                media_ids: mediaIds,
                nonce: cgm_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    $('#cgm-gallery-items').append(response.data.html);
                    initSortable(); // Re-initialize sortable
                    toggleEmptyState();
                    showMessage(response.data.count + ' item(s) added to gallery. Remember to save your changes.', 'success');
                } else {
                    showMessage(response.data.message || cgm_ajax.strings.error, 'error');
                }
            },
            error: function () {
                showMessage(cgm_ajax.strings.error, 'error');
            },
            complete: function () {
                hideLoading($('#cgm-gallery-items'));
            }
        });
    }

    // Remove media item
    $(document).on('click', '.cgm-remove-item', function (e) {
        e.preventDefault();

        if (!confirm(cgm_ajax.strings.confirm_remove)) {
            return;
        }

        const item = $(this).closest('.cgm-gallery-item');
        const itemId = item.data('id');

        showLoading(item);

        $.ajax({
            url: cgm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cgm_remove_media',
                item_id: itemId,
                nonce: cgm_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    item.fadeOut(400, function () {
                        $(this).remove();
                        toggleEmptyState();
                        showMessage('Item removed successfully.', 'success');
                    });
                } else {
                    showMessage(response.data.message || cgm_ajax.strings.error, 'error');
                    hideLoading(item);
                }
            },
            error: function () {
                showMessage(cgm_ajax.strings.error, 'error');
                hideLoading(item);
            }
        });
    });

    // Edit item toggle
    $(document).on('click', '.cgm-edit-item', function (e) {
        e.preventDefault();

        const item = $(this).closest('.cgm-gallery-item');
        const details = item.find('.cgm-item-details');

        if (details.is(':visible')) {
            details.slideUp();
            $(this).removeClass('active');
        } else {
            // Close all other open details
            $('.cgm-item-details:visible').slideUp();
            $('.cgm-edit-item.active').removeClass('active');

            // Open this one
            details.slideDown();
            $(this).addClass('active');

            // Focus on first input
            setTimeout(function () {
                details.find('input:first').focus();
            }, 300);
        }
    });

    // Save gallery
    $('#cgm-save-gallery').on('click', function (e) {
        e.preventDefault();

        if (isLoading) return;

        const items = [];

        $('#cgm-gallery-items .cgm-gallery-item').each(function () {
            const item = $(this);
            items.push({
                id: item.data('id'),
                title: item.find('.cgm-item-title').val(),
                alt_text: item.find('.cgm-item-alt').val()
            });
        });

        if (items.length === 0) {
            showMessage('No items to save.', 'info');
            return;
        }

        showLoading($(this));

        $.ajax({
            url: cgm_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cgm_save_gallery',
                items: JSON.stringify(items),
                nonce: cgm_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    showMessage(cgm_ajax.strings.saved, 'success');
                } else {
                    showMessage(response.data.message || cgm_ajax.strings.error, 'error');
                }
            },
            error: function () {
                showMessage(cgm_ajax.strings.error, 'error');
            },
            complete: function () {
                hideLoading($('#cgm-save-gallery'));
            }
        });
    });

    // Handle input changes
    $(document).on('input', '.cgm-item-title, .cgm-item-alt', function () {
        const item = $(this).closest('.cgm-gallery-item');
        if (!item.hasClass('cgm-modified')) {
            item.addClass('cgm-modified');
            showMessage('You have unsaved changes. Remember to save your gallery.', 'info');
        }
    });

    // Keyboard shortcuts
    $(document).on('keydown', function (e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
            e.preventDefault();
            $('#cgm-save-gallery').trigger('click');
        }

        // Escape to close edit details
        if (e.keyCode === 27) {
            $('.cgm-item-details:visible').slideUp();
            $('.cgm-edit-item.active').removeClass('active');
        }
    });

    // Warn about unsaved changes
    let hasUnsavedChanges = false;

    $(document).on('input', '.cgm-item-title, .cgm-item-alt', function () {
        hasUnsavedChanges = true;
    });

    $('#cgm-save-gallery').on('click', function () {
        hasUnsavedChanges = false;
    });

    $(window).on('beforeunload', function () {
        if (hasUnsavedChanges) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    // Auto-save draft periodically (optional)
    let autoSaveTimer;

    function scheduleAutoSave() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function () {
            if (hasUnsavedChanges && !isLoading) {
                // Could implement auto-save here
                console.log('Auto-save would happen here');
            }
        }, 30000); // 30 seconds
    }

    $(document).on('input', '.cgm-item-title, .cgm-item-alt', scheduleAutoSave);

    // Initialize on page load
    initSortable();
    toggleEmptyState();

    // Add some visual feedback for drag operations
    $('#cgm-gallery-items').on('sortstart', function (e, ui) {
        ui.item.addClass('cgm-dragging');
    });

    $('#cgm-gallery-items').on('sortstop', function (e, ui) {
        ui.item.removeClass('cgm-dragging');
    });

    // Enhanced media type detection and preview
    function enhanceMediaPreview() {
        $('.cgm-gallery-item video').each(function () {
            const video = this;
            video.addEventListener('loadedmetadata', function () {
                // Could add duration display or other metadata
            });
        });
    }

    // Call enhance function after adding new items
    $(document).ajaxSuccess(function (event, xhr, settings) {
        if (settings.data && settings.data.indexOf('action=cgm_get_media') > -1) {
            enhanceMediaPreview();
        }
    });

    // Initial enhancement
    enhanceMediaPreview();
});
