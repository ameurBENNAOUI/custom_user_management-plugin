jQuery(document).ready(function($) {
    // Delete user
    $('.delete-user').on('click', function() {
        var user_id = $(this).data('id');
        var nonce = customUserManager.nonce;

        var deleteConfirmed = confirm('Are you sure you want to delete this user?');
        if (!deleteConfirmed) {
            return;
        }

        $.ajax({
            url: customUserManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'custom_user_manager_delete_user',
                user_id: user_id,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                alert('Failed to process request');
            }
        });
    });

    // Add points to user
$('.add-points').on('click', function() {
    var user_id = $(this).data('id');
    var nonce = customUserManager.nonce;
    var pointsToAdd = prompt('Enter the number of points to add:');
    if (pointsToAdd === null || isNaN(pointsToAdd) || pointsToAdd < 1) {
        return;
    }

    $.ajax({
        url: customUserManager.ajaxUrl,
        type: 'POST',
        data: {
            action: 'custom_user_manager_add_points',
            user_id: user_id,
            points: pointsToAdd,
            nonce: nonce
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data);
            }
        },
        error: function() {
            alert('Failed to process request');
        }
    });
});


    // Disable user
    $('.disable-user').on('click', function() {
        var user_id = $(this).data('id');
        var nonce = customUserManager.nonce;

        $.ajax({
            url: customUserManager.ajaxUrl,
            type: 'POST',
            data: {
                action: 'custom_user_manager_disable_user',
                user_id: user_id,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                alert('Failed to process request');
            }
        });
    });
});
