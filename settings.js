jQuery(document).ready(function ($) {
    $("#custom_user_manager_settings_form").on("submit", function (e) {
        e.preventDefault();

        var data = {
            action: "custom_user_manager_save_settings",
            settings: {
                openai_key: $("input[name='custom_user_manager_settings[openai_key]']").val(),
                stability_api_key: $("input[name='custom_user_manager_settings[stability_api_key]']").val(),
                paypal_client_id: $("input[name='custom_user_manager_settings[paypal_client_id]']").val(),
                paypal_client_secret: $("input[name='custom_user_manager_settings[paypal_client_secret]']").val(),
                paypal_enable_points: $("input[name='custom_user_manager_settings[paypal_enable_points]']:checked").val()
            }
        };

        $.post(ajaxurl, data, function (response) {
            alert(response.data.message);
        });
    });
});
