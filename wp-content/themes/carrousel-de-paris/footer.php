    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <h3 class="footer-logo-text"><?php echo esc_html(get_theme_mod('carrousel_footer_logo', 'CARROUSEL DE PARIS')); ?></h3>
                </div>
                <p><?php echo wp_kses_post(get_theme_mod('carrousel_footer_description', 'Le cabaret légendaire de <strong>Joséphine Baker</strong> devient itinérant et vient vers vous pour vous amuser, vous divertir, vous émerveiller.')); ?></p>
                <div class="social-links">
                    <?php if (get_theme_mod('carrousel_facebook')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('carrousel_facebook')); ?>" class="social-link facebook" title="Facebook" target="_blank" rel="noopener">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/facebook.svg" alt="Facebook" />
                        </a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('carrousel_instagram')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('carrousel_instagram')); ?>" class="social-link instagram" title="Instagram" target="_blank" rel="noopener">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/instagram.svg" alt="Instagram" />
                        </a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('carrousel_twitter')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('carrousel_twitter')); ?>" class="social-link twitter" title="X (Twitter)" target="_blank" rel="noopener">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/x-twitter.svg" alt="X (Twitter)" />
                        </a>
                    <?php endif; ?>
                    <?php if (get_theme_mod('carrousel_youtube')): ?>
                        <a href="<?php echo esc_url(get_theme_mod('carrousel_youtube')); ?>" class="social-link youtube" title="YouTube" target="_blank" rel="noopener">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/youtube.svg" alt="YouTube" />
                        </a>
                    <?php endif; ?>
                    <?php
                    // If no social links are set, show default placeholders
                    if (!get_theme_mod('carrousel_facebook') && !get_theme_mod('carrousel_instagram') && !get_theme_mod('carrousel_twitter') && !get_theme_mod('carrousel_youtube')):
                    ?>
                        <a href="#" class="social-link facebook" title="Facebook">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/facebook.svg" alt="Facebook" />
                        </a>
                        <a href="#" class="social-link instagram" title="Instagram">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/instagram.svg" alt="Instagram" />
                        </a>
                        <a href="#" class="social-link twitter" title="X (Twitter)">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/x-twitter.svg" alt="X (Twitter)" />
                        </a>
                        <a href="#" class="social-link youtube" title="YouTube">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/social/youtube.svg" alt="YouTube" />
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-section">
                <h3><?php echo esc_html(get_theme_mod('carrousel_footer_services_title', 'NOS SPECTACLES')); ?></h3>
                <ul>
                    <?php
                    $services = get_theme_mod('carrousel_footer_services', "Cabaret traditionnel\nSpectacles sur mesure\nSoirées privées\nÉvénements corporate\nMariages & célébrations");
                    $services_array = array_filter(explode("\n", $services));
                    foreach ($services_array as $service): ?>
                        <li><?php echo esc_html(trim($service)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-section">
                <h3><?php echo esc_html(get_theme_mod('carrousel_footer_contact_title', 'CONTACT')); ?></h3>
                <p>📧 <a href="mailto:<?php echo esc_attr(get_theme_mod('carrousel_email', 'contact@carrouseldeparis.fr')); ?>"><?php echo esc_html(get_theme_mod('carrousel_email', 'contact@carrouseldeparis.fr')); ?></a></p>
                <p>📱 <a href="tel:<?php echo esc_attr(str_replace(' ', '', get_theme_mod('carrousel_phone', '+33123456789'))); ?>"><?php echo esc_html(get_theme_mod('carrousel_phone', '+33 1 23 45 67 89')); ?></a></p>
                <p>📍 <?php echo wp_kses_post(nl2br(esc_html(get_theme_mod('carrousel_footer_address', 'Paris, France\nSpectacles itinérants sur demande')))); ?></p>
                <p>🕐 <?php echo wp_kses_post(nl2br(esc_html(get_theme_mod('carrousel_footer_hours', 'Lun-Ven: 9h-18h\nSam-Dim: Sur rendez-vous')))); ?></p>
            </div>

            <div class="footer-section">
                <h3><?php echo esc_html(get_theme_mod('carrousel_footer_heritage_title', 'L\'HÉRITAGE')); ?></h3>
                <p><?php echo wp_kses_post(get_theme_mod('carrousel_footer_heritage_p1', 'Dans l\'esprit de <strong>Joséphine Baker</strong>, nous perpétuons la tradition du cabaret parisien avec passion et authenticité.')); ?></p>
                <p><?php echo wp_kses_post(get_theme_mod('carrousel_footer_heritage_p2', 'Chaque spectacle est une invitation à l\'admiration et à la découverte d\'artistes de tous les horizons.')); ?></p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo wp_kses_post(get_theme_mod('carrousel_footer_copyright', 'Carrousel de Paris. Tous droits réservés. | Créé avec passion pour perpétuer l\'héritage de Joséphine Baker')); ?></p>
        </div>
    </footer>

    <?php wp_footer(); ?>
    </body>

    </html>