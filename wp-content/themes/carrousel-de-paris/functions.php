<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

function carrousel_enqueue_assets()
{
    // Enqueue Google Fonts first
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@500&family=Cinzel+Decorative:wght@400;700&display=swap', array(), null);

    // Enqueue CSS files in proper order
    wp_enqueue_style('carrousel-variables', get_template_directory_uri() . '/css/variables.css', array(), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-base', get_template_directory_uri() . '/css/base.css', array('carrousel-variables'), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-utilities', get_template_directory_uri() . '/css/utilities.css', array('carrousel-base'), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-layout', get_template_directory_uri() . '/css/layout.css', array('carrousel-utilities'), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-header', get_template_directory_uri() . '/css/header.css', array('carrousel-layout'), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-curtains', get_template_directory_uri() . '/css/curtains.css', array('carrousel-layout'), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-gallery', get_template_directory_uri() . '/css/gallery.css', array('carrousel-layout'), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-forms', get_template_directory_uri() . '/css/forms.css', array('carrousel-layout'), wp_get_theme()->get('Version'));

    wp_enqueue_style('carrousel-footer', get_template_directory_uri() . '/css/footer.css', array('carrousel-layout'), wp_get_theme()->get('Version'));
    wp_enqueue_style('carrousel-mobile', get_template_directory_uri() . '/css/mobile.css', array('carrousel-layout'), wp_get_theme()->get('Version'));

    // Enqueue main stylesheet (which should now be mostly empty or have only theme header)
    wp_enqueue_style('carrousel-main', get_stylesheet_uri(), array('carrousel-mobile'), wp_get_theme()->get('Version'));

    // Enqueue jQuery (WordPress includes it by default)
    wp_enqueue_script('jquery');

    // Enqueue Glide.js CSS and JS
    wp_enqueue_style('glide-core', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/css/glide.core.min.css', array(), '3.6.0');
    wp_enqueue_style('glide-theme', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/css/glide.theme.min.css', array('glide-core'), '3.6.0');
    wp_enqueue_script('glide', 'https://cdn.jsdelivr.net/npm/@glidejs/glide@3.6.0/dist/glide.min.js', array(), '3.6.0', true);

    // Enqueue Lightbox CSS and JS
    wp_enqueue_style('lightbox', 'https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/css/lightbox.min.css', array(), '2.11.4');
    wp_enqueue_script('lightbox', 'https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/js/lightbox.min.js', array('jquery'), '2.11.4', true);

    // Enqueue custom JS
    wp_enqueue_script('carrousel-main', get_template_directory_uri() . '/js/main.js', array('jquery', 'glide', 'lightbox'), wp_get_theme()->get('Version'), true);

    // Localize script for Ajax (if needed for contact form)
    wp_localize_script('carrousel-main', 'carrousel_ajax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('carrousel_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'carrousel_enqueue_assets');

// Configure SMTP for email sending
function carrousel_configure_smtp($phpmailer)
{
    // Only configure SMTP if not in local development mode
    if (defined('CARROUSEL_LOCAL_EMAIL') && CARROUSEL_LOCAL_EMAIL) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.gmail.com';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 587;
    $phpmailer->SMTPSecure = 'tls'; // Use TLS encryption

    // Get SMTP credentials from WordPress options or constants
    $smtp_username = defined('SMTP_USERNAME') ? SMTP_USERNAME : get_option('carrousel_smtp_username');
    $smtp_password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : get_option('carrousel_smtp_password');
    $smtp_from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : get_option('carrousel_smtp_from_email');
    $smtp_from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : get_option('carrousel_smtp_from_name', get_bloginfo('name'));

    if ($smtp_username && $smtp_password) {
        $phpmailer->Username = $smtp_username;
        $phpmailer->Password = $smtp_password;

        // Set from email and name
        if ($smtp_from_email) {
            $phpmailer->From = $smtp_from_email;
            $phpmailer->FromName = $smtp_from_name;
        }

        // Enable SMTP debugging (comment out in production)
        // $phpmailer->SMTPDebug = 2; // Uncomment for debugging
    } else {
        error_log('SMTP credentials not configured for Carrousel de Paris');
    }
}
add_action('phpmailer_init', 'carrousel_configure_smtp');

// Override wp_mail from address if SMTP is configured
function carrousel_mail_from($original_email_address)
{
    $smtp_from_email = defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : get_option('carrousel_smtp_from_email');
    return $smtp_from_email ? $smtp_from_email : $original_email_address;
}
add_filter('wp_mail_from', 'carrousel_mail_from');

// Override wp_mail from name if SMTP is configured
function carrousel_mail_from_name($original_email_from)
{
    $smtp_from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : get_option('carrousel_smtp_from_name');
    return $smtp_from_name ? $smtp_from_name : $original_email_from;
}
add_filter('wp_mail_from_name', 'carrousel_mail_from_name');

// Theme setup
function carrousel_theme_setup()
{
    // Add theme support for post thumbnails
    add_theme_support('post-thumbnails');

    // Add theme support for title tag
    add_theme_support('title-tag');

    // Add theme support for HTML5
    add_theme_support('html5', array(
        'comment-list',
        'comment-form',
        'gallery',
        'caption',
        'style',
        'script'
    ));

    // Add theme support for automatic feed links
    // add_theme_support('automatic-feed-links'); // Disabled for one-page site

    // Add theme support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Add theme support for custom background
    add_theme_support('custom-background', array(
        'default-color' => '000000',
    ));

    // Set default content width
    global $content_width;
    if (!isset($content_width)) {
        $content_width = 900;
    }

    // Add theme support for custom header
    add_theme_support('custom-header', array(
        'default-image' => '',
        'width'         => 1200,
        'height'        => 600,
        'flex-height'   => true,
        'flex-width'    => true,
    ));

    // Add theme support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');
}
add_action('after_setup_theme', 'carrousel_theme_setup');

// Ensure admin bar is always shown for logged-in users
function carrousel_force_show_admin_bar()
{
    // Force show admin bar for logged-in users
    if (is_user_logged_in()) {
        show_admin_bar(true);
        add_filter('show_admin_bar', '__return_true');
    }
}
add_action('init', 'carrousel_force_show_admin_bar');

// Register navigation menus - Simplified for one-page site
function carrousel_register_menus()
{
    register_nav_menus(array(
        'social' => __('Social Links', 'carrousel')
    ));
}
add_action('init', 'carrousel_register_menus');

// Register widget areas - Simplified for one-page site
function carrousel_widgets_init()
{
    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'carrousel'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'carrousel'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'carrousel_widgets_init');

// Disable search functionality for one-page site
function carrousel_disable_search()
{
    // Disable search widget
    unregister_widget('WP_Widget_Search');

    // Remove search from admin bar (but keep the admin bar itself)
    // add_filter('wp_admin_bar_class', '__return_false'); // Commented out to keep admin bar
}
add_action('widgets_init', 'carrousel_disable_search');

// Additional search disabling measures
function carrousel_disable_search_completely()
{
    // Remove search from admin bar (but keep the admin bar itself)
    // add_filter('show_admin_bar', '__return_false'); // Commented out to keep admin bar

    // Disable search query vars
    add_filter('query_vars', function ($query_vars) {
        $key = array_search('s', $query_vars);
        if ($key !== false) {
            unset($query_vars[$key]);
        }
        return $query_vars;
    });

    // Disable search in admin
    add_action('admin_init', function () {
        // Remove search box from posts list
        add_filter('disable_search', '__return_true');
    });
}
add_action('init', 'carrousel_disable_search_completely');

// Remove search from WordPress queries completely
function carrousel_disable_search_query($query_vars)
{
    if (isset($query_vars['s'])) {
        wp_redirect(home_url('/'), 301);
        exit;
    }
    return $query_vars;
}
add_filter('request', 'carrousel_disable_search_query', 10, 1);

// Redirect search queries to homepage
function carrousel_redirect_search()
{
    if (is_search()) {
        wp_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('template_redirect', 'carrousel_redirect_search');

// Redirect all non-homepage requests to homepage for one-page site
function carrousel_redirect_to_homepage()
{
    // Don't redirect admin pages, login, or AJAX requests
    if (is_admin() || $GLOBALS['pagenow'] === 'wp-login.php' || wp_doing_ajax()) {
        return;
    }

    // Don't redirect if already on homepage
    if (is_front_page() || is_home()) {
        return;
    }

    // Don't redirect contact form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrousel_contact'])) {
        return;
    }

    // Redirect everything else to homepage
    wp_redirect(home_url('/'), 301);
    exit;
}
add_action('template_redirect', 'carrousel_redirect_to_homepage', 1);

// Disable RSS feeds for one-page site
function carrousel_disable_feeds()
{
    wp_redirect(home_url('/'), 301);
    exit;
}
add_action('do_feed', 'carrousel_disable_feeds', 1);
add_action('do_feed_rdf', 'carrousel_disable_feeds', 1);
add_action('do_feed_rss', 'carrousel_disable_feeds', 1);
add_action('do_feed_rss2', 'carrousel_disable_feeds', 1);
add_action('do_feed_atom', 'carrousel_disable_feeds', 1);

// Add security headers
function carrousel_security_headers()
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}
add_action('send_headers', 'carrousel_security_headers');

// Optimize WordPress for performance
function carrousel_optimize_performance()
{
    // Remove WordPress version from head
    remove_action('wp_head', 'wp_generator');

    // Remove unnecessary emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    // Clean up WordPress head
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'carrousel_optimize_performance');

// Handle contact form submission
function carrousel_handle_contact_form_submission()
{
    // Only process if this is a POST request to our contact form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carrousel_contact'])) {

        // Verify nonce
        if (!wp_verify_nonce($_POST['carrousel_nonce'], 'carrousel_contact_form')) {
            wp_die('Security check failed');
        }

        // Sanitize and validate form data
        $first_name = sanitize_text_field($_POST['firstName']);
        $last_name = sanitize_text_field($_POST['lastName']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);

        // Basic validation
        $errors = array();

        if (empty($first_name)) {
            $errors[] = 'Le prénom est requis.';
        }

        if (empty($last_name)) {
            $errors[] = 'Le nom est requis.';
        }

        if (empty($email) || !is_email($email)) {
            $errors[] = 'Une adresse e-mail valide est requise.';
        }

        if (empty($subject)) {
            $errors[] = 'Le sujet est requis.';
        }

        if (empty($message)) {
            $errors[] = 'Le message est requis.';
        }

        if (!empty($errors)) {
            // Redirect back with error
            wp_redirect(add_query_arg(array('contact' => 'error', 'errors' => implode('|', $errors)), home_url()));
            exit;
        }

        // Prepare email
        $to = 'contact@carrouseldeparis.com';
        $email_subject = '[Carrousel de Paris] ' . $subject;
        $email_message = "Nouveau message depuis le site web:\n\n";
        $email_message .= "Nom: {$first_name} {$last_name}\n";
        $email_message .= "E-mail: {$email}\n";
        $email_message .= "Sujet: {$subject}\n\n";
        $email_message .= "Message:\n{$message}\n\n";
        $email_message .= "---\n";
        $email_message .= "Envoyé depuis: " . home_url();

        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . $first_name . ' ' . $last_name . ' <' . $email . '>'
        );

        // Send email
        $mail_sent = wp_mail($to, $email_subject, $email_message, $headers);

        if ($mail_sent) {
            // Log successful email send
            error_log("Contact form email sent successfully to: {$to}");

            // Send confirmation email to user
            $user_subject = 'Confirmation de réception - Carrousel de Paris';
            $user_message = "Bonjour {$first_name},\n\n";
            $user_message .= "Nous avons bien reçu votre message et vous remercions de votre intérêt pour le Carrousel de Paris.\n\n";
            $user_message .= "Nous vous répondrons dans les plus brefs délais.\n\n";
            $user_message .= "Cordialement,\n";
            $user_message .= "L'équipe du Carrousel de Paris\n\n";
            $user_message .= "---\n";
            $user_message .= "Votre message:\n{$message}";

            $user_headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
            );

            $confirmation_sent = wp_mail($email, $user_subject, $user_message, $user_headers);

            if (!$confirmation_sent) {
                error_log("Failed to send confirmation email to: {$email}");
            }

            wp_redirect(add_query_arg('contact', 'success', home_url()));
        } else {
            // Log email failure
            error_log("Failed to send contact form email from: {$email}");
            wp_redirect(add_query_arg('contact', 'error', home_url()));
        }
        exit;
    }
}
add_action('template_redirect', 'carrousel_handle_contact_form_submission');



// Add custom image sizes for gallery
function carrousel_custom_image_sizes()
{
    add_image_size('gallery-thumb', 400, 600, true);
    add_image_size('gallery-large', 800, 1200, true);
}
add_action('after_setup_theme', 'carrousel_custom_image_sizes');

// Helper function to get gallery images - now uses the Gallery Manager plugin
function carrousel_get_gallery_images()
{
    // Check if Gallery Manager plugin is active and has items
    if (function_exists('cgm_get_gallery_items')) {
        $gallery_items = cgm_get_gallery_items();

        if (!empty($gallery_items)) {
            return $gallery_items;
        }
    }

    // Fallback to default images if plugin is not active or no items exist
    $gallery_images = array(
        array(
            'src' => get_template_directory_uri() . '/images/gallery/cabaret1.webp',
            'alt' => 'Cabaret Performance 1',
            'title' => 'Cabaret Performance 1',
            'type' => 'image',
            'is_video' => false
        ),
        array(
            'src' => get_template_directory_uri() . '/images/gallery/cabaret2.webp',
            'alt' => 'Cabaret Performance 2',
            'title' => 'Cabaret Performance 2',
            'type' => 'image',
            'is_video' => false
        ),
        array(
            'src' => get_template_directory_uri() . '/images/gallery/cabaret3.webp',
            'alt' => 'Cabaret Performance 3',
            'title' => 'Cabaret Performance 3',
            'type' => 'image',
            'is_video' => false
        )
    );

    return $gallery_images;
}

// Customizer additions
function carrousel_customize_register($wp_customize)
{
    // Add section for hero header content
    $wp_customize->add_section('carrousel_hero', array(
        'title'    => __('Hero Header Content', 'carrousel'),
        'priority' => 20,
    ));

    // Main title setting
    $wp_customize->add_setting('carrousel_main_title', array(
        'default'           => 'SIMONE LUMBROSO',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_main_title', array(
        'label'   => __('Main Title', 'carrousel'),
        'section' => 'carrousel_hero',
        'type'    => 'text',
    ));

    // Subtitle setting
    $wp_customize->add_setting('carrousel_subtitle', array(
        'default'           => 'DANS LE TOURBILLON DU',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_subtitle', array(
        'label'   => __('Subtitle', 'carrousel'),
        'section' => 'carrousel_hero',
        'type'    => 'text',
    ));

    // Featured title setting
    $wp_customize->add_setting('carrousel_featured_title', array(
        'default'           => 'CARROUSEL DE PARIS',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_featured_title', array(
        'label'   => __('Featured Title (Glowing Text)', 'carrousel'),
        'section' => 'carrousel_hero',
        'type'    => 'text',
    ));

    // Add section for about content
    $wp_customize->add_section('carrousel_about', array(
        'title'    => __('About Section', 'carrousel'),
        'priority' => 25,
    ));

    // About section title
    $wp_customize->add_setting('carrousel_about_title', array(
        'default'           => 'L\'HÉRITAGE DU CABARET',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_about_title', array(
        'label'   => __('About Section Title', 'carrousel'),
        'section' => 'carrousel_about',
        'type'    => 'text',
    ));

    // About paragraph 1
    $wp_customize->add_setting('carrousel_about_paragraph_1', array(
        'default'           => 'SIMONE LUMBROSO de nouveau dans le tourbillon du CARROUSEL DE PARIS, le cabaret légendaire de JOSÉPHINE BAKER devient itinérant, et vient vers vous à votre demande, pour vous amuser, vous divertir, vous émerveiller. Un florilège d\'émotions sensuelles où chaque spectacle sera une invitation à l\'admiration et à la découverte d\'artistes de tous les horizons.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_about_paragraph_1', array(
        'label'   => __('About Paragraph 1', 'carrousel'),
        'section' => 'carrousel_about',
        'type'    => 'textarea',
    ));

    // About paragraph 2
    $wp_customize->add_setting('carrousel_about_paragraph_2', array(
        'default'           => 'Sous les projecteurs, danseuses, humoristes, contorsionnistes, magiciens se métamorphoseront tels des phénix renaissants, à chaque escale de nos voyages, pour vous offrir des soirées inoubliables.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_about_paragraph_2', array(
        'label'   => __('About Paragraph 2', 'carrousel'),
        'section' => 'carrousel_about',
        'type'    => 'textarea',
    ));

    // About quote
    $wp_customize->add_setting('carrousel_about_quote', array(
        'default'           => 'Comment pourrais-je exprimer la neurasthénie, de ce manque de représentations, de cette magie du spectacle et de l\'amour que nous pouvons donner.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_about_quote', array(
        'label'   => __('About Quote', 'carrousel'),
        'section' => 'carrousel_about',
        'type'    => 'textarea',
    ));

    // About paragraph 3
    $wp_customize->add_setting('carrousel_about_paragraph_3', array(
        'default'           => 'Grâce à cette femme grandiose, JOSÉPHINE BAKER qui nous a poussé à agir et à continuer son œuvre. C\'est décidé, nous allons repartir pour perpétuer son héritage et son envie de faire découvrir ce monde incroyable, sans prétention aucune, mais avec l\'ambition de raviver la magie du cabaret et de son évolution !',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_about_paragraph_3', array(
        'label'   => __('About Paragraph 3', 'carrousel'),
        'section' => 'carrousel_about',
        'type'    => 'textarea',
    ));

    // About paragraph 4
    $wp_customize->add_setting('carrousel_about_paragraph_4', array(
        'default'           => 'De lieu en lieu, nuit après nuit, jour après jour, grâce à notre formidable équipe d\'artistes. Le cabaret le plus déjanté de Paris se déplaçant à votre demande de ville en ville.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_about_paragraph_4', array(
        'label'   => __('About Paragraph 4', 'carrousel'),
        'section' => 'carrousel_about',
        'type'    => 'textarea',
    ));

    // About content setting (for advanced users who want full HTML control)
    $wp_customize->add_setting('carrousel_about_content', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_about_content', array(
        'label'   => __('Custom About Content (Advanced)', 'carrousel'),
        'section' => 'carrousel_about',
        'type'    => 'textarea',
        'description' => __('Leave empty to use the individual paragraphs above. This field allows full HTML control for advanced users.', 'carrousel'),
    ));

    // Add section for contact section
    $wp_customize->add_section('carrousel_contact_section', array(
        'title'    => __('Contact Section', 'carrousel'),
        'priority' => 28,
    ));

    // Contact section title
    $wp_customize->add_setting('carrousel_contact_title', array(
        'default'           => 'CONTACTEZ-NOUS',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_contact_title', array(
        'label'   => __('Contact Section Title', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    // Contact form labels and placeholders
    $wp_customize->add_setting('carrousel_form_firstname_placeholder', array(
        'default'           => 'Prénom *',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_form_firstname_placeholder', array(
        'label'   => __('First Name Placeholder', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('carrousel_form_lastname_placeholder', array(
        'default'           => 'Nom *',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_form_lastname_placeholder', array(
        'label'   => __('Last Name Placeholder', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('carrousel_form_email_placeholder', array(
        'default'           => 'Adresse e-mail *',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_form_email_placeholder', array(
        'label'   => __('Email Placeholder', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('carrousel_form_subject_placeholder', array(
        'default'           => 'Sujet *',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_form_subject_placeholder', array(
        'label'   => __('Subject Placeholder', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('carrousel_form_message_placeholder', array(
        'default'           => 'Votre message *',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_form_message_placeholder', array(
        'label'   => __('Message Placeholder', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('carrousel_form_submit_text', array(
        'default'           => 'Envoyer le Message',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_form_submit_text', array(
        'label'   => __('Submit Button Text', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    // Contact info text
    $wp_customize->add_setting('carrousel_contact_info_title', array(
        'default'           => 'Carrousel de Paris',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_contact_info_title', array(
        'label'   => __('Contact Info Title', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('carrousel_contact_info_subtitle', array(
        'default'           => 'Spectacles sur mesure • Cabaret itinérant',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_contact_info_subtitle', array(
        'label'   => __('Contact Info Subtitle', 'carrousel'),
        'section' => 'carrousel_contact_section',
        'type'    => 'text',
    ));

    // Add section for contact information
    $wp_customize->add_section('carrousel_contact', array(
        'title'    => __('Contact Information', 'carrousel'),
        'priority' => 30,
    ));

    // Phone number setting
    $wp_customize->add_setting('carrousel_phone', array(
        'default'           => '+33 1 23 45 67 89',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_phone', array(
        'label'   => __('Phone Number', 'carrousel'),
        'section' => 'carrousel_contact',
        'type'    => 'text',
    ));

    // Email setting
    $wp_customize->add_setting('carrousel_email', array(
        'default'           => 'contact@carrouseldeparis.fr',
        'sanitize_callback' => 'sanitize_email',
    ));

    $wp_customize->add_control('carrousel_email', array(
        'label'   => __('Email Address', 'carrousel'),
        'section' => 'carrousel_contact',
        'type'    => 'email',
    ));

    // Add section for form messages
    $wp_customize->add_section('carrousel_messages', array(
        'title'    => __('Form Messages', 'carrousel'),
        'priority' => 32,
    ));

    // Success message
    $wp_customize->add_setting('carrousel_success_message', array(
        'default'           => 'Votre message a été envoyé avec succès!',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_success_message', array(
        'label'   => __('Success Message', 'carrousel'),
        'section' => 'carrousel_messages',
        'type'    => 'text',
    ));

    // Error message
    $wp_customize->add_setting('carrousel_error_message', array(
        'default'           => 'Une erreur est survenue. Veuillez réessayer.',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_error_message', array(
        'label'   => __('Error Message', 'carrousel'),
        'section' => 'carrousel_messages',
        'type'    => 'text',
    ));

    // Add section for social links
    $wp_customize->add_section('carrousel_social', array(
        'title'    => __('Social Media Links', 'carrousel'),
        'priority' => 35,
    ));

    // Facebook URL
    $wp_customize->add_setting('carrousel_facebook', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('carrousel_facebook', array(
        'label'   => __('Facebook URL', 'carrousel'),
        'section' => 'carrousel_social',
        'type'    => 'url',
    ));

    // Instagram URL
    $wp_customize->add_setting('carrousel_instagram', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('carrousel_instagram', array(
        'label'   => __('Instagram URL', 'carrousel'),
        'section' => 'carrousel_social',
        'type'    => 'url',
    ));

    // Twitter URL
    $wp_customize->add_setting('carrousel_twitter', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('carrousel_twitter', array(
        'label'   => __('Twitter URL', 'carrousel'),
        'section' => 'carrousel_social',
        'type'    => 'url',
    ));

    // YouTube URL
    $wp_customize->add_setting('carrousel_youtube', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('carrousel_youtube', array(
        'label'   => __('YouTube URL', 'carrousel'),
        'section' => 'carrousel_social',
        'type'    => 'url',
    ));

    // Gallery section is now managed by the Gallery Manager plugin
    // Visit Gallery Manager in the admin menu to manage gallery items
    // The gallery settings have been moved from the customizer to provide
    // unlimited media items with drag-and-drop ordering functionality

    // Add section for footer content
    $wp_customize->add_section('carrousel_footer', array(
        'title'    => __('Footer Content', 'carrousel'),
        'priority' => 40,
    ));

    // Footer logo text
    $wp_customize->add_setting('carrousel_footer_logo', array(
        'default'           => 'CARROUSEL DE PARIS',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_footer_logo', array(
        'label'   => __('Footer Logo Text', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'text',
    ));

    // Footer description
    $wp_customize->add_setting('carrousel_footer_description', array(
        'default'           => 'Le cabaret légendaire de <strong>Joséphine Baker</strong> devient itinérant et vient vers vous pour vous amuser, vous divertir, vous émerveiller.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_footer_description', array(
        'label'   => __('Footer Description', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'textarea',
    ));

    // Services section title
    $wp_customize->add_setting('carrousel_footer_services_title', array(
        'default'           => 'NOS SPECTACLES',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_footer_services_title', array(
        'label'   => __('Services Section Title', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'text',
    ));

    // Services list
    $wp_customize->add_setting('carrousel_footer_services', array(
        'default'           => "Cabaret traditionnel\nSpectacles sur mesure\nSoirées privées\nÉvénements corporate\nMariages & célébrations",
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('carrousel_footer_services', array(
        'label'   => __('Services List (one per line)', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'textarea',
        'description' => __('Enter each service on a new line.', 'carrousel'),
    ));

    // Contact section title
    $wp_customize->add_setting('carrousel_footer_contact_title', array(
        'default'           => 'CONTACT',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_footer_contact_title', array(
        'label'   => __('Contact Section Title', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'text',
    ));

    // Address
    $wp_customize->add_setting('carrousel_footer_address', array(
        'default'           => 'Paris, France\nSpectacles itinérants sur demande',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('carrousel_footer_address', array(
        'label'   => __('Address', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'textarea',
    ));

    // Business hours
    $wp_customize->add_setting('carrousel_footer_hours', array(
        'default'           => 'Lun-Ven: 9h-18h\nSam-Dim: Sur rendez-vous',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('carrousel_footer_hours', array(
        'label'   => __('Business Hours', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'textarea',
    ));

    // Heritage section title
    $wp_customize->add_setting('carrousel_footer_heritage_title', array(
        'default'           => 'L\'HÉRITAGE',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('carrousel_footer_heritage_title', array(
        'label'   => __('Heritage Section Title', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'text',
    ));

    // Heritage paragraph 1
    $wp_customize->add_setting('carrousel_footer_heritage_p1', array(
        'default'           => 'Dans l\'esprit de <strong>Joséphine Baker</strong>, nous perpétuons la tradition du cabaret parisien avec passion et authenticité.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_footer_heritage_p1', array(
        'label'   => __('Heritage Paragraph 1', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'textarea',
    ));

    // Heritage paragraph 2
    $wp_customize->add_setting('carrousel_footer_heritage_p2', array(
        'default'           => 'Chaque spectacle est une invitation à l\'admiration et à la découverte d\'artistes de tous les horizons.',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_footer_heritage_p2', array(
        'label'   => __('Heritage Paragraph 2', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'textarea',
    ));

    // Copyright text
    $wp_customize->add_setting('carrousel_footer_copyright', array(
        'default'           => 'Carrousel de Paris. Tous droits réservés. | Créé avec passion pour perpétuer l\'héritage de Joséphine Baker',
        'sanitize_callback' => 'wp_kses_post',
    ));

    $wp_customize->add_control('carrousel_footer_copyright', array(
        'label'   => __('Copyright Text', 'carrousel'),
        'section' => 'carrousel_footer',
        'type'    => 'textarea',
        'description' => __('The year will be automatically added.', 'carrousel'),
    ));
}
add_action('customize_register', 'carrousel_customize_register');

// Add SMTP settings to WordPress admin
function carrousel_smtp_admin_menu()
{
    add_options_page(
        'SMTP Configuration',
        'SMTP Settings',
        'manage_options',
        'carrousel-smtp',
        'carrousel_smtp_admin_page'
    );
}
add_action('admin_menu', 'carrousel_smtp_admin_menu');

// SMTP settings page
function carrousel_smtp_admin_page()
{
    if (isset($_POST['submit'])) {
        // Save SMTP settings
        update_option('carrousel_smtp_username', sanitize_text_field($_POST['smtp_username']));
        update_option('carrousel_smtp_password', sanitize_text_field($_POST['smtp_password']));
        update_option('carrousel_smtp_from_email', sanitize_email($_POST['smtp_from_email']));
        update_option('carrousel_smtp_from_name', sanitize_text_field($_POST['smtp_from_name']));

        echo '<div class="notice notice-success"><p>SMTP settings saved!</p></div>';
    }

    if (isset($_POST['test_email'])) {
        // Send test email
        $test_email = sanitize_email($_POST['test_email_address']);
        if ($test_email) {
            $subject = 'Test Email from Carrousel de Paris';
            $message = 'This is a test email to verify SMTP configuration is working correctly.';
            $sent = wp_mail($test_email, $subject, $message);

            if ($sent) {
                echo '<div class="notice notice-success"><p>Test email sent successfully to ' . esc_html($test_email) . '!</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Failed to send test email. Please check your SMTP settings.</p></div>';
            }
        }
    }

    // Get current settings
    $smtp_username = get_option('carrousel_smtp_username', '');
    $smtp_password = get_option('carrousel_smtp_password', '');
    $smtp_from_email = get_option('carrousel_smtp_from_email', '');
    $smtp_from_name = get_option('carrousel_smtp_from_name', get_bloginfo('name'));
?>
    <div class="wrap">
        <h1>SMTP Configuration</h1>
        <p>Configure Gmail SMTP settings for the contact form.</p>

        <div style="background: #fff; padding: 20px; margin: 20px 0; border-left: 4px solid #00a0d2;">
            <h3>Gmail Setup Instructions:</h3>
            <ol>
                <li><strong>Enable 2-Step Verification</strong> on your Gmail account if not already enabled</li>
                <li><strong>Generate an App Password:</strong>
                    <ul>
                        <li>Go to <a href="https://myaccount.google.com/security" target="_blank">Google Account Security</a></li>
                        <li>Under "Signing in to Google", click "2-Step Verification"</li>
                        <li>At the bottom, click "App passwords"</li>
                        <li>Select "Mail" and "Other (custom name)" - enter "Carrousel de Paris"</li>
                        <li>Copy the generated 16-character password</li>
                    </ul>
                </li>
                <li><strong>Use the App Password</strong> (not your regular Gmail password) in the SMTP Password field below</li>
            </ol>
        </div>

        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">SMTP Username (Gmail Address)</th>
                    <td>
                        <input type="email" name="smtp_username" value="<?php echo esc_attr($smtp_username); ?>" class="regular-text" placeholder="your-email@gmail.com" required />
                        <p class="description">Your full Gmail address</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">SMTP Password (App Password)</th>
                    <td>
                        <input type="password" name="smtp_password" value="<?php echo esc_attr($smtp_password); ?>" class="regular-text" placeholder="16-character app password" required />
                        <p class="description">Use the App Password generated from Google, not your regular password</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">From Email</th>
                    <td>
                        <input type="email" name="smtp_from_email" value="<?php echo esc_attr($smtp_from_email); ?>" class="regular-text" placeholder="contact@carrouseldeparis.fr" />
                        <p class="description">Email address that emails will appear to come from (usually same as SMTP username)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">From Name</th>
                    <td>
                        <input type="text" name="smtp_from_name" value="<?php echo esc_attr($smtp_from_name); ?>" class="regular-text" placeholder="Carrousel de Paris" />
                        <p class="description">Name that emails will appear to come from</p>
                    </td>
                </tr>
            </table>

            <?php submit_button('Save SMTP Settings'); ?>
        </form>

        <hr>

        <h2>Test Email</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Send Test Email To</th>
                    <td>
                        <input type="email" name="test_email_address" class="regular-text" placeholder="test@example.com" required />
                        <input type="submit" name="test_email" class="button" value="Send Test Email" />
                        <p class="description">Send a test email to verify SMTP configuration</p>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}

// Disable comments for one-page site (optional)
function carrousel_disable_comments()
{
    // Disable support for comments and trackbacks in post types
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }

    // Close comments on the front-end
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);

    // Hide existing comments
    add_filter('comments_array', '__return_empty_array', 10, 2);

    // Remove comments page in menu
    add_action('admin_menu', function () {
        remove_menu_page('edit-comments.php');
    });

    // Redirect any comment queries to homepage
    add_action('template_redirect', function () {
        if (is_comment_feed()) {
            wp_redirect(home_url('/'), 301);
            exit;
        }
    });
}
add_action('admin_init', 'carrousel_disable_comments');

// Disable sitemaps for one-page site
function carrousel_disable_sitemaps()
{
    // Disable default WordPress sitemaps
    add_filter('wp_sitemaps_enabled', '__return_false');

    // Remove sitemap functionality
    remove_action('init', 'wp_sitemaps_get_server');
}
add_action('init', 'carrousel_disable_sitemaps');

// Clean up WordPress head for one-page site
function carrousel_clean_wp_head()
{
    // Remove feed links
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);

    // Remove REST API links
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove wp-json links
    remove_action('template_redirect', 'rest_output_link_header', 11);
}
add_action('init', 'carrousel_clean_wp_head');

// Contact form AJAX handler
function carrousel_handle_contact_form()
{
    // Check nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'carrousel_nonce')) {
        wp_die(json_encode(array('success' => false, 'message' => 'Erreur de sécurité.')));
    }

    // Sanitize and validate form data
    $firstName = sanitize_text_field($_POST['firstName']);
    $lastName = sanitize_text_field($_POST['lastName']);
    $email = sanitize_email($_POST['email']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);

    // Validation
    $errors = array();

    if (empty($firstName)) {
        $errors[] = 'Prénom est requis.';
    }

    if (empty($lastName)) {
        $errors[] = 'Nom est requis.';
    }

    if (empty($email) || !is_email($email)) {
        $errors[] = 'Adresse e-mail valide est requise.';
    }

    if (empty($subject)) {
        $errors[] = 'Sujet est requis.';
    }

    if (empty($message)) {
        $errors[] = 'Message est requis.';
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        wp_die(json_encode(array(
            'success' => false,
            'message' => implode(' ', $errors)
        )));
    }

    // Prepare email
    $to = 'contact@carrouseldeparis.com'; // Send to contact email
    $email_subject = '[' . get_bloginfo('name') . '] ' . $subject;

    $email_message = "Nouveau message de contact:\n\n";
    $email_message .= "Nom: " . $firstName . " " . $lastName . "\n";
    $email_message .= "Email: " . $email . "\n";
    $email_message .= "Sujet: " . $subject . "\n\n";
    $email_message .= "Message:\n" . $message . "\n\n";
    $email_message .= "---\n";
    $email_message .= "Envoyé depuis: " . home_url();

    // Email headers
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $firstName . ' ' . $lastName . ' <' . $email . '>'
    );

    // Send email using wp_mail
    $mail_sent = wp_mail($to, $email_subject, $email_message, $headers);

    if ($mail_sent) {
        wp_die(json_encode(array(
            'success' => true,
            'message' => 'Votre message a été envoyé avec succès! Nous vous répondrons bientôt.'
        )));
    } else {
        wp_die(json_encode(array(
            'success' => false,
            'message' => 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.'
        )));
    }
}

// Hook the function to wp_ajax_ and wp_ajax_nopriv_ for both logged-in and non-logged-in users
add_action('wp_ajax_carrousel_contact_form', 'carrousel_handle_contact_form');
add_action('wp_ajax_nopriv_carrousel_contact_form', 'carrousel_handle_contact_form');
