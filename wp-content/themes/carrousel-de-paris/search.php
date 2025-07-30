<?php

/**
 * Search Results Template - One Page Site
 * 
 * Search functionality is disabled for this one-page site.
 * All search requests are redirected to the homepage.
 */

// Redirect search requests to homepage
wp_redirect(home_url('/'), 301);
exit;
