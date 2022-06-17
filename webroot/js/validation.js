
function validations(){
	
// Empty fields validations starts
	
	//alert('validation js running');

var f_name=$("#f_name").val();
var m_name=$("#m_name").val();
var l_name=$("#l_name").val();
var email=$("#email").val();
var phone=$("#phone").val();
var designation=$("#designation").val();
var password=$("#passwordValidation").val();
var confirm_password=$("#confpassValidation").val();
var firm_name=$("#firm_name").val();
var address=$("#address").val();
var city_name=$("#city_name").val();
var state_name=$("#state_name").val();
var commodity=$("#commodity").val();
var selected_sub_commodity=$("#selected_sub_commodity").val();
var documents=$("#documents").val();
var doc_file=$("#doc_file").val();
var once_card_no=$("#once_card_no").val();
var captchacode=$("#captchacode").val();


if(f_name==""){
	alert("Some Fields are missing");
$("#error_f_name").show().text("Please enter your first name.");
$("#error_f_name").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_f_name").fadeOut();},8000);
$("#f_name").click(function(){$("#error_f_name").hide().text;});
return false;
}


else if(m_name==""){
alert("Some Fields are missing");
$("#error_m_name").show().text("Please enter your middle name.");
$("#error_m_name").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_m_name").fadeOut();},8000);
$("#m_name").click(function(){$("#error_m_name").hide().text;});
return false;
}


else if(l_name==""){
	alert("Some Fields are missing");
$("#error_l_name").show().text("Please enter your last name.");
$("#error_l_name").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_l_name").fadeOut();},8000);
$("#l_name").click(function(){$("#error_l_name").hide().text;});
return false;
}

else if(email==""){
	alert("Some Fields are missing");
$("#error_email").show().text("Please enter your email.");
$("#error_email").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_email").fadeOut();},8000);
$("#email").click(function(){$("#error_email").hide().text;});
return false;
}

else if(phone==""){
	alert("Some Fields are missing");
$("#error_phone").show().text("Please enter your Phone No.");
$("#error_phone").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_phone").fadeOut();},8000);
$("#phone").click(function(){$("#error_phone").hide().text;});
return false;
}

else if(designation==""){
	alert("Some Fields are missing");
$("#error_designation").show().text("Please enter your designation.");
$("#error_designation").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_designation").fadeOut();},8000);
$("#designation").click(function(){$("#error_designation").hide().text;});
return false;
}

else if(password==""){
	alert("Some Fields are missing");
$("#error_password").show().text("Please enter your password.");
$("#error_password").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_password").fadeOut();},8000);
$("#passwordValidation").click(function(){$("#error_password").hide().text;});
return false;
}

else if(confirm_password==""){
	alert("Some Fields are missing");
$("#error_confirm_password").show().text("Please enter confirm password.");
$("#error_confirm_password").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_confirm_password").fadeOut();},8000);
$("#confpassValidation").click(function(){$("#error_confirm_password").hide().text;});
return false;
}

else if(firm_name==""){
	alert("Some Fields are missing");
$("#error_firm_name").show().text("Please enter your Firm name.");
$("#error_firm_name").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_firm_name").fadeOut();},8000);
$("#firm_name").click(function(){$("#error_firm_name").hide().text;});
return false;
}


else if(address==""){
	alert("Some Fields are missing");
$("#error_address").show().text("Please enter your address.");
$("#error_address").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_address").fadeOut();},8000);
$("#address").click(function(){$("#error_address").hide().text;});
return false;
}


else if(city_name==""){
	alert("Some Fields are missing");
$("#error_city_name").show().text("Please enter your City.");
$("#error_city_name").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_city_name").fadeOut();},8000);
$("#city_name").click(function(){$("#error_city_name").hide().text;});
return false;
}


else if(state_name==""){
	alert("Some Fields are missing");
$("#error_state_name").show().text("Please enter your State.");
$("#error_state_name").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_state_name").fadeOut();},8000);
$("#state_name").click(function(){$("#error_state_name").hide().text;});
return false;
}


/* else if(commodity==""){
	alert("Some Fields are missing");
$("#error_commodity").show().text("Please Select your commodity.");
$("#error_commodity").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_commodity").fadeOut();},8000);
$("#commodity").click(function(){$("#error_commodity").hide().text;});
return false;
}


else if(selected_sub_commodity==""){
	alert("Some Fields are missing");
$("#error_sub_commodity").show().text("Please Select your Sub commodity.");
$("#error_sub_commodity").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_sub_commodity").fadeOut();},8000);
$("#selected_sub_commodity").click(function(){$("#error_sub_commodity").hide().text;});
return false;
} */


else if(documents==""){
	alert("Some Fields are missing");
$("#error_documents").show().text("Please Select your Document.");
$("#error_documents").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_documents").fadeOut();},8000);
$("#documents").click(function(){$("#error_documents").hide().text;});
return false;
}


else if(doc_file==""){
	alert("Some Fields are missing");
$("#error_doc_file").show().text("Please Select your Document file.");
$("#error_doc_file").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_doc_file").fadeOut();},8000);
$("#doc_file").click(function(){$("#error_doc_file").hide().text;});
return false;
}



else if(captchacode==""){
	alert("Some Fields are missing");
$("#error_captchacode").show().text("Please enter your password.");
$("#error_captchacode").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
//setTimeout(function(){ $("#error_password").fadeOut();},8000);
$("#captchacode").click(function(){$("#error_captchacode").hide().text;});
return false;
}


else if(once_card_no==""){
	alert("Some Fields are missing");
	$("#error_aadhar_card_no").show().text("Please enter aadhar card no.");
	$("#error_aadhar_card_not").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
	//setTimeout(function(){ $("#error_password").fadeOut();},8000);
	$("#once_card_no").click(function(){$("#error_aadhar_card_no").hide().text;});
	return false;
}

// Empty fields validations ends




// Client side password Encription starts

var PasswordValue = document.getElementById('passwordValidation').value;

		//alert(PasswordValue);
		//var Passwordcheck = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{7,15}$/g; 
		//alert(Passwordcheck);
		
	
		if(PasswordValue.match(/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-zA-Z])[a-zA-Z0-9!@#$%^&*]{7,15}$/g))   
		{ 
			//alert('Password matched to the pattern'); 
		}
		else{
			alert('Password length should be min. 8 character, min. 1 number, min. 1 Special char. and min. 1 Capital Letter');
			return false;
			
		}
	
								var SaltValue = document.getElementById('hiddenSaltvalue').value;
								var EncryptPass = calcMD5(PasswordValue);
			
								var SaltedPass = SaltValue.concat(EncryptPass);
								
								document.getElementById('passwordValidation').value = SaltedPass;
								
								//alert(EncryptPass);
								//exit();
								
								
								var ConfpassValue = document.getElementById('confpassValidation').value;
								var SaltValue = document.getElementById('hiddenSaltvalue').value;
								var EncryptConfPass = calcMD5(ConfpassValue);
			
								var SaltedConfPass = SaltValue.concat(EncryptConfPass);
								
								document.getElementById('confpassValidation').value = SaltedConfPass;
								
								//alert(EncryptConfPass);
								exit();

// Client side password Encription ends



}




