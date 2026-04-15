$(document).ready(function() {
    // Profile image upload preview
    $("#profileImageUpload").on("change", function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $("#profileImage").attr("src", e.target.result);
                uploadProfileImage(file);
            }
            reader.readAsDataURL(file);
        }
    });

    // Upload profile image
    function uploadProfileImage(file) {
        const formData = new FormData();
        formData.append('action', 'update_profile');
        // We still need other required fields: username, email, full_name.
        // But for image upload, we might not have them if the form isn't filled?
        // Actually, the API requires them.
        // We should grab values from the form inputs.
        
        formData.append('username', $("#username").val());
        formData.append('email', $("#email").val());
        formData.append('full_name', $("#full_name").val());
        formData.append('image', file);

        $.ajax({
            url: '../api/profile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
              showLoader(2);
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Profile image updated successfully!',
                        confirmButtonColor: '#89986D'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#89986D'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to upload image',
                    confirmButtonColor: '#89986D'
                });
            }

            complete: function () {
              hideLoader();
            },
        });
    }

    // Profile form submission
    $("#profile_form").on("submit", function(e) {
        e.preventDefault();

        const password = $("#password").val();
        const confirmPassword = $("#confirm_password").val();

        if (password && password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Passwords do not match',
                confirmButtonColor: '#89986D'
            });
            return;
        }

        const formData = new FormData(this);
        formData.append('action', 'update_profile');

        $.ajax({
            url: '../api/profile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () {
              showLoader(2);
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Profile updated successfully!',
                        confirmButtonColor: '#89986D'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#89986D'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update profile',
                    confirmButtonColor: '#89986D'
                });
            }

            complete: function () {
              hideLoader();
            },
        });
    });
});
