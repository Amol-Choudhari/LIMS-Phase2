	var global_user;
	var global_designation;
	var designation;

	$('#loc_id').change(function (e) {

		if(fromusers()==false){
			e.preventDefault();
		}
	});

	$('#designation').change(function (e) {

		if(get_users_By_Loc_id()==false){
			e.preventDefault();
		}
	});


	$('#category_code').change(function (e) {

		if(get_commodity()==false){
			e.preventDefault();
		}
	});

	//to get default users list and selected, from desgination onchange ajax
	$("#designation").change();

	//to get user designation from selecting location dropdown
	function fromusers(){

		var loc_id = $("#loc_id").val();

		if(loc_id=="0"){

			$('#desDiv').hide();
			$('#xyz').html('');
			$("#xyz").html("<div class='form-group row'><label class='col-sm-3 col-form-label'>Name <span class='required-star'>*</span></label><div class='custom-file col-sm-9'><input type='text' name='name' id='name' placeholder='Enter a Name ' required class='form-control'></div></div><div class='form-group row'><label class='col-sm-3 col-form-label'>Address <span class='required-star'>*</span></label><div class='custom-file col-sm-9'><input type='text' name='address' id='address' placeholder='Enter address' required 	class='form-control'></div></div>");
		
		}else{

			$('#desDiv').show();
			$('#xyz').html('');
			$("#xyz").html("<div class='form-group row'><label class='col-sm-3 col-form-label'>Received From <span class='required-star'>*</span></label><div class='custom-file col-sm-9'><select class='form-control' name='users' id='users' ><option value='' >---Select---</option></select><span id='error_loc_id' class='error invalid-feedback'></span></div></div>");

			$("#designation").find('option').remove();
			var role_code = $("#designation").val();

			if(loc_id!="0"){

				$.ajax({
					type: "POST",
					url:"get_designation_By_Loc_id",
					data: {loc_id: loc_id},

					beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},

					success: function (data) {
						if(data.indexOf('[error]') !== -1){

							var msg =data.split('~');
							errormsg(msg[1]);
							return;

						}else{

							var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
							resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

							var op='';
							op += "<option value='0'>-----Select-----</option>";
							$.each(resArray, function (key, value) {

								var selected='';
								if(value['id']==global_designation){
									selected='selected';
								}else{
									selected='';
								}
								op += "<option value=" + value['id'] + " "+selected+">" + value['role']+ "</option>";
							});
						}

						//$('#designation option').remove();
						$('#designation').html(op);

						if(global_designation!=''){
							get_users_By_Loc_id();
						}

						global_designation='';
					}
				});
			}
		}
	}


	//to get users list according to designation from designation dropdown
	function get_users_By_Loc_id(){

		var receivedfrom = $("#receivedfrom").val();
		var loc_id = $("#loc_id").val();
		var role_code = $("#designation").val();

		if(role_code=='' || role_code==null){
			return;
		}

		if(loc_id!="0"){

			$.ajax({
				type: "POST",
				url:"get_users_By_Loc_id",
				data: {loc_id: loc_id,role_code:role_code},

				beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},

				success: function (data) {
					if(data.indexOf('[error]') !== -1){
						var msg =data.split('~');
						errormsg(msg[1]);
						return;
					}else{
						var op='';
						var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
							resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

						$.each(resArray, function (key, value) {
							var selected='';

							if(value['id']==receivedfrom)
								selected='selected';
							else
								selected='';

							op += "<option value=" + value['id'] + " "+selected+">" + value['f_name'] +" "+ value['l_name']+ "</option>";

						});
					}
					//global_user='';
					$('#users').html('');
					$('#users').html(op);
				}
			});
		}
	}


	//to get commodity list according to the category selected and load in the dropdown
	function get_commodity(){

		$("#commodity_code").find('option').remove();
		var commodity = $("#category_code").val();
		$.ajax({
			type: "POST",
			async:true,
			url:"../AjaxFunctions/show-commodity-dropdown",
			data: {commodity:commodity},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {
				$("#commodity_code").append(data);
			}
		});
	}


	$("#no_of_packets").focusout(function(e){

		var sample_type = $("#sample_type_code").val();
		//This condition i.e sample type 3 is added because to restrict the replica entry on the
		//on the packet entries as there is no replica entry for Commercial
		//sample type suggested by the DMI - Akash [29-11-2022]

		if (sample_type != 3) {
			if(get_serial_no()==false){
				e.preventDefault();
			}
		}
	});


	$(document).ready(function() {

		$('#smpl_drwl_dt').datepicker({
			endDate: '+0d',
			autoclose: true,
			todayHighlight: true,
			format: 'dd/mm/yyyy'
		});


		var datePicker = $('#smpl_drwl_dt').datepicker().on('changeDate', function(ev) {

			var org_sample_code = $("#org_sample_code").val();
			var date1=$("#smpl_drwl_dt").val();

			//if org sample code not in session
			if(org_sample_code != 'undefined'){

				var d=new Date(date1.split("/").reverse().join("-"));

				var dd=d.getDate();
				var mm=d.getMonth()+1;
				var yy=d.getFullYear();

				$.ajax({

					type:"POST",
					url:"check_inw_date",
					data:{org_sample_code:org_sample_code},
					cache:false,

					beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success : function(data)
					{
						var data = data.match(/~([^']+)~/)[1];

						if(data != 'NULL'){

							var d2=new Date(data.split("/").reverse().join("-"));
							var dd=d.getDate();
							var mm=d.getMonth()+1;
							var yy=d.getFullYear();

							var date2 = dd+"/"+mm+"/"+yy;
							if(d <= d2){

							}
							else{
								var msg="The Sample is received on "+data+", so the sample drawing date must be before this date. ";
								alert(msg);
								$("#smpl_drwl_dt").val('');
							}
						}
					}
				});
			}
		});
	});


	function get_serial_no(){

		var no_of_packets	= $("#no_of_packets").val();
		
		if(no_of_packets>15){
			$.alert("You can enter maximum 15.");
			return false;
		}

		var conveniancecount 	= $("div[name*='repDiv']").length;
		var originalCnt			= conveniancecount;
		var toAdd				= no_of_packets-conveniancecount;
		conveniancecount		= conveniancecount+1;
		var addCnt				= conveniancecount+toAdd;

		if(no_of_packets<(conveniancecount-1)){
			for(var i=originalCnt;i>no_of_packets;i--){
				$("#replica_serial_no_div"+i+"").remove();
			}
			return;
		}

		for(i=conveniancecount;i<addCnt;i++){
			$('#elementRow').append('<div class="col-md-3" id="replica_serial_no_div'+i+'" name="repDiv"></div>');
			$("#replica_serial_no_div"+i+"").append('<label>Replica Sr. No. '+ i +'</label><input type="text" name="replica_serial_no'+i+'" maxlength="25" id="replica_serial_no'+i+'" placeholder="Serial Number" class="form-control" >');
		}
	}

	var sample_status = $("#sample_status").val();

	if(sample_status != ''){

		var status_arr = ['R','D','P'];

		if($.inArray(sample_status,status_arr)== -1){
			
			$("input").attr('disabled',true);
			$("select").attr('disabled',true);
			$("textarea").attr('disabled',true);
		}
	}
