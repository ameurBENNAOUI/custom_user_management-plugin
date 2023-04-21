<?php

require_once __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;



// use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Http\HttpClientOptions;



function get_firebase_instance() {
    static $firebase = null;

    if ($firebase === null) {
        $httpClientOptions = HttpClientOptions::default()
            ->withTimeout(60); // Increase the timeout to 60 seconds

        $factory = (new Factory())
            ->withServiceAccount(__DIR__ . '/firebase-credentials.json')
            ->withHttpClientOptions($httpClientOptions);

        $firebase = $factory->createDatabase();
    }

    return $firebase;
}

