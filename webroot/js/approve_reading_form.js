


//Below code is added by AkashT for final,retest and average button clicks.-->
	var glob_test_code;
	var glob_test_name;
	var glob_chemist_code;
	var glob_test_result;
	var glob_chemist_name;

//Added By AkashT to add the Color to the "<tr>" of first DIV.table for displaying Approved % Unapproved Tests. Also for selected row-->

	var approved_color_test       = [];
	var to_be_approved_color_test = [];
	var get_tr_id_for_color       = [];
	var chemist_code_new          = [];
	//var chemist_name_new          = [];
	//var status_new                = [];
	var chemist_code_apt          = [];
	var final_test                = [];
	var final_result_one          = [];


	var resultarr   = [];
	var testcodearr = [];
	var testnamearr = [];
	var testtypearr = [];
	var rowidarr    = [];
	var duplicate_flag = "";

	$("#menu-toggle").click(function (e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});
	$("#menu-toggle-2").click(function (e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled-2");
		$('#menu ul').hide();
	});


	$(document).ready(function(){

		$("#sample_type").change(function(e){
			console.log('hello');

			if(getfinalizeflag()==false){
				e.preventDefault();

			}else{

				if(getduplicateflag()==false){
					e.preventDefault();

				}else{

					let stage_sample_code =$("#stage_sample_code");
					if(checkFinalResultCount()==false){
						e.preventDefault();

					}

				}

			}

		});


		$('#d1 tbody').on('click', 'tr', function() {

				var id = $(this).attr('id');
				var tnam = $(this).attr('tnam');
				get_parameter(tnam,id);

		});

		$('#d2 tbody').on('click', 'tr', function() {

				var id = $(this).attr('id');
				var tval = $(this).attr('tval');
				var ttcode = $(this).attr('ttcode');
				var ttname = $(this).attr('ttname');
				var tttype = $(this).attr('tttype');
				var tkey = $(this).attr('tkey');
				var tdflag = $(this).attr('tdflag');
				var tscode = $(this).attr('tscode');

				if(tdflag == 'undefined'){
					tdflag = '';
				}

				if(get_final_result(tval,ttcode,ttname,tttype,id,tkey,tdflag,tscode)!=false)
				{
					checkForRetest(tscode);
				}

		});



	});


	function getfinalizeflag(){

		var sample_code = $("#sample_code").val();
		if (sample_code != ""){
			$.ajax({
				type: "POST",
				url: 'get_finalise_flag',
				data: {sample_code: sample_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

					if(resArray.indexOf('[error]') !== -1){
						var msgArr = resArray.split("~");
						var msg=msgArr[1];
						alert(msg);
						return;
					}else{
						if(resArray==0){

							$("#ral").attr("disabled", false);
							$("#oic").attr("disabled", false);
							$("#re_test").attr("disabled", false);
							$("#avrage").attr("disabled", false);
							$("#duplicate_id").show();
							$("#dupilcate").show();
							$("#ral").show();
						}
					}
				}
			});
		}
	}

	function getduplicateflag(){

		var sample_code = $("#sample_code").val();
		if (sample_code != ""){
			$.ajax({
				type: "POST",
				url: 'get_duplicate_flag',
				data: {sample_code: sample_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {

					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

					if(resArray.indexOf('[error]') !== -1){
						var msgArr = resArray.split("~");
						var msg=msgArr[1];
						alert(msg);
						return;
					}else{
						if(resArray==0){
						}else{
							$("#re_test").attr("disabled", false);
							$("#save_duplicate").attr("disabled", false);
							duplicate_flag="D";
						}
					}

				}
			});
		}
	}



//---------Retesting function Done by AkashT for the button Retest------------>
	function retesting(){

			if(confirm("Do you want to Re-test this sample?")){

				var sample_code = $("#sample_code").val();
				var tran_date = $("#tran_date").val();
				var category_code = $("#category_code").val();
				var commodity_code = $("#commodity_code").val();
				var test_code=$("#test_code").val();
				var final_result=$("#final_result").val();
				var status_flag='R';
					if (sample_code != "")
					{
					  $.ajax({
						type: "POST",
						url: 'retesting_sample',
						data: {status_flag:status_flag,test_code:test_code,sample_code: sample_code,tran_date:tran_date,category_code:category_code,commodity_code:commodity_code},
						beforeSend: function (xhr) { // Add this line
								xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
						},
						success: function (data) {

							var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

							if(resArray.indexOf('[error]') !== -1){
								var msg =resArray.split('~');
								alert(msg[1]);
								return;
							}else{
								if(resArray==1)
								{
									var msg="The Selected Test is Marked Successful for Retest!!!";
									alert(msg);
									window.location = '';
									return;
								}
							}
						}
					});
				  }
		    }

	}


	function checkFinalResultCount(val)
	{
		var sample_code = $("#sample_code").val();
		$.ajax({
			type: "POST",
			url: 'checkFinalResultCount',
			data: {sample_code: sample_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {

				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

				if(resArray.indexOf('[error]') !== -1){
					var msgArr = resArray.split("~");
					var msg=msgArr[1];
					alert(msg);
					return;
				}else{
					if(resArray==1){
						return;
					}else{
						alert("Allocated to Multiple Chemist and Results are Pending from One or More Chemist!");
						window.location='available_for_approve_reading';
					}

				}

			}
		});
	}

	function getdetails()
	{

		var type=$("#type:checked").val();
			var xyz;
			var pqr;
		if(type=='V')
		{
			view1();

			$("#final").css("background-color", "");
			$("#re_test").css("background-color", "");
		}
		 else{

			$("#d2").hide();
			$(".fsStyle2").hide();

			var sample_code = $("#sample_code").val();

			$("#d1 tbody").find('tr').remove();

			if (sample_code != "")
			{
				$.ajax({
						type: "POST",
						url: 'get_details',
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

								//Below code added by AkashT to color the row for displying the tested and untested TESTS. -->
								approved_color_test = [];
								to_be_approved_color_test = [];
								//Added by AkashT to count the test whhich are approved and unapproved. -->
								resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

								var i=1; var test_count=0; var final_tests_count=0;


								//below updated by Amol, new loop
								var new_arr = {};
								var lp=0;
								$.each(resArray, function (key, value) {

									var str = '';
									$.each( value,function (key1, value1) {

										str += value1+'~';
									});

									var split_str = str.split('~');

									var para1 = split_str[0];
									var para2 = split_str[1];
									var para3 = split_str[2];

									var arr1 = {};
									arr1[para1] = para2;

									new_arr[lp] = arr1;

									lp=lp+1;
								});
								//above updated by Amol, new loop

								$.each(new_arr, function (key, value) {

									$.each( value,function (key1, value1) {

									//Added by AkashT to test count which give the count for matching the result and test names that are approved-->
										test_count = test_count+1;

									//Added by AkashT to match the tests and seperate the test code , test name and results and show in thee table.-->
										var str=key1+'~'+value1+'~'+key;


										$.each( final_result_one,function (final_result_key, final_result_value) {

											var split_value=final_result_value.split(":");
											xyz = split_value[0];
											pqr = split_value[1];
											if(xyz==key1){

												return false;
											}

										});

									//Added below code on 20/12/19 by AkashT for marking the approved result / final test by green color-->
										if (final_test.indexOf(parseInt(key1)) !='-1') {
										approved_color_test.push(i);

											//$("#d1 tbody").append('<tr  id='+i+' tnam="'+str+'" onclick="get_parameter(\''+str+'\','+i+')" style="background-color: #00b04f;"><td>'+i+'</td><td>'+value1+'</td><td>'+pqr+'</td></tr>');
											$("#d1 tbody").append('<tr  id='+i+' tnam="'+str+'" class="d1gcolor00"><td>'+i+'</td><td>'+value1+'</td><td>'+pqr+'</td></tr>');

											 final_tests_count = final_tests_count+1;
										} else {
									//Added below code on 20/12/19 by AkashT for marking the unapproved tests Orange color-->
											to_be_approved_color_test.push(i);

											//$("#d1 tbody").append('<tr style="background-color: #ffba66" id='+i+' tnam="'+str+'" onclick="get_parameter(\''+str+'\','+i+')"><td>'+i+'</td><td>'+value1+'</td><td></td></tr>');
											$("#d1 tbody").append('<tr class="d1gcolorff" id='+i+' tnam="'+str+'"><td>'+i+'</td><td>'+value1+'</td><td></td></tr>');

										}
										$("#d1 tbody tr").css("cursor","pointer");

										if(value==null){
											value="-";
										}
										i=i+1;
									});
								});
								$("#d1").show();
								$(".fsStyle1").show();

								if(test_count == final_tests_count){
									$("#finalise_id").show();
									$("#finalize").attr("disabled", false);
									$("#finalize").removeAttr("title");
								}else{
									//$("#finalise_id").hide();
									$("#finalize").attr("disabled", true);
								}
							}

						}
				});

			}
		}
	}


	function get_parameter(str1,row_id)
	{
		resultarr         = [];
		testcodearr	      = [];
		testnamearr	      = [];
		testtypearr	      = [];
		chemist_codearr   = [];
		rowidarr          = [];
		chemist_code_new  = [];
		//chemist_name_new  = [];
		//status_new        = [];

		var sum_result1=0;
		$("#final").css("background-color", "#006400");
		$("#re_test").css("background-color", "#006400");
		var data		= str1.split('~');
		var test_code 	= data['0'];
		var test_name1 	= data['1'];
		var test_type 	= data['2'];
		var avg			= 0;
		var avg1		= 0;
		var r_id		= row_id;
		var fr			= 0;
		var sample_code = $("#sample_code").val();
		$("#duplicate_record").click(function (e) {
			if($(this).prop("checked")==true){
			}
		});

  //Below code added by AkashT to set the Color to the ROW based on Approved [GREEN] & Unapproved [ORANGE] test results.-->
	  $("#d1 tbody #"+r_id).css("background-color","#d2ff4d");
		get_tr_id_for_color=row_id;
		$.each(approved_color_test, function( key, value ) {
						if (value==get_tr_id_for_color) {
							$("#d1 tbody #"+value).css("background-color","#d2ff4d");
						}else{
							$("#d1 tbody #"+value).css("background-color","#00b04f");
						}
     });

       $.each(to_be_approved_color_test, function( key, value ) {
			if (value==get_tr_id_for_color) {
							$("#d1 tbody #"+value).css("background-color","#d2ff4d");
						}else{
							$("#d1 tbody #"+value).css("background-color","#ffba66");
						}
     });
        $("#avb").show();
		    $("#approvedresultsmodal").show();

	   $.ajax({
			type: "POST",
			url: 'check_multiple_test_alloc',
			async:false,
			data: {sample_code: sample_code,test_code:test_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data)
			{
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
				var obj     = JSON.parse(resArray);
				var data1   = JSON.stringify(obj[0]);
				var data2   = JSON.stringify(obj[1]);
				var result1 = JSON.parse(data1);
				var result2 = JSON.parse(data2);
				chemist_name_new = result1;
				status_new = result2;

				$.ajax({
					type: "POST",
					url: 'getfinal_result',
					async:false,
					data: {duplicate_flag:duplicate_flag,sample_code: sample_code,test_code:test_code},
					beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function (data)
					{
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response

						if(resArray.indexOf('[error]') !== -1){
							var msg =resArray.split('~');
							alert(msg[1]);
							$("#sample_code").val('');
							$("#test_code").val('');
							return;
						}else{
							var i	= 1;
							if(resArray==1){

								$("#final").css("background-color", "");
								$("#re_test").css("background-color", "");
								$("#avrage").css("background-color", "");
								$("#avrage").attr("disabled", true);
							  //$("#finalize").hide();
							}
							if( resArray!=1){

								resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

								$.each(resArray, function (key, value) {

									$("#test_code").val(test_code);
									$("#final_result").val(value);
									fr = $("#final_result").val();
									$(" #d2 tbody ").find('tr').css("background-color","");
								});
									$("#final").attr("disabled", true);
									$("#re_test").attr("disabled", true);
									$("#final").css("background-color", "#006400");
									$("#re_test").css("background-color", "#006400");
									$("#avrage").css("background-color", "");
									$("#finalise_id").show();
									$("#finalize").attr("disabled", false);

							}
						}





					}
				});


				$.ajax({
					type: "POST",
					url: 'get_alloc_test',
					data: {sample_code: sample_code,test_code:test_code},
					beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function (data) {

						var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response

						if(resArray.indexOf('[error]') !== -1){

							var msg =resArray.split('~');
							alert(msg[1]);
							$("#sample_code").val('');
							$("#test_code").val('');
							$("#").val();
							return;

						}else{

							$("#d2 tbody ").find('tr').remove();
							$(" #d2 tbody ").find('tr').css("background-color","");
							var i=2;
							var fr=$("#final_result").val();
							$('#d2 th').eq(0).text( test_name1 );
							$('#d2 th').eq(1).text( "Sample" );
							$('#d2 th').eq(2).text( "Chemist" );
							$('#d2 th').eq(3).text( "Status" );

							$("#re_test").attr("disabled", true);
							$("#avrage").attr("disabled", true);
						  //$("#finalize").attr("disabled", false);
							$("#final").attr("disabled", true);
						  //$("#finalize").hide();

						  resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

							$.each(resArray, function (key, value) {

							key = value['chemist_code'];
							value = value['result'];

								if(value==null)
								{
									value='-';
									$("#avrage").attr("disabled",true);
								}

								if(fr==value)
								{
									if(fr!=0)
									{

											resultarr.push(value);
											testcodearr.push(test_code);
											testnamearr.push(test_name1);
											testtypearr.push(test_type);
											rowidarr.push(i+i);
											chemist_codearr.push(key);
											$("#chemist_code").val(key);

											//$("#d2 tbody").append('<tr id="'+i+i+'" onclick="get_final_result(\''+value+'\','+test_code +',\''+test_name1+'\',\''+test_type+'\','+i+i+',\''+key+'\','+sample_code+');checkForRetest('+sample_code+') "  ><td >'+value+'</td><td >'+key+'</td><td>'+chemist_name_new[key]+'</td><td>'+status_new[key]+'</td></tr>');
											$("#d2 tbody").append('<tr id="'+i+i+'" tval="'+value+'"  ttcode="'+test_code+'" ttname="'+test_name1+'" tttype="'+test_type+'" tkey="'+key+'" tscode="'+sample_code+'"><td >'+value+'</td><td >'+key+'</td><td>'+chemist_name_new[key]+'</td><td>'+status_new[key]+'</td></tr>');

									}else{

											resultarr.push(value);
											testcodearr.push(test_code);
											testnamearr.push(test_name1);
											testtypearr.push(test_type);
											chemist_codearr.push(key);
											rowidarr.push(i+i);
											$("#chemist_code").val(key);
											//$("#d2 tbody").append('<tr id="'+i+i+'" onclick="get_final_result(\''+value+'\','+test_code +',\''+test_name1+'\',\''+test_type+'\','+i+i+',\''+key+'\',\''+duplicate_flag+'\','+sample_code+') ;checkForRetest('+sample_code+')"  ><td >'+value+'</td><td >'+key+'</td><td>'+chemist_name_new[key]+'</td><td>'+status_new[key]+'</td></tr>');
											$("#d2 tbody").append('<tr id="'+i+i+'" tval="'+value+'"  ttcode="'+test_code+'" ttname="'+test_name1+'" tttype="'+test_type+'" tkey="'+key+'" tdflag="'+duplicate_flag+'" tscode="'+sample_code+'" ><td >'+value+'</td><td >'+key+'</td><td>'+chemist_name_new[key]+'</td><td>'+status_new[key]+'</td></tr>');
									}

								}else {


									resultarr.push(value);
									testcodearr.push(test_code);
									testnamearr.push(test_name1);
									testtypearr.push(test_type);
									chemist_codearr.push(key);
									rowidarr.push(i+i);
									$("#chemist_code").val(key);
									//$("#d2 tbody").append('<tr id="'+i+i+'" onclick="get_final_result(\''+value+'\','+test_code +',\''+test_name1+'\',\''+test_type+'\','+i+i+',\''+key+'\',\''+duplicate_flag+'\','+sample_code+');checkForRetest('+sample_code+') "  ><td >'+value+'</td> <td >'+key+'</td><td>'+chemist_name_new[key]+'</td><td>'+status_new[key]+'</td></tr>');
									$("#d2 tbody").append('<tr id="'+i+i+'" tval="'+value+'"  ttcode="'+test_code+'" ttname="'+test_name1+'" tttype="'+test_type+'" tkey="'+key+'" tdflag="'+duplicate_flag+'" tscode="'+sample_code+'"  ><td >'+value+'</td> <td >'+key+'</td><td>'+chemist_name_new[key]+'</td><td>'+status_new[key]+'</td></tr>');
								}

								if(value==null)
								{
									value="-";

								}
								if(test_type=='Formula' && duplicate_flag!="D")
								{
								$("#avrage").attr("disabled", false);
								}
								else{
					// CHANGE DISPLAY PROPERTY OF AVRAGE BUTTON FALSE TO TURE, TO SOLVED AVRAGE BUTTON ISSUED. DONE BY PRAVIN BHAKARE ON 24-06-2019
					//------------------------------------------------------------------------------------------------------------------------------//
									$("#avrage").attr("disabled", true);
								}
								i=i+1;
							});
						}

						if(test_type=='Formula' && duplicate_flag=="S")
						{
							for(i=0;i<resultarr.length;i++)
							{
								sum_result1=parseFloat(sum_result1)+parseFloat(resultarr[i]);
							}
							avg1=parseFloat(sum_result1)/resultarr.length;
							avg=avg1.toFixed(4);
							if(fr==avg){
								$(" #d2 tbody ").find('tr').css("background-color","#9fc66f");
								$("#final").css("background-color", "");
								$("#avrage").css("background-color", "#006400");
							}

							$("#avrage").click(function (e) {

								$("#avrage").css("background-color", "#9fc66f");
								$("#test_code").val(test_code);
								$("#final_result").val(avg);
								//$("#finalise_id").show();
								resultarr=[];
							});
						}
						$("#d2 tbody tr").css("cursor","pointer");
						$("#d2").show();
						$(".fsStyle2").show();
						$("#button_id").show();
					}
				});


			}
		});

	}

	function checkForRetest(sample_code){

		$("#avb").show();
		$("#approvedresultsmodal").show();

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

						$("#re_test").attr("disabled", false);
						$("#finalize").attr("disabled", false);
						$("#ral").show();
						$("#finalize").show();
						$("#oic").show();

					}
				}
			}
		});
	}


	function get_final_result(result,test_code1,test_name,test_type,r_id1,chem_code,duplicate_flag,sam_code)
	{
	 //Added by AkashT for selection of TR-->

			$("#d2 tbody ").find('tr').css("background-color","");
			$("#d2 tbody #"+r_id1).css("background-color","#9fc66f");
			$("#final").attr("disabled",false);
			$("#re_test").attr("disabled", false);
		  var sum_result=0;

	 //Added by AkashT for final,retest and average button clicks. [Global array accesing/Array for store-->

			glob_test_code    =  test_code1;
			glob_test_name    =  test_name;
			glob_chemist_code =  chem_code;
			glob_test_result  =  result;

	 //Added by AkashT for Array-->

			testcodearr.push(test_code1);
			testnamearr.push(test_name);
			testtypearr.push(test_type);
			   rowidarr.push(r_id1);

			var fr1=$("#final_result").val();
			$("#final").css("background-color", "#006400");
		  $("#re_test").css("background-color", "#006400");
		   $("#avrage").css("background-color", "");

		var d2_r_id=r_id1;
		for(i=0;i<resultarr.length;i++)
		{
			if(testtypearr[i]=='Formula')
			{
				$("#avrage").attr("disabled", false);
				sum_result=parseFloat(sum_result)+parseFloat(resultarr[i]);
			}
			else{
			//CHANGE DISPLAY PROPERTY OF AVRAGE BUTTON FALSE TO TURE, TO SOLVED AVRAGE BUTTON ISSUED. DONE BY PRAVIN BHAKARE ON 24-06-2019.
					$("#avrage").attr("disabled", true);

				}
		}

		if(testtypearr[0]=='Formula' )
		{
			$("#avrage").attr("disabled", false);
		}
		avg1=parseFloat(sum_result)/resultarr.length;
		avg=avg1.toFixed(4);
		if(fr1==avg){
			$(" #d2 tbody ").find('tr').css("background-color","#9fc66f");
			$("#final").css("background-color", "");
			$("#avrage").css("background-color", "#006400");
		}
	}



	function view1(){

					final_test = [];           //Added by AkashT for count test that are finalised. [Empty the Array].   //
					final_result_one= [];     //Added by AkashT for count++ test that are finalised. [Empty the Array]. //
					var test_code_123;       //Added by AkashT for count TEST-CODE that are finalised.                 //
					var test_result_123;    //Added by AkashT for count TEST-RESULT that are finalised.               //
					$("#avb").prop("hidden", false);
					$("#finalise_id").show();
					$("#finalize").show();
					$("#ral_id").hide();
					$("#cal_id").hide();
					$("#ho_id").hide();

					var i=1;
					$("#check_div tbody").empty();
					var sample_code    =  $("#sample_code").val();
					var category_name  =  $("#category_code").find(":selected").text();
					var commodity_name =  $("#commodity_code").find(":selected").text();
					var category_code  =  $("#category_code").val();
					var commodity_code =  $("#commodity_code").val();
					var button         =  $("#button").val();
		$.ajax({
			type: "POST",
			url: 'view_data',
			data: {button:button,sample_code:sample_code,category_code:category_code,commodity_code:commodity_code},
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
					if( sample_code){
						$("#categ_name1").show();
						 $("#comm_name1").show();
						$("#categ_name2").empty();
						 $("#comm_name2").empty();
						$("#categ_name2").append(category_name);
						 $("#comm_name2").append(commodity_name);
					}else{
						$("#categ_name1").show();
						 $("#comm_name1").show();
						$("#categ_name2").empty();
						 $("#comm_name2").empty();
					}

					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

					$.each(resArray, function (key, value){

						$.each(value,function (key1, value1){

		//Below code added by AkashT To get the test count, test code, test namees and final result array elements and the push the array-->
							var rowcontent	= "<tr><td>"+i+"</td>";

							//$.each( value1,function (key2, value2){
							    if(key1=='test_code'){
									final_test.push(value1);
									test_code_123 = value1;

								}else if(key1=='sample_code'){

								}else if(key1=='final_result'){
									test_result_123 = value1;
								}
								else {
									rowcontent	= rowcontent+"<td>"+value1+"</td>";
								}
							//});

							final_result_one.push(test_code_123 +':'+test_result_123);
							$("#d1 tbody").append(rowcontent);
						});
						i++;
					});
					getdetails();
				}
			}
		});
	}
