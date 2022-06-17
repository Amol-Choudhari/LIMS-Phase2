  $(document).ready(function(){
  //added on 06-05-2021 for profile pic
  bsCustomFileInput.init();

  //aadhar card validation
  //commented on 15-06-2018 by Amol, no provision to store aadhar
  /*	$('#once_card_no').focusout(function(){

    var once_card_no = $('#once_card_no').val();

    if(once_card_no.match(/^(?=.*[0-9])[0-9]{12}$/g) || once_card_no.match(/^[X-X]{8}[0-9]{4}$/i)){}else{//also allow if 8 X $ 4 nos found

      //alert("aadhar card number should be of 12 numbers only");
      $("#error_aadhar_card_no").show().text("Should not blank, Only numbers allowed, min & max length is 12");
      $("#error_aadhar_card_no").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
      $("#once_card_no").click(function(){$("#error_aadhar_card_no").hide().text;});
      return false;
    }
  });
  */


  $('#log_table').DataTable({"order": []});
  $('#user_logs_table').DataTable({"order": []});

  $("#profile_pic").change(function(){
    file_browse_onclick('profile_pic');
    return false;
  });



  $("#updateprofile").click(function(e){

  	if(add_user_validations()==false){
  		e.preventDefault();
  	}else{
  		$("#updateprofiledetails").submit();
  	}

  });


  $("#changepass").click(function(e){

  	if(change_password_validations()==false){
  		e.preventDefault();
  	}else{
  		$("#changepassform").submit();
  	}

  });

});
