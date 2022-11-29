$(document).ready(function(){


	//added for click preview check and show save button 11-07-2022
	$("#save").hide();
	$("#reportlink").click(function (e) {

		if($('#reportlink').is(':checked')){
			$("#save").show();
		}
		else{
			$("#save").hide();
		}

	  
	});



	$("#stage_sample_code").change();

	// To check sub grading value is checked or not
	$('#subgradelist').hide();
	$("#first").hide();
	var duplicate_flag="";


	$("#allGradeListChecked").change(function() {

		if($(this).prop('checked') == true) {

			$('#subgradelist').show()
			$('#grade_code').prop('disabled', true);
			$("#grade_code").val('');
		} else {
			$('#subgradelist').hide();
			$("#fullGradelist").val('');
			$('#grade_code').prop('disabled', false);
		}
	});


	 $("#save").click(function (e) {

		e.preventDefault();
		$("#array").val(' ');
		$("#array").val(arr);
		//alert('he');
		var category_code=$("#category_code").val();
		var commodity_code=$("#commodity_code").val();
		var sample_code=$("#sample_code").val();
		var grd_standrd = null;
		var remark=$("#remark").val();
		var remark_new=$("#remark_new").val();
		var tran_date=$("#tran_date").val();
		var result_flg= $('input[name=result_flg]:checked', '#frm_final_grading').val();
		var login_timestamp=$("#login_timestamp").val();
		var arraygrade = null;
		var grade_code = null;
		var subgrade = null;
		
		// To check sub grading value is checked or not ,
	
		if(commodity_code==''){
			var msg="Please Select Sample Commodity!!";
			alert(msg);
			return ;
		}
		else if(sample_code==''){
			var msg="Please Select Sample !!";
			alert(msg);
			return;
		}
		

		else if(remark==''){
			var msg="Remark by Inward Officer Required!!";
			alert(msg);
			return;
		}

		else if(remark_new==''){
			var msg="Please Enter Your Remark!!";
			alert(msg);
			return;
		}
		
		if(result_flg == 'R' || result_flg == 'N'){

		}else{
			var msg="please Check one of the action from given!!";
			alert(msg);
			return;
		}
	
		if(result_flg == 'R'){//if set for retest, dont ask for esign

			$("#button").val('add');
			var button=$("#button").val();

			// Add one new filed "subgrade" to add subgrading value in data array */
			$.ajax({
				type: "POST",
				url: 'grading_by_oic',
				data: {result_flg:result_flg,remark:remark,remark_new:remark_new,login_timestamp:login_timestamp,
					   button:button,grd_standrd:grd_standrd,category_code:category_code,commodity_code:commodity_code,
					   sample_code:sample_code,arraygrade:arraygrade,tran_date:tran_date,subgrade:subgrade,
					   grade_code:grade_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {

					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

					//To disaply the message, after save the grading by inward officer or sent to sample for retesting.
					if(resArray == 11 || resArray==2 ){
						window.location = 'ilc_finalized_zscore';

					}else if(resArray==0)
					{
						alert("Sample has been marked for retest and sent back to respective Inward Officer/Lab Incharge!!");
						window.location = 'ilc_finalized_zscore';

					}
					else
					{
						$.each(resArray, function (key, value) {
							alert(value);
							return false;
						});
					}

				}
			});

		}else{

			//to set some post values in seesion, to be used after redirecting from cdac
			$.ajax({
				type: "POST",
				url: 'set_post_sessions',
				async:false,
				data: {remark:remark,remark_new:remark_new,category_code:category_code,
						commodity_code:commodity_code,subgrade:subgrade,grade_code:grade_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					
					var res = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
					
					if(res == 1){
						
						//to esign and final grading of sample
						esign_consent_box();
						
					}
				}
			});


		}


	 });

});
