<?php

/**
 * 404 Template - One Page Site
 * 
 * This theme is designed as a one-page site.
 * All 404 requests are redirected to the homepage.
 */

// Redirect 404 errors to homepage for one-page site
wp_redirect(home_url('/'), 301);
exit;
