
 $(document).ready(function () {

        $('#date_validity').datepicker({
          endDate: '',
          autoclose: true,
          todayHighlight: true,
          format: 'dd/mm/yyyy'
        });
  });
  
 $('#test_parameters').multiselect({
    maxWidth: 200,
    placeholder: 'Select Option'
});


$('#category_code').change(function (e) {

  if(get_commodity()==false){
    e.preventDefault();
  }

});

$('#commodity_code').change(function (e) {

  if(get_parameters()==false){
    e.preventDefault();
  }

});

//call to login validations
$('#savebtn').click(function (e) {
    
    if (add_nabl_form_validations() == false) {
        e.preventDefault();  
    } else {  
      $('#save_report').submit();  
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

//this function is used to get test parameters list in multiple listing mode/format
function get_parameters(){

  $("#test_parameters").find('option').remove();
  
  var commodity_code = $("#commodity_code").val();
  $.ajax({
      type: "POST",
      async:true,
      url:"../NablAccreditation/show-parameters-dropdown",
      data: {commodity_code:commodity_code},
      beforeSend: function (xhr) { // Add this line
          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
      },
      success: function (data) {
          $(".ms-options").html(data);
		  getTestParameterOptions();//to append hidden option in select element
      }
  });
}

//it is also required for multiselect when list show through ajax, to append hidden option in select elment
//this will help to select from list and post array
function getTestParameterOptions(){

   $("#test_parameters").find('option').remove();
  
	  var commodity_code = $("#commodity_code").val();
	  $.ajax({
		  type: "POST",
		  async:true,
		  url:"../NablAccreditation/test-parameter-option",
		  data: {commodity_code:commodity_code},
		  beforeSend: function (xhr) { // Add this line
			  xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		  },
		  success: function (data) {
			  $("#test_parameters").html(data);
		  }
	  });

}

function add_nabl_form_validations(){

    var nabl_certificate =$("#nabl_certificate").val();
    var date_validity    =$("#date_validity").val();
    var category_code    = $("#category_code").val();
    var commodity_code   = $("#commodity_code").val();
    var test_parameters  = $("#test_parameters").val();
    var office           = $("#office").val();
   

    if(nabl_certificate==""){

      $("#error_nabl_certificate").show().text("Please Enter NABL Certificate Number.");
      $("#nabl_certificate").addClass("is-invalid");
      $("#nabl_certificate").click(function(){$("#error_nabl_certificate").hide().text;$("#nabl_certificate").removeClass("is-invalid");});
      value_return = 'false';
    }


    if(date_validity==""){

      $("#error_date_validity").show().text("Please Enter Date of Validity.");
      $("#date_validity").addClass("is-invalid");
      $("#date_validity").click(function(){$("#error_date_validity").hide().text;$("#date_validity").removeClass("is-invalid");});
      value_return = 'false';
    }


     if(category_code==""){

      $("#error_category_code").show().text("Please Select Category.");
      $("#category_code").addClass("is-invalid");
      $("#category_code").click(function(){$("#error_category_code").hide().text;$("#category_code").removeClass("is-invalid");});
      value_return = 'false';
    }


    if(commodity_code==""){

      $("#error_commodity_code").show().text("Please Select Commodity.");
      $("#commodity_code").addClass("is-invalid");
      $("#commodity_code").click(function(){$("#error_commodity_code").hide().text;$("#commodity_code").removeClass("is-invalid");});
      value_return = 'false';
    }

    if(test_parameters==""){

      $("#error_test_parameters").show().text("Please Select Test Parameters.");
      $("#test_parameters").addClass("is-invalid");
      $("#test_parameters").click(function(){$("#error_test_parameters").hide().text;$("#test_parameters").removeClass("is-invalid");});
      value_return = 'false';
    }

    if(office==""){

      $("#error_office").show().text("Please Select For RAL/CAL.");
      $("#office").addClass("is-invalid");
      $("#office").click(function(){$("#error_office").hide().text;$("#office").removeClass("is-invalid");});
      value_return = 'false';
    }
	
	if(value_return=='false'){
		return false;
	}
}  