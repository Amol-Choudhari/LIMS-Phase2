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

      var test_length = $("#test_select1 option").length;
     
      if(test_length < 2){

        alert('Please Select Test to Allocate, No Test Selected.');
        return false;
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
