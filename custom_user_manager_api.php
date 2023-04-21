
<?php



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

    register_rest_route('custom-user-manager/v1', '/process-data', [
        'methods' => 'POST',
        'callback' => 'custom_user_manager_process_data',
    ]);

   
}
add_action('rest_api_init', 'custom_user_manager_register_rest_routes');



function custom_user_manager_process_data(WP_REST_Request $request) {
    $user_id = $request->get_param('user_id');
    $type = $request->get_param('type');
    $data = json_decode($request->get_param('data'), true);

    // Process the data based on the parameters
    // ...

    // Return a JSON response
    return new WP_REST_Response(['message' => 'Data processed successfully.'], 200);
}


// curl --location --request POST 'https://your-wordpress-site.com/wp-json/custom-user-manager/v1/process-data' \
// --header 'Content-Type: application/x-www-form-urlencoded' \
// --data-urlencode 'user_id=example_user_id' \
// --data-urlencode 'type=example_type' \
// --data-urlencode 'data={"key": "value"}'






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
