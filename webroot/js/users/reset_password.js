$('#submit').click(function (e) {
    if (reset_password_validations() == false) {
        e.preventDefault();
    } else {
        $('#reset_password_form').submit();
    } 
    
});

$('#new_captcha').click(function (e) { 
    get_new_captcha();
});

function get_new_captcha(){
    $.ajax({
        type: "POST",
        async:true,
        url:"users/refresh_captcha_code",
        beforeSend: function (xhr) { // Add this line
            xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
        },
        success: function (data) {
            $("#captcha_img").html(data);
        }
    });
}