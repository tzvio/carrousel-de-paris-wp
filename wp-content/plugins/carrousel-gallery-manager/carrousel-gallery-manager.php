<?php

/**
 * Plugin Name: Carrousel Gallery Manager
 * Description: Dynamic gallery management plugin for Carrousel de Paris with drag-and-drop ordering for images and videos. Allows unlimited media items with intuitive management interface.
 * Version: 1.1.0
 * Author: Carrousel de Paris
 * Text Domain: carrousel-gallery-manager
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CGM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CGM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CGM_VERSION', '1.1.0');

// Main plugin class
class CarrouselGalleryManager
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_cgm_save_gallery', array($this, 'save_gallery'));
        add_action('wp_ajax_cgm_get_media', array($this, 'get_media'));
        add_action('wp_ajax_cgm_remove_media', array($this, 'remove_media'));
        add_action('admin_notices', array($this, 'admin_notices'));

        // Create database table on activation
        register_activation_hook(__FILE__, array($this, 'create_gallery_table'));
        register_activation_hook(__FILE__, array($this, 'activation_notice'));
    }

    public function init()
    {
        // Load text domain
        load_plugin_textdomain('carrousel-gallery-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function activation_notice()
    {
        set_transient('cgm_activation_notice', true, 30);
    }

    public function create_gallery_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'carrousel_gallery';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            media_id mediumint(9) NOT NULL,
            media_type varchar(20) NOT NULL DEFAULT 'image',
            title text,
            alt_text text,
            sort_order int(11) NOT NULL DEFAULT 0,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY media_id (media_id),
            KEY sort_order (sort_order),
            KEY is_active (is_active)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function add_admin_menu()
    {
        add_menu_page(
            __('Gallery Manager', 'carrousel-gallery-manager'),
            __('Gallery Manager', 'carrousel-gallery-manager'),
            'manage_options',
            'carrousel-gallery-manager',
            array($this, 'admin_page'),
            'dashicons-format-gallery',
            25
        );
    }

    public function enqueue_admin_scripts($hook)
    {
        if ('toplevel_page_carrousel-gallery-manager' !== $hook) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');

        wp_enqueue_script(
            'cgm-admin-script',
            CGM_PLUGIN_URL . 'assets/admin.js',
            array('jquery', 'jquery-ui-sortable'),
            CGM_VERSION,
            true
        );

        wp_enqueue_style(
            'cgm-admin-style',
            CGM_PLUGIN_URL . 'assets/admin.css',
            array(),
            CGM_VERSION
        );

        wp_localize_script('cgm-admin-script', 'cgm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cgm_nonce'),
            'strings' => array(
                'confirm_remove' => __('Are you sure you want to remove this item?', 'carrousel-gallery-manager'),
                'loading' => __('Loading...', 'carrousel-gallery-manager'),
                'error' => __('An error occurred. Please try again.', 'carrousel-gallery-manager'),
                'saved' => __('Gallery saved successfully!', 'carrousel-gallery-manager'),
            )
        ));
    }

    public function admin_page()
    {
        $items_count = $this->get_gallery_items_count();
?>
        <div class="wrap">
            <h1>
                <?php _e('Gallery Manager', 'carrousel-gallery-manager'); ?>
                <span class="title-count theme-count"><?php echo $items_count; ?></span>
            </h1>

            <p class="description">
                <?php _e('Manage your Carrousel de Paris gallery with drag-and-drop ordering. Add images and videos from your media library, then arrange them in your preferred order.', 'carrousel-gallery-manager'); ?>
            </p>

            <div class="cgm-admin-container">
                <div class="cgm-toolbar">
                    <button type="button" class="button button-primary" id="cgm-add-media">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Add Media', 'carrousel-gallery-manager'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="cgm-save-gallery">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Save Gallery', 'carrousel-gallery-manager'); ?>
                    </button>

                    <?php if ($items_count > 0): ?>
                        <span class="cgm-stats">
                            <?php printf(__('Total items: %d', 'carrousel-gallery-manager'), $items_count); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="cgm-gallery-container">
                    <div id="cgm-gallery-items" class="cgm-sortable">
                        <?php $this->render_gallery_items(); ?>
                    </div>

                    <div id="cgm-empty-state" class="cgm-empty-state" <?php echo $this->has_gallery_items() ? 'style="display:none;"' : ''; ?>>
                        <div class="cgm-empty-content">
                            <span class="dashicons dashicons-format-gallery"></span>
                            <h3><?php _e('No media items yet', 'carrousel-gallery-manager'); ?></h3>
                            <p><?php _e('Click "Add Media" to start building your gallery', 'carrousel-gallery-manager'); ?></p>
                            <p class="description">
                                <?php _e('You can add both images and videos. Items will appear in your gallery in the order you arrange them here.', 'carrousel-gallery-manager'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="cgm-info-box">
                    <h4><?php _e('Instructions:', 'carrousel-gallery-manager'); ?></h4>
                    <ul>
                        <li><?php _e('Click "Add Media" to select images or videos from your media library', 'carrousel-gallery-manager'); ?></li>
                        <li><?php _e('Drag and drop items to reorder them', 'carrousel-gallery-manager'); ?></li>
                        <li><?php _e('Click the edit icon to modify titles and alt text', 'carrousel-gallery-manager'); ?></li>
                        <li><?php _e('Click the trash icon to remove items', 'carrousel-gallery-manager'); ?></li>
                        <li><?php _e('Remember to click "Save Gallery" after making changes', 'carrousel-gallery-manager'); ?></li>
                    </ul>

                    <div class="cgm-keyboard-shortcuts">
                        <h4><?php _e('Keyboard Shortcuts:', 'carrousel-gallery-manager'); ?></h4>
                        <p><kbd>Ctrl+S</kbd> (or <kbd>Cmd+S</kbd>) - <?php _e('Save gallery', 'carrousel-gallery-manager'); ?></p>
                        <p><kbd>Esc</kbd> - <?php _e('Close edit panel', 'carrousel-gallery-manager'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    private function render_gallery_items()
    {
        $items = $this->get_gallery_items();

        foreach ($items as $item) {
            $this->render_gallery_item($item);
        }
    }

    private function render_gallery_item($item)
    {
        $media = get_post($item->media_id);
        if (!$media) return;

        $media_url = wp_get_attachment_url($item->media_id);
        $media_type = $item->media_type;
        $is_video = in_array($media_type, array('video', 'mp4', 'webm', 'ogg'));

    ?>
        <div class="cgm-gallery-item" data-id="<?php echo esc_attr($item->id); ?>" data-media-id="<?php echo esc_attr($item->media_id); ?>">
            <div class="cgm-item-preview">
                <?php if ($is_video): ?>
                    <video controls muted>
                        <source src="<?php echo esc_url($media_url); ?>" type="video/<?php echo esc_attr($media_type); ?>">
                    </video>
                    <div class="cgm-media-type-badge">
                        <span class="dashicons dashicons-video-alt3"></span>
                    </div>
                <?php else: ?>
                    <img src="<?php echo esc_url(wp_get_attachment_image_url($item->media_id, 'medium')); ?>" alt="<?php echo esc_attr($item->alt_text); ?>">
                    <div class="cgm-media-type-badge">
                        <span class="dashicons dashicons-format-image"></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cgm-item-controls">
                <button type="button" class="cgm-edit-item" title="<?php _e('Edit', 'carrousel-gallery-manager'); ?>">
                    <span class="dashicons dashicons-edit"></span>
                </button>
                <button type="button" class="cgm-remove-item" title="<?php _e('Remove', 'carrousel-gallery-manager'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
                <span class="cgm-drag-handle" title="<?php _e('Drag to reorder', 'carrousel-gallery-manager'); ?>">
                    <span class="dashicons dashicons-menu"></span>
                </span>
            </div>

            <div class="cgm-item-details">
                <input type="text" class="cgm-item-title" placeholder="<?php _e('Title', 'carrousel-gallery-manager'); ?>" value="<?php echo esc_attr($item->title); ?>">
                <input type="text" class="cgm-item-alt" placeholder="<?php _e('Alt text', 'carrousel-gallery-manager'); ?>" value="<?php echo esc_attr($item->alt_text); ?>">
            </div>
        </div>
        <?php
    }

    private function get_gallery_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'carrousel_gallery';

        return $wpdb->get_results(
            "SELECT * FROM $table_name WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
        );
    }

    private function has_gallery_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'carrousel_gallery';

        $count = $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name WHERE is_active = 1"
        );

        return $count > 0;
    }

    public function save_gallery()
    {
        check_ajax_referer('cgm_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $items = isset($_POST['items']) ? json_decode(stripslashes($_POST['items']), true) : array();

        global $wpdb;
        $table_name = $wpdb->prefix . 'carrousel_gallery';

        foreach ($items as $index => $item) {
            $wpdb->update(
                $table_name,
                array(
                    'title' => sanitize_text_field($item['title']),
                    'alt_text' => sanitize_text_field($item['alt_text']),
                    'sort_order' => intval($index),
                    'updated_at' => current_time('mysql')
                ),
                array('id' => intval($item['id'])),
                array('%s', '%s', '%d', '%s'),
                array('%d')
            );
        }

        wp_send_json_success(array('message' => __('Gallery saved successfully!', 'carrousel-gallery-manager')));
    }

    public function get_media()
    {
        check_ajax_referer('cgm_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $media_ids = isset($_POST['media_ids']) ? array_map('intval', $_POST['media_ids']) : array();

        if (empty($media_ids)) {
            wp_send_json_error(array('message' => __('No media selected.', 'carrousel-gallery-manager')));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'carrousel_gallery';

        $added_items = array();
        $max_order = $wpdb->get_var("SELECT MAX(sort_order) FROM $table_name WHERE is_active = 1") ?: 0;

        foreach ($media_ids as $media_id) {
            // Check if media already exists
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE media_id = %d AND is_active = 1",
                $media_id
            ));

            if ($exists) {
                continue; // Skip if already exists
            }

            $media = get_post($media_id);
            if (!$media) continue;

            $media_type = get_post_mime_type($media_id);
            $is_video = strpos($media_type, 'video/') === 0;

            $result = $wpdb->insert(
                $table_name,
                array(
                    'media_id' => $media_id,
                    'media_type' => $is_video ? 'video' : 'image',
                    'title' => $media->post_title,
                    'alt_text' => get_post_meta($media_id, '_wp_attachment_image_alt', true),
                    'sort_order' => ++$max_order,
                    'is_active' => 1,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s')
            );

            if ($result) {
                $item = new stdClass();
                $item->id = $wpdb->insert_id;
                $item->media_id = $media_id;
                $item->media_type = $is_video ? 'video' : 'image';
                $item->title = $media->post_title;
                $item->alt_text = get_post_meta($media_id, '_wp_attachment_image_alt', true);
                $item->sort_order = $max_order;

                $added_items[] = $item;
            }
        }

        if (!empty($added_items)) {
            ob_start();
            foreach ($added_items as $item) {
                $this->render_gallery_item($item);
            }
            $html = ob_get_clean();

            wp_send_json_success(array('html' => $html, 'count' => count($added_items)));
        } else {
            wp_send_json_error(array('message' => __('No new items were added.', 'carrousel-gallery-manager')));
        }
    }

    public function remove_media()
    {
        check_ajax_referer('cgm_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

        if (!$item_id) {
            wp_send_json_error(array('message' => __('Invalid item ID.', 'carrousel-gallery-manager')));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'carrousel_gallery';

        $result = $wpdb->update(
            $table_name,
            array('is_active' => 0, 'updated_at' => current_time('mysql')),
            array('id' => $item_id),
            array('%d', '%s'),
            array('%d')
        );

        if ($result !== false) {
            wp_send_json_success(array('message' => __('Item removed successfully.', 'carrousel-gallery-manager')));
        } else {
            wp_send_json_error(array('message' => __('Failed to remove item.', 'carrousel-gallery-manager')));
        }
    }

    // Public function to get gallery items for frontend use
    public static function get_frontend_gallery_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'carrousel_gallery';

        $items = $wpdb->get_results(
            "SELECT * FROM $table_name WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
        );

        $gallery_items = array();
        foreach ($items as $item) {
            $media_url = wp_get_attachment_url($item->media_id);
            $is_video = $item->media_type === 'video';

            if ($media_url) {
                $gallery_items[] = array(
                    'id' => $item->id,
                    'media_id' => $item->media_id,
                    'src' => $media_url,
                    'title' => $item->title,
                    'alt' => $item->alt_text,
                    'type' => $item->media_type,
                    'is_video' => $is_video,
                    'thumbnail' => $is_video ? wp_get_attachment_image_url($item->media_id, 'medium') : $media_url
                );
            }
        }

        return $gallery_items;
    }

    public function admin_notices()
    {
        // Show notice about gallery management on relevant admin pages
        $screen = get_current_screen();
        if ($screen && ($screen->id === 'customize' || $screen->base === 'appearance_page_theme_editor')) {
            $gallery_items_count = $this->get_gallery_items_count();
        ?>
            <div class="notice notice-info">
                <p>
                    <strong><?php _e('Gallery Management:', 'carrousel-gallery-manager'); ?></strong>
                    <?php if ($gallery_items_count > 0): ?>
                        <?php printf(__('Your gallery currently has %d media item(s). ', 'carrousel-gallery-manager'), $gallery_items_count); ?>
                    <?php else: ?>
                        <?php _e('Your gallery is empty. ', 'carrousel-gallery-manager'); ?>
                    <?php endif; ?>
                    <a href="<?php echo admin_url('admin.php?page=carrousel-gallery-manager'); ?>" class="button button-secondary">
                        <?php _e('Manage Gallery', 'carrousel-gallery-manager'); ?>
                    </a>
                </p>
            </div>
        <?php
        }

        // Show activation notice
        if (get_transient('cgm_activation_notice')) {
            delete_transient('cgm_activation_notice');
        ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <strong><?php _e('Carrousel Gallery Manager activated!', 'carrousel-gallery-manager'); ?></strong>
                    <?php _e('You can now manage your gallery with unlimited media items and drag-and-drop ordering.', 'carrousel-gallery-manager'); ?>
                    <a href="<?php echo admin_url('admin.php?page=carrousel-gallery-manager'); ?>" class="button button-primary" style="margin-left: 10px;">
                        <?php _e('Get Started', 'carrousel-gallery-manager'); ?>
                    </a>
                </p>
            </div>
<?php
        }
    }

    private function get_gallery_items_count()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'carrousel_gallery';

        return $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_name WHERE is_active = 1"
        ) ?: 0;
    }
}

// Initialize the plugin
CarrouselGalleryManager::get_instance();

// Helper function for themes
function cgm_get_gallery_items()
{
    return CarrouselGalleryManager::get_frontend_gallery_items();
}
