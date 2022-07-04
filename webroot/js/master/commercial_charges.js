///// COMMERCIAL CHARGES /////

    $('#save').on('click', function(e){
        if(chargesValidations()==false){
            e.preventDefault();
        }else{
            $("#frm_category").submit();
        }
    });



    //This function is used for applicant login validations.
    function chargesValidations(){

        var charges_type = $("#charges_type").val();
        var charges      = $("#charges").val();
        var value_return = 'true';

        if(charges_type == ""){

            $("#error_charges_type").show().text("Please Enter Charges Type");
            $("#charges_type").addClass("is-invalid");
            $("#charges_type").click(function(){$("#error_charges_type").hide().text;$("#charges_type").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(charges == ""){

            $("#error_charges").show().text("Please Enter Charges !!");
            $("#charges").addClass("is-invalid");
            $("#charges").click(function(){$("#error_charges").hide().text;$("#charges").removeClass("is-invalid");});
            value_return = 'false';

        }

        if(value_return == 'false'){

            var msg = "Please Check Some Fields are Missing or not Proper.";
            renderToast('error', msg);
            return false;
        
        }

    }


    $('#category_code').change(function (e) {

        if(get_commodity()==false){
            e.preventDefault();
        }
    
    });



    //to get commodity list according to the category selected
    //and load in the dropdown
    function get_commodity(){

        $("#commodity_code").find('option').remove();
        var commodity = $("#category_code").val();
        $.ajax({
            type: "POST",
            async:true,
            url:"../AjaxFunctions/show-commodity-dropdown",
            data: {commodity:commodity},
            beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function (data) {
                    $("#commodity_code").append(data);
            }
        });

    }



    $('#commodity_code').change(function (e) {

        if(checkCommodityUsed()==false){
            e.preventDefault();
        }
    
    });


    function checkCommodityUsed(){

        var commodity = $("#commodity_code").val();

        $.ajax({
            type: "POST",
            async:true,
            url:"../AjaxFunctions/check_if_commodity_added",
            data: {commodity:commodity},
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function (data) {
                if (data == 'yes') {
                    $("#commodity_code").val("");
                    $.alert("Selected Commodity charges is added already. Please Check the List!");
                    return false;
                }
              
            }
        });
    }