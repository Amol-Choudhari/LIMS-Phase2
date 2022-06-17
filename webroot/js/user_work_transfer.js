//below ajax added on 14-04-2021 by Amol
$("#from_office").change(function () {
	
	var from_office_id = $("#from_office").val();
	
	$.ajax({
		type: "POST",
		url: 'get_from_user_list_for_transfer',
		data: { from_office_id: from_office_id },
		beforeSend: function (xhr) { // Add this line
				xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		},
		success: function(data) {
			$("#from_user").empty();
			$("#from_user").append(data);			
		}
	}); 
});

$("#from_user").change(function () {
	
	var from_user_id = $("#from_user").val();
	var from_office_id = $("#from_office").val();//added on 14-04-2021 by Amol
	
	$.ajax({
		type: "POST",
		url: 'get_to_user_list_for_transfer',//changed function name on 14-04-2021 by Amol
		data: { from_office_id: from_office_id,from_user_id: from_user_id },//added extra field on 14-04-2021 by Amol
		beforeSend: function (xhr) { // Add this line
				xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		},
		success: function(data) {
			$("#to_user").empty();
			$("#to_user").append(data);			
		}
	}); 
});


$("#view").click(function(e){
	
	if(validation()==false){
		e.preventDefault();
	}else{
		$("#user_work_tranfer").submit();
	}
	
});



function validation(){
	
	var from_user = $('#from_user').val();
	var to_user = $('#to_user').val();
	var reason = $('#reason').val();
	var from_office = $('#from_office').val();
	var Result = 'True';

	if(from_office == ''){
		
		$("#error_from_office").show().text("Please Select From Office.");
		$("#from_office").addClass("is-invalid");
		$("#from_office").click(function(){$("#error_from_office").hide().text;$("#from_office").removeClass("is-invalid");});
		Result = 'False';
	}
	if(from_user == ''){
		
		$("#error_from_user").show().text("Please Select From User.");
		$("#from_user").addClass("is-invalid");
		$("#from_user").click(function(){$("#error_from_user").hide().text;$("#from_user").removeClass("is-invalid");});
		Result = 'False';
	}
	if(to_user == ''){
		
		$("#error_to_user").show().text("Please Select To user.");
		$("#to_user").addClass("is-invalid");
		$("#to_user").click(function(){$("#error_to_user").hide().text;$("#to_user").removeClass("is-invalid");});
		Result = 'False';
	}
	if(reason == ''){
		
		$("#error_reason").show().text("Please Enter Valid Reason");
		$("#reason").addClass("is-invalid");
		$("#reason").click(function(){$("#error_reason").hide().text;$("#reason").removeClass("is-invalid");});
		Result = 'False';
	}
	
	if(Result == 'False'){
		var msg = "Please check some fields are missing or not proper.";
		renderToast('error', msg);
		return false;
	}else{
		exit();
	}

	//Fire
}

