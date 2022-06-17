//on page load
$("#data_table_div").prop("hidden", true);
$("#abc").prop("hidden", true);
var commodity_code;
document.cookie = "type=add";
//initMenu();
$('#avb').hide();
$('#commo').hide();
$("#delete_div").hide();
$("#update_div").hide();

$("#stage_sample_code").change(function(e){

	if(getsampledetails()==false){
		e.preventDefault();
	}

});


$("#test_select").change(function(e){

	if(get_parameter()==false){
		e.preventDefault();
	}
});


$('#test_table').on('click', 'tr', function() {
	var str = $(this).attr('id');
	var testrowid = str.split('_');
	var test_select1 = $('#'+str+' td').eq(1).text(); // the first <td>
	var testid = testrowid[1];

	$("#test_title").html("Test reading for<b> "+test_select1+"</b>");
	get_parameter(testid)
});

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

							if(value['login_timestamp']!=null){

								var sample_allocated_date = value['login_timestamp'].split(" ");
								/*update sample_allocated_date, */
								var d1=new Date(sample_allocated_date[0].split("/").reverse().join("-"));
								var alloc_date1 = d1.getDate() + '/' + (d1.getMonth() + 1) + '/' + d1.getFullYear();

							}else{
								alloc_date1	='';
							}

							$('#sample_alloc').append("<p class='btn' style='background:#e6e6fa'> Sample allocated on " + alloc_date1 +"</p><br>");

							if(value['recby_ch_date']!=null){

								var d2=new Date(value['recby_ch_date'].split("/").reverse().join("-"));
								var d3=new Date(value['expect_complt'].split("/").reverse().join("-"));

								var sample_acc1 = d2.getDate() + '/' + (d2.getMonth() + 1) + '/' + d2.getFullYear();
								var expect_cmpl1 = d3.getDate() + '/' + (d3.getMonth() + 1) + '/' + d3.getFullYear();

							}else{
								sample_acc1	='';
								expect_cmpl1	= '';

							}
							$('#sample_acc').append("<p class='btn' style='background:#e6e6fa'>Sample Accepted on " + sample_acc1 +"</p><br>");
							$('#expect_cmpl').append("<p class='btn' style='background:#aabcbf'>Expected Completion on " + expect_cmpl1 +"<br>");

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
						$("#test_table tbody").append("<tr id='tr_" + value['test_code'] + "' class='teststatusentered'><td>" + i + "</td><td>" + value['test_name'] + "</td><td>"+value['method_name']+"</td><td>" + value['test_result'] + "</td><td>" + value['test_unit'] + "</td></tr>");

					}else{
						$("#test_table tbody").append("<tr id='tr_" + value['test_code'] + "' class='teststatustobe'><td>" + i + "</td><td>" + value['test_name'] + "</td><td>"+value['method_name']+"</td><td>" + value['test_result'] + "</td><td>" + value['test_unit'] + "</td></tr>");

					}

					if (value == null)
					{
						value = "-";
					}

					i = i + 1;
				});

				if(method_undefined == 'yes'){

					$('#method_error_message').append("<p class='alert alert-danger'>Some of the Test method names are undefined. Please inform the Administrator.</p>");
				}


				$("#test_table").show();
				$('#color_mark').show();
				$('#finalize_div').show();
				//$("#finalize_div").height($(".table-responsive").height());
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

					$("#test_select").append("<option value='" + value['test_code'] + "'>" + value['test_name'] + "</option>");
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


					if(resArray.indexOf('[error]') !== -1){
						var msg="You have passed incorrect values!";
						alert(msg);

					}else{
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

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


										if(resArray.indexOf('[error]') !== -1){

											var msg="You have passed incorrect values!";
											alert(msg);

										}else{
											resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

											$.each(resArray, function(key2, value2) {

											  $("#formula").val(value2);

											  //Added by Akash for displaying the Test Formulae at the Chemist Level
											  $("#test_formulae").append('<label class="alert alert-info">Calculation Formulae:' +value2+ '</label>');

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
																	str1+="<tr id='tr_" + test_c + "'><td>" + i + "</td><td>" + test_name + "</td><td>" + data + "</td></tr>";
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

																$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">' + val1['field_name']+' ('+val1['field_unit'] + ')</label><div class="col-md-4"> <input type="text" blrval1="'+data[0]+'" blrval2="'+data[1]+'" class="form-control input"  id="' + val1['field_value'] + '"  name="' + val1['field_value'] + '" required/></div></div>');

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
																											$("#test_r").prop("disabled", true);
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
														$("#test_r").prop("disabled", true);
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
                                $("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-6" for="">Test Result for Yes/No</label><div class="col-md-6"><select class="form-control" id="res" name="result" required><option val="Select" selected="selected">-----Select-----</option><option val="yes">Yes</option><option val="no">No</option></select></div></div>');

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
														$("#test_r").prop("disabled", true);
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
                                $("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-6" for="">Test Result for Present/Absent</label><div class="col-md-6"><select class="form-control" id="res" name="result" required><option val="">-----Select-----</option><option val="present">Present</option><option val="absence">Absence</option></select></div></div>');

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
													$("#test_r").prop("disabled", true);
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
                                $("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-6" for="">Test Result for Positive/Negative</label><div class="col-md-6"><select class="form-control" id="res" name="result" required><option val="">-----Select-----</option><option val="positive">Positive</option><option val="negative">Negative</option></select></div></div>');

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
														$("#test_r").prop("disabled", true);
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

													$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">' + val1['field_name']+' ('+val1['field_unit'] + ')</label><div class="col-md-4"><input type="text" class="form-control input"  id="'+val1['field_value']+'"  name="' + val1['field_value'] + '"  blrval1="'+data[0]+'" blrval2="'+data[1]+'" required/></div><button align="center" id="calculate_r" class="btn btn-primary" ocparam1="'+test_vall+'"  ocparam2="'+test_vallcd+'">Calculate</button></div> ');

													var id = $("#"+val1['field_value']+"").val();
													$("#"+val1['field_value']).val();

												}else{

													//var test_vall=val1['field_value'];

													$.each(val1, function(key, val) {


													$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-8 text-left" for="">' + val1['field_name']+' ('+val1['field_unit'] + ')</label><div class="col-md-4"><input type="text" class="form-control input"  id="'+val1['field_value']+'"  name="' + val1['field_value'] + '"  blrval1="'+data[0]+'" blrval2="'+data[1]+'" required/></div></div><div id="res_div"></div> <button id="calculate_r" class="btn btn-primary" ocparam1="'+test_vall+'">Calculater</button>');
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
												$("#input_parameter_text").append('<div class="form-group"><label class="control-label col-md-6" for="">' + val + '</label><div class="col-md-6"><input type="test" class="form-control" id="res" name="result" required/></div></div>');
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


	$("#input_parameter_text").on("click","#calculate_r",function(e){

			let id = $(this).attr('ocparam1');
			let valnew = $("#"+id).val();
			let test_vallcd = $(this).attr('ocparam2');
			if(valnew != 'undefined' && test_vallcd != 'undefined')
	    {
					newFunction(valnew,test_vallcd);
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

					var result=value['ri'];
					console.log(result);
					$("#res").val(result);
					$("#save").show();
				});
			}
		}
	});

}



/*$("#myModal").on("hide.bs.modal", function() {

	if ($('#close1').data('clicked')) {} else if ($('#close').data('clicked')) {} else {
		return false;
	}

	$('#close1').data('clicked', false);
	$('#close').data('clicked', false);
	$('form#test')[0].reset();
	var x = getCookie("select_sample_code");

	$("#sample_code").val(x);
	getData();

});*/


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

$("#input_parameter_text").on("keyup","input[type='text']",function(e){
	 disableSave();
});

function disableSave(){
			$('#save').prop("disabled", true);
}
