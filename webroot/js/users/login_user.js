
    $("#login_btn").click(function(e){

        if(myFunction()==false){
            e.preventDefault();
        }else{
            $("#login_user_form").submit();
        }

    });


    function myFunction(){

        var email=$("#email").val();
        var password=$("#passwordValidation").val();
        var captchacode=$("#captchacode").val();
        var value_return = 'true';

        if(!email.match(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/)){

            $("#error_email").show().text("Entered email id is not valid.");
            $("#email").addClass("is-invalid");
            $("#email").click(function(){$("#error_email").hide().text;$("#email").removeClass("is-invalid");});
            value_return = 'false';
            
        }else if(email==""){

            $("#error_email").show().text("Please Enter Email ID");
            $("#email").addClass("is-invalid");
            $("#email").click(function(){$("#error_email").hide().text;$("#email").removeClass("is-invalid");});
            value_return = 'false';
        
        }
        
        if(password==""){

            $("#error_password").show().text("Please Enter Password");
            $("#passwordValidation").addClass("is-invalid");
            $("#passwordValidation").click(function(){$("#error_password").hide().text;$("#passwordValidation").removeClass("is-invalid");});
            value_return = 'false';
        
        }
        
        if(captchacode==""){
        
            $("#error_captchacode").show().text("Please Enter Captcha");
            $("#captchacode").addClass("is-invalid");
            $("#captchacode").click(function(){$("#error_captchacode").hide().text;$("#captchacode").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(value_return == 'false'){
            
            var msg = "Please check some fields are missing or not proper.";
            renderToast('error', msg);
            return false;
            exit;
        }

        var PasswordValue = document.getElementById('passwordValidation').value;
        var SaltValue = document.getElementById('hiddenSaltvalue').value;
        var EncryptPass = sha512(PasswordValue);
        var SaltedPass = SaltValue.concat(EncryptPass);
        var Saltedsha512pass = sha512(SaltedPass);

        document.getElementById('passwordValidation').value = Saltedsha512pass;
        document.getElementById('hiddenSaltvalue').value = '';
        //exit();
    }


    $("#new_captcha").click(function(e){
        get_new_captcha();
    });


    function get_new_captcha(){
        $.ajax({
            type: "POST",
            async:true,
            url:"../users/refresh_captcha_code",
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function (data) {
                $("#captcha_img").html(data);
            }
        });
    }
