$(document).ready(function(){

    $("#stage_sample_code").change();

    $(document).ready(function () {

      $("#select_test_div").hide();

        $('#expect_complt').datepicker({

            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy'
        
        });
    });


    $("#save").click(function(){

      $("#tests").val(arrtestOrigin);
   
    });


    $("#save").click(function(){

      /*var test_length = $("#test_select1 option").length;
     
      if(test_length < 2){

        alert('Please Select Test to Allocate, No Test Selected.');
        return false;
      }
      */

        if (sample_allocate_validations() == false) {
            e.preventDefault();
        } else {
            $('#frm_sample_allocate').submit();  
        }     

    });


    $("#user_type").change(function(e){
		
		getflag();//added here on 05-05-2022 by Amol, to show analysis flag

      if(get_users()==false){

        e.preventDefault();
      
      }else{

        if(getqty()==false){

          e.preventDefault();

        }else{

          if(getuserdetail_new()==false){
            
            e.preventDefault();

          }else{

            if(getalloctest1()==false){
              e.preventDefault();

            }else{

                if(getflag()==false){
                  e.preventDefault();

                }
            }
          }
        }
      }
    
    });


    $("#alloc_to_user_code").change(function(e){

      if(getalloctest()==false){
        e.preventDefault();
      }else{
        if(getchem_code()==false){
          e.preventDefault();

        }else{
          if(getuserdetail()==false){
            e.preventDefault();
          }
        }
      }
    });

    $("#sample_qnt").change(function(e){
        if(chk_qnt()==false){
          e.preventDefault();

        }
    });


    $("#test_select").change(function(e){

      if(gettest()==false){
        e.preventDefault();
      }
    });


    $("#moveleft").click(function(e){

      if(move_left()==false){
        e.preventDefault();
      }
    });


    $("#moveright").click(function(e){

      if(getsampledetails()==false){
        e.preventDefault();
      }
    });

    $("#test_select1").click(function(e){

      if(removetest()==false){
        e.preventDefault();
      }
    });


});



    function sample_allocate_validations(){

        var stage_sample_code = $("#stage_sample_code").val();
        var category_code=$("#category_code").val();
        var commodity_code = $("#commodity_code").val();
        var sample_type = $("#sample_type").val();
        var user_type = $("#user_type").val();
        var alloc_to_user_code = $("#alloc_to_user_code").val();
        var sample_qnt = $("#sample_qnt").val();
        var sample_unit = $("#sample_unit").val();
        var expect_complt = $('#expect_complt').val();

        var value_return = 'true';

        // Sample Code
        if(stage_sample_code==""){

            $("#error_stage_sample_code").show().text("Sample Code Can'b be Blank!");
            $("#stage_sample_code").addClass("is-invalid");
            $("#stage_sample_code").click(function(){$("#error_stage_sample_code").hide().text;$("#stage_sample_code").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(category_code==""){

            $("#error_category_code").show().text("Category Can'b be Blank!");
            $("#category_code").addClass("is-invalid");
            $("#category_code").click(function(){$("#error_category_code").hide().text;$("#category_code").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(commodity_code==""){

            $("#error_commodity_code").show().text("Commodity Can'b be Blank!");
            $("#commodity_code").addClass("is-invalid");
            $("#commodity_code").click(function(){$("#error_commodity_code").hide().text;$("#commodity_code").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(sample_type==""){

            $("#error_sample_type").show().text("Sample Type Can'b be Blank!");
            $("#sample_type").addClass("is-invalid");
            $("#sample_type").click(function(){$("#error_sample_type").hide().text;$("#sample_type").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(user_type==""){

            $("#error_user_type").show().text("Please select the User Type.");
            $("#user_type").addClass("is-invalid");
            $("#user_type").click(function(){$("#error_user_type").hide().text;$("#user_type").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(alloc_to_user_code==""){

            $("#error_alloc_to_user_code").show().text("Please select the user name.");
            $("#alloc_to_user_code").addClass("is-invalid");
            $("#alloc_to_user_code").click(function(){$("#error_alloc_to_user_code").hide().text;$("#alloc_to_user_code").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(sample_qnt==""){

            $("#error_sample_qnt").show().text("Please Enter the quantity!");
            $("#sample_qnt").addClass("is-invalid");
            $("#sample_qnt").click(function(){$("#error_sample_qnt").hide().text;$("#sample_qnt").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(sample_unit==""){

            $("#error_sample_unit").show().text("Please select the sample unit!");
            $("#sample_unit").addClass("is-invalid");
            $("#sample_unit").click(function(){$("#error_sample_unit").hide().text;$("#sample_unit").removeClass("is-invalid");});
            value_return = 'false';
        }

        if(expect_complt==""){

            $("#error_expect_complt").show().text("Please Enter the expected completion date.");
            $("#expect_complt").addClass("is-invalid");
            $("#expect_complt").click(function(){$("#error_expect_complt").hide().text;$("#expect_complt").removeClass("is-invalid");});
            value_return = 'false';
        }




        if(value_return == 'false'){
            var msg = "Please check some fields are missing or not proper.";
            renderToast('error', msg);
            return false;
        } else {
            exit();
        }

    }