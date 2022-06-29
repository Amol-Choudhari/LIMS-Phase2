	$("#payment_receipt_document").change(function(){

		file_browse_onclick('payment_receipt_document');
		return false;
	});


	$("#submit_payment_detail").click(function(e){

		if(payment_fields_validation() == false){
			e.preventDefault();
		}else{
			$("#payment_modes").submit();
		}
	});


	var payment_confirmation_status = $("#payment_confirmation_status").val();

	$("#not_confirmed_reason").hide();

	if(payment_confirmation_status == 'payment_not_submit'){

		$("#payment_details").hide();
		$("#submit_payment_detail").hide();
		$('#bharatkosh_payment_done-yes').click(function(){

			$("#payment_details").show();
			$("#submit_payment_detail").show();
		});
		
		$('#bharatkosh_payment_done-no').click(function(){

			$("#payment_details").hide();
			$("#submit_payment_detail").hide();
		});
	}

	$(document).ready(function () {

		bsCustomFileInput.init();

		$('#payment_trasaction_date').datepicker({
			format: "dd/mm/yyyy",
			autoclose: true,
			endDate: new Date()
		});


		$('#payment_amount').focusout(function(){

			var input_payment_amount = $('#payment_amount').val();
			var actual_payment_amount = $('#actual_payment').val();
			
			if(input_payment_amount != actual_payment_amount)
			{
				$("#error_payment_amount").show().text("Please enter valid payment amount");
				$("#error_payment_amount").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				setTimeout(function(){ $("#error_payment_amount").fadeOut();},2000);
			}

		});

		//added new script with ajax below to check unique trsnsaction id.
		//on 14-10-2019 by Amol
		$('#payment_transaction_id').focusout(function(){

			var trans_id = $("#payment_transaction_id").val();
			
			$.ajax({
				type : 'POST',
				url : '../AjaxFunctions/check_unique_trans_id_for_appl',
				async : true,
				data : {trans_id:trans_id},
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success : function(response){

					response = response.match(/~([^']+)~/)[1];

					if($.trim(response)=='no'){

						$.alert({
							title: "Transaction ID error!",
						    content: 'The Transaction/Receipt Id "'+ trans_id  +'" is already used. Please verify and enter again.',
						    type: 'red',
						    typeAnimated: true,
						    buttons: {
						        tryAgain: {
						            text: 'Try again',
						            btnClass: 'btn-red',
						            action: function(){
						            	$("#payment_transaction_id").val('');
						            }
						        },
							}
						});
					}
				}
			});
		});
	
	
	});


	if($('#bharatkosh_payment_done-yes').is(":checked")){
		$("#submit_payment_detail").show();
	}
	
	if(payment_confirmation_status == 'replied'){
		$("#not_confirmed_reason").show();
		$("#submit_payment_detail").hide();
	}

	if(payment_confirmation_status == 'pending' || payment_confirmation_status == 'confirmed'){
		$("#submit_payment_detail").hide();
		$("#final_submit_btn").hide();
	}






	function payment_fields_validation(){

		var input_payment_amount = $('#payment_amount').val();
		var input_payment_transaction_id = $('#payment_transaction_id').val();
		var input_payment_trasaction_date = $('#payment_trasaction_date').val();
		var input_payment_receipt_document = $('#payment_receipt_document').val();
		var actual_payment_amount = $('#actual_payment').val();

		var value_return = 'true';

		var advancepayment = $('#advancepayment').val();	


		if(advancepayment == 'yes'){

			actual_payment_amount = input_payment_amount;
		}

		if($('input[name="bharatkosh_payment_done"]:checked').val() == 'yes'){

			if(input_payment_amount == '')
			{
				$("#error_payment_amount").show().text("Please enter valid payment amount");
				$("#error_payment_amount").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				$("#payment_amount").click(function(){$("#error_payment_amount").hide().text;});
				value_return = 'false';

			}else{

				if(input_payment_amount != actual_payment_amount)
				{
					$("#error_payment_amount").show().text("Please enter valid payment amount");
					$("#error_payment_amount").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					$("#payment_amount").click(function(){$("#error_payment_amount").hide().text;});
					value_return = 'false';
				}
			}

			if(input_payment_transaction_id == ''){

				$("#error_payment_transaction_id").show().text("Please enter payment transction id");
				$("#error_payment_transaction_id").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				$("#payment_transaction_id").click(function(){$("#error_payment_transaction_id").hide().text;});
				value_return = 'false';

			}

			if(input_payment_trasaction_date == ''){

				$("#error_payment_trasaction_date").show().text("Please select payment transction date");
				$("#error_payment_trasaction_date").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				$("#payment_trasaction_date").click(function(){$("#error_payment_trasaction_date").hide().text;});
				value_return = 'false';

			}
			if($('#payment_receipt_document_value').text() == ""){

				if(input_payment_receipt_document == ''){

					$("#error_payment_receipt_document").show().text("Please upload payment receipt");
					$("#error_payment_receipt_document").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					$("#payment_receipt_document").click(function(){$("#error_payment_receipt_document").hide().text;});
					value_return = 'false';

				}
			}
			if(value_return == 'false')
			{
				$.alert("Please check some fields are missing or not proper.");
				return false;
			}
			else{
				exit();

			}
		}
	}