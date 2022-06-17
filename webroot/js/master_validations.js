// Master Validations 

////This function is used for add_user, edit_user & user_profile form validations(admin users)
	function category_master(){

		var category_name=$("#category_name").val();
		var l_category_name=$("#l_category_name").val();
		var min_quantity=$("#min_quantity").val();
		var value_return = 'true';

		if(category_name==""){

			$("#error_category_name").show().text("Please Enter Category Name");
			$("#category_name").addClass("is-invalid");
			$("#category_name").click(function(){$("#error_category_name").hide().text;$("#category_name").removeClass("is-invalid");});
			value_return = 'false';

		}

		if(l_category_name==""){

			$("#error_l_name").show().text("Please Enter Category Name (हिन्दी)");
			$("#l_category_name").addClass("is-invalid");
			$("#l_category_name").click(function(){$("#error_l_name").hide().text;$("#l_category_name").removeClass("is-invalid");});
			value_return = 'false';

		}


		if(min_quantity==""){

			$("#error_email").show().text("Please Enter Minimum Quantity To Be Graded");
			$("#min_quantity").addClass("is-invalid");
			$("#min_quantity").click(function(){$("#error_email").hide().text;$("#min_quantity").removeClass("is-invalid");});
			value_return = 'false';

		}else{

			if(email.match(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/)){}else{

				$("#error_email").show().text("Please Enter Valid Email Address");
				$("#email").addClass("is-invalid");
				$("#email").click(function(){$("#error_email").hide().text;$("#email").removeClass("is-invalid");});
				value_return = 'false';
			}

		}

		if(value_return == 'false'){
			var msg = "Please check some fields are missing or not proper.";
			renderToast('error', msg);
			return false;
		}else{
			exit();
		}

	}