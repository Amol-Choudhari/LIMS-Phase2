

	//This function is used for change password input validations.
	function change_password_validations(){

		// Empty Field validation
					
		var oldpass=$("#Oldpassword").val();
		var newpass=$("#Newpassword").val();
		var confpass=$("#confpass").val();
		
		var value_return = 'true';
		
		if(oldpass==""){
			
			$("#Oldpassword").addClass("is-invalid");
			$("#error_oldpass").show().text("Please enter your old password.");
			//setTimeout(function(){ $("#error_email").fadeOut();},8000);
			$("#Oldpassword").click(function(){$("#error_oldpass").hide().text; $(this).removeClass("is-invalid");});
			value_return = 'false';
		}
			
		if(newpass==""){
			
			$("#Newpassword").addClass("is-invalid");
			$("#error_newpass").show().text("Please enter your new password.");
			//setTimeout(function(){ $("#error_password").fadeOut();},8000);
			$("#Newpassword").click(function(){$("#error_newpass").hide().text; $(this).removeClass("is-invalid");});
			value_return = 'false';
		}
			
		if(confpass==""){
			
			$("#confpass").addClass("is-invalid");
			$("#error_confpass").show().text("Please confirm your new password.");
			//setTimeout(function(){ $("#error_password").fadeOut();},8000);
			$("#confpass").click(function(){$("#error_confpass").hide().text; $(this).removeClass("is-invalid");});
			value_return = 'false';
		}
			
		if(value_return == 'false'){

			// alert("Please check some fields are missing or not proper.");
			var msg = "Please check some fields are missing or not proper.";
			renderToast('error', msg);
			return false;
		
		}else{
			
			//old password encription
					
			var OldpasswordValue = document.getElementById('Oldpassword').value;
			var SaltValue = document.getElementById('hiddenSaltvalue').value;
			
			var OldpassEncryptpass = sha512(OldpasswordValue);
			
			var OldpassSaltedpass = SaltValue.concat(OldpassEncryptpass);
			
			var OldpassSaltedsha512pass = sha512(OldpassSaltedpass);

			document.getElementById('Oldpassword').value = OldpassSaltedsha512pass;
			
			//new password encription

			var NewpasswordValue = document.getElementById('Newpassword').value;

			if(NewpasswordValue.match(/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-zA-Z])[a-zA-Z0-9!@#$%^&*]{7,15}$/g))   
			{ 
				//alert('Password matched to the pattern'); 
			}
			else{
				var msg = "Password length should be min. 8 character, min. 1 number, min. 1 Special char. and min. 1 Capital Letter";
				renderToast('error', msg);
				return false;
				
			}
			
			var NewpassEncryptpass = sha512(NewpasswordValue);

			var NewpassSaltedpass = SaltValue.concat(NewpassEncryptpass);
			
			document.getElementById('Newpassword').value = NewpassSaltedpass;

			//Confirm password encription

			var ConfpassValue = document.getElementById('confpass').value;
			var ConfpassEncrypt = sha512(ConfpassValue);

			var ConfpassSalted = SaltValue.concat(ConfpassEncrypt);
			
			document.getElementById('confpass').value = ConfpassSalted;
			document.getElementById('hiddenSaltvalue').value = '';
			
			exit();
			
		}
		
	}

		//This function is used for Forgot password input validations.
		function forgot_password_validations(){
			
			// Empty Field validation
			var customer_id=$("#customer_id").val();
			var email=$("#email").val();
			var captchacode=$("#captchacode").val();
			
			var value_return = 'true';
			
			if(customer_id==""){
					
				$("#error_customer_id").show().text("Please enter Applicant Id");
				// $("#error_customer_id").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				//setTimeout(function(){ $("#error_customer_id").fadeOut();},8000);
				$("#customer_id").addClass("is-invalid");
				$("#customer_id").click(function(){$("#error_customer_id").hide().text; $("#customer_id").removeClass("is-invalid");});

				value_return = 'false';
			}
				
			if(email==""){
				
				$("#error_email").show().text("Please enter your email.");
				// $("#error_email").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				//setTimeout(function(){ $("#error_email").fadeOut();},8000);
				$("#email").addClass("is-invalid");
				$("#email").click(function(){$("#error_email").hide().text; $("#email").removeClass("is-invalid");});

				value_return = 'false';
			}
			else{
				
				if(!email.match(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/))   
				{ 
					
					$("#error_email").show().text("Entered email id is not valid.");
					// $("#error_email").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					//setTimeout(function(){ $("#error_email").fadeOut();},8000);
					$("#email").addClass("is-invalid");
					$("#email").click(function(){$("#error_email").hide().text; $("#email").removeClass("is-invalid");});
					value_return = 'false';
					
				}
			
			}
				
			if(captchacode==""){
				
				$("#error_captchacode").show().text("Please enter Captcha code.");
				// $("#error_captchacode").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				//setTimeout(function(){ $("#error_captchacode").fadeOut();},8000);
				$("#captchacode").addClass("is-invalid");
				$("#captchacode").click(function(){$("#error_captchacode").hide().text; $("#captchacode").removeClass("is-invalid");});

				value_return = 'false';
			}
			
			if(value_return == 'false')
			{
				// alert("Please check some fields are missing or not proper.");
				var msg = "Please check some fields are missing or not proper.";
				renderToast('error', msg);
				return false;
			}
			else{

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
			$("#error_newpass").css({"color":"red","font-size":"14px"});
			//setTimeout(function(){ $("#error_password").fadeOut();},8000);
			$("#Newpassword").click(function(){$("#error_newpass").hide().text;});
			value_return = 'false';
			}
			
		else if(confpass==""){
			
			$("#error_confpass").show().text("Please confirm your new password.");
			$("#error_confpass").css({"color":"red","font-size":"14px"});
			//setTimeout(function(){ $("#error_password").fadeOut();},8000);
			$("#confpass").click(function(){$("#error_confpass").hide().text;});
			value_return = 'false';
			}
			
		else if(captchacode==""){
			
			$("#error_captchacode").show().text("Please enter your verification code.");
			$("#error_captchacode").css({"color":"red","font-size":"14px"});
			//setTimeout(function(){ $("#error_email").fadeOut();},8000);
			$("#captchacode").click(function(){$("#error_captchacode").hide().text;});
			value_return = 'false';
			}



		//new password encription

				var NewpasswordValue = document.getElementById('Newpassword').value;
				
				if(NewpasswordValue.match(/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[A-Z])(?=.*[a-z])[a-zA-Z0-9!@#$%^&*]{8,15}$/g))   
				{ 
					
				}
				else{
					alert('Password length should be min. 8 char, min. 1 number, min. 1 Special char. and min. 1 Capital Letter');
					return false;
					
				}
				
				
				var SaltValue = document.getElementById('hiddenSaltvalue').value;
				var NewpassEncryptpass = sha512(NewpasswordValue);
				
				var NewpassSaltedpass = SaltValue.concat(NewpassEncryptpass);

				document.getElementById('Newpassword').value = NewpassSaltedpass;
				
				

		//Confirm password encription

				var ConfpassValue = document.getElementById('confpass').value;
				var ConfpassEncrypt = sha512(ConfpassValue);

				var ConfpassSalted = SaltValue.concat(ConfpassEncrypt);
				
				document.getElementById('confpass').value = ConfpassSalted;
				
				
				
				
				if(value_return == 'false')
				{
					alert("Please check some fields are missing or not proper.");
					return false;
				}
				else{
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
			$("#error_size_".concat(field_id)).css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
			//setTimeout(function(){ $("#error_size_".concat(field_id)).fadeOut();},8000);
			$("#".concat(field_id)).click(function(){$("#error_size_".concat(field_id)).hide().text;});
			$('#'.concat(field_id)).val('')
			
			value_return = 'false';

		}
		
		
		if (ext_type_array.lastIndexOf(get_file_ext) == -1){
			
			
			$("#error_type_".concat(field_id)).show().text("Please select file of jpg, pdf type only");
			$("#error_type_".concat(field_id)).css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
			//setTimeout(function(){ $("#error_type_".concat(field_id)).fadeOut();},8000);
			$("#".concat(field_id)).click(function(){$("#error_type_".concat(field_id)).hide().text;});
			$('#'.concat(field_id)).val('');
			
			value_return = 'false';
		
		}
		
		if(value_return == 'false')
			{
				return false;
			}
			else{
				exit();			
			}
		
	}


	// function for whitespace and blank value validation by pravin 10-07-2017
	function check_whitespace_validation_textarea(field_value){
		
		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and maximum 180 characters allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value != "")
		{
			if(field_length == update_field_value)
			{
				if(field_length <= 180)
				{
					return true;
				}
					return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}else{
				return {result: false, error_message: error_message1};
			 }
		
	}
	
	
	// function for whitespace and blank value validation by pravin 10-07-2017
	function check_whitespace_validation_textarea(field_value){
		
		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and maximum 180 characters allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value != "")
		{
			
			//if(field_length == update_field_value)
			//{
				
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				if(field_length <= 180)
				{
					return true;
				}
					return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}else{
				return {result: false, error_message: error_message1};
		}
		
	}

	
	// function for whitespace and blank value validation by pravin 10-07-2017
	function check_whitespace_validation_textbox(field_value){
		
		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and maximum 50 characters allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value != "")
		{
			//if(field_length == update_field_value)
			//{
			
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				if(field_length <= 50)
				{
					return true;
				}
				
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}else{
			
			return {result: false, error_message: error_message1};
		}
		
	}
	
	
	// function for Alpha character, whitespace character and blank value validation by pravin 10-07-2017
	function check_alpha_character_validation(field_value){
		
		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and maximum 50 character alphabets value allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^[A-z ]{1,50}$/) == null)
		{
			
			return {result: false, error_message: error_message1};
			
		}else{
			
			//if(field_length == update_field_value){
				
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				
				return true;
			
			}else{
				
				return {result: false, error_message: error_message1};
			}
		}
		
	}
	
	
	// function for number validation by pravin 10-07-2017
	function check_number_validation(field_value)
	{	
		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and maximum 20 numeric value allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^(?=.*[0-9])[0-9]{1,20}$/g) == null)
		{
			//if(field_length == update_field_value)
			//{
				
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
				
		}				
		
		return true;
	}
	
	
	// function for email validation by pravin 10-07-2017
	function check_email_validation(field_value)
	{
		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'Please enter valid email address like(abc@gmail.com)';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/) == null)
		{
			//if(field_length == update_field_value)
			//{
				
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}				
		
		return true;
	}
	
	
	// function for aadhar validation by pravin 10-07-2017
	function check_aadhar_validation(field_value)
	{
		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and 12 digit numeric value required like(526548547512)';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^(?=.*[0-9])[0-9]{12}$/g) == null)
		{
			//if(field_length == update_field_value)
			//{
				
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}				
		
		return true;
	}
	
	
	// function for number with decimal two validation by pravin 10-07-2017
	function check_number_with_decimal_two_validation(field_value){

		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and maximum 20 digit numeric value with 2 decimal point allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^\d{1,25}(\.\d{1,2})?$/) == null){
		
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}				
		
		return true;
	}

	
	// function for number with decimal four validation by pravin 10-07-2017
	function check_number_with_decimal_four_validation(field_value){

		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and maximum 20 digit numeric value with 4 decimal point allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^\d{1,25}(\.\d{1,4})?$/) == null){
	
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0)
			{	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}				
		
		return true;
	}	
	
	// function for mobile number validation by pravin 10-07-2017
	function check_mobile_number_validation(field_value){

		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and 10 digit numeric value required like(9638527412)';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^(?=.*[0-9])[0-9]{10}$/g) == null){

			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0){	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}				
		
		return true;
	}
	
	
	// function for landline number validation by pravin 10-07-2017
	function check_landline_number_validation(field_value){

		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and Min. 6 and Max. 12 digit numeric value allowed like(071222656880)';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^(?=.*[0-9])[0-9]{6,12}$/g) == null){
		
			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0){	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}				
		
		return true;
	}
	
	
	// function for postal code number validation by pravin 10-07-2017
	function check_postal_code_validation(field_value){

		var field_length = field_value.length;
		var field_trim = $.trim(field_value);
		var update_field_value = field_trim.length;
		var error_message1 = 'This field is mandatory and 6 digit numeric value allowed';
		var error_message2 = 'Please Remove blank space before and after the text';
		
		if(field_value.match(/^(?=.*[0-9])[0-9]{6}$/g) == null){

			// change validation rule for whitespace after and before word by pravin 04-08-2017
			if(update_field_value > 0){	
				return {result: false, error_message: error_message1};
			}
				return {result: false, error_message: error_message1};
		}				
		
		return true;
	}
	
	
	// function for blank file upload validation by pravin 10-07-2017
	function check_file_upload_validation(field_value){

		var error_message = 'Please upload the required file';
		
		if(field_value == ""){

			return {result: false, error_message:error_message };
		}				
		
		return true;
	}
	
	
	// function for drop_down validation by pravin 10-07-2017
	function check_drop_down_validation(field_value){

		var error_message = 'Please select the required valid option';
		
		if(field_value == ""){

			return {result: false, error_message:error_message};
		}				
		
		return true;
	}
	
	
	// function for radio button validation by pravin 10-07-2017
	function check_radio_button_validation(field_value){

		var error_message = 'Please select the option';
		
		if($('input[name="data['+field_value+']"]:checked').val() != "yes" && $('input[name="data['+field_value+']"]:checked').val() != "no"){
			
			return {result: false, error_message:error_message};
			
		}
		
		return true;
	}
	
	
	// function for radio value validation by pravin 10-07-2017
	function check_radio_value(field_value){

		 if($('input[name="data['+field_value+']"]:checked').val() == "yes"){
					
			return 'yes';
					
		}else if ($('input[name="data['+field_value+']"]:checked').val() == "no"){
							
			return 'no';
							
		}
		
	}
	
	
	

	
	


// DISPLAY FORM RELATED ALERTS/MESSAGES IN NEW TEMPLATE
// By Aniket Ganvir dated 10th DEC 2020
function renderToast(theme, msgTxt) {

	$('#toast-msg-'+theme).html(msgTxt);
	$('#toast-msg-box-'+theme).fadeIn('slow');
	$('#toast-msg-box-'+theme).delay(3000).fadeOut('slow');

}