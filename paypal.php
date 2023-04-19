<?php
require_once __DIR__ . '/vendor/autoload.php';

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

// Replace these with your actual PayPal API credentials
$client_id = 'AXFm7EbWmA9-B6scAAvsMf0u3oVWvZ2TOe4Dz91769sTdZdYFPXZpSMXBQO0w4kExHSUEOMjsmHqMw9f';
$client_secret = 'EBl_7t22eUira3Z0Z4eLvwB-RAvH_00GzfCPfB9wTB3M1aHw2M-2dLt0G993_p3XiNA3BQaOroMdwu7o';

$api_context = new ApiContext(
    new OAuthTokenCredential(
        $client_id,
        $client_secret
    )
);

// Set the API context to use the live or sandbox environment
$api_context->setConfig(
    array(
        'mode' => 'sandbox', // Change this to 'live' for the live environment
        'log.LogEnabled' => true,
        'log.FileName' => '/PayPal.log',
        'log.LogLevel' => 'DEBUG',
        'cache.enabled' => true,
    )
);

return $api_context;
