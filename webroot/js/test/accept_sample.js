$(document).ready(function () {

	$('#input_parameter_text').DataTable();
	$("#radio_options").hide();
	$("#abc").hide();
	$("#save_a").hide();
	$("#save_r_n").hide();

	$('.action').change(function(){

		var action=$(".action:checked").val();
		if(action=='A')
		{
			$("#save_r_n").hide();
			$("#abc").hide();
			$("#save_a").show();

		}else if(action=='R'){

			$("#save_a").hide();
			$("#abc").show();
			$("#save_r_n").show();

		}


	});

	$('.check_id').click(function(){

		check();
	});



	$("#save_a").click(function(e){ 	

		$.confirm({
			
			icon: 'fas fa-info-circle',
			content: 'Are you sure want to Accept the selected sample?',
			columnClass: 'col-md-6 col-md-offset-3',
			buttons: {

				confirm: { 

					btnClass: 'btn-green',
					action: function () {

						var final_str="";
						var chemist_code =$("#chemist_code").val("");
		
						$.each($("input[name='checkboxArray[]']:checked"), function()
						{
							id=$(this).attr("id");
							final_str += id+"-";
						});
		
						$.ajax({
							type: "POST",
							url: '../Test/accept_sample_bychemist',
							data:{final_str:final_str},
							beforeSend: function (xhr) { // Add this line
									xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
							},
							success: function (data) {
								var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
								resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
		
								var chemist_code = '';
								$.each(resArray, function (key, value){
		
									chemist_code += value+',';
		
								});
		
								$.alert({
									title: 'Success',
									type: 'green',
									columnClass: 'col-md-6 col-md-offset-3',
									content: "The Selected Samples With Code <b> "+chemist_code+" </b> are accepted!!! ",
									onClose: function(){
										location.reload();
									}
								});
							}
						});;
					}
				},
				
				cancel:{
					
					btnClass: 'btn-red',
					action: function () {
						location.reload();
						return false;
					}
				},
				
			}
		});
	});



	 $("#save_r_n").click(function(e){

		 if($("#sendback_remark").val() == ''){

			$.alert({
				icon: 'fas fa-exclamation-triangle',
				title: 'Alert',
				type: 'red',
				columnClass: 'col-md-6 col-md-offset-3',
				content:'Please Enter Valid Reason to Send the Sample Back!'
			});
			
			return false;
		}

		$.confirm({
			
			title: 'Info',
			icon: 'fas fa-info-circle',
			type: 'blue',
			content: 'Are you sure to send back the selected samples?',
			columnClass: 'col-md-6 col-md-offset-3',
			buttons: {

				confirm: { 

					btnClass: 'btn-blue',
					action: function () {

						$("#abc").prop("hidden", false);
						var sendback_remark =$("#sendback_remark").val();
						var final_str1="";

						$.each($("input[name='checkboxArray[]']:checked"), function()
						{
							id=$(this).attr("id");
							final_str1 += id+"-";
						});

						$.ajax({

							type: "POST",
							url: '../Test/alloc_cancel',
							data:{final_str1:final_str1,sendback_remark:sendback_remark},
							beforeSend: function (xhr) { // Add this line
									xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
							},
							success: function (data) {

								var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
								resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

								var chemist_code = '';
								$.each(resArray, function (key, value){

									chemist_code += value+',';

								});

								$.alert({
									title: 'Success',
									type: 'green',
									columnClass: 'col-md-6 col-md-offset-3',
									content: "The Selected Samples With Code <b>"+chemist_code+"</b> are Sent Back to Reallocate!!!",
									onClose: function(){
										location.reload();
									}
								});
							}
						});
					}
				},
				
				cancel:{
					
					btnClass: 'btn-red',
					action: function () {
						location.reload();
						return false;
					}
				},
				
			}
		});
	});



});
