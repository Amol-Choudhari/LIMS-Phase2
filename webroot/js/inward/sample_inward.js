//JS File for Sample Inward


	var acc_rej_flg = $("input[name='acc_rej_flg']:checked").val();

	if(acc_rej_flg=='A'){

		$("#acc_rej_flg").prop("checked", true);

	}else if(acc_rej_flg=='R') {

		$("#acc_rej_flg1").prop("checked", true);$("#rej_reason").attr("required",true);

	}else if(acc_rej_flg=='P') {

		$("#acc_rej_flg2").prop("checked", true);
	}


	// ADDED THIS BELOW CODE TO GRAY OUT THE SAMPLE TYPE SELECTION AFTER SAVE.
	var sample_type = $("#sample_type").val();
	if(sample_type != ''){
		$('#sample_type_code').attr("style", "pointer-events: none;").css("background-color", "lightgray");
	}


  	$(document).ready(function () {

		$('#expiry_year').datepicker({
			format: "yyyy",
			autoclose: true,
			minViewMode: 2
		});

		$('#letr_date').datepicker({
			endDate: '+0d',
			autoclose: true,
			todayHighlight: true,
			format: 'dd/mm/yyyy'
		});

		$('#received_date').datepicker({
			endDate: '+0d',
			autoclose: true,
			todayHighlight: true,
			format: 'dd/mm/yyyy'
		});

		$("#abc").prop("disabled", true);
		$("#abc").hide();

		// For Accept This new Type Confirm Message is applied on 09-05-2022 By Akash
		$('#acc_rej_flg').click(function(){

			$.confirm({
				title: 'confirm',
				content: 'Are you sure want to Accept this sample ?',
				icon: 'glyphicon glyphicon-ok-circle',
				type: 'green',
				buttons: {
					confirm: {
						btnClass: 'btn-success',
						action: function(){
							$("#abc").prop("disabled", true);
							$("#abc").hide();
						}
					},
					cancel: function () {
						$("#acc_rej_flg").prop('checked', false);
						$("#abc").hide();
					}
				}
			});

			$("#rej_reason").attr("required",false); //added on 30-04-2021 by Amol

		});


		//For Reject This new Type Confirm Message is applied on 09-05-2022 By Akash
		$('#acc_rej_flg1').click(function(){

			$.confirm({
				title: 'confirm',
				content: 'Are you sure want to Reject this sample ?',
				icon: 'glyphicon glyphicon-remove-circle',
				type: 'red',
				buttons: {
					confirm: {
						btnClass: 'btn-danger',
						action: function(){

							var date=new Date();
							var day = date.getDate();
							var month = date.getMonth()+1;
							var year = date.getFullYear();
							raj_date=year + '-' + month + '-' + day;

							$("#abc").prop("disabled", false);
							$("#reject_date").val(raj_date);
							$("#abc").show();
							$("#rej_reason").attr("required",true);//added on 30-04-2021 by Amol
						}
					},
					cancel: function () {
						$("#acc_rej_flg1").prop('checked', false);
						$("#abc").prop("disabled", true);
						$("#abc").hide();
					}
				}
			});
		});


		// For Pending This new Type Confirm Message is applied on 09-05-2022 By Akash
		$('#acc_rej_flg2').click(function(){

			$.confirm({
				title: 'confirm',
				content: 'Are you sure to put this sample in pending ?',
				icon: 'glyphicon glyphicon-info-sign',
				type: 'orange',
				buttons: {
					confirm: {
						btnClass: 'btn-warning',
						action: function(){
							$("#abc").prop("disabled", true);
							$("#abc").hide();
						}
					},
					cancel: function () {
						$("#acc_rej_flg2").prop('checked', false);
						$("#abc").hide();
					}
				}
			});

			$("#rej_reason").attr("required",false);//added on 30-04-2021 by Amol
		});


		if($('#acc_rej_flg1').is(':checked')){

			$("#abc").show();
		}

	});


	var org_sample_code = $("#org_sample_code").val();

	if (org_sample_code == '') {

		$(document).ready(function () {

			$('#letr_date').datepicker({
				endDate: '+0d',
				autoclose: true,
				todayHighlight: true,
				format: 'dd/mm/yyyy'
			}).on('changeDate', function (selected) {
				var minDate = new Date(selected.date.valueOf());
				$('#received_date').datepicker('setStartDate', minDate);
			});

			$('#received_date').datepicker({
				endDate: '+0d',
				autoclose: true,
				todayHighlight: true,
				format: 'dd/mm/yyyy'
			}).on('changeDate', function (selected) {
				var maxDate = new Date(selected.date.valueOf());
				$('#letr_date').datepicker('setEndDate', maxDate);
			});

			$('#letr_date').datepicker('setDate', 'today');
			$('#received_date').datepicker('setDate', 'today');

		});
	}


	//call to login validations
	$('#save').click(function (e) {

		if (sample_inward_form_validations() == false) {
			e.preventDefault();
		} else {
			$('#frm_sample_inward').submit();
		}
	});



	function sample_inward_form_validations(){

		var letr_ref_no=$("#letr_ref_no").val();
		var letr_date = $("#letr_date").val();
		var received_date = $("#received_date").val();
		var sample_type_code = $("#sample_type_code").val();
		var container_code = $("#container_code").val();
		var entry_flag = $("#entry_flag").val();
		var par_condition_code = $("#par_condition_code").val();
		var sam_condition_code = $('#sam_condition_code').val();
		var sample_total_qnt = $('#sample_total_qnt').val();
		var parcel_size = $('#parcel_size').val();
		var category_code = $('#category_code').val();
		var commodity_code = $('#commodity_code').val();
		var ref_src_code = $('#ref_src_code').val();
		var expiry_month = $('#expiry_month').val();
		var expiry_year = $('#expiry_year').val();
		var customer_name=$("#customer_name").val();
		var customer_email_id = $("#customer_email_id").val();
		var street_address = $("#street_address").val();
		var state = $("#state").val();
		var district = $("#district").val();
		var postal_code = $("#postal_code").val();
		var customer_mobile_no = $("#customer_mobile_no").val();

		var value_return = 'true';


		if(letr_ref_no==""){

			$("#error_letr_ref_no").show().text("Please Enter Letter Reference Number.");
			$("#letr_ref_no").addClass("is-invalid");
			$("#letr_ref_no").click(function(){$("#error_letr_ref_no").hide().text;$("#letr_ref_no").removeClass("is-invalid");});
			value_return = 'false';
		}


		if(letr_date==""){

			$("#error_letr_date").show().text("Please Enter Letter Date.");
			$("#letr_date").addClass("is-invalid");
			$("#letr_date").click(function(){$("#error_letr_date").hide().text;$("#letr_date").removeClass("is-invalid");});
			value_return = 'false';
		}


		if(received_date==""){

			$("#error_received_date").show().text("Please Enter Received Date.");
			$("#received_date").addClass("is-invalid");
			$("#received_date").click(function(){$("#error_received_date").hide().text;$("#received_date").removeClass("is-invalid");});
			value_return = 'false';
		}


		if(sample_type_code==""){

			$("#error_Sample_Type").show().text("Please Select Sample Type.");
			$("#sample_type_code").addClass("is-invalid");
			$("#sample_type_code").click(function(){$("#error_Sample_Type").hide().text;$("#sample_type_code").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(container_code==""){

			$("#error_container_code").show().text("Please Select Container Type.");
			$("#container_code").addClass("is-invalid");
			$("#container_code").click(function(){$("#error_container_code").hide().text;$("#container_code").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(entry_flag==""){

			$("#error_entry_flag").show().text("Please Select Physical Appearance.");
			$("#entry_flag").addClass("is-invalid");
			$("#entry_flag").click(function(){$("#error_entry_flag").hide().text;$("#entry_flag").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(par_condition_code==""){

			$("#error_parcel_condition").show().text("Please Select Package Condition.");
			$("#par_condition_code").addClass("is-invalid");
			$("#par_condition_code").click(function(){$("#error_parcel_condition").hide().text;$("#par_condition_code").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(sam_condition_code==""){

			$("#error_sample_condition").show().text("Please Select Sample Condition.");
			$("#sam_condition_code").addClass("is-invalid");
			$("#sam_condition_code").click(function(){$("#error_sample_condition").hide().text;$("#sam_condition_code").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(sample_total_qnt==""){

			$("#error_sample_total_qnt").show().text("Please Enter Quantity.");
			$("#sample_total_qnt").addClass("is-invalid");
			$("#sample_total_qnt").click(function(){$("#error_sample_total_qnt").hide().text;$("#sample_total_qnt").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(parcel_size==""){

			$("#error_parcel_size").show().text("Please Select Unit.");
			$("#parcel_size").addClass("is-invalid");
			$("#parcel_size").click(function(){$("#error_parcel_size").hide().text;$("#parcel_size").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(category_code==""){

			$("#error_category_code").show().text("Please Select Commodity Category.");
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

		if(ref_src_code==""){

			$("#error_ref_src_code").show().text("Please Enter Reference Source Code.");
			$("#ref_src_code").addClass("is-invalid");
			$("#ref_src_code").click(function(){$("#error_ref_src_code").hide().text;$("#ref_src_code").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(expiry_month==""){

			$("#error_expiry_month").show().text("Please Select Expiry Month.");
			$("#expiry_month").addClass("is-invalid");
			$("#expiry_month").click(function(){$("#error_expiry_month").hide().text;$("#expiry_month").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(expiry_year==""){

			$("#error_expiry_year").show().text("Please Select Expiry Year.");
			$("#expiry_year").addClass("is-invalid");
			$("#expiry_year").click(function(){$("#error_expiry_year").hide().text;$("#expiry_year").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(customer_name==""){

			$("#error_customer_name").show().text("Please Enter Customer Full Name.");
			$("#customer_name").addClass("is-invalid");
			$("#customer_name").click(function(){$("#error_customer_name").hide().text;$("#customer_name").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(customer_email_id==""){

			$("#error_customer_email_id").show().text("Please Enter Email.");
			$("#customer_email_id").addClass("is-invalid");
			$("#customer_email_id").click(function(){$("#error_customer_email_id").hide().text;$("#customer_email_id").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(street_address==""){

			$("#error_street_address").show().text("Please Enter Street Address.");
			$("#street_address").addClass("is-invalid");
			$("#street_address").click(function(){$("#error_street_address").hide().text;$("#street_address").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(state==""){

			$("#error_state").show().text("Please Select State.");
			$("#state").addClass("is-invalid");
			$("#state").click(function(){$("#error_state").hide().text;$("#state").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(district==""){

			$("#error_district").show().text("Please Select District.");
			$("#district").addClass("is-invalid");
			$("#district").click(function(){$("#error_district").hide().text;$("#district").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(postal_code==""){

			$("#error_postal_code").show().text("Please Enter Postal Code.");
			$("#postal_code").addClass("is-invalid");
			$("#postal_code").click(function(){$("#error_postal_code").hide().text;$("#postal_code").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(customer_mobile_no==""){

			$("#error_customer_mobile_no").show().text("Please Enter Customer Mobile Number.");
			$("#customer_mobile_no").addClass("is-invalid");
			$("#customer_mobile_no").click(function(){$("#error_customer_mobile_no").hide().text;$("#customer_mobile_no").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(value_return == 'false'){

			var msg = "Please check some fields are missing or not proper.";
			renderToast('error', msg);
			return false;

		}else{
			exit();
		}
	}


	/// For Comercial Type Sample ///

	$('#sample_type_code').change(function (e) {
		//e.preventDefault();
		var sample_type_code = $('#sample_type_code').val();

		if(sample_type_code == '3'){
			
			$.confirm({
				title: 'Commercial Sample',
				content:"<u>Please Note :</u> As you have selected <b>Commercial</b> Sample Type. <br> You need to submit the payment details in the <i><b>Payment Section</b></i> which will available after saving the <i><b>Inward Section</b></i>.<br>",
				icon: 'glyphicon glyphicon-info-sign',
				type: 'info',
				columnClass: 'm',
				buttons: {
					OK: {
						btnClass: 'btn-info',
						action: function(){
						
						}
					},
					cancel: function () {
						$("#sample_type_code option:eq(0)").prop("selected", true);
					}
				}
			});

		}
	});


	//this is for the commecial flow to check the commodity charges are filled or not.
	$('#commodity_code').change(function (e) {

		var sample_type_code = $('#sample_type_code').val();
		if (sample_type_code != "") {
			if (sample_type_code == '3') {
				if(checkCommodityUsed()==false){
					e.preventDefault();
				}	
			}
		}
	});


	$('#sample_type_code').change(function (e) {
		
		var sample_type_code = $('#sample_type_code').val();
		if (sample_type_code != "") {
			if (sample_type_code == '3') {
				if(checkCommodityUsed()==false){
					e.preventDefault();
				}	
			}
		}
	});

	
	function checkCommodityUsed(){

		var commodity = $("#commodity_code").val();
		var commodityName = $("#commodity_code option:selected").text();

		if (commodity !='') {
			
			$.ajax({
				type: "POST",
				async:true,
				url:"../AjaxFunctions/check_if_commodity_added",
				data: {commodity:commodity},
				beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					if (data == 'no') {

						$.confirm({
							title:'Commercial Charges',
							content: 'There is no <i>charges</i> available for the <b>'+ commodityName +'</b> commodity. <br/> Please use the <i>Commercial Charges</i> Master to add the charges for commodity. </br> <b>OR</b> Contact the Administrator.',
							icon: 'glyphicon glyphicon-info-sign',
							type: 'info',
							columnClass: 'm',
							buttons: {
								OK: {
									btnClass: 'btn-info',
									action: function(){
									}
								}
							}
						});
					}
				}
			});
		}
	}


	$("#state").change(function(){
		get_district();
	});


	//function to district
	function get_district(){

		$("#district").find('option').remove();
		var state = $("#state").val();	

		$.ajax({
			
			type: "POST",
			async:true,
			url:"../AjaxFunctions/show-district-dropdown",
			data: {state:state},
			beforeSend: function (xhr) { // Add this line
				xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			}, 
			success: function (data) {
				$("#district").append(data);
			}
		});
	}
