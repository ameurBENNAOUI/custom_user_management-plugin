<?php

require_once __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;



function custom_user_manager_get_firebase_instance() {
    $serviceAccountJsonString = file_get_contents(__DIR__ . '/firebase-credentials.json');
    $serviceAccountArray = json_decode($serviceAccountJsonString, true);
    $serviceAccount = ServiceAccount::fromValue($serviceAccountArray);
    $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->createAuth();

    return $firebase;
}




// require_once __DIR__ . '/vendor/autoload.php';

// // use Kreait\Firebase\Factory;

// function get_firebase_instance() {
//     $serviceAccount = __DIR__ . '/firebase-credentials.json';

//     $firebase = (new Factory)
//         ->withServiceAccount($serviceAccount)
//         ->create();

//     return $firebase;
// }



// function save_options_to_firebase($option, $old_value, $value) {
//     $firebase = get_firebase_instance();
//     $database = $firebase->getDatabase();

//     // Set the options in the Firebase Realtime Database
//     $database->getReference('custom_user_manager_settings')->set($value);
// }

// add_action('update_option_custom_user_manager_settings', 'save_options_to_firebase', 10, 3);




// function custom_user_manager_firebase() {
//     $factory = (new Factory)->withServiceAccount(__DIR__ . '/firebase-credentials.json');
//     $database = $factory->createDatabase();
//     return $database;
// }

// function custom_user_manager_save_options_firebase($options) {
//     $database = custom_user_manager_firebase();
//     $reference = $database->getReference('custom_user_manager_settings');
//     $reference->set($options);
// }


// add_action('update_option_custom_user_manager_settings', 'custom_user_manager_save_options_firebase', 10, 1);

use Kreait\Firebase\Auth;

// function custom_user_manager_get_firebase_users() {
//     $firebase = custom_user_manager_get_firebase_instance();
//     $auth = $firebase->getAuth();

//     $users = [];
//     $pagedUsers = $auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);

//     foreach ($pagedUsers as $userRecord) {
//         $users[] = [
//             'id' => $userRecord->uid,
//             'name' => $userRecord->displayName,
//             'email' => $userRecord->email,
//             // Add other user properties if needed
//         ];
//     }

//     return $users;
// }



// function custom_user_manager_get_firebase_users() {
//     // Initialize Firebase Auth instance
//     $auth = custom_user_manager_get_firebase_instance();

//     // Fetch Firebase users
//     $users = [];
//     $pagedResponse = $auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);

//     foreach ($pagedResponse as $response) {
//         foreach ($response->getUsers() as $userRecord) {
//             $users[] = [
//                 'id' => $userRecord->uid,
//                 'name' => $userRecord->displayName,
//                 'email' => $userRecord->email,
//             ];
//         }
//     }

//     return $users;
// }

function custom_user_manager_get_firebase_users() {
    // Initialize Firebase Auth instance
    $auth = custom_user_manager_get_firebase_instance();

    // Fetch Firebase users
    $users = [];
    $pagedResponse = $auth->listUsers($defaultMaxResults = 1000, $defaultBatchSize = 1000);

    foreach ($pagedResponse as $userRecord) {
        $points = isset($userRecord->customAttributes['points']) ? $userRecord->customAttributes['points'] : 0;

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
        // echo '<button class="disable-user">Disable</button> ';
        // echo '<button class="delete-user">Delete</button>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

// function custom_user_manager_enqueue_scripts($hook) {
//     if ('custom-user-manager_page_custom-user-manager-firebase-users' == $hook) {
//         wp_enqueue_script('custom-user-manager-firebase-users', plugin_dir_url(__FILE__) . 'js/firebase-users.js', array('jquery'), '1.0', true);
//     }
// }
// add_action('admin_enqueue_scripts', 'custom_user_manager_enqueue_scripts');


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
    $current_points = isset($userRecord->customAttributes['points']) ? $userRecord->customAttributes['points'] : 0;

    // Update the custom attributes with the new points
    $updatedCustomAttributes = array_merge($userRecord->customAttributes, ['points' => $current_points + $points]);
    $auth->setCustomUserAttributes($user_id, $updatedCustomAttributes);

    wp_send_json_success('Points added successfully.');
    wp_die();
}


// function custom_user_manager_add_points() {
//     $user_id = $_POST['user_id'];
//     $points = intval($_POST['points']);

//     if (!$user_id || !$points) {
//         wp_send_json_error('Invalid user ID or points value.');
//         wp_die();
//     }

//     // Add your logic to update the points for the Firebase user with the given user_id
//     // ...

//     wp_send_json_success('Points added successfully.');
//     wp_die();
// }

add_action('wp_ajax_custom_user_manager_add_points', 'custom_user_manager_add_points');



// Add points to user function
// function custom_user_manager_add_points() {
//     $userId = $_POST['user_id'];
//     $pointsToAdd = intval($_POST['points']);

//     // Initialize Firebase and Auth instances
//     $firebase = custom_user_manager_get_firebase_instance();
//     $auth = $firebase->getAuth();

//     // Fetch user's custom claims and add points
//     $user = $auth->getUser($userId);
//     $customClaims = $user->customClaims;
//     $currentPoints = isset($customClaims['points']) ? intval($customClaims['points']) : 0;
//     $updatedPoints = $currentPoints + $pointsToAdd;

//     // Update custom claims with new points
//     $customClaims['points'] = $updatedPoints;
//     $auth->setCustomUserClaims($userId, $customClaims);

//     wp_send_json_success("Points added successfully. New point balance: $updatedPoints");
// }
// add_action('wp_ajax_custom_user_manager_add_points', 'custom_user_manager_add_points');

function custom_user_manager_disable_user() {
    check_ajax_referer('custom-user-manager-nonce', 'security');

    $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';

    if (!$user_id) {
        wp_send_json_error(array('message' => 'User ID is missing.'));
    }

    $firebase = custom_user_manager_get_firebase_instance();
    $auth = $firebase->getAuth();

    try {
        $auth->disableUser($user_id); // Update this line
        wp_send_json_success(array('message' => 'User disabled successfully.'));
    } catch (Exception $e) {
        wp_send_json_success(array('message' => 'jjjj'));
        wp_send_json_error(array('message' => $e->getMessage()));
    }
}


// Disable user function
// function custom_user_manager_disable_user() {
//     $userId = $_POST['user_id'];

//     // Initialize Firebase and Auth instances
//     $firebase = custom_user_manager_get_firebase_instance();
//     $auth = $firebase->getAuth();

//     // Update user's disabled status
//     $auth->updateUser($userId, ['disabled' => true]);

//     wp_send_json_success('User disabled successfully.');
// }
add_action('wp_ajax_custom_user_manager_disable_user', 'custom_user_manager_disable_user');




// Delete user function
function custom_user_manager_delete_user() {
    $userId = $_POST['user_id'];

    // Initialize Firebase and Auth instances
    // $firebase = custom_user_manager_get_firebase_instance();
    // $auth = custom_user_manager_get_firebase_instance()->auth();

    $auth = custom_user_manager_get_firebase_instance()->getAuth();


    // $auth = $firebase->getAuth();


    // Delete user
    $auth->deleteUser($userId);

    wp_send_json_success('User deleted successfully.');
}
add_action('wp_ajax_custom_user_manager_delete_user', 'custom_user_manager_delete_user');

