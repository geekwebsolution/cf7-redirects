<?php
/*
    Plugin Name: Contact Form 7 Redirects
    Description:  Contact Form 7 Redirection after mail sent.
    Author: Geek Code Lab
    Version: 1.7
    Author URI: https://geekcodelab.com/
    Text Domain: contact-form-7-redirects
*/

// do not allow direct access
if(isset($_SERVER['SCRIPT_NAME'])) {
    if (strpos(strtolower($_SERVER['SCRIPT_NAME']), strtolower(basename(__FILE__)))) {
        header('HTTP/1.0 403 Forbidden');
        exit('Forbidden');
    }
}

/**
 * Global vars
 */

define('CF7RGK_BUILD', '1.7');  // Used to force load of latest .js files
define('CF7RGK_FILE', __FILE__); // For use in other files
define('CF7RGK_PATH', plugin_dir_path(__FILE__));
define('CF7RGK_URL', plugin_dir_url(__FILE__));

/**
 * Admin notice
 */
add_action( 'admin_init', 'cf7rgk_plugin_load' );

function cf7rgk_plugin_load(){
	if ( ! ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ) {
		add_action( 'admin_notices', 'cf7rgk_install_contact_form_7_admin_notice' );
		deactivate_plugins("contact-form-7-redirects/contact-form-7-redirects.php");
		return;
	}
}

function cf7rgk_install_contact_form_7_admin_notice(){ ?>
	<div class="error">
		<p>
			<?php
			// translators: %s is the plugin name.
			echo esc_html( sprintf( __( '%s is enabled but not effective. It requires Contact Form 7 in order to work.', 'Contact Form 7 Redirects' ), 'contact-form-7-redirects' ) );
			?>
		</p>
	</div>
	<?php
}


/**
 * Includes files
 */

if ( is_admin() ) {
    require_once CF7RGK_PATH . 'library/class-admin.php';
}
require_once CF7RGK_PATH . 'library/class-util.php';


/**
 * Initialize plugin links
 */
$plugin = plugin_basename( __FILE__ );
CF7RGK_util::CF7RGK_setup();

$plugin = plugin_basename(__FILE__);
add_filter( "plugin_action_links_$plugin", 'cf7rgk_add_plugin_settings_link');
function cf7rgk_add_plugin_settings_link( $links ) {
	$support_link = '<a href="https://geekcodelab.com/contact/" style="color:#46b450;font-weight: 600;" target="_blank" >' . __( 'Support', 'contact-form-7-redirects' ) . '</a>'; 
	array_unshift( $links, $support_link );

    $setting_link = '<a href="'. admin_url('admin.php?page=wpcf7') .'">' . __( 'Settings', 'contact-form-7-redirects' ) . '</a>';
	array_unshift( $links, $setting_link );

	return $links;
}

/**
 * Ajax for search page list
 */
add_action('wp_ajax_cf7rgk_redirect_page_ajax', 'cf7rgk_redirect_page_ajax_callback');
add_action('wp_ajax_nopriv_cf7rgk_redirect_page_ajax', 'cf7rgk_redirect_page_ajax_callback');

// AJAX callback function
function cf7rgk_redirect_page_ajax_callback()
{
    $result = array();
    $search = $_POST['search'];

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

    echo json_encode($result);

    wp_die();
}

/**
 * Ajax for search post list
 */
add_action('wp_ajax_cf7rgk_redirect_post_ajax', 'cf7rgk_redirect_post_ajax_callback');
add_action('wp_ajax_nopriv_cf7rgk_redirect_post_ajax', 'cf7rgk_redirect_post_ajax_callback');

// AJAX callback function
function cf7rgk_redirect_post_ajax_callback()
{
    $result = array();
    $search = $_POST['search'];

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

    echo json_encode($result);

    wp_die();
}
