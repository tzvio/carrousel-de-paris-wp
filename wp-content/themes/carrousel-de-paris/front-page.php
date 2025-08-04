<?php get_header(); ?>

<div class="curtain-wrapper curtain-wrapper-left">
    <img src="<?php echo get_template_directory_uri(); ?>/images/curtain_wing.png" alt="Left Curtain" class="curtain curtain-left">
</div>
<div class="curtain-wrapper curtain-wrapper-right">
    <img src="<?php echo get_template_directory_uri(); ?>/images/curtain_wing.png" alt="Right Curtain" class="curtain curtain-right flipped">
</div>

<main id="main" class="site-main">
    <header class="hero-header">
        <div class="visuals">
            <img src="<?php echo get_template_directory_uri(); ?>/images/dancer.webp" alt="Cabaret Dancer" class="dancer">
            <img src="<?php echo get_template_directory_uri(); ?>/images/phoneix.webp" alt="Phoenix" class="phoenix">
            <img src="<?php echo get_template_directory_uri(); ?>/images/lion.webp" alt="Lion Magician" class="lion">
            <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Carrousel de Paris Logo" class="header-logo">
        </div>

        <div class="header-text">
            <h1><?php echo esc_html(get_theme_mod('carrousel_main_title', 'SIMONE LUMBROSO')); ?></h1>
            <h2><?php echo esc_html(get_theme_mod('carrousel_subtitle', 'DANS LE TOURBILLON DU')); ?></h2>
            <h1 class="glow-text"><?php echo esc_html(get_theme_mod('carrousel_featured_title', 'CARROUSEL DE PARIS')); ?></h1>
        </div>
    </header>

    <div class="gallery-container">
        <!-- Glide.js Gallery -->
        <div class="glide" id="carrousel-gallery">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <?php
                    // Get gallery items from Gallery Manager plugin or fallback to default
                    $gallery_images = carrousel_get_gallery_images();

                    foreach ($gallery_images as $item):
                        $is_video = isset($item['is_video']) ? $item['is_video'] : false;
                        $media_src = esc_url($item['src']);
                        $media_title = esc_attr($item['title']);
                        $media_alt = esc_attr($item['alt']);

                        // For videos, use thumbnail for lightbox if available
                        $lightbox_src = $is_video && isset($item['thumbnail']) ? esc_url($item['thumbnail']) : $media_src;
                    ?>
                        <li class="glide__slide">
                            <div class="gallery-item <?php echo $is_video ? 'gallery-item-video' : 'gallery-item-image'; ?>">
                                <?php if ($is_video): ?>
                                    <!-- Video items don't use lightbox, just display inline -->
                                    <div class="gallery-video-container">
                                        <video controls muted preload="metadata">
                                            <source src="<?php echo $media_src; ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <div class="gallery-overlay">
                                            <span class="gallery-play-icon">▶</span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Image items use lightbox -->
                                    <a href="<?php echo $lightbox_src; ?>" data-lightbox="cabaret-gallery"
                                        data-title="<?php echo $media_title; ?>">
                                        <img src="<?php echo $media_src; ?>" alt="<?php echo $media_alt; ?>">
                                        <div class="gallery-overlay"></div>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Navigation arrows -->
            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left" data-glide-dir="<">‹</button>
                <button class="glide__arrow glide__arrow--right" data-glide-dir=">">›</button>
            </div>
        </div>
    </div>

    <section class="about-section">
        <div class="about-content">
            <h2 class="about-title"><?php echo esc_html(get_theme_mod('carrousel_about_title', 'L\'HÉRITAGE DU CABARET')); ?></h2>

            <?php
            // Get about content from customizer or use default paragraphs
            $about_content = get_theme_mod('carrousel_about_content', '');
            if (empty($about_content)): ?>

                <p class="about-text">
                    <?php echo esc_html(get_theme_mod('carrousel_about_paragraph_1', 'SIMONE LUMBROSO de nouveau dans le tourbillon du CARROUSEL DE PARIS, le cabaret légendaire de JOSÉPHINE BAKER devient itinérant, et vient vers vous à votre demande, pour vous amuser, vous divertir, vous émerveiller. Un florilège d\'émotions sensuelles où chaque spectacle sera une invitation à l\'admiration et à la découverte d\'artistes de tous les horizons.')); ?>
                </p>

                <p class="about-text">
                    <?php echo esc_html(get_theme_mod('carrousel_about_paragraph_2', 'Sous les projecteurs, danseuses, humoristes, contorsionnistes, magiciens se métamorphoseront tels des phénix renaissants, à chaque escale de nos voyages, pour vous offrir des soirées inoubliables.')); ?>
                </p>

                <p class="about-text quote">
                    <?php echo esc_html(get_theme_mod('carrousel_about_quote', 'Comment pourrais-je exprimer la neurasthénie, de ce manque de représentations, de cette magie du spectacle et de l\'amour que nous pouvons donner.')); ?>
                </p>

                <p class="about-text">
                    <?php echo esc_html(get_theme_mod('carrousel_about_paragraph_3', 'Grâce à cette femme grandiose, JOSÉPHINE BAKER qui nous a poussé à agir et à continuer son œuvre. C\'est décidé, nous allons repartir pour perpétuer son héritage et son envie de faire découvrir ce monde incroyable, sans prétention aucune, mais avec l\'ambition de raviver la magie du cabaret et de son évolution !')); ?>
                </p>

                <p class="about-text">
                    <?php echo esc_html(get_theme_mod('carrousel_about_paragraph_4', 'De lieu en lieu, nuit après nuit, jour après jour, grâce à notre formidable équipe d\'artistes. Le cabaret le plus déjanté de Paris se déplaçant à votre demande de ville en ville.')); ?>
                </p>

            <?php else:
                echo wp_kses_post($about_content);
            endif; ?>
        </div>
    </section>

    <section class="contact-section">
        <h2 class="contact-title"><?php echo esc_html(get_theme_mod('carrousel_contact_title', 'CONTACTEZ-NOUS')); ?></h2>

        <?php
        // Display contact form success/error messages
        if (isset($_GET['contact'])) {
            if ($_GET['contact'] === 'success') {
                echo '<div class="contact-message success">' . esc_html(get_theme_mod('carrousel_success_message', 'Votre message a été envoyé avec succès!')) . '</div>';
            } elseif ($_GET['contact'] === 'error') {
                echo '<div class="contact-message error">' . esc_html(get_theme_mod('carrousel_error_message', 'Une erreur est survenue. Veuillez réessayer.')) . '</div>';
            }
        }
        ?>

        <form class="contact-form" id="contactForm" novalidate action="<?php echo esc_url(home_url()); ?>" method="POST">
            <?php wp_nonce_field('carrousel_contact_form', 'carrousel_nonce'); ?>
            <input type="hidden" name="carrousel_contact" value="1">

            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="firstName" name="firstName" placeholder="<?php echo esc_attr(get_theme_mod('carrousel_form_firstname_placeholder', 'Prénom *')); ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" id="lastName" name="lastName" placeholder="<?php echo esc_attr(get_theme_mod('carrousel_form_lastname_placeholder', 'Nom *')); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="<?php echo esc_attr(get_theme_mod('carrousel_form_email_placeholder', 'Adresse e-mail *')); ?>" required>
            </div>

            <div class="form-group">
                <input type="text" id="subject" name="subject" placeholder="<?php echo esc_attr(get_theme_mod('carrousel_form_subject_placeholder', 'Sujet *')); ?>" required>
            </div>

            <div class="form-group">
                <textarea id="message" name="message" placeholder="<?php echo esc_attr(get_theme_mod('carrousel_form_message_placeholder', 'Votre message *')); ?>" required></textarea>
            </div>

            <button type="submit" class="submit-btn"><?php echo esc_html(get_theme_mod('carrousel_form_submit_text', 'Envoyer le Message')); ?></button>
        </form>

        <div class="contact-info">
            <p><strong><?php echo esc_html(get_theme_mod('carrousel_contact_info_title', 'Carrousel de Paris')); ?></strong></p>
            <p><?php echo esc_html(get_theme_mod('carrousel_contact_info_subtitle', 'Spectacles sur mesure • Cabaret itinérant')); ?></p>
            <p>📧 <a href="mailto:<?php echo esc_attr(get_theme_mod('carrousel_email', 'contact@carrouseldeparis.fr')); ?>"><?php echo esc_html(get_theme_mod('carrousel_email', 'contact@carrouseldeparis.fr')); ?></a></p>
            <p>📱 <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('carrousel_phone', '+33123456789'))); ?>"><?php echo esc_html(get_theme_mod('carrousel_phone', '+33 1 23 45 67 89')); ?></a></p>
        </div>
    </section>
</main>

<?php get_footer(); ?>