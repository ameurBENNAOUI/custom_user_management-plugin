jQuery(document).ready(function ($) {
    // Add Points button click event
    $('.add-points').on('click', function () {
        var userId = $(this).closest('tr').find('td:first').text();
        var pointsToAdd = prompt("Enter the number of points to add:");

        if (pointsToAdd !== null && !isNaN(pointsToAdd) && pointsToAdd.trim() !== '') {
            $.post(ajaxurl, {
                action: 'custom_user_manager_add_points',
                user_id: userId,
                points: pointsToAdd
            }, function (response) {
                alert(response.data);
            });
        } else {
            alert("Invalid points value. Please enter a valid number.");
        }
    });

    // Disable User button click event
    $('.disable-user').on('click', function () {
        var userId = $(this).closest('tr').find('td:first').text();
        $.post(ajaxurl, {
            action: 'custom_user_manager_disable_user',
            user_id: userId
        }, function (response) {
            alert(response.data);
        });
    });

    // Delete User button click event
    $('.delete-user').on('click', function () {
        if (confirm('Are you sure you want to delete this user?')) {
            var userId = $(this).closest('tr').find('td:first').text();
            $.post(ajaxurl, {
                action: 'custom_user_manager_delete_user',
                user_id: userId
            }, function (response) {
                alert(response.data);
            });
        }
    });


    $.post(ajaxurl, {
        action: 'custom_user_manager_add_points',
        user_id: userId,
        points: pointsToAdd
    }, function (response) {
        alert(response.data);
    });
    
});
