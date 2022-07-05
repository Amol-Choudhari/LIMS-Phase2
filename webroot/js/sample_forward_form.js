// SAMPLE FORWARD FORM JS//

	//$('#stage_sample_code').select2();
	//$('#reject_box_field').hide();
	//$('#sample_reject').hide();
	$('#stage_sample_code_s').multiselect();

	//this is common used function to get sample details with category,commoditytype etc
	//when sample code is selected from dropdown.
	function getsampledetails(){

		$("#category_code").find('option').remove();
		$("#commodity_code").find('option').remove();
		$("#sample_type").find('option').remove();

		var sample_code=$("#stage_sample_code").val();
			
		if(sample_code != ''){

			$.ajax({
				type: "POST",
				url: '../AjaxFunctions/get_sample_cat_comm_type_details',
				async:false,
				data: {sample_code:sample_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {

					$('#acc_rej_flg').prop('disabled', false);
					$('#acc_rej_flg1').prop('disabled', false);
					$('#acc_rej_flg2').prop('disabled', false);
					$('#acc_rej_flg').prop('checked', false);
					$('#acc_rej_flg1').prop('checked', false);
					$('#acc_rej_flg2').prop('checked', false);
					$("#dst_loc_id").empty();
					$("#dst_usr_cd").empty();

					var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

					$.each(resArray, function (key, value) {

						$("#sample_type").append("<option value='" + $.trim(value['sample_type_code']) + "'>" + $.trim(value['sample_type_desc']) + "</option>");
						$("#commodity_code").append("<option value='" + $.trim(value['commodity_code']) + "'>" + $.trim(value['commodity_name']) + "</option>");
						$("#category_code").append("<option value='" + $.trim(value['category_code']) + "'>" + $.trim(value['category_name']) + "</option>");

					});
				}
			});

		}else{

			$('#acc_rej_flg').prop('disabled', true);
			$('#acc_rej_flg1').prop('disabled', true);
			$('#acc_rej_flg2').prop('disabled', true);

			$('#acc_rej_flg').prop('checked', false);
			$('#acc_rej_flg1').prop('checked', false);
			$('#acc_rej_flg2').prop('checked', false);

			$("#dst_loc_id").empty();
			$("#dst_usr_cd").empty();
		}
	
	}


	//to get the user name according to the office type and designation selected
	//commonly used on multiple windows
	function get_user_name(){

		$("#dst_usr_cd").find('option').remove();
		var dst_loc_id=$("#dst_loc_id").val();
		var user_flag = $('input[name=ral_cal]:checked').val();

		$.ajax({

			type:"POST",
			url:"get_user",
			data:{dst_loc_id:dst_loc_id,user_flag:user_flag},
			cache:false,
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success : function(data){

				var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
				resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON


				if($.trim(resArray)=='[error]'){

					var msg="Incorrect Destination location";
					$("#dst_loc_id").val('');
					alert(msg);
					return false;

				}else{

					if(resArray==0){

						$.alert({
							title: 'alert',
							type: 'red',
							icon: "fas fa-exclamation-circle",
							content: 'Inward Officer not present at this location',
							onClose: function(){
								location.reload();
							}
						});

						$("#ral").prop("disabled", true);
					
					}else{

						$("#ral").prop("disabled", false);
						var loc=$("#dst_loc_id option:selected").text();

						$("#dst_usr_cd").append("<option value='0'>---Select---</option>");//line added  for ilc flow on 07-06-2022 by Shreeya
						$.each(resArray, function (key, value) {
							$("#dst_usr_cd").append("<option value="+value['id']+">"+value['f_name']+' '+value['l_name']+"</option>")	;
						});
					}
				}
			}
		});
	
	}

	//for sample accept window
	//to fetch and load homo. and observ. methods for selected sample according to the commodity
	function select_homo(){

		var category_code=$("#category_code").val();
		var commodity_code=$("#commodity_code").val();
		var sample_code=$("#inward_id").val();

		var cnt	= 0;

		$.ajax({//this ajax called creates select dropdown input fields
			type: "POST",
			url:"get_commodity_obs",
			async:false,
			data: {category_code: category_code,commodity_code:commodity_code},
			beforeSend: function (xhr) { // Add this line
				xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {

				var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
				resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

				// the New Confirm code is added on 17-05-2022 by Akash
				if(resArray.length==0){
						
					$.confirm({
						title: 'alert',
						icon: "fas fa-exclamation-circle",
						type: 'red',
						content: 'Homogenization are pending !',
						buttons: {
							OKAY: function () {
								location.reload();
							},
							cancel: function () {
								location.reload();
								return;
							}
						}
					});
				}

				if(resArray.indexOf('[error]') !== -1){

					var msg = resArray.split("~");
					alert(msg[1]);

					$("#inward_id").val('');
					$("#category_code").val('');
					$("#commodity_code").val('');
					$("#dst_loc_id").val('');
					$("#stage_sample_code").val('');
					$("#sample_type").val('');
					$("#dst_usr_cd").val('');
					$("#acc_rej_flg").val('');
					return false;

				}else{

					$('#homgen1').empty();
					$.each(resArray, function(key, value){

						$('#homgen1').append('<div class="col-md-7"><label>'+value['m_sample_obs_desc']+'</label></div><div class="col-md-5"><select required class="form-control" id="general_obs_code'+value['m_sample_obs_code']+'" name="general_obs_code'+cnt+'"  ><option hidden="hidden" value="">-----Select-----</option></select></div>');

						$.ajax({//this ajax call load options in the created select input fields.
							type: "POST",
							url:"get_commodity_obs1",
							async:false,
							data: {m_sample_obs_code: value['m_sample_obs_code']},
							beforeSend: function (xhr) { // Add this line
									xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
							},
							success: function (data1) {

								var resArray1 = data1.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
								resArray1 = JSON.parse(resArray1);//response is JSOn encoded to parse JSON

								$.each(resArray1, function(key1, value1){
									if(value['m_sample_obs_code']==value1['m_sample_obs_code']){

										$('#general_obs_code'+value['m_sample_obs_code']+'').append('<option value='+value1['m_sample_obs_type_code']+'~'+value1['m_sample_obs_code']+'>'+value1['m_sample_obs_type_value']+'</option>');
									}
								});

								//to be execute when already accepted samples open in view mode with values.
								//to fetch already saved homo. & oberv. values.
								if(sample_code){
									$('#homCnt').val(cnt);
									var a=$('#homCnt').val();
									for (i=0;i<=a;i++){
										$("select[name=general_obs_code"+i+"]").prop("disabled", true);
									}
									var dist_code='sample_code=' + sample_code;

									$.ajax({
										type:"POST",
										url:"get_commodity_rgs",
										async:false,
										data:dist_code,
										cache:false,
										beforeSend: function (xhr) { // Add this line
												xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
										},
										success : function(data3){

											var resArray3 = data3.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
											resArray3 = JSON.parse(resArray3);//response is JSOn encoded to parse JSON

											$.each(resArray3, function(key, value){
												$('#general_obs_code'+value['m_sample_obs_code']+'').val(value['m_sample_obs_type_code']+'~'+value['m_sample_obs_code']);
											});
										}
									});
								}
							}
						});
						
						cnt++;

						$('#homgen1').append('</div></div>');
						$('#homCnt').val(cnt);
					});
				}
			}
		});
	}



	//$('#stage_sample_code').select2();
	$(document).ready(function(){

		$("#stage_sample_code").click(function(e){

			if(getsampledetails()==false){
				e.preventDefault();
			}

		});

		$("#stage_sample_code").change(function(e){

			if(getsampledetails()==false){
				e.preventDefault();
			}

		});


		$("#dst_loc_id").change(function(e){//update event from 'click' to 'change' on 18-05-2022 by Amol

			if(get_user_name()==false){
				e.preventDefault();
			}

		});

		$('input[name="ral_cal"]').prop('disabled', true);
		$('#stage_sample_code').change();//default to load details
		$('#forwarded_samples_list').DataTable();
		$('#rejected_samples_list').DataTable();
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
					
					//commented on 18-05-2022 as on change applied separatly above by Amol
					//$("#dst_loc_id").change();
				}
			});
		});

	});
