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

// Register Own Custom Post Type
function stc_register_testimonial_cpt() {
    register_post_type(
        'stc_testimonial', [
            'labels' => [
                'name' => __('Testimonials', 'simple-testimonials'),
                'singular_name' => __('Testimonial', 'simple-testimonials'),
            ],
        'public' => true,
        'supports'  => array( 'title', 'editor', 'thumbnail' ),/*To add items on post,if left withouth 'supports' it will create some of them */
        'menu_icon' => 'dashicons-testimonial',


    ]);
}
add_action('init', 'stc_register_testimonial_cpt');


function stc_testimonials_shortcode( $atts = array(), $content = null, $tag = '' ){
    //Shortcode attributess
    $atts = shortcode_atts(
        [
        'layout' => 'grid', // grid or slider
        'count' => 5,
        'order'  => 'DESC',     // ASC or DESC
        ],
        $atts,//Where the list of attributes is going to come from, this is passed in the shortcode , so if notthing is added it will be ignored and go back to default,first values
        $tag // Specific filter for our shortcode
    );
    
    $args = array(
        'post_type' => 'stc_testimonial', //cpt key, for woocommerce products "product" for page "page"
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['count']),
    );
    //Custom WP loop
    $my_query = new WP_Query($args);

    if (! $my_query->have_posts()) return '<p>No testimonials found.</p>';
  
    ob_start();

    echo '<div class="simple-testimonials layout-' . esc_attr($atts['layout']) . '">';
    //wp_kses_post(get_the_content()) Sanitizes content for allowed HTML tags for post content.
    while ($my_query->have_posts()) {
        $my_query->the_post();
        echo '<div class="testimonial">';
        echo '<p class="testimonial-content">' . wp_kses_post(get_the_content()) . '</p>';
        echo '<p class="testimonial-author">— ' . esc_html(get_the_title()) . '</p>';
        echo '</div>';
    }

    echo '</div>';
    /**
     * At the end we have to reset because we are changing the default wordpress query using a custom query,
     * so we dont affect other queries that may be on this same page it returns the wordpress $post query 
     * object to its original state
     */
    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('simple_testimonial_card', 'stc_testimonials_shortcode');

// Optional: enqueue CSS for basic styling
function stc_enqueue_styles() {
    wp_enqueue_style('simple-testimonials-cards-style', plugin_dir_url(__FILE__) . 'assets/css/simple-testimonials-cards.css');
}
add_action('wp_enqueue_scripts', 'stc_enqueue_styles');

// Delete all testimonial posts when the plugin is uninstalled

function stc_delete_testimonials_on_uninstall() {

    /*We need to loop through the posts we created to delete ourt CPT */
    /*Get can use wp_query, but we can get the posts with the get_posts function */

    // Fetch all testimonial post IDs
    $testimonials = get_posts([
        'post_type' => 'stc_testimonial',
        'numberposts' => -1,/*number of posts to get in my list, so I pass -1 to bring all posts */
        'post_status'   => 'any',
        'fields' => 'ids', // only get post IDs
    ]);

    // Delete each testimonial
    foreach ($testimonials as $testimonial) {
        wp_delete_post($testimonial, true); // true = force delete (Does not send to trash)
    }
}


register_uninstall_hook(__FILE__, 'stc_delete_testimonials_on_uninstall');