
	$(document).ready(function(){

		$("#stage_sample_code").change(function(e){

			if(getsampledetails()==false){
				e.preventDefault();
			}else{
				if(enable_disble()==false){
					e.preventDefault();
				}else{
					if(get_remark()==false){
						e.preventDefault();
					}
				}
			}
		});


		$("#grd_standrd").change(function(e){
			if(getdetails()==false){
				e.preventDefault();
			}
		});

	});

	var arr = new Array();

	function getsampledetails(){

		$("#category_code").find('option').remove();
		$("#commodity_code").find('option').remove();
		$("#sample_type").find('option').remove();
		$("#grade_code").find('option').remove(); // remove all options from grade_code dropdown, done by pravin bhakare 22-11-2019
		$("#grd_standrd").val('');
		$(".fsStyle1").hide();

		var grade_value = null;
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

					var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
					
					$.each(resArray, function (key, value) {

						$("#sample_type").append("<option value='" + $.trim(value['sample_type_code']) + "'>" + $.trim(value['sample_type_desc']) + "</option>");
						$("#commodity_code").append("<option value='" + $.trim(value['commodity_code']) + "'>" + $.trim(value['commodity_name']) + "</option>");
						$("#category_code").append("<option value='" + $.trim(value['category_code']) + "'>" + $.trim(value['category_name']) + "</option>");

						// Selecting the grades of a particular commodityode dropdown,
						$.ajax({
							type: "POST",
							url: 'get_sample_commodity_grads',
							data: {commodity_code: value['commodity_code'],sample_code:sample_code},
							beforeSend: function (xhr) { // Add this line
								xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
							},
							success: function (data) {

								var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response
								resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

								$("#grade_code").append("<option value=''>--Select--</option>");

								$('#subgradelist').hide();
								$('#allGradeListChecked').prop('checked', false);
								$("#fullGradelist").val('');

								$.each(resArray, function (key, value) {

									if(key == 0){

										grade_value = value['grade'];

										if(value['sub_grad_check_iwo']=='checked'){
											$('#subgradelist').show();
											$('#grade_code').prop('disabled', true);
											$("#grade_code").val('');
											$('#allGradeListChecked').prop('checked', true);
											$("#fullGradelist").val(grade_value);

										}else{
											$('#subgradelist').hide();
											$('#grade_code').prop('disabled', false);
											$('#allGradeListChecked').prop('checked', false);
											$("#fullGradelist").val('');
										}
									}

									if(key != 0){

										if(grade_value == value['grade_code']){
											$("#grade_code").append("<option value='" + $.trim(value['grade_code']) + "' selected>" + $.trim(value['grade_desc']) + "</option>");
										}else{
											$("#grade_code").append("<option value='" + $.trim(value['grade_code']) + "'>" + $.trim(value['grade_desc']) + "</option>");
										}
									}
								});

								// enable grading drop down ,
								$('#grade_code').prop('disabled', false);
							}
						});
					});
				}
			});
		}
	}




	function enable_disble(){

		$("#method_code").attr("disabled", false);
		$("#grd_standrd").attr("disabled", false);
		$("#grade_code").attr("disabled", false);
	}


	function getdetails(){

		$("#save").attr("disabled", false);
		$("#delete").attr("disabled", true);

		$("#method_code").attr("disabled", false);
		$("#grd_standrd").attr("disabled", false);
		//var arr = new Array();

		var sample_code = $("#sample_code").val();
		var grd_standrd = $("#grd_standrd").val();
		var category_code=  $("#category_code").val();
		var commodity_code=  $("#commodity_code").val();


		$("#d1 tbody").find('tr').remove();

		if (sample_code != ""){

			$.ajax({
				type: "POST",
				url: 'getfinal_result',
				data: {sample_code: sample_code,grd_standrd:grd_standrd,category_code:category_code,commodity_code:commodity_code},
				beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {

					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

					$("#d1 tbody").find('tr').remove();

					if(resArray.indexOf('[error]') !== -1){
						var msg = resArray.split('~');
						alert(msg[1]);
						return;
					}else{

						var array = resArray.split("~");

						if(array[1]!='1'){

							$("#save").attr("disabled", false);
							var i=1;

							resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

							$.each(resArray, function (key, value){

								var rowcontent="<tr><td>"+i+"</td>";
								$.each( value,function (key1, value1){
									
									if(key1!='test_code'){
										$("#first").show();
										rowcontent=rowcontent+"<td>"+value1+"</td>";
										arr.push(value1);
									}
								});

								rowcontent = rowcontent+"</tr>";
								$("#d1 tbody").append(rowcontent);
								i++;
							});

							$("#d1").show();
							$(".fsStyle1").show();

						}else{
							$("#d1").hide();
							$(".fsStyle1").hide();
							var msg="Fill the grade for commodity!";
							alert(msg);
							$("#save").attr("disabled", true);
						}
					}
				}
			});
		}
	}



	function get_remark(){

		$("#method_code").attr("disabled", false);
		$("#grd_standrd").attr("disabled", false);

		var sample_code = $("#sample_code").val();

		$.ajax({

			type:"POST",
			url:"get_remark",
			data:{sample_code: sample_code},
			cache:false,
			beforeSend: function (xhr) { // Add this line
				xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success : function(data){

				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

				if(data==0){
					var msg="Please Enter valid sample code!!!";
					errormsg(msg);
					return false;
				}
				else{

					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

					$.each(resArray, function (key, value) {

						$("#remark").val(value['remark'])	;

						if(value['remark']==null){
							$("#abc").prop("hidden", false);
							$("#byINW").prop("hidden", true);
							$('#byDOL').css('float','left');

						}
						else{
							$("#abc").prop("hidden", false);
							$("#byINW").prop("hidden", false);
							$('.byDOL').css('float','right');
						}
					});
				}
			}
		});
	}
