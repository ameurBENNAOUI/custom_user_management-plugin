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
    add_menu_page('Custom User Manager', 'Custom User Manager', 'manage_options', 'custom-user-manager', 'custom_user_manager_firebase_users');
    // add_submenu_page('custom-user-manager', 'Users', 'Users', 'manage_options', 'custom-user-manager', 'custom_user_manager_users');
    add_submenu_page('custom-user-manager', 'Settings', 'Settings', 'manage_options', 'custom-user-manager-settings', 'custom_user_manager_settings');
    add_submenu_page('custom-user-manager', 'Firebase Users', 'Firebase Users', 'manage_options', 'custom-user-manager-firebase-users', 'custom_user_manager_firebase_users');

}
add_action('admin_menu', 'custom_user_manager_menu');


require_once plugin_dir_path(__FILE__) . 'custom_user_manager_firebase.php'; // If you are developing a plugin



// User submenu page
function custom_user_manager_users() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Fetch all users
    $users = get_users();

    // HTML for the user submenu
    echo '<div class="wrap">';
    echo '<h1>' . __('Custom User Manager - Users', 'custom-user-manager') . '</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>' . __('Username', 'custom-user-manager') . '</th><th>' . __('Status', 'custom-user-manager') . '</th><th>' . __('Points', 'custom-user-manager') . '</th><th>' . __('Actions', 'custom-user-manager') . '</th></tr></thead>';
    echo '<tbody>';
    foreach ($users as $user) {
        $status = $user->user_status == 0 ? 'Enabled' : 'Disabled';
        $points = get_user_meta($user->ID, 'custom_user_points', true) ?: 0;
        echo '<tr><td>' . esc_html($user->user_login) . '</td><td>' . esc_html($status) . '</td><td>' . esc_html($points) . '</td><td><button class="add-points" data-id="' . $user->ID . '">Add Points</button> <button class="delete-user" data-id="' . $user->ID . '">Delete</button> <button class="disable-user" data-id="' . $user->ID . '">Disable</button></td></tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}


// ----------------------------------------------------------
// Handle AJAX request for adding points to a user
// function custom_user_manager_add_points() {
//     // Verify nonce and user capabilities
//     if (!wp_verify_nonce($_POST['nonce'], 'custom-user-manager-nonce') || !current_user_can('edit_users')) {
//         wp_send_json_error('Unauthorized request');
//     }

//     $user_id = intval($_POST['user_id']);
//     $points_to_add = intval($_POST['points']);

//     // Retrieve existing points and add the new points
//     $current_points = get_user_meta($user_id, 'custom_user_points', true) ?: 0;
//     $new_points = $current_points + $points_to_add;

//     // Update user points
//     $result = update_user_meta($user_id, 'custom_user_points', $new_points);

//     if ($result) {
//         wp_send_json_success('Points added');
//     } else {
//         wp_send_json_error('Failed to add points');
//     }
// }
// add_action('wp_ajax_custom_user_manager_add_points', 'custom_user_manager_add_points');
// -------------------------------------------------------------




require_once plugin_dir_path(__FILE__) . 'custom_user_manager_settings.php'; // If you are developing a plugin



// // Setting submenu page
// function custom_user_manager_settings() {
//     // Check user capabilities
//     if (!current_user_can('manage_options')) {
//         return;
//     }

//     // HTML for the settings submenu
//     echo '<div class="wrap">';
//     echo '<h1>' . __('Custom User Manager - Settings', 'custom-user-manager') . '</h1>';
//     echo '<p>' . __('Settings for Custom User Manager plugin.', 'custom-user-manager') . '</p>';
//     echo '</div>';
// }


// Enqueue scripts
// function custom_user_manager_enqueue_scripts($hook) {
//     if ($hook !== 'toplevel_page_custom-user-manager') {
//         return;
//     }

//     // if ('custom-user-manager_page_custom-user-manager-firebase-users' == $hook) {
//     //     wp_enqueue_script('custom-user-manager-firebase-users', plugin_dir_url(__FILE__) . 'js/firebase-users.js', array('jquery'), '1.0', true);
//     // }

//     // wp_enqueue_script('custom-user-manager-script', plugins_url('js/custom-user-manager.js', __FILE__), array('jquery'), '1.0', true);
//     wp_localize_script('custom-user-manager-script', 'customUserManager', array(
//         'ajaxUrl' => admin_url('admin-ajax.php'),
//         'nonce' => wp_create_nonce('custom-user-manager-nonce'),
//     ));
// }
// add_action('admin_enqueue_scripts', 'custom_user_manager_enqueue_scripts');


function custom_user_manager_enqueue_scripts($hook) {
    if ($hook !== 'custom-user-manager_page_custom-user-manager-firebase-users') {
        return;
    }
    wp_enqueue_script('custom-user-manager-firebase-users', plugin_dir_url(__FILE__) . 'firebase-users.js', array('jquery'), false, true);


    // wp_enqueue_script('custom-user-manager-firebase-users', plugins_url('/firebase-users.js', __FILE__), array('jquery'), false, true);
}


add_action('admin_enqueue_scripts', 'custom_user_manager_enqueue_scripts');





// wp_enqueue_script('custom-user-manager-firebase-users', plugin_dir_url(__FILE__) . 'js/firebase-users.js', array('jquery'), '1.0', true);



// Handle AJAX request for deleting a user
// function custom_user_manager_delete_user() {
//     // Verify nonce and user capabilities
//     if (!wp_verify_nonce($_POST['nonce'], 'custom-user-manager-nonce') || !current_user_can('delete_users')) {
//         wp_send_json_error('Unauthorized request');
//     }

//     $user_id = intval($_POST['user_id']);
//     $result = wp_delete_user($user_id);

//     if ($result) {
//         wp_send_json_success('User deleted');
//     } else {
//         wp_send_json_error('Failed to delete user');
//     }
// }
// add_action('wp_ajax_custom_user_manager_delete_user', 'custom_user_manager_delete_user');

// Handle AJAX request for disabling a user
// function custom_user_manager_disable_user() {
//     // Verify nonce and user capabilities
//     if (!wp_verify_nonce($_POST['nonce'], 'custom-user-manager-nonce') || !current_user_can('edit_users')) {
//         wp_send_json_error('Unauthorized request');
//     }

//     $user_id = intval($_POST['user_id']);
//     $result = update_user_meta($user_id, 'custom_user_disabled', '1');

//     if ($result) {
//         wp_send_json_success('User disabled');
//     } else {
//         wp_send_json_error('Failed to disable user');
//     }
// }
// add_action('wp_ajax_custom_user_manager_disable_user', 'custom_user_manager_disable_user');



//require 

// Register REST API route
function custom_user_manager_register_rest_routes() {
    register_rest_route('custom-user-manager/v1', '/add-points', array(
        'methods' => 'POST',
        'callback' => 'custom_user_manager_process_payment',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('custom-user-manager/v1', '/execute-payment', array(
        'methods' => 'POST',
        'callback' => 'custom_user_manager_execute_payment',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('custom-user-manager/v1', '/success-payment', array(
        'methods' => 'GET',
        'callback' => 'custom_user_manager_success_payment',
    ));

    register_rest_route('custom-user-manager/v1', '/cancel-payment', array(
        'methods' => 'GET',
        'callback' => 'custom_user_manager_cancel_payment',
    ));

   
}
add_action('rest_api_init', 'custom_user_manager_register_rest_routes');
// add_action('rest_api_init', 'custom_user_manager_register_rest_routes');



// Process payment and add points to user
function custom_user_manager_process_payment(WP_REST_Request $request) {
    $user_id = $request->get_param('user_id');
    $payment_method = $request->get_param('payment_method');
    $token = $request->get_param('token');
    $points = $request->get_param('points');

    // return $request->get_param('token');



    if (!$user_id || !$payment_method || !$token || !$points) {
        return new WP_Error('invalid_parameters', 'Missing required parameters.', array('status' => 400));
    }

    $amount = $points / 100; // 100 points = 1 USD

    // return $amount;:

    $api_context = require plugin_dir_path(__FILE__) . 'paypal.php';

    if ($payment_method === 'paypal') {
        // Process PayPal payment
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency('USD')
            ->setTotal(10.00);

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount)
            ->setDescription('Purchase of ' . $points . ' points');

        $redirectUrls = new \PayPal\Api\RedirectUrls();

        // return $redirectUrls ;
        // $redirectUrls->setReturnUrl('YOUR_RETURN_URL')
        $redirectUrls->setReturnUrl(site_url('wp-json/custom-user-manager/v1/execute-payment?user_id=' . $user_id . '&points=' . $points))
        // $redirectUrls->setReturnUrl('https://aitnews.com/')

            ->setCancelUrl(site_url('wp-json/custom-user-manager/v1/execute-payment?user_id=' . $user_id ));

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($api_context);

            return $payment->getApprovalLink();
            // Redirect user to the PayPal approval URL
            header('Location: ' . $payment->getApprovalLink());
            exit();
        } catch (Exception $ex) {
            // Log and handle the exception
            error_log($ex->getMessage());
            return new WP_Error('paypal_error', 'PayPal payment failed.', array('status' => 500));
        }
    }
    
    elseif ($payment_method === 'stripe') {

        require_once 'vendor/autoload.php';

        $api_key = 'sk_test_51Mz7TkB4hXzPYrYHdqONkwJeHk68el4hYxSDxBAH808Cm71L6HKJFiwWnUEadD3kDJu3WmpvSVHHuVb6Qx8hlDVl00a20HeoPa';
        \Stripe\Stripe::setApiKey($api_key);
        
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amount * 100,
                        'product_data' => [
                            'name' => 'Points',
                            'description' => 'Purchase of ' . $points . ' points',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => site_url('wp-json/custom-user-manager/v1/success-payment?user_id=' . $user_id . '&points=' . $points),
                'cancel_url' => site_url('wp-json/custom-user-manager/v1/cancel-payment?user_id=' . $user_id),
            ]);
            return $session->url;
        
            // Redirect user to the Stripe Checkout page
            header('Location: ' . $session->url);
            exit();
        } catch (Exception $e) {
            // Error occurred during payment processing
            return new WP_Error('stripe_error', $e->getMessage(), array('status' => 500));
        }

        
        // Process Stripe payment
        // Add your Stripe API credentials and SDK code here
    } else {
        return new WP_Error('invalid_payment_method', 'Invalid payment method.', array('status' => 400));
    }

    // If payment is successful, add points to user
    $current_points = get_user_meta($user_id, 'custom_user_points', true) ?: 0;
    $new_points = $current_points + $points;
    update_user_meta($user_id, 'custom_user_points', $new_points);

    return rest_ensure_response(array(
        'success' => true,
        'message' => 'Payment processed and points added.',
        'points' => $new_points,
    ));
}

// Register REST API route
// function custom_user_manager_register_rest_routes() {
//     // ...
//     register_rest_route('custom-user-manager/v1', '/execute-payment', array(
//         'methods' => 'POST',
//         'callback' => 'custom_user_manager_execute_payment',
//         'permission_callback' => '__return_true',
//     ));
// }
// add_action('rest_api_init', 'custom_user_manager_register_rest_routes');

// add_action('rest_api_init', function () {
//     register_rest_route('custom-user-manager/v1', '/success-payment', array(
//         'methods' => 'GET',
//         'callback' => 'custom_user_manager_success_payment',
//     ));

//     register_rest_route('custom-user-manager/v1', '/cancel-payment', array(
//         'methods' => 'GET',
//         'callback' => 'custom_user_manager_cancel_payment',
//     ));
// });

function custom_user_manager_success_payment(WP_REST_Request $request) {
    $user_id = $request->get_param('user_id');
    $points = $request->get_param('points');

    // Add points to the user
    $current_points = get_user_meta($user_id, 'custom_user_points', true) ?: 0;
    $new_points = $current_points + $points;
    update_user_meta($user_id, 'custom_user_points', $new_points);

    // Return a success response or redirect the user to a success page
}

function custom_user_manager_cancel_payment(WP_REST_Request $request) {
    $user_id = $request->get_param('user_id');

    // Handle the canceled payment, e.g., show an error message or redirect the user to a cancel page
}







// Execute payment and add points to user
function custom_user_manager_execute_payment(WP_REST_Request $request) {
    $user_id = $request->get_param('user_id');
    $payment_id = $request->get_param('paymentId');
    $payer_id = $request->get_param('PayerID');
    $points = $request->get_param('points');

    if (!$user_id || !$payment_id || !$payer_id || !$points) {
        return new WP_Error('invalid_parameters', 'Missing required parameters.', array('status' => 400));
    }

    $api_context = require plugin_dir_path(__FILE__) . 'paypal.php';

    // Execute PayPal payment
    $payment = \PayPal\Api\Payment::get($payment_id, $api_context);
    $execution = new \PayPal\Api\PaymentExecution();
    $execution->setPayerId($payer_id);

    try {
        $payment->execute($execution, $api_context);

        // If payment is successful, add points to user
        $current_points = get_user_meta($user_id, 'custom_user_points', true) ?: 0;
        $new_points = $current_points + $points;
        update_user_meta($user_id, 'custom_user_points', $new_points);

        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Payment processed and points added.',
            'points' => $new_points,
        ));
    } catch (Exception $ex) {
        // Log and handle the exception
        error_log($ex->getMessage());
        return new WP_Error('paypal_error', 'PayPal payment execution failed.', array('status' => 500));
    }
}

function custom_user_manager_activation() {
    $default_options = array(
        'openai_key' => '',
        'stability_api_key' => '',
        'paypal_client_id' => '',
        'paypal_client_secret' => ' ',
        // 'paypal_client_secret' => '',
        'paypal_enable_points' => '0'
    );

    add_option('custom_user_manager_settings', $default_options);
}

register_activation_hook(__FILE__, 'custom_user_manager_activation');

