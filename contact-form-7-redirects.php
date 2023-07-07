<?php
/*
Plugin Name: Contact Form 7 Redirects

Description:  Contact Form 7 Redirection after mail sent.

Author: Geek Code Lab

Version: 1.6

Author URI: https://geekcodelab.com/
 */

//do not allow direct access
if (strpos(strtolower($_SERVER['SCRIPT_NAME']), strtolower(basename(__FILE__)))) {
    header('HTTP/1.0 403 Forbidden');
    exit('Forbidden');
}

/* * ******************
 * Global constants
 * ****************** */


// ********** Be sure to use "Match case," and do UPPER and lower case seperately ****************

define('CF7RGK_BUILD', '1.6');  // Used to force load of latest .js files
define('CF7RGK_FILE', __FILE__); // For use in other files
define('CF7RGK_PATH', plugin_dir_path(__FILE__));
define('CF7RGK_URL', plugin_dir_url(__FILE__));

register_activation_hook( __FILE__, 'cf7rgk_plugin_activate' );
function cf7rgk_plugin_activate() {
	if ( ! ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ) {
		die( '<b>Contact Form 7 Redirects</b> plugin is deactivated because it require <b>Contact Form 7</b> plugin installed and activated.' );
	}
}



/* * ******************
 * Includes
 * ****************** */
//function to run on activation 
 
require_once CF7RGK_PATH . 'library/class-util.php';

if ( is_admin() ) {
    require_once CF7RGK_PATH . 'library/class-admin.php';
}
//  Initialize plugin settings and hooks ... 
$plugin = plugin_basename( __FILE__ );
CF7RGK_util::CF7RGK_setup();

$plugin = plugin_basename(__FILE__);
add_filter( "plugin_action_links_$plugin", 'cf7rgk_add_plugin_settings_link');
function cf7rgk_add_plugin_settings_link( $links ) {
	$support_link = '<a href="https://geekcodelab.com/contact/" style="color:#46b450;font-weight: 600;" target="_blank" >' . __( 'Support' ) . '</a>'; 
	array_unshift( $links, $support_link );

	return $links;
}



// Register AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_cf7rgk_redirect_page_ajax', 'cf7rgk_redirect_page_ajax_callback');
add_action('wp_ajax_nopriv_cf7rgk_redirect_page_ajax', 'cf7rgk_redirect_page_ajax_callback');

// AJAX callback function
function cf7rgk_redirect_page_ajax_callback()
{
    $result = array();
    $search = $_POST['search']; // Get the search term from the AJAX request

    $args = array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'posts_per_page' => -1,
    );

    // Check if the search term is numeric (post ID)
    if (is_numeric($search)) {
        $args['p'] = $search; // Set the 'p' parameter to the search term (post ID)
    } else {
        $args['s'] = $search; // Set the 's' parameter to the search term (post title)
    }

    // Get the pages that match the search term
    $pages = get_posts($args);

    foreach ($pages as $page_id) {
        $page_title = get_the_title($page_id);

        // Add the page ID and title to the result array
        $result[] = array(
            'id'    => $page_id,
            'title' => $page_title,
        );
    }

    // Encode the result array as JSON and send the response
    echo json_encode($result);

    wp_die(); // Terminate the script execution
}

// Register AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_cf7rgk_redirect_post_ajax', 'cf7rgk_redirect_post_ajax_callback');
add_action('wp_ajax_nopriv_cf7rgk_redirect_post_ajax', 'cf7rgk_redirect_post_ajax_callback');

// AJAX callback function
function cf7rgk_redirect_post_ajax_callback()
{
    $result = array();
    $search = $_POST['search']; // Get the search term from the AJAX request

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids'
    );

    // Check if the search term is numeric (post ID)
    if (is_numeric($search)) {
        $args['p'] = $search; // Set the 'p' parameter to the search term (post ID)
    } else {
        $args['s'] = $search; // Set the 's' parameter to the search term (post title)
    }


    // Get the posts that match the search term
    $posts = get_posts($args);

    foreach ($posts as $post_id) {
        $post_title = get_the_title($post_id);

        // Add the post ID and title to the result array
        $result[] = array(
            'id'    => $post_id,
            'title' => $post_title,
        );
    }

    // Encode the result array as JSON and send the response
    echo json_encode($result);

    wp_die(); // Terminate the script execution
}
