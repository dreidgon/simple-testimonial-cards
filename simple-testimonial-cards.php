<?php

/*
Plugin Name: Simple Testimonial cards
Description: Adds a testimonial custom post type and shortcode to display them.
Version: 1.0
Author: Dreidgon
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: simple-testimonials
*/

/**
 * Security check:
 * ABSPATH is a WP constant, if it's not defined, 
 * it tipically means that the plugin file is being
 * accessed outside of WP environment* 
 */
 




if ( ! defined('ABSPATH')){
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}