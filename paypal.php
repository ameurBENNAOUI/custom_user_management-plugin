<?php
require_once __DIR__ . '/vendor/autoload.php';

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

// Replace these with your actual PayPal API credentials
$client_id = 'AXFm7EbWmA9-B6scAAvsMf0u3oVWvZ2TOe4Dz91769sTdZdYFPXZpSMXBQO0w4kExHSUEOMjsmHqMw9f';
$client_secret = 'EBl_7t22eUira3Z0Z4eLvwB-RAvH_00GzfCPfB9wTB3M1aHw2M-2dLt0G993_p3XiNA3BQaOroMdwu7o';

$apiContext = new ApiContext(
    new OAuthTokenCredential(
        $client_id,
        $client_secret
    )
);

$apiContext->setConfig(
    array(
        'mode' => 'sandbox', // or 'live' for production
        'log.LogEnabled' => true,
        'log.FileName' => plugin_dir_path(__FILE__) . 'PayPal.log',
        'log.LogLevel' => 'DEBUG', // 'DEBUG', 'INFO', 'WARN', 'ERROR'
    )
);

return $apiContext;
