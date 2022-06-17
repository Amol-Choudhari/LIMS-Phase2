
$(document).ready(function(){
	
	// showing confirmation message for updating BEVO category
	$('#frm_category').on('submit', function(){
		alert();
		var $catCode = $('#category_code').val();

		if(add_user_validations()==false){
			e.preventDefault();
		}else{
			if($catCode=='106'){
				if(confirm('WARNING: You are updating BEVO category. This may affect already processed applications/Samples. Are you sure to update?') == 1){
					return true;
				} else {
					return false;
				}
			} else {

				$("#frm_category").submit();
			}

			
		}
		
	

	
		

	});

});
