<?php
/**
 * Plugin Name: Custom User Manager
 * Description: A simple plugin to manage users with a custom interface.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 */

 require_once plugin_dir_path(__FILE__) . 'paypal.php';



// Register the plugin's menu and submenus
function custom_user_manager_menu() {
    // Create the main menu item
    add_menu_page('Custom User Manager', 'Custom User Manager', 'manage_options', 'custom-user-manager', 'custom_user_manager_firebase_users');

    // Add the 'Firebase Users' submenu as the first submenu item
    add_submenu_page('custom-user-manager', 'Firebase Users', 'Firebase Users', 'manage_options', 'custom-user-manager', 'custom_user_manager_firebase_users');

    // Uncomment the following line to add the 'Users' submenu
    // add_submenu_page('custom-user-manager', 'Users', 'Users', 'manage_options', 'custom-user-manager-users', 'custom_user_manager_users');
    add_submenu_page('custom-user-manager', 'Settings', 'Settings', 'manage_options', 'custom-user-manager-settings', 'custom_user_manager_settings');
}
add_action('admin_menu', 'custom_user_manager_menu');



require_once plugin_dir_path(__FILE__) . 'custom_user_manager_firebase.php'; // If you are developing a plugin
require_once plugin_dir_path(__FILE__) . 'custom_user_manager_settings.php'; // If you are developing a plugin
require_once plugin_dir_path(__FILE__) . 'custom_user_manager_api.php'; // If you are developing a plugin






function custom_user_manager_enqueue_scripts($hook) {
    // if ($hook !== 'custom-user-manager_page_custom-user-manager-firebase-users') {
    //     return;
    // }
    wp_enqueue_script('custom-user-manager-firebase-users', plugin_dir_url(__FILE__) . '/js/firebase-users.js', array('jquery'), false, true);


}


add_action('admin_enqueue_scripts', 'custom_user_manager_enqueue_scripts');










function custom_user_manager_activation() {
    $default_options = array(
        'openai_key' => '',
        'stability_api_key' => '',
        'paypal_client_id' => '',
        'paypal_client_secret' => ' ',
        'paypal_enable_points' => '0'
    );

    add_option('custom_user_manager_settings', $default_options);
}

register_activation_hook(__FILE__, 'custom_user_manager_activation');

