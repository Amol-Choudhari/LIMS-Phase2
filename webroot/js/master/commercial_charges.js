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