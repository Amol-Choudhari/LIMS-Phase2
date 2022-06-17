//on page load
$("#data_table_div").prop("hidden", true);
$("#abc").prop("hidden", true);
var commodity_code;
document.cookie = "type=add";
initMenu();
$('#avb').hide();
$('#commo').hide();
$("#delete_div").hide();
$("#update_div").hide();

//when sample code is selected from dropdown.
function getsampledetails(){
			
	$("#category_code").find('option').remove();
	$("#commodity_code").find('option').remove();
	$("#sample_type").find('option').remove();
	
	$("#grd_standrd").val('');
	$("#remark").val('');
	$(".fsStyle1").hide();
	
			
	var sample_code=$("#stage_sample_code").val();
	if(sample_code != ''){
		$.ajax({
			type: "POST",
			url: '../AjaxFunctions/get_sample_cat_comm_type_details',
			data: {sample_code:sample_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {
					
					var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
	
					if(resArray != 'null'){
						
						$.each(resArray, function (key, value) {
							
							$("#sample_type").append("<option value='" + $.trim(value['sample_type_code']) + "'>" + $.trim(value['sample_type_desc']) + "</option>");
							$("#commodity_code").append("<option value='" + $.trim(value['commodity_code']) + "'>" + $.trim(value['commodity_name']) + "</option>");
							$("#category_code").append("<option value='" + $.trim(value['category_code']) + "'>" + $.trim(value['category_name']) + "</option>");

							getData();
							
							$("#test_list").css("display", "block");
						});
					
					}else{
						
						$("#test_list").css("display", "none");
						$("#sample_alloc").css("display", "none");
						$("#sample_acc").css("display", "none");
						$("#expect_cmpl").css("display", "none");
						$("#method_error_message").css("display", "none");
					}
			}
		});
	}
}




function getData(){	

	$("#data_table_div").prop("hidden", false);
	$("#test_parameter").find('li').remove();
	$("#test_select").find('option').remove();
	$("#test_table tbody").find('tr').remove();
	$('#sample_alloc').text(""); 
	$('#sample_acc').text("");
	$('#expect_cmpl').text("");		
	$('#method_error_message').text(""); 
	var sample_code = $("#sample_code").val();
	var user_code = $("#user_code").val();
	
	document.cookie = "sample=" + sample_code;
	if (sample_code != "-----Select-----") {
		
		$.ajax({
			type: "POST",
			url: '../Test/get_commodity',
			data: {sample_code: sample_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function(data) 
			{
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
				
				if(resArray.indexOf('[error]') !== -1){
					var msg="You have passed incorrect values!";
					alert(msg);
				
				}else{

					if(resArray!='')
					{
						var data1 = resArray.split("~");
						$('#commo').show();
						$("#commodity_code").val(data1[1]);
						commodity_code = data1[1];
					}
				}
			}
		});
		
		$.ajax({
			
			type: "POST",
			url: '../Test/get_alloc_date',
			data:{sample_code: sample_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function(data) 
			{
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
			
				if(resArray.indexOf('[error]') !== -1){
					var msg="You have passed incorrect values!";
					alert(msg);
				
				}else{
					
					var isFirst	= 1;
					
					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
					
					$.each(resArray, function(key, value) {
						
						if(isFirst){
							
							if(value[0]['login_timestamp']!=null){

								var sample_allocated_date = value[0]['login_timestamp'].split(" ");
				
								/*update sample_allocated_date, */
								var alloc_date1 = sample_allocated_date[0];	 
							
							}else{
								alloc_date1	='';
							}
							
							$('#sample_alloc').append("Sample allocated on " + alloc_date1 +"<br>");
							
							if(value[0]['recby_ch_date']!=null){
								
								var sample_acc = new Date(value[0]['recby_ch_date']);
								var d2=new Date(value[0]['recby_ch_date'].split("/").reverse().join("-"));
								 
								var expect_cmpl = new Date(value[0]['expect_complt']);
								var d3=new Date(value[0]['expect_complt'].split("/").reverse().join("-"));
								
								var sample_acc1 = d2.getDate() + '/' + (d2.getMonth() + 1) + '/' + d2.getFullYear();
								var expect_cmpl1 = d3.getDate() + '/' + (d3.getMonth() + 1) + '/' + d3.getFullYear();
							
							}else{
								
								sample_acc1	='';
								$('#sample_acc').append("Sample Accepted on " + sample_acc1 +"<br>");
								
								expect_cmpl1	= '';
								$('#expect_cmpl').append("Expected on " + expect_cmpl1 +"<br>");
							}
							
						}
						isFirst	= 0;
						
					
					}); 
					//}

				}
											  
			}
		});
		
		
		// Updated get sample data logic,
		var commodity_code_value = $("#commodity_code").val();										  
		
		$.ajax({
			
			type: "POST",
			url: '../Test/get_sample_data',
			data:{sample_code: sample_code,user_code:user_code,commodity_code:commodity_code_value},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function(data) {
				
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
				resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
			
				var i = 1;
				var method_undefined = 'no';
				
				$.each(resArray, function(key, value) {
					
					if(value['method_name'] == 'Undefined' && method_undefined == 'no'){
						
						method_undefined = 'yes';
					}
					if(value['test_result'] == null){
						$("#test_table tbody").append("<tr id='tr_" + value['test_code'] + "'onclick='get_parameter(" + value['test_code'] + ")' style='background-color:#ffba66'><td>" + i + "</td><td>" + value['test_name'] + "</td><td>"+value['method_name']+"</td><td>" + value['test_result'] + "</td><td>" + value['test_unit'] + "</td></tr>");
					
					}else{
						$("#test_table tbody").append("<tr id='tr_" + value['test_code'] + "'onclick='get_parameter(" + value['test_code'] + ")' style='background-color:#00b04f'><td>" + i + "</td><td>" + value['test_name'] + "</td><td>"+value['method_name']+"</td><td>" + value['test_result'] + "</td><td>" + value['test_unit'] + "</td></tr>");
					
					}
					
					if (value == null)
					{
						value = "-";
					}

					i = i + 1;
				});
				
				if(method_undefined == 'yes'){
						
					$('#method_error_message').append("One or more tests are there whose method name is undefined. Please inform the administrator.");
				}
				
				
				$("#test_table").show();
				$('#color_mark').show();
				$('#finalize_div').show();
				$("#finalize_div").height($(".table-responsive").height());						
			}
		});
		
		var sample_code = $("#sample_code").val();
		
		$.ajax({
			
			type: "POST",
			url: '../Test/get_incomplete_test',
			data: {sample_code: sample_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function(data) {
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
				
				if(resArray.indexOf('[error]') !== -1){
					var msg="You have passed incorrect values!";
					alert(msg);
				
				}else{
					
					if (resArray == '1') {
						$("#finalize").prop("disabled", true);
					} else {		
						$("#finalize").prop("disabled", false);
						$("#finalize").removeAttr("title");
						$("#finalize").show();
					}
				}
			}
		});
		
		$.ajax({
			
			type: "POST",
			url: '../Test/get_test_by_commodity_id',
			data: {sample_code: sample_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function(data) {
				
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
				resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

				$.each(resArray, function(key, value) {

					$("#test_select").append("<option value='" + key + "'>" + value + "</option>");
				});
			}
		});
	
	} else {
		
		var msg="select sample code";
		alert(msg);
	  
	}
}
		
		
		

function get_parameter(test_code) {
		
	$("#input_parameter_text").empty();
	$("#res_div").hide();
	$('#test_table').hide();
	$('#color_mark').hide();
	$('#finalize_div').hide();
	
	var test_select = test_code;
	var field;
	var  flag1 = false;
	var  str1 = "";
	
	$('#test_table').on('click', 'tr', function() {
		var td = this.cells[1]; // the first <td>
		var test_select1 = $(td).text();
		
		$("#test_title").html("Test reading for<b> "+test_select1+"</b>");
	});
	
	var sample = $("#sample_code").val();
	var sample_code = $("#sample_code").val();
	var user_code = $("#user_code").val();
	$("#sample").val(sample);
	$("#test_v").val(test_code);
	$("#modal_type").val("");
	$("#abc").prop("hidden", true);
	
	document.cookie = "sample=" + sample_code;
	document.cookie = "select_sample_code=" + sample_code;	
	
        if (test_select != "") {
			
            $.ajax({
				type: "POST",
				url: '../Test/get_test_type',
				data: {test_select: test_select},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function(data) {
					
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
						
					if(resArray.indexOf('[error]') !== -1){
						var msg="You have passed incorrect values!";
						alert(msg);
					}else{
						
                        $.each(resArray, function(key, value) {
							
                            if (key == "formula" || key == "Formula") {
								
                                $("#input_parameter_text").empty();
                                $("#modal_type").val("f");
								document.cookie = "sample=" + sample_code;
								
								$.ajax({
									
									type: "POST",
									url: '../Test/get_test_formulae1',
									data: {test_select: test_select},
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
									success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
										
										if(resArray.indexOf('[error]') !== -1){
											
											var msg="You have passed incorrect values!";
											alert(msg);
										
										}else{
											
											$.each(resArray, function(key2, value2) {
											
											  $("#formula").val(key2);							  
											});
										}
									}
								});
								
								document.cookie = "test_type=Formula";
								document.cookie = "sample=" + sample_code;
								
                                $("#calculate").show();
								
								$.ajax({
									
									type: "POST",
									url: '../Test/get_dependent_test',
									data: { test_select: test_select },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
									success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response										
										
										if(resArray.indexOf('[error]') !== -1){
											
											var msg="You have passed incorrect values!";
											alert(msg);
										
										}else{
											
											var i=1;
											var test_name;
											var field_value;
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function(key1, val1) {
												
												field_value=key1;
												test_name=val1;
												
												$.ajax({
													
													type: "POST",
													url: '../Test/get_test_by_name',
													data: {
														test_name: test_name
													},
													beforeSend: function (xhr) { // Add this line
															xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
													},
													success: function(data) {
														
														var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
														var test_c = resArray;
														
														$.ajax({
															
															type: "POST",
															url: '../Test/get_sample_data_bytest',
															data: {
																test_c: test_c,
																sample_code: sample_code
															},
															beforeSend: function (xhr) { // Add this line
																	xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
															},
															success: function(data) {
																
																var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
																
																resArray = resArray.split('~');
																
																if(resArray[1]>0)
																{
																	field = resArray[0];
																	$("#"+field_value).val(resArray[0]);
																	str1+="<tr id='tr_" + test_c + "'onclick='get_parameter(" + test_c + ")'><td>" + i + "</td><td>" + test_name + "</td><td>" + data + "</td></tr>";
																	i++;												    
																}else{
																	var msg=" Dependent test "+test_name+" not allocated";
																	alert(msg);
																	location.reload();
																}
															}
														});
													}
												});
											});
											
											document.cookie = "sample=" + sample_code;
											
											$.ajax({
												
												type: "POST",
												url: '../Test/get_test_parameter1',
												data: { test_select: test_select },
												beforeSend: function (xhr) { // Add this line
														xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
												},
												success: function(data) {
													
													var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
													
													if(resArray.indexOf('[error]') !== -1){
														var msg="You have passed incorrect values!";
														alert(msg);
													
													}else{
												
														var flag = false;
														$('#myModal').modal('show');
														
														resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
														
														$.each(resArray, function(key1, val1) {
															
															var data = val1['field_validation'].split(",");
															
															if(val1['field_validation']!=""){
																
																var pattern = '/^[0-9]{1,' + data[0] + '}\.[0-9]{1,' + data[1] + '}$/';
																$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">' + val1['a']['field_name']+' ('+val1['field_unit'] + ')</label><div class="col-md-4"><input type="text" class="form-control input"  onkeyup="disableSave();"  id="' + val1['field_value'] + '"  name="' + val1['field_value'] + '"  onblur=" var final_val= parseFloat(this.value);this.value=final_val.toFixed(' + data[1] + ');var i=' + pattern + '.test(this.value);if(i==false){errormsg(\'Digits before decimal point should be less than or equal to ' + data[0] + '\'); this.value=\'\';}this.style.borderColor = ' + pattern + '.test(this.value) ? \'inherit\' : \'red\';"required/></div></div>');
																
																var id = $("#"+val1['field_value']+"").val();
																$("#"+val1['field_value']).val();
																
															}else{
																
																$.each(val1, function(key, val) {
																	$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">' + val + '</label><div class="col-md-4"><input type="text" class="form-control input" id="' + key + '"  name="' + key + '"   required/></div><label class="control-label col-md-6" for="">' + val + '</label></div>');
																	var id = $("#" + key).val();
																	$("#" + key).val();
																});	
																
															}
														});


														$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">Result in %</label><div class="col-md-4"><input type="text" class="form-control" id="res"  name="result" value="" readonly ></div>');
														document.cookie = "sample=" + sample_code;
														
														$.ajax({
															
															type: "POST",
															url: '../Test/get_sample',
															data: {
																sample_code: sample_code,
																test_select: test_select,
																user_code:user_code
															},
															beforeSend: function (xhr) { // Add this line
																	xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
															},
															success: function(data) {
																
																var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response																
																
																if(resArray.indexOf('[error]') !== -1){
																	var msg = resArray.split('~');
																	alert(msg[1]); 
																	return;
																
																}else{
																	
																	resArray = resArray.replace('[[', '');
																	resArray = resArray.replace(']]', '');
																	var i = 1;
																	
																	resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
																	
																	$.each(resArray, function(key, val) {
																		
																		if(key=='result')
																		{
																			if(val=='NA')
																			{
																				
																				$("#abc").prop("hidden", false);
																				$('#save').prop("disabled", false);
																				$("#calculate").show();
																			}
																			else
																			{
																				$("#abc").prop("hidden", true);
																			}
																		}
																				
																		if (i > 8 && i <22) {
																			if(key==field_value){
																				$("#"+field_value).val(field);	
																			}else if ($("#" + key).length) {
																
																				$("#" + key).val(val);
																			}
																		}
																		if (i == 35) {

																			$("#res_div").show();
																			$("#res").val(val);
																			document.cookie = "type=update";
																		}
																		 if (i == 38) {
																			 
																			$("#remark").val(val);

																		}
																		i++;
																	});
															
																}
															}
														});
													
													}
												}
											});
															
										}
									}
                                });      
								
                            } else if (key == "YN") {
								
								document.cookie = "sample=" + sample_code;
								
                                $.ajax({
                                    type: "POST",
                                    url: '../Test/get_sample',
                                    data: {
                                        sample_code: sample_code,
                                        test_select: test_select,
										user_code: user_code
                                    },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
                                    success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										
										if(resArray.indexOf('[error]') !== -1){
											var msg = resArray.split('~');
											alert(msg[1]); 
											return;
										
										}else{
											resArray = resArray.replace('[[', '');
											resArray = resArray.replace(']]', '');
											var i = 1;
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function(key, val) {
												
												if(key=='result')
												{
													if(val=='NA')
													{
														$("#abc").prop("hidden", false);
														$('#save').prop("disabled", false);
														$("#calculate").hide();
													}
													else
													{
														$("#abc").prop("hidden", true);
													}
												}
												if (i == 35) {

													$("#res").val(val);
													document.cookie = "type=update";
												}
												if (i == 38) {
																
													$("#remark").val(val);
																
												}
												i++;
											});
										}
                                      
                                    }
                                });
								
                                $('#myModal').modal('show');
                                $('#save').prop("disabled", false);
                                $("#calculate").hide();
                                document.cookie = "test_type=other";
                                $("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-4" for="">Test Result for Yes/No</label><div class="col-md-6"><select class="form-control" id="res" name="result" required><option val="Select" selected="selected">-----Select-----</option><option val="yes">Yes</option><option val="no">No</option></select></div></div>');
                            
							} else if (key == "PA") {
								
                                $.ajax({
									
                                    type: "POST",
                                    url: '../Test/get_sample',
                                    data: {
                                        sample_code: sample_code,
                                        test_select: test_select,
										user_code: user_code
                                    },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
                                    success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										
										if(resArray.indexOf('[error]') !== -1){
											
											var msg = resArray.split('~');
											alert(msg[1]); 
											return;
											
										}else{
											
											resArray = resArray.replace('[[', '');
											resArray = resArray.replace(']]', '');
											var i = 1;
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function(key, val) {
												if(key=='result')
												{
													if(val=='NA')
													{
														$("#abc").prop("hidden", false);
														$('#save').prop("disabled", false);
														$("#calculate").hide();
													}
													else
													{
														$("#abc").prop("hidden", true);
													}
												}
												if (i == 35) {
													$("#res").val(val);
													document.cookie = "type=update";
												}
												if (i == 38) {
										
													$("#remark").val(val);

												}
												i++;
											});
										}
                                        
                                    }
                                });
								
                                $('#myModal').modal('show');
                                $('#save').prop("disabled", false);
                                $("#calculate").hide();
                                document.cookie = "test_type=other";
                                $("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-4" for="">Test Result for Present/Absent</label><div class="col-md-6"><select class="form-control" id="res" name="result" required><option val="">-----Select-----</option><option val="present">Present</option><option val="absence">Absence</option></select></div></div>');
                           
							} else if (key == "PN") {
							   
                                $.ajax({
                                    type: "POST",
                                    url: '../Test/get_sample',
                                    data: {
                                        sample_code: sample_code,
                                        test_select: test_select,
                                        user_code: user_code
                                    },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
                                    success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										
                                        resArray = resArray.replace('[[', '');
                                        resArray = resArray.replace(']]', '');
                                        var i = 1;
										
										resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
										
                                        $.each(resArray, function(key, val) {
											
											if(key=='result')
											{
												if(val=='NA')
												{
													$("#abc").prop("hidden", false);
													$('#save').prop("disabled", false);
													$("#calculate").hide();
												}
												else
												{
													$("#abc").prop("hidden", true);
												}
											}
                                            if (i == 35) {
                                                $("#res").val(val);
                                                document.cookie = "type=update";
                                            }
											if (i == 38) {
                                                $("#remark").val(val);

                                            }
                                            i++;
                                        });
													
                                    }
                                });
                                $('#myModal').modal('show');
                                $('#save').prop("disabled", false);
                                $("#calculate").hide();
                                document.cookie = "test_type=other";
                                $("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-4" for="">Test Result for Positive/Negative</label><div class="col-md-6"><select class="form-control" id="res" name="result" required><option val="">-----Select-----</option><option val="positive">Positive</option><option val="negative">Negative</option></select></div></div>');
							
							}else if (key == "RT") {
								
								$("#input_parameter_text").empty();
                                $("#modal_type").val("r");
								document.cookie = "sample=" + sample_code;
								$("#calculate_r").show();
								$("#calculate").hide();
                                
								$.ajax({
									
                                    type: "POST",
                                    url: '../Test/get_sample',
                                    data: {
                                        sample_code: sample_code,
                                        test_select: test_select,
										user_code: user_code
                                    },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
                                    success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										
										if(resArray.indexOf('[error]') !== -1){
											
											var msg = resArray.split('~');
											alert(msg[1]); 
											return;
											
										}else{
											
											resArray = resArray.replace('[[', '');
											resArray = resArray.replace(']]', '');
											var i = 1;
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function(key, val) {
												if(key=='result')
												{
													if(val=='NA')
													{
														$("#abc").prop("hidden", false);
														$('#save').prop("disabled", false);
														$("#calculate").hide();
													}
													else
													{
														$("#abc").prop("hidden", true);
													}
												}
												if (i == 35) {
													document.cookie = "type=update";
												}
												if (i == 38) {

												}
												i++;
											});
										}
                                       
                                    }
                                });
								
								$.ajax({
                                    type: "POST",
                                    url: '../Test/get_test_parameter1',
                                    data: {
                                        //sample_code: sample_code,
                                        test_select: test_select
                                    },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
                                    success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										
										if(resArray.indexOf('[error]') !== -1){
											var msg="You have passed incorrect values!";
											alert(msg);
										
										}else {
											
											var flag = false;
                                            $('#myModal').modal('show');
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function(key1, val1) {
												
												var data = val1['field_validation'].split(",");
												
												if(val1['field_validation']!=""){
													
													var test_vall=val1['field_value'];
													var test_vallcd=val1['test_code'];										
													
													var pattern = '/^[0-9]{1,' + data[0] + '}\.[0-9]{1,' + data[1] + '}$/';
													$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">' + val1['a']['field_name']+' ('+val1['field_unit'] + ')</label><div class="col-md-4"><input type="text" class="form-control input"  onkeyup="disableSave();"  id="'+val1['field_value']+'"  name="' + val1['field_value'] + '"  onblur=" var final_val= parseFloat(this.value);this.value=final_val.toFixed(' + data[1] + ');var i=' + pattern + '.test(this.value);if(i==false){errormsg(\'Digits before decimal point should be less than or equal to ' + data[0] + '\'); this.value=\'\';}this.style.borderColor = ' + pattern + '.test(this.value) ? \'inherit\' : \'red\';"required/></div><button align="center" id="calculate_r" class="btn btn-primary" onclick="newFunction(' + test_vall + '.value,'+test_vallcd +') "> Calculate</button></div> ');
													
													
													var id = $("#"+val1['field_value']+"").val();
													$("#"+val1['field_value']).val();
													
												}else{
													
													$.each(val1, function(key, val) {

													var pattern = '/^[0-9]{1,' + data[0] + '}\.[0-9]{1,' + data[1] + '}$/';
													$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">' + val1['a']['field_name']+' ('+val1['field_unit'] + ')</label><div class="col-md-4"><input type="text" class="form-control input"  onkeyup="disableSave();"  id="'+val1['field_value']+'"  name="' + val1['field_value'] + '"  onblur=" var final_val= parseFloat(this.value);this.value=final_val.toFixed(' + data[1] + ');var i=' + pattern + '.test(this.value);if(i==false){errormsg(\'Digits before decimal point should be less than or equal to ' + data[0] + '\'); this.value=\'\';}this.style.borderColor = ' + pattern + '.test(this.value) ? \'inherit\' : \'red\';"required/></div></div><div id="res_div"></div> <button id="calculate_r" class="btn btn-primary" onclick="newFunction(' + test_vall + '.value) ">Calculater</button>');
														var id = $("#" + key).val();
														$("#" + key).val();
													});	
												}
                                            }); 
											
											$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">Result</label><div class="col-md-4"><input type="text" class="form-control" id="res"  name="result" value="" readonly ></div>');

                                           
										}
                                      
                                    }
                                });
					
                                $('#myModal').modal('show');
                                $('#save').prop("disabled", false);
                            
							}else {
								
                                $('#myModal').modal('show');
                                $('#save').prop("disabled", false);
                                $("#calculate").hide();
                               
                                $.ajax({
									
                                    type: "POST",
                                    url: '../Test/get_test_parameter',
                                    data: {
                                        test_select: test_select
                                    },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
                                    success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										
										if(resArray.indexOf('[error]') !== -1){
											var msg = resArray.split('~');
											alert(msg[1]); 
											return;
											
										}else{
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function(key, val) {

												$.ajax({
													type: "POST",
													url: '../Test/get_sample',
													data: {
														sample_code: sample_code,
														test_select: test_select,
														user_code: user_code
													},
													beforeSend: function (xhr) { // Add this line
															xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
													},
													success: function(data) {
														
														var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
														
														if(resArray.indexOf('[error]') !== -1){
															var msg = resArray.split('~');
															alert(msg[1]); 
															return;
														
														}else{
															resArray = resArray.replace('[[', '');
															resArray = resArray.replace(']]', '');
															var i = 1;
															
															resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
															
															$.each(resArray, function(key, val) {
																if (i == 35) {
																	$("#res").val(val);
																	document.cookie = "type=update";
																}
																i++;
															});
														}
														
													}
												}); 
												var pattern = '/^[a-zA-Z0-9]*$/';
												$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-4" for="">' + val + '</label><div class="col-md-6"><input type="test" class="form-control" id="res" name="result" onblur=" var i=' + pattern + '.test(this.value);if(i==false){bootbox.alert(\'Value should be alphabetic only\');}this.style.borderColor = ' + pattern + '.test(this.value) ? \'inherit\' : \'red\';"required/></div></div>');
											});
										}
									}
                                });
								$.ajax({
                                    type: "POST",
                                    url: '../Test/get_sample',
                                    data: {
                                        sample_code: sample_code,
                                        test_select: test_select,
										user_code: user_code
                                    },
									beforeSend: function (xhr) { // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
									},
                                    success: function(data) {
										
										var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
										
										if(resArray.indexOf('[error]') !== -1){
											var msg="You have passed incorrect values!";
											alert(msg[1]); 
											
										}else
										{ 
												
											resArray = resArray.replace('[[', '');
											resArray = resArray.replace(']]', '');
											var i = 1;
											
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
											
											$.each(resArray, function(key, val) {
												
												if (i == 35) {
													$("#res").val(val);
													document.cookie = "type=update";
												}
												if (i == 38) {
												
													$("#remark").val(val);
												}
												i++;
											});
										}
                                    
                                    }
                                });
                                $("#calculate").hide();
                                document.cookie = "test_type=other";

                            }
						});
					}
                }
            });
            
		} else {
			var msg=" select test first";
			alert(msg);
		   
		}
        var sample_code = $("#sample_code").val();

    }
	
	
	
