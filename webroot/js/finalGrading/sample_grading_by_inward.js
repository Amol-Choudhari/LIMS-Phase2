
	//Starting Validations 
	$(document).ready(function(){

		$("#get_zscore").click(function () {//first save the zscore then show forward button
			$('#frd_to_oic').show();
			
		});

			$("#stage_sample_code").change(function(e){

			
				if(getsampledetails()==false){
					e.preventDefault();
				}else{
					if(enable_disble()==false){
						e.preventDefault();
					}
				}
				/* added for ilc sample Done by shreeya on 17-11-2022*/
				var sampletype = $("#sample_type").val();

				if(sampletype == 9){
						
					$('#frd_to_oic').hide();//final inward window hide forward button on 18-11-22

					if(getdetailsilc()==false){
						e.preventDefault();
					}
				}
					
				
						
					
			});

			

		$("#grd_standrd").change(function(e){

			var sampletype = $("#sample_type").val();

			if(getdetails()==false){
				e.preventDefault();
			}
		});

	})

	var arr = new Array();


	//function to get the sample details by ajax
	function getsampledetails(){

		$("#category_code").find('option').remove();
		$("#commodity_code").find('option').remove();
		$("#sample_type").find('option').remove();
		$("#grade_code").find('option').remove(); // remove all options from grade_code dropdown, done by pravin bhakare 22-11-2019
		$("#grd_standrd").val('');
		$(".fsStyle1").hide();


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



	//to enable and disable the fields
	function enable_disble(){

		$("#method_code").attr("disabled", false);
		$("#grd_standrd").attr("disabled", false);
		$("#grade_code").attr("disabled", false);
	}


	//to get the details of final result
	function getdetails(){

		$("#save").attr("disabled", false);
		$("#delete").attr("disabled", true);
		$("#method_code").attr("disabled", false);
		$("#grd_standrd").attr("disabled", false);

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

									//$.each( value1,function (key2, value2){

										if(key1!='test_code'){
											$("#first").show();

											rowcontent=rowcontent+"<td>"+value1+"</td>";
											arr.push(value1);
										}

									//});

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
							$.alert(msg);
							$("#save").attr("disabled", true);
						}
					}
				}
			});
		}
	}

	//added for ilc flow click  grd standard details 
	//show the  non grade table list to get the details of final result
	// Done By Shreeya on 17-11-2022
	function getdetailsilc(){

		$("#save").attr("disabled", false);
		$("#delete").attr("disabled", true);
		$("#method_code").attr("disabled", false);
		$("#grd_standrd").attr("disabled", false);

		var sample_code = $("#sample_code").val();
		var grd_standrd = 2;// it is requried but not used for ilc so given default value intentionaly
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

								var j=0;
								$.each( value,function (key1, value1){

									//$.each( value1,function (key2, value2){

										if(key1!='test_code' && j<3){//for ilc sample show only two td
											$("#first").show();

											rowcontent=rowcontent+"<td>"+value1+"</td>";
											arr.push(value1);
										}

									//});

									j++;

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
							$.alert(msg);
							$("#save").attr("disabled", true);
						}
					}
				}
			});
		}
	}
