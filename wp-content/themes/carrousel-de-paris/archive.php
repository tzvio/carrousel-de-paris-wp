<?php

/**
 * Archive Template - One Page Site
 * 
 * This theme is designed as a one-page site.
 * All archive requests are redirected to the homepage.
 */

// Redirect to homepage for one-page site
wp_redirect(home_url('/'), 301);
exit;