$("#calculate").click("on", function(e) {
			
	e.preventDefault();
	$('#save').prop("disabled", false);			
	var flag = 0;
	
	$('#modal_test').find(':input.input').each(function() {
		
		$("#abc").prop("hidden", true);
		var val = $(this).val();

		if (val == "") {
			flag = true;
		}
		
    });
	
	if (flag == false) {
		
		$('#save').prop("disabled", false);
		var test_select = $("#test_v").val();
		var validation;
		
		if (test_select != "") {
			
			$.ajax({
				
				type: "POST",
				url: '../Test/get_test_formulae',
				data: {
					test_select: test_select
				},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function(data) {
					
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
					
					if(resArray.indexOf('[error]') !== -1){
					var msg="You have passed incorrect values!";
					alert(msg);
				
				}else{
					
					if(resArray!='1'){
						
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
						
						$.each(resArray, function(key1, value1) {
							
							validation = key1;
							
							$.each(value1, function(key, value) {
								$("#formula").val(value);
							});
							
						});
						
						var formula 	= $("#formula").val();
						var string 		= '';
						var i			= 0;
						var chars 		= new Array();
						
						while (i < formula.length) {
							
							var letters = /^[A-Za-z]+$/;
							if (formula[i].match(letters)) {
								var val = $("#" + formula[i]).val();
								string 	= string + val;
								
							} else {
								string	= string + formula[i];
							}
							i++;
						}
						
						var result = eval(string);
						
						//Apply validation for negative, NaN, Infinity value for calculate result,
						if (Math.sign(result) === -1 || typeof result == 'NaN ' || typeof result == 'Infinity') {
							
							alert("Calculated result is not in a range. Please check input parameters.");
							$('#save').attr("disabled", true);
						} 
						$("#res_div").show();
						$("#res").val(result.toFixed(validation));

						
					}
					else{
						var msg=" Formula for this test is expired!";
						alert(msg);
						
					}
				}
				}
			});
		} else {
			var msg="Select previous data first";
			alert(msg);
		   
		}
	} else {
		var msg="Please fill all input first";
			alert(msg);
		
	}
});


