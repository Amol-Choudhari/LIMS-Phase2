//Validation to check the existing password by AKASH on 31-12-2021
$('#Oldpassword').focusout(function (e) { 
  
    var Oldpassword = $("#Oldpassword").val();

    if (Oldpassword != '') {
        
        $.ajax({
            type : 'POST',
            url : '../AjaxFunctions/check_old_password',
            async : true,
            data : {Oldpassword:Oldpassword},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success : function(response){
    
                if($.trim(response)=='yes'){
    
                    $.alert({
                        title: "Alert!",
                        content: 'The Old Password is Not Matched!!',
                        typeAnimated: true,
                        buttons: {
                            Retry: {
                                text: 'Retry',
                                btnClass: 'btn-red',
                                action: function(){
                                    $("#Oldpassword").val('');
                                }
                            },
                        }
                    });
                }
            }
        });
    }

    
}); 


//FOR CHECKING THE Password & CONFIRM Passwordd ARE SAME OR NOT ON 31-12-2021 BY AKASH
$('#confpass').focusout(function(){

    var NewPassword = $("#Newpassword").val();
    var ConfirmedPassword = $('#confpass').val();
    if (NewPassword != '') {
        if (NewPassword != ConfirmedPassword) {
            $.alert("Confirm password doesn't match with New password");
            $('#confpass').val('');
        }
    }
});


//FOR CHECKING THE OLD AND NEW PASSWORD - AKASH [09-09-2022]
$('#Newpassword').focusout(function(){

    var NewPassword = $("#Newpassword").val();
    var Oldpassword = $("#Oldpassword").val();
    if (NewPassword != '') {
        if (NewPassword == Oldpassword) {
            $.alert('The New password cannot be the same as the Old password');
            $('#Newpassword').val('');
        }
    }
});