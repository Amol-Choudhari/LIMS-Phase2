	//call to login validations
	$('#ral').click(function (e) {

		if (sample_accept_validations() == false) {
			e.preventDefault();
		} else {
			$('#frm_sample_forward').submit();
		}
	});	
	
	function sample_accept_validations(){

		var acc_rej_flg=$("#acc_rej_flg").val();
		var dst_loc_id = $("#dst_loc_id").val();
		var dst_usr_cd = $("#dst_usr_cd").val();
		var actual_received_qty = $("#actual_received_qty").val();
		var acc_accepted_flag = $("#acc_accepted_flag").val();
		var value_return = 'true';
		
		if(acc_rej_flg==""){

			$("#error_acc_rej_flg").show().text("Please Enter Letter Reference Number.");
			$("#acc_rej_flg").addClass("is-invalid");
			$("#acc_rej_flg").click(function(){$("#error_acc_rej_flg").hide().text;$("#acc_rej_flg").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(dst_loc_id==""){

			$("#error_dst_loc_id").show().text("Please Enter Letter Reference Number.");
			$("#dst_loc_id").addClass("is-invalid");
			$("#dst_loc_id").click(function(){$("#error_dst_loc_id").hide().text;$("#dst_loc_id").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(dst_usr_cd==""){

			$("#error_dst_usr_cd").show().text("Please Enter Letter Reference Number.");
			$("#dst_usr_cd").addClass("is-invalid");
			$("#dst_usr_cd").click(function(){$("#error_dst_usr_cd").hide().text;$("#dst_usr_cd").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(actual_received_qty==""){

			$("#error_actual_received_qty").show().text("Please Enter Quanity.");
			$("#actual_received_qty").addClass("is-invalid");
			$("#actual_received_qty").click(function(){$("#error_actual_received_qty").hide().text;$("#actual_received_qty").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(acc_accepted_flag==""){

			$("#error_acc_accepted_flag").show().text("Please Enter Letter Reference Number.");
			$("#acc_accepted_flag").addClass("is-invalid");
			$("#acc_accepted_flag").click(function(){$("#error_acc_accepted_flag").hide().text;$("#acc_accepted_flag").removeClass("is-invalid");});
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
	
	$(document).ready(function(){
	
		//applied conditional selection by default to Duplicate analysis, if sample type is challenged
		//added on 27-10-2021 by Amol
		$('input[name="ral_cal"]').click(function(){

			if($("#sample_type").val()=='Challenged'){	
				$("#result_dupl_duplicate_flag").prop("checked", true);
			}else{
				$("#result_dupl_duplicate_flag").prop("checked", false);
			}
		});


		$('input[name="result_dupl_flag"]').change(function(){

			if($("#sample_type").val()=='Challenged'){
				
				if($("#result_dupl_single_flag").is(':checked')){
					
					alert("This is Challenged sample, so it must be duplicate analysis only");
					$("#result_dupl_duplicate_flag").prop("checked", true);
				}
			}
		});

		$('#abc').hide();
		$('#xyz').hide();
		document.getElementById('homgen').style.display = 'none';
		$('#forwarded_samples_list').DataTable();
		$('input[name="ral_cal"]').prop('disabled', true);
		$("#type").hide();

		$('input[name=ral_cal]').click(function() {

			var ral=$('input[name=ral_cal]:checked').val();

			$.ajax({
				type:"POST",
				url:"get_office",
				data:{ral:ral},
				async:true,
				cache:false,
				beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success : function(data){
					$("#dst_loc_id").empty();
					$("#dst_loc_id").append(data);
					$("#dst_usr_cd").empty();
					$("#dst_usr_cd").append("<option>-----Select-----</option>");
					$("#dst_loc_id").change();
				}
			});
		});


		$("#acc_accepted_flag").change(function(){

			if($("#stage_sample_code").val() == ''){

				alert('Please select Sample code first');
				$("#acc_accepted_flag").prop('checked', false);
				return;
			}
			
			select_homo();    
			$("#ral").prop("disabled", false);
			$("#abc").prop("disabled", true);
			$('#abc').hide();
			$('#xyz').show();
			$('#homgen').show();
			$("#acc_rej_flg_rejected1").prop('checked', false);

		});

		
		// the New Confirm code is added on 17-05-2022 by Akash
		$("#acc_rej_flg_rejected1").change(function(){
			
			$.confirm({
				title: 'alert',
				icon: "fas fa-exclamation-circle",
				type:'orange',
				content: 'Are you sure want to Reject this sample ?',
				buttons: {
					confirm: function () {
						$("#ral").prop("disabled", false);
						$("#abc").prop("disabled", false);
						$("#acc_accepted_flag").prop('checked', false);
						$('#abc').show();
						$('#xyz').hide();
						$('#homgen').hide();
					},
					cancel: function () {
						$("#ral").prop("disabled", true);
						$("#acc_rej_flg_rejected1").prop('checked', false);
						$("#acc_accepted_flag").prop('checked', false);
						$('#xyz').hide();
						$('#homgen').hide();
					}
				}
			});
		});

		$("#stage_sample_code").change();

	});

	
	$("#actual_received_qty").focusout(function() {

		var actualqty = parseFloat($("#actualqty").val());
		var qtyinput = parseFloat($("#actual_received_qty").val()); 
		
		if (qtyinput != '') {
			if(qtyinput > actualqty){
				$.alert("The Value You Have entered is Greater than the actual recieved value");
				$("#actual_received_qty").val('');
				return false;
			};
		}
		
	});

	$(".allow_decimal").on("input", function(evt) {
		var self = $(this);
		self.val(self.val().replace(/[^0-9\.]/g, ''));
		if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
		{
		  evt.preventDefault();
		}
	});
