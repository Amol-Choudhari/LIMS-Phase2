$(document).ready(function(){

  $("#stage_sample_code").change();

  $("#calculate").click(function(e) {

    e.preventDefault();
    $('#save').prop("disabled", false);
    var flag = 0;

    $('#modal_test').find(':input.input').each(function() {

      $("#abc").prop("hidden", true);
      var val = $(this).val();

      if (val == "") {
        flag = true;
      }

    });

    if (flag == false) {

      $('#save').prop("disabled", false);
      var test_select = $("#test_v").val();
      var validation;

      if (test_select != "") {

        $.ajax({

          type: "POST",
          url: '../Test/get_test_formulae',
          data: {
            test_select: test_select
          },
          beforeSend: function (xhr) { // Add this line
              xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
          },
          success: function(data) {

            var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

            if(resArray.indexOf('[error]') !== -1){
            var msg="You have passed incorrect values!";
            alert(msg);

          }else{

            if(resArray!='1'){

              resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

              $.each(resArray, function(key1, value1) {

                validation = value1['res_validation_range'];
                $("#formula").val(value1['test_formulae']);

                /*$.each(value1, function(key, value) {
                  $("#formula").val(value['test_formulae']);
                });*/

              });

              var formula 	= $("#formula").val();
              var string 		= '';
              var i			= 0;
              var chars 		= new Array();

              while (i < formula.length) {

                var letters = /^[A-Za-z]+$/;
                if (formula[i].match(letters)) {
                  var val = $("#" + formula[i]).val();
                  string 	= string + val;

                } else {
                  string	= string + formula[i];
                }
                i++;
              }

              var result = eval(string);

              //Apply validation for negative, NaN, Infinity value for calculate result,
              if (Math.sign(result) === -1 || typeof result == 'NaN' || typeof result == 'Infinity') {
              //if (Math.sign(result) === -1 || result == 'NaN' || result == 'Infinity') {

                $.alert({

                  icon: 'fas fa-exclamation-triangle',
									title: 'Alert',
									type: 'red',
									columnClass: 'col-md-6 col-md-offset-3',
									content: "Calculated result is not in a range. Please check input parameters.",
								});

                $('#save').attr("disabled", true);
              }
              $("#res_div").show();
              $("#res").val(result.toFixed(validation));


            }
            else{
              var msg=" Formula for this test is expired!";
              $.alert(msg);

            }
          }
          }
        });
      } else {
        var msg="Select previous data first";
        $.alert(msg);

      }
    } else {
      var msg="Please fill all input first";
        $.alert(msg);

    }
  });


  $("#close").click(function(){
    window.location = '';
  });

  $("#close1").click(function(){
    window.location = '';
  });


  $("#test_r").click(function(e) {
    e.preventDefault();


    $.confirm({

			title:'',
      icon: 'far fa-question-circle',
      type: 'orange',
      content: 'Do u want to clear the test result?',
      columnClass: 'col-md-5 col-md-offset-3',
     
      buttons: {

        confirm: { 

          btnClass: 'btn-blue',
          action: function () {

            var chemist_code=$("#sample").val();
            var test_code=$("#test_v").val();
            $("#abc").prop("hidden", false);
      
            $.ajax({
                type: "POST",
                url: '../Test/update_cant_perform_test',
                data: {chemist_code:chemist_code,test_code:test_code},
                beforeSend: function (xhr) { // Add this line
                    xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                },
                success:function(data)
                {
                  $('#res').val('NA');
      
                  var msg="Please enter the remark/reason and save the action.";
                  $("#abc").prop("hidden", false);
                  $('#save').prop("disabled", false);
                  $.alert(msg);
      
                  $("#test_r").prop("disabled", true);
      
                }
            });
          }
        },
        
        cancel:{
          
          btnClass: 'btn-red',
  
        },
        
      }
    });


  });


    $("#save").click(function(e) {

      e.preventDefault();
      var res=$("#res").val();

        if(res!='' && res!='-----Select-----')
        {
          $.confirm({
			
            title: 'Info',
            icon: 'fas fa-info-circle',
            type: 'blue',
            content: 'Do u want to save the test result?',
            columnClass: 'col-md-5 col-md-offset-3',
            buttons: {
      
              confirm: { 
      
                btnClass: 'btn-blue',
                action: function () {
      
                  $("#modal_test").submit();
                }
              },
              
              cancel:{
                
                btnClass: 'btn-red',
        
              },
              
            }
          });
        
        }else{
          var msg="Fill the result first";
          $.alert(msg);
        }
    });



  $('#finalize').click(function(e) {

    // disable save button after click on save button,
    $("#finalize").prop("disabled", true);

        $.confirm({
          
          title: 'Info',
          icon: 'fas fa-info-circle',
          type: 'blue',
          content: 'Do u want to finalize the test results?',
          columnClass: 'col-md-5 col-md-offset-3',
          buttons: {

            confirm: { 

              btnClass: 'btn-blue',
              action: function () {

                $('#test').submit();
              }
            },
            
            cancel:{
              
              btnClass: 'btn-red',
              action: function () {
              
                $("#finalize").prop("disabled", false);
              }
            },
            
          }
        });
  
  });


  $('#save').on("mouseover", function(e) {
    e.preventDefault();
    var formula = $("#formula").val();
    if (formula == 'formula') {
      $("#calculate").trigger("click");
    }
  });


 $("#input_parameter_text").on("blur","input[type='text']",function(e){

   let id = $(this).attr('id');
   let para1 = $(this).attr('blrval1');
   let para2 = $(this).attr('blrval2');

   if(para1 != 'undefined' && para2 != 'undefined')
   {
      var final_val= parseFloat($(this).val());

      $(this).val(final_val.toFixed(para2));

      var pattern = '/^[0-9]{1,' + para1 + '}\.[0-9]{1,' + para2 + '}$/';
      var regexp  = new RegExp(`^[0-9]{1,${para1}}\.[0-9]{1,${para2}}$`);
      var i= regexp.test($(this).val());

      if(i==false)
      {
      	$.alert('Digits before decimal point should be less than or equal to '+para1);
      	$(this).val(' ');
      }

      let colorval = regexp.test($(this).val()) ? 'inherit' : 'red';
      $(this).css('borderColor',colorval);
   }

 });

  $("#input_parameter_text").on("blur","input[type='test']",function(e){

      var i= /^[a-zA-Z0-9]*$/.test($(this).val());
      if(i==false)
      {
        $.alert('Value should be alphabetic only');
      }
      let colorval = /^[a-zA-Z0-9]*$/.test($(this).val())  ? 'inherit' : 'red';
      $(this).css('borderColor',colorval);
  });



});
