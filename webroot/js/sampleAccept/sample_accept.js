	
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

	var actualqty = $("#actualqty").val().trim();

	$("#actual_received_qty").focusout(function() {
		var qtyinput = $("#actual_received_qty").val().trim(); 
		if (qtyinput != '') {
			if(qtyinput > actualqty){
				$.alert("The Value You Have entered is Greater than the actual recieved value");
				$("#actual_received_qty").val('');
				return false;
			};
		}
		
	});

// enable save button after keyup of inputfield, done by pravin bhakare,11-12-2019
//function disable_save_btn(){
//	$("#ral").prop("disabled", false);
//}