function newFunction(valnew,test_vallcd){
		
	var valnew = valnew;
	var test_vallcd = test_vallcd;

	$('#save').prop("disabled", false);			
	var flag = 0;
	
	$('#modal_test').find(':input.input').each(function() {
		
		$("#abc").prop("hidden", true);
		var val = $(this).val();
		if (val == "") {
			flag = true;
		}
	});

	var valnew = valnew;
	var validation;
	
	$.ajax({
		
		type: "POST",
		url: '../Test/get_test_singlevalue',
		data: {valnew:valnew,test_vallcd:test_vallcd},
		beforeSend: function (xhr) { // Add this line
				xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		},
		success: function(data) {
			
			var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
			
			// Check input RI value is in range nor not. if not in range then show alert message
			if(resArray == 1){
				
				alert('RI value not as per range. Please re-enter RI value as per range');
				$("#res").val("");							
				$("#save").attr("disabled", true);
			
			}else{
				
				resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
				
				$.each(resArray, function (key, value) {
					
					var result=value[0]['ri'];
					$("#res").val(result);
					$("#save").show();
				});
			}
		}
	});

}



$("#myModal").on("hide.bs.modal", function() {

	if ($('#close1').data('clicked')) {} else if ($('#close').data('clicked')) {} else {
		return false;
	}
	
	$('#close1').data('clicked', false);
	$('#close').data('clicked', false);
	$('form#test')[0].reset();
	var x = getCookie("select_sample_code");

	$("#sample_code").val(x);
	getData();

});


