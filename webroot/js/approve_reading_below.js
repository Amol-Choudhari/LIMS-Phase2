
$(document).ready(function(){
	
	
//Below lines are added on 26/12/19 by AkashT for save,and view result, close the div----------->
	$('#save').hide();           
	$("#menubuttons").hide();

	$("#stage_sample_code").change();
	view1();

		$("#ral").click(function (e) {
			var sample_code = $("#sample_code").val();
			var tran_date = $("#tran_date").val();
			if (sample_code != "")
			{
				$.ajax({
					type: "POST",
					url: 'forward_ral',
					data: {sample_code: sample_code,tran_date:tran_date},
					beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function (data) {
						var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
						
						if(resArray.indexOf('[error]') !== -1){
							var msg =resArray.split('~');
							alert(msg[1]); 
							$("#sample_code").val('');
								return;
						}else{
							
							resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
							
							$.each(resArray, function (key, value){

								alert("The finalized result has been sent to RAL,Inward Officer for verification !!!");
								$("#ral").attr("disabled", true);
								window.location = 'available_for_approve_reading';
							});
						}
					}
				});
			}
		});
		
		$("#cal").click(function (e) {
			var sample_code = $("#sample_code").val();
			var tran_date = $("#tran_date").val();
			if (sample_code != "")
			{
				$.ajax({
					type: "POST",
					url: 'forward_ral',
					data: {sample_code: sample_code,tran_date:tran_date},
					beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function (data) {
						
						var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
						
						$.each(resArray, function (key, value){
								
							alert("The finalized result has been sent to CAL,Nagpur for verification !!!");
							$("#cal").attr("disabled", true);
							window.location = 'available_for_approve_reading';
						});
					}
				});
			}
		});
		
		$("#ho").click(function (e) {
			var sample_code = $("#sample_code").val();
			var tran_date = $("#tran_date").val();
			if (sample_code != "")
			{
				$.ajax({
					type: "POST",
					url: 'forward_ral',
					data: {sample_code: sample_code,tran_date:tran_date},
					beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function (data) {
						
						var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
						
						$.each(resArray, function (key, value){
						
							var msg="The finalized result has been sent to ho,"+value['ro_office']+" for verification !!!";
							alert(msg);
							$("#ho").attr("disabled", true); 
							//location.reload();
							window.location = 'available_for_approve_reading';
							return; 
						});
					}
				});
			}
		});
		
		$("#oic").click(function (e) {
			var sample_code = $("#sample_code").val();
			var tran_date = $("#tran_date").val();
			if (sample_code != "")
			{
				$.ajax({
					type: "POST",
					url: 'forward_oic',
					data: {sample_code: sample_code,tran_date:tran_date},
					beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function (data) {
						
						var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
						
						$.each(resArray, function (key, value){
							
							<?php if($_SESSION['user_flag']=='RAL'){ ?>
								
								alert("The finalized result has been sent to RAL,Offce Incharge for verification !!!");
								$("#ral").attr("disabled", true);
								//window.location = '';
								window.location = 'ApproveReading/available_for_approve_reading';
							
							<?php } elseif($_SESSION['user_flag']=='CAL'){ ?>
								
								alert("The finalized result has been sent to CAL,"+value['ro_office']+" for verification !!!");
								$("#cal").attr("disabled", true);
								//window.location = '';
									window.location = 'ApproveReading/available_for_approve_reading';
							
							<?php	} ?>
						});
					}
				});
			}
		});
	 
		$("#finalize").click(function (e) {
			
			var sample_code = $("#sample_code").val();
			var tran_date = $("#tran_date").val();
										
			$.ajax({
				type: "POST",
				url: 'checkForIsFinalize',
				data: {sample_code: sample_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
						
					if(resArray.indexOf('[error]') !== -1){
							var msg =resArray.split('~');
							alert(msg[1]); 
							return;
					}else{
					
						if(resArray.indexOf('Exists') !== -1){
						
							//$("#finalize").attr("disabled", true);
							$("#finalize").hide();
							//$("#finalise_id").hide();
							$("#dupilcate").attr("disabled", true);
							$("#dupilcate").hide();
							$("#duplicate_id").hide();
							<?php if($_SESSION['user_flag']=='RAL' && $_SESSION['role']=='Inward Officer'){ ?>
				
								$("#cal_id").hide();
								$("#ral_id").show();
								$("#ral").show();
							 
								$("#oic_id").show();
								$("#oic").show();
								$("#ral").attr("disabled", false);
								$("#cal").attr("disabled", true);
								$("#ho").attr("disabled", true);
								$("#oic").attr("disabled", false);
								
							<?php } elseif($_SESSION['user_flag']=='RAL' && $_SESSION['role']=='Lab Incharge') { ?>
							
								$("#cal_id").show();
								$("#ral_id").hide();
								$("#cal").show();
								$("#ral").text("Send to Inward Officer");
								$("#oic_id").hide();
								$("#oic").show();
								$("#ral").attr("disabled", true);
								$("#cal").attr("disabled", false);
								$("#ho").attr("disabled", true);
												 
							<?php }elseif($_SESSION['user_flag']=='CAL' && $_SESSION['role']=='Lab Incharge'){ ?>
							
								$("#cal_id").show();
								$("#ral_id").hide();
								$("#cal").show();
								$("#ral").text("Send to Inward Officer");
								$("#oic_id").hide();
								$("#oic").show();
								$("#ral").attr("disabled", true);
								$("#cal").attr("disabled", false);
								$("#ho").attr("disabled", true);					
								
							<?php }elseif($_SESSION['user_flag']=='CAL' && $_SESSION['role']=='Inward Officer'){ ?>
							
								$("#cal_id").show();
								$("#cal").show();
								$("#ral_id").hide();
								$("#oic_id").show();
								$("#oic").show();
								$("#cal").attr("disabled", false);
								$("#ho").attr("disabled", true);
								$("#ral").attr("disabled", true);
								$("#oic").attr("disabled", false);
							<?php	} ?>
							return;
						
						}else{ 
						
							if (sample_code != "")
							{ 
								var posted_ro_office=$("#posted_ro_office").val();
								var user_flag=$("#user_flag").val();
								var user_code=$("#user_code").val();
								
								$.ajax({
									
									type: "POST",
									url: 'finalized_sample',
									data: {sample_code: sample_code,posted_ro_office:posted_ro_office,user_flag:user_flag,user_code:user_code,tran_date:tran_date},
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
									success: function (data) { 
									
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
										
										if(resArray.indexOf('[error]') !== -1){
											var msgArr =resArray.split('~');
											var msg=msgArr[1];
											alert(msg);
											return;
											
										}else{
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function (key, value){
												
												$("#finalize").hide();
												
												var msg="All test for Sample "+value['sample_code']+" has been Finalized  !!!";
												alert(msg);										
												//$("#finalize").attr("disabled", true); 
												
												<?php if($_SESSION['user_flag']=='RAL'){ ?>
												
													$("#cal_id").hide();
													$("#ral").show();
													$("#ral_id").show();
													$("#oic_id").show();
													$("#oic").show();
													$("#ral").attr("disabled", false);
													$("#cal").attr("disabled", true);
													$("#ho").attr("disabled", true);
													$("#oic").attr("disabled", false);
													
												<?php }elseif($_SESSION['user_flag']=='CAL'){ ?>
											
													$("#cal_id").show();
													$("#ral_id").hide();
													$("#oic").show();
													$("#cal").show();
													$("#oic_id").show();
													$("#cal").attr("disabled", false);
													$("#ho").attr("disabled", true);
													$("#ral").attr("disabled", true);
													$("#oic").attr("disabled", false);
													
												<?php	}else{ ?>
												
													$("#ho_id").show();
													$("#ral_id").hide();
													$("#cal_id").hide();
													$("#oic_id").show();
													$("#cal").attr("disabled", false);
													$("#ho").attr("disabled", false);
													$("#ral").attr("disabled", true);
													$("#oic").attr("disabled", false);
													
												<?php } ?>
												return; 
											});										
										}
									}
								});
							}
						}
					}
				}
			});		   
		});


	//--Added/Adjusted below click events [final,average&retest] on 26/12/19 by AkashT. to save the approved test.-->
	 
		$("#final").click(function (e) {
					
					  var test_code = glob_test_code;
					  var test_name2 = glob_test_name;
					  var result1 = glob_test_result;
					  $("#test_code").val(test_code);
						$("#final_result").val(result1);
						$("#chemist_code").val(glob_chemist_code);		
						$("#final").css("background-color", "");
						$("#re_test").css("background-color", "#006400");	
						$('#save').trigger('click');
					  $("#re_test").attr("disabled", true);
						$("#final").attr("disabled", true);
					   $("#avrage").attr("disabled", true);
		});


		$("#avrage").click(function (e) { 
		
						var test_code = glob_test_code;			
						$("#avrage").css("background-color", "#006400");
						$("#test_code").val(test_code);
						$("#final_result").val(avg);
						rowidarr=[];
						$('#save').trigger('click');
					  $("#re_test").attr("disabled", true);
						$("#final").attr("disabled", true);
					   $("#avrage").attr("disabled", true);
		});

		
		$("#re_test").click(function (e) {
			
						var test_code = glob_test_code;
						var test_name2 = glob_test_name;
						var result1 = glob_test_result;	
						$("#test_code").val(test_code);
						$("#final_result").val(result1);			
						$("#final").css("background-color", "#9fc66f");
						$("#re_test").css("background-color", "");
						retesting();
		});
	
	
		$("#save").click(function (e) {
				
			e.preventDefault();
			$("#button").val('add');	
			var button			 =   $("#button").val();			
			var test_code		 =   $("#test_code").val();
			var final_result	 =   $("#final_result").val();
			var category_code	 =   $("#category_code").val();  
			var commodity_code	 =   $("#commodity_code").val();
			var sample_code		 =   $("#sample_code").val();
			var chemist_code	 =   $("#chemist_code").val();
			var login_timestamp	 =   $("#login_timestamp").val();
			
			if(duplicate_flag=="D"){var duplicate_record="D";}
			
			var tran_date	= $("#tran_date").val();
			$.ajax({
				type: "POST",
				url: 'approve_reading',
				data: {duplicate_flg:duplicate_record,login_timestamp:login_timestamp,chemist_code:chemist_code,button:button,test_code:test_code,final_result:final_result,category_code:category_code,commodity_code:commodity_code,sample_code:sample_code,tran_date:tran_date},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
				
					if(resArray.indexOf('[error]') !== -1){
						
						var msg =resArray.split('~');
						alert(msg[1]); 
						return;
						
					}else{
						
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
						
						$.each(resArray, function (key, value){
							
							var msg	= "Test result for "+value['test_name']+" is approved!!";
							alert(msg);
						
							view1();
							$.ajax({
									type: "POST",
									url: 'get_finalise_flag',
									data: {sample_code: sample_code},
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
									success: function (data) {
									
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
										
										if(resArray.indexOf('[error]') !== -1){
											var msgArr = resArray.split("~");
											var msg=msgArr[1];
											alert(msg);
											return;
										}else{
											if(resArray==0){											
												$("#ral").attr("disabled", false); 
												$("#oic").attr("disabled", false); 
												$("#re_test").attr("disabled", true);
												$("#final").attr("disabled", true);
												$("#avrage").attr("disabled", true);
											}
										}
									}
								});
							return;
							
						});
					}
					
				}
			});
		});
		

});
