

//This function is used for applicant login validations.
function login_customer_validations(){

	var customer_id=$("#customer_id").val();
	var password=$("#passwordValidation").val();
	var captchacode=$("#captchacode").val();

	var value_return = 'true';

	if(customer_id==""){

		$("#error_customer_id").show().text("Please Enter First Name");
		$("#customer_id").addClass("is-invalid");
		$("#customer_id").click(function(){$("#error_customer_id").hide().text;$("#customer_id").removeClass("is-invalid");});
		value_return = 'false';
	}

	if(password==""){

		$("#error_password").show().text("Please Enter First Name");
		$("#passwordValidation").addClass("is-invalid");
		$("#passwordValidation").click(function(){$("#error_password").hide().text;$("#passwordValidation").removeClass("is-invalid");});
		value_return = 'false';

	}

	if(captchacode==""){

		$("#error_captchacode").show().text("Please Enter First Name");
		$("#captchacode").addClass("is-invalid");
		$("#captchacode").click(function(){$("#error_captchacode").hide().text;$("#captchacode").removeClass("is-invalid");});
		value_return = 'false';
	}

	if(value_return == 'false'){

		var msg = "Please Check Some Fields are Missing or not Proper.";
		renderToast('error', msg);
		return false;
	
	}else{

		var PasswordValue = document.getElementById('passwordValidation').value;
		var SaltValue = document.getElementById('hiddenSaltvalue').value;
		var EncryptPass = sha512(PasswordValue);

		var SaltedPass = SaltValue.concat(EncryptPass);

		var Saltedsha512pass = sha512(SaltedPass);

		document.getElementById('passwordValidation').value = Saltedsha512pass;

		exit();
	}


}


	//This function is used for change password input validations.
	function change_password_validations(){

		// Empty Field validation
		var oldpass=$("#Oldpassword").val();
		var newpass=$("#Newpassword").val();
		var confpass=$("#confpass").val();

		var value_return = 'true';

		if(oldpass==""){

			$("#error_oldpass").show().text("Please enter your old password.");
			$("#Oldpassword").addClass("is-invalid");
			$("#Oldpassword").click(function(){$("#error_oldpass").hide().text;$("#Oldpassword").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(newpass==""){

			$("#error_newpass").show().text("Please enter your new password.");
			$("#Newpassword").addClass("is-invalid");
			$("#Newpassword").click(function(){$("#error_newpass").hide().text;$("#Newpassword").removeClass("is-invalid");});
			value_return = 'false';
		}

		if(confpass==""){

			$("#error_confpass").show().text("Please confirm your new password.");
			$("#confpass").addClass("is-invalid");
			$("#confpass").click(function(){$("#error_confpass").hide().text;$("#confpass").removeClass("is-invalid");});
			value_return = 'false';
		}

		//added this condition on 10-02-2021 by Amol
		var user_id = $("#user_id").val();
		if(newpass==user_id){

			$.alert('Please Note: You can not use your User Id as your password');
			$("#Newpassword").val('');//clear field
			$("#confpass").val('');
			value_return = 'false';
		}


		if(value_return == 'false')
		{
			var msg = "Please check some fields are missing or not proper.";
			renderToast('error', msg);
			return false;
		}
		else{

			//old password Encryption

			var OldpasswordValue = document.getElementById('Oldpassword').value;
			var SaltValue = document.getElementById('hiddenSaltvalue').value;
			var OldpassEncryptpass = sha512(OldpasswordValue);
			var OldpassSaltedpass = SaltValue.concat(OldpassEncryptpass);
			var OldpassSaltedsha512pass = sha512(OldpassSaltedpass);
			document.getElementById('Oldpassword').value = OldpassSaltedsha512pass;

			//new password Encryption
			var NewpasswordValue = document.getElementById('Newpassword').value;

			if(NewpasswordValue.match(/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-zA-Z])[a-zA-Z0-9!@#$%^&*]{7,15}$/g))
			{
				//alert('Password matched to the pattern');
			}
			else{
				$.alert({

					title: 'Alert!',
					type: 'red',
					columnClass: 'medium',
					content	:'Password length should be min. 8 character, min. 1 number, min. 1 Special char. and min. 1 Capital Letter'
				});
				var oldpass=$("#Oldpassword").val();
				var newpass=$("#Newpassword").val();
				var confpass=$("#confpass").val();

				return false;

			}

			var NewpassEncryptpass = sha512(NewpasswordValue);

			var NewpassSaltedpass = SaltValue.concat(NewpassEncryptpass);

			document.getElementById('Newpassword').value = NewpassSaltedpass;


			//Confirm password Encryption

			var ConfpassValue = document.getElementById('confpass').value;
			var ConfpassEncrypt = sha512(ConfpassValue);
			var ConfpassSalted = SaltValue.concat(ConfpassEncrypt);
			document.getElementById('confpass').value = ConfpassSalted;
			document.getElementById('hiddenSaltvalue').value = '';
			exit();

		}


	}




	//File validation common function
	//This function is called on file upload browse button to validate selected file
	function file_browse_onclick(field_id){

		var selected_file = $('#'.concat(field_id)).val();
		var ext_type_array = ["jpg" , "pdf"];

		var get_file_size = $('#'.concat(field_id))[0].files[0].size;
		var get_file_ext = selected_file.split(".");

		var value_return = 'true';

		get_file_ext = get_file_ext[get_file_ext.length-1].toLowerCase();

		if(get_file_size > 2097152)
		{

			$("#error_size_".concat(field_id)).show().text("Please select file below 2mb");
			$("#error_size_".concat(field_id)).addClass("is-invalid");
			$("#".concat(field_id)).click(function(){$("#error_size_").hide().text;$("#".concat(field_id)).removeClass("is-invalid");});
			$('#'.concat(field_id)).val('');

			value_return = 'false';

		}


		if (ext_type_array.lastIndexOf(get_file_ext) == -1){


			$("#error_type_".concat(field_id)).show().text("Please select file of jpg, pdf type only");
			$("#error_type_".concat(field_id)).addClass("is-invalid");
			$("#".concat(field_id)).click(function(){$("#error_type_").hide().text;$("#".concat(field_id)).removeClass("is-invalid");});
			$('#'.concat(field_id)).val('');

			value_return = 'false';

		}

		if(value_return == 'false'){
			return false;
		}else{
			exit();
		}

	}





	//This function is used for Reset password input validations.
	function reset_password_validations(){
		
		var newpass=$("#Newpassword").val();
		var confpass=$("#confpass").val();
		var captchacode=$("#captchacode").val();

		var value_return = 'true';

			
		if(newpass==""){
			
			$("#error_newpass").show().text("Please enter your new password.");
			$("#Newpassword").addClass("is-invalid");
			$("#Newpassword").click(function(){$("#error_newpass").hide().text;$("#Newpassword").removeClass("is-invalid");});
			value_return = 'false';
		}
			
		if(confpass==""){
			
			$("#error_confpass").show().text("Please confirm your new password.");
			$("#confpass").addClass("is-invalid");
			$("#confpass").click(function(){$("#error_confpass").hide().text;$("#confpass").removeClass("is-invalid");});
			value_return = 'false';
		}
			
		if(captchacode==""){
			
			$("#error_captchacode").show().text("Please enter your verification code.");
			$("#captchacode").addClass("is-invalid");
			$("#captchacode").click(function(){$("#error_captchacode").hide().text;$("#captchacode").removeClass("is-invalid");});
			value_return = 'false';
		}


		//added this condition on 10-02-2021 by Amol
			var user_id = $("#user_id").val();
			if(newpass==user_id){
				
				$.alert('Please Note: You can not use your User Id as your password');
				$("#Newpassword").val('');//clear field
				$("#confpass").val('');
				value_return = 'false';
			}
				
				
			if(value_return == 'false')
			{
				$.alert("Please check some fields are missing or not proper.");
				return false;
			}
			else{
				
				//new password Encryption

				var NewpasswordValue = document.getElementById('Newpassword').value;
				
				if(NewpasswordValue.match(/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[A-Z])(?=.*[a-z])[a-zA-Z0-9!@#$%^&*]{8,15}$/g))   
				{ 
					
				}
				else{
					$.alert('Password length should be min. 8 char, min. 1 number, min. 1 Special char. and min. 1 Capital Letter');
					return false;
					
				}
				
				
				var SaltValue = document.getElementById('hiddenSaltvalue').value;
				var NewpassEncryptpass = sha512(NewpasswordValue);
				
				var NewpassSaltedpass = SaltValue.concat(NewpassEncryptpass);

				document.getElementById('Newpassword').value = NewpassSaltedpass;
				
				

			//Confirm password Encryption

				var ConfpassValue = document.getElementById('confpass').value;
				var ConfpassEncrypt = sha512(ConfpassValue);

				var ConfpassSalted = SaltValue.concat(ConfpassEncrypt);
				
				document.getElementById('confpass').value = ConfpassSalted;
				document.getElementById('hiddenSaltvalue').value = '';
				
				exit();
				
			}
		
		
	}



	// Validate parameters that are used in sms message, if it is in pre-defined parameter list. (Done By Pravin 09-03-2018)
	function sms_message_parameter_validation(masterId=null){

		var form_validation = masters_validation(masterId);
		if(form_validation == true){
			var sms_parameter_list = ['submission_date','firm_name','amount','commodities','applicant_name','applicant_mobile_no','company_id',
									'certificate_valid_upto','premises_id','firm_email','firm_certification_type','ro_name','ro_mobile_no','ro_office','ro_email_id',
									'mo_name','mo_mobile_no','mo_office','mo_email_id','io_name','io_mobile_no','io_office','io_email_id','dyama_name','dyama_mobile_no',
									'dyama_email_id','jtama_name','jtama_mobile_no','jtama_email_id','ama_name','ama_mobile_no','ama_email_id','io_scheduled_date','applicant_email',
									'home_link','pao_name','pao_mobile_no','pao_email_id','ho_mo_name','ho_mo_mobile_no','ho_mo_email_id']
			var sms_message = $('#sms_message').val();
			var total_occurrences = substr_count(sms_message,'%%');
			var parameter_not_inarray = '';
			while (total_occurrences > 0) {
				//var matches = sms_message.substr(sms_message.indexOf('%%')+1);
				var matches = sms_message.split("%%");

				if(matches[1]){

					var result = inArray(matches[1], sms_parameter_list);

					if(result == false){
						var parameter_not_inarray = parameter_not_inarray + '%%'+matches[1]+'%%' +', ';
					}
					var replace_value = '%%'+matches[1]+'%%';
					var sms_message = sms_message.replace(replace_value, matches[1]);
					var total_occurrences = substr_count(sms_message,'%%');

				}
			}
			if(parameter_not_inarray){
				$("#error_sms_message").show().text("The parameter "+parameter_not_inarray+" is/are not defined. You can only use the pre-defined parameters.");
				$("#error_sms_message").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				$("#sms_message").click(function(){$("#error_sms_message").hide().text;});
				return false;

			}else{ exit(); }

		}else{
			return false;
		}

	}


	function substr_count(string,substring,start,length)
	{
	var c = 0;
	if(start) { string = string.substr(start); }
	if(length) { string = string.substr(0,length); }
	for (var i=0;i<string.length;i++)
	{
	if(substring == string.substr(i,substring.length))
	c++;
	}
	return c;
	}

	function inArray(str_value, str_array) {
		var length = str_array.length;
		for(var i = 0; i < length; i++) {
			if(str_array[i] == str_value) return true;
		}
		return false;
	}

	// DISPLAY FORM RELATED ALERTS/MESSAGES IN NEW TEMPLATE
	// By Aniket Ganvir dated 10th DEC 2020
	function renderToast(theme, msgTxt) {

		$('#toast-msg-'+theme).html(msgTxt);
		$('#toast-msg-box-'+theme).fadeIn('slow');
		$('#toast-msg-box-'+theme).delay(3000).fadeOut('slow');

	}
