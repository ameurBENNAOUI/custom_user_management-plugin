<?php

require_once __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Auth;



function custom_user_manager_get_firebase_instance() {
    $serviceAccountJsonString = file_get_contents(__DIR__ . '/firebase-credentials.json');
    $serviceAccountArray = json_decode($serviceAccountJsonString, true);
    $serviceAccount = ServiceAccount::fromValue($serviceAccountArray);
    $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->createAuth();

    return $firebase;
}


function custom_user_manager_get_firebase_users() {
    // Initialize Firebase Auth instance
    $auth = custom_user_manager_get_firebase_instance();

    // Fetch Firebase users
    $users = [];
    $pagedResponse = $auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);

    foreach ($pagedResponse as $userRecord) {
        $points = isset($userRecord->customClaims['points']) ? $userRecord->customClaims['points'] : 0;

        $users[] = [
            'id' => $userRecord->uid,
            'name' => $userRecord->displayName,
            'email' => $userRecord->email,
            'points' => $points,

        ];
    }

    return $users;
}

function custom_user_manager_firebase_users() {
    // Fetch Firebase users
    $users = custom_user_manager_get_firebase_users();

    // Display the table
    echo '<div class="wrap">';
    echo '<h1>Firebase Users</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th>ID</th>';
    echo '<th>Name</th>';
    echo '<th>Email</th>';
    echo '<th>Points</th>';

    echo '<th>Actions</th>';
    echo '</tr></thead><tbody>';

    // Loop through the users and display them in the table
    foreach ($users as $user) {
        echo '<tr>';
        echo '<td>' . $user['id'] . '</td>';
        echo '<td>' . $user['name'] . '</td>';
        echo '<td>' . $user['email']   . '</td>';
        echo '<td>' . $user['points']   . '</td>';

        echo '<td>';
        echo '<button class="add-points">Add Points</button> ';

        
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}


function custom_user_manager_add_points() {
    if (!isset($_POST['user_id']) || !isset($_POST['points'])) {
        wp_send_json_error('Invalid user ID or points value.');
        wp_die();
    }

    $user_id = $_POST['user_id'];
    $points = intval($_POST['points']);

    // Get the Firebase Auth instance
    $auth = custom_user_manager_get_firebase_instance();
    
    // Fetch the user record
    $userRecord = $auth->getUser($user_id);

    // Get the current points from custom attributes
    $current_points = isset($userRecord->customClaims['points']) ? $userRecord->customClaims['points'] : 0;

    // Update the custom attributes with the new points
    $updatedCustomAttributes = array_merge($userRecord->customClaims, ['points' => $current_points + $points]);
    $auth->setCustomUserAttributes($user_id, $updatedCustomAttributes);

    wp_send_json_success('Points added successfully.');
    wp_die();
}


add_action('wp_ajax_custom_user_manager_add_points', 'custom_user_manager_add_points');



