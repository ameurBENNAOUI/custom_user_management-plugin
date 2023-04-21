<?php
function stripe_payment( WP_REST_Request $request ) {
    require_once 'vendor/autoload.php'; // Make sure to include the Stripe PHP library

    $api_key = 'your_stripe_secret_key'; // Replace with your Stripe secret key
    \Stripe\Stripe::setApiKey($api_key);

    $data = json_decode( $request->get_body(), true );

    try {
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $data['amount'], // Amount to charge, in the smallest currency unit (e.g., cents)
            'currency' => $data['currency'], // Currency code, such as 'usd' or 'eur'
            'payment_method' => $data['payment_method_id'],
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);

        if ($intent->status == 'succeeded') {
            // Payment was successful, handle post-payment actions here
            return new WP_REST_Response( array( 'status' => 'success', 'message' => 'Payment successful' ), 200 );
        } else {
            // Payment failed
            return new WP_REST_Response( array( 'status' => 'error', 'message' => 'Payment failed' ), 400 );
        }
    } catch ( Exception $e ) {
        // Error occurred during payment processing
        return new WP_REST_Response( array( 'status' => 'error', 'message' => $e->getMessage() ), 500 );
    }
}
