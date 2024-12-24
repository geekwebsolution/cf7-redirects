<?php

if (!defined('ABSPATH')) exit;

/**
 * License manager module
 */
function cf7rgk_updater_utility() {
    $prefix = 'CF7RGK_';
    $settings = [
        'prefix' => $prefix,
        'get_base' => CF7RGK_PLUGIN_BASENAME,
        'get_slug' => CF7RGK_PLUGIN_DIR,
        'get_version' => CF7RGK_BUILD,
        'get_api' => 'https://download.geekcodelab.com/',
        'license_update_class' => $prefix . 'Update_Checker'
    ];

    return $settings;
}

register_activation_hook( __FILE__ , 'cf7rgk_updater_activate' );
add_action('upgrader_process_complete', 'cf7rgk_updater_activate');

function cf7rgk_updater_activate() {

    // Refresh transients
    delete_site_transient('update_plugins');
    delete_transient('cf7rgk_plugin_updates');
    delete_transient('cf7rgk_plugin_auto_updates');
}

require_once(CF7RGK_PATH . 'updater/class-update-checker.php');