function getCookie(cname) {
			
	var name = cname + "=";
	var ca = document.cookie.split(';');
	
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}


$("#close1").click(function(e) {
	e.preventDefault();
	$(this).data('clicked', true);
	$('#save').prop("disabled", true);
});

$("#close").click(function(e) {
	e.preventDefault();
	$(this).data('clicked', true);
	$('#sample').val("");
	$('#save').prop("disabled", true);
});

$("#save").click(function(e) {
	e.preventDefault();
	var res=$("#res").val();

	if(res!='' && res!='-----Select-----')
	{
		if(confirm("Do u want to save the test result?")
		{   
			var data = $('#modal_test').serialize();
			$.post('test', data);
			$("#close1").click();
		}
	}
	else{
		 var msg="Fill the result first";
		alert(msg);
	}
});


$("#test_r").click(function(e) {
	e.preventDefault();
	if(confirm("Do u want to clear the test result?"){
					
		var chemist_code=$("#sample").val();
		var test_code=$("#test_v").val();
		$("#abc").prop("hidden", false);
		
		$.ajax({
				type: "POST",
				url: '../Test/update_cant_perform_test',
				data: {chemist_code:chemist_code,test_code:test_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success:function(data)
				{
					$('#res').val('NA');
					
					var msg=" Successful";
					$("#abc").prop("hidden", false);
					$('#save').prop("disabled", false);
					alert(msg);
							
					$("#test_r").prop("disabled", true);
					
				}
		 });

	}
});



$('#finalize').click(function(e) {
			
	// disable save button after click on save button,
	$("#finalize").prop("disabled", true);
		
	if(confirm("Do u want to finalize the test results?"){			
		$('#test').submit();
	
	}else{
		$("#finalize").prop("disabled", false);
	}
});


$('#save').on("mouseover", function(e) {
	e.preventDefault();
	var formula = $("#formula").val();
	if (formula == 'formula') {
		$("#calculate").trigger("click");
	}
});


$("#sample_code").on("change", function(e)
{
	$("#input_parameter_div").find('label').remove();
	$("#input_parameter_div").find('input').remove();
	$("#res_div").hide();
	$("#input_parameter_text").find('input').remove();
	$("#input_parameter_text").find('label').remove();
});