    $(document).ready(function(){

        get_office_name();
        
        $('#ddo_id').change(function(){
            get_office_name();
        });

    });


    function get_office_name(){

        let user_code = $("#ddo_id").val();

        if (user_code != "") {

            $.ajax({
                type: "POST",
                url: '../Ajaxfunctions/get_user_office_by_id',
                data: {user_code: user_code},
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                },
                success: function (data) {
                    $("#posted_office").html(data);
                }
            });

        }
    }
