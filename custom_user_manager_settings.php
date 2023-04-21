<?php






// Display the custom user manager settings page
function custom_user_manager_settings() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <div class="wrap">
        <h1><?php echo __('Custom User Manager - Settings', 'custom-user-manager'); ?></h1>
        <p><?php echo __('Settings for Custom User Manager plugin.', 'custom-user-manager'); ?></p>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_user_manager_settings');
            do_settings_sections('custom_user_manager_settings');
            submit_button(__('Save Settings', 'custom-user-manager'));
            ?>
        </form>
    </div>
    <?php
}


// Register the custom user manager settings
function custom_user_manager_settings_init() {
    register_setting('custom_user_manager_settings', 'custom_user_manager_settings');

    // Section 1: OpenAI API Key
    add_settings_section('openai_api_key_section', 'OpenAI API Key', '', 'custom_user_manager_settings');
    add_settings_field('openai_key', 'OpenAI_KEY', 'openai_key_field_callback', 'custom_user_manager_settings', 'openai_api_key_section');

    // Section 2: Stability.ai API Key
    add_settings_section('stability_api_key_section', 'Stability.ai API Key', '', 'custom_user_manager_settings');
    add_settings_field('stability_api_key', 'STABILITY_API_KEY', 'stability_api_key_field_callback', 'custom_user_manager_settings', 'stability_api_key_section');

    // Section 3: PayPal Developer API
    add_settings_section('paypal_api_section', 'PayPal Developer API', '', 'custom_user_manager_settings');
    add_settings_field('paypal_client_id', 'YOUR_PAYPAL_CLIENT_ID', 'paypal_client_id_field_callback', 'custom_user_manager_settings', 'paypal_api_section');
    add_settings_field('paypal_client_secret', 'YOUR_PAYPAL_CLIENT_SECRET', 'paypal_client_secret_field_callback', 'custom_user_manager_settings', 'paypal_api_section');
    add_settings_field('paypal_enable_points', 'Enable or Disable Points', 'paypal_enable_points_field_callback', 'custom_user_manager_settings', 'paypal_api_section');


    // Section 4: Point Settings
    add_settings_section('point_settings_section', 'Point Settings', '', 'custom_user_manager_settings');
    add_settings_field('point_per_currency', '100 Point per', 'point_per_currency_field_callback', 'custom_user_manager_settings', 'point_settings_section');
    add_settings_field('currency', 'Currency', 'currency_field_callback', 'custom_user_manager_settings', 'point_settings_section');


    // Section 5: Stripe API Keys
    add_settings_section('stripe_api_keys_section', 'Stripe API Keys', '', 'custom_user_manager_settings');
    add_settings_field('stripe_publishable_key', 'Stripe Publishable Key', 'stripe_publishable_key_field_callback', 'custom_user_manager_settings', 'stripe_api_keys_section');
    add_settings_field('stripe_secret_key', 'Stripe Secret Key', 'stripe_secret_key_field_callback', 'custom_user_manager_settings', 'stripe_api_keys_section');

}

add_action('admin_init', 'custom_user_manager_settings_init');


// Callback functions for displaying fields
function openai_key_field_callback() {
    $options = get_option('custom_user_manager_settings');
    echo '<input type="text" name="custom_user_manager_settings[openai_key]" value="' . esc_attr($options['openai_key']) . '">';
}

function stability_api_key_field_callback() {
    $options = get_option('custom_user_manager_settings');
    echo '<input type="text" name="custom_user_manager_settings[stability_api_key]" value="' . esc_attr($options['stability_api_key']) . '">';
}

function paypal_client_id_field_callback() {
    $options = get_option('custom_user_manager_settings');
    echo '<input type="text" name="custom_user_manager_settings[paypal_client_id]" value="' . esc_attr($options['paypal_client_id']) . '">';
}



function paypal_client_secret_field_callback() {
    $options = get_option('custom_user_manager_settings');
    echo '<input type="text" name="custom_user_manager_settings[paypal_client_secret]" value="' . esc_attr($options['paypal_client_secret']) . '">';
}
    
    
function paypal_enable_points_field_callback() {
    $options = get_option('custom_user_manager_settings');
    $checked = isset($options['paypal_enable_points']) && $options['paypal_enable_points'] == '1' ? 'checked' : '';
    echo '<label><input type="radio" name="custom_user_manager_settings[paypal_enable_points]" value="1" ' . $checked . '> Enable</label>';
    $checked = isset($options['paypal_enable_points']) && $options['paypal_enable_points'] == '0' ? 'checked' : '';
    echo '<label><input type="radio" name="custom_user_manager_settings[paypal_enable_points]" value="0" ' . $checked . '> Disable</label>';
}




// Callback function for displaying the point per currency field
function point_per_currency_field_callback() {
    $options = get_option('custom_user_manager_settings');
    echo '<input type="text" name="custom_user_manager_settings[point_per_currency]" value="' . esc_attr($options['point_per_currency']) . '">  ( Euro/USD )';
}

// Callback function for displaying the currency dropdown field
function currency_field_callback() {
    $options = get_option('custom_user_manager_settings');
    $currency = isset($options['currency']) ? $options['currency'] : '';
    echo '<select name="custom_user_manager_settings[currency]">';
    echo '<option value="USD" ' . selected($currency, 'USD', false) . '>USD</option>';
    echo '<option value="EUR" ' . selected($currency, 'EUR', false) . '>EUR</option>';
    echo '</select>';
}




function stripe_publishable_key_field_callback() {
    $options = get_option('custom_user_manager_settings');
    echo '<input type="text" name="custom_user_manager_settings[stripe_publishable_key]" value="' . esc_attr($options['stripe_publishable_key']) . '">';
}

function stripe_secret_key_field_callback() {
    $options = get_option('custom_user_manager_settings');
    echo '<input type="text" name="custom_user_manager_settings[stripe_secret_key]" value="' . esc_attr($options['stripe_secret_key']) . '">';
}
