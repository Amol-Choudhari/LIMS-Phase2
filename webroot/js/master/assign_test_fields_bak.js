$(document).ready(function(){

	var test_code = $("#test_code").val();
	var pre_checked_count = 0 ;

    $.ajax({
    	type: 'POST',         
		url: 'get_test_fields_data',
     	data:{test_code:test_code},
     	async:true,
	    cache:false,
	    beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    },
    	success: function (data) {
		
            $.each($.parseJSON(data), function (key1, val1) {
					

					$("#input_parameter_text #"+val1['field_code']).prop( "checked", true );
					$("#input_parameter_text #"+val1['field_code']).prop( "disabled", true );//this line added on 02-01-2020 by Amol, to not change already checked
					
					$( "#text_"+val1['field_code'] ).val(val1['field_validation']);
					$( "#unit_"+val1['field_code'] ).val(val1['field_unit']);
					$( "#tr_"+val1['field_code']).addClass('assignfieldcolor');

					if(val1['field_type']=='D')
						{
						 $( "#dep_"+val1['field_code'] ).prop( "checked", true );
						 $( "#dep_"+val1['field_code'] ).prop( "disabled", true );//this line added on 02-01-2020 by Amol, to not change already checked
						}							
						
					pre_checked_count = parseInt(pre_checked_count)+1;
			 });
		}
	});



    $("#assigntestform").submit(function(e)
    { 
        e.preventDefault();
		
        var str="";
        var str1="";
        var key;
        var flag=false;
        var id;
        var final_str="";
        var total=$(this).find('input[name="checkboxArray[]"]:checked').length;
        $("#save").prop("disabled", true);
		
		$.each($("input[name='checkboxArray[]']:checked"), function()
		{
			var number="";
			id=$(this).attr("id");
			var val=$("#text_"+id).val();
			var unit=$("#unit_"+id).val();
			var dep_value=$("#dep_"+id+":checked").length > 0;
			
			if(!val){
				val=0;
			}

			if(!unit){
				unit=0;
			}

			var dep_val=0;

			if(dep_value==true)
			{
				dep_val=1;
			}
			else
			{
				dep_val=0;
			}

			//alert(dep_val);
			var data=val.split(",");
			var first_num=data[0];
			var precision=data[1];

			for(var i=0;i<first_num;i++)
			{
				number+="9";
			} 

			if(precision>0)
			{
				number+="."
				for(var j=0;j<precision;j++)
					{
					number+="9";
					}
			}		

			str+="<li>"+$(this).val()+"(Eg:range "+number+")</li>";

			if(val=="")
			{
				flag=true;
			}

			final_str=id+"~"+val+"~"+dep_val+"~"+unit;
			str1+=final_str+"-";
		});



		
		str1=str1.replace(/-\s*$/, "");


		if(!flag)
		{
			
			var field_arr=str1;
			var test_code=$("#test_code").val();

			if(str1=='')
			{	alert('No field is selected to assigned this test, Please select then click save.');
				//above line added on 01-01-2020 and below code removed, which was saving records if no field selected.
				//by Amol
				
			}else{

				//added below code with new condition to check total with count on 01-01-2020 by Amol
				var count = pre_checked_count
				pre_checked_count = 0;
				
				//added this if else on 02-01-2020 by Amol for validations.
				if(count != total){

					
						
						if(!confirm("As you are updating existing test fields, it is mandatory to update the formula for this test from Create formula master"))
						{
							location.reload();							
						}else{
							

							$.ajax({
								type: "POST",								
								url: 'saved-assigned-test-fields',
								data:{test_code:test_code,field_arr:field_arr},
								beforeSend: function (xhr) { // Add this line
							          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
							    },
								success: function (data) {
									
									if(data=='1')
									{
										var msg="Selected fields assigned successfully";
										alert(msg);	
										location.reload();										
									}
									else{
										var msg="Error in assigned selected fields";
										alert(msg);
										
									}
									
									
								}
							});
							
												
						}

					
				}else if(count == total){
					
					$.ajax({
						type: "POST",						
						url: 'saved-assigned-test-fields',
						data:{test_code:test_code,field_arr:field_arr},
						beforeSend: function (xhr) { // Add this line
					          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					    },
						success: function (data) {
							
							if(data=='1')
							{
								var msg="Selected fields assigned successfully";
								alert(msg);
							
							}
							else{
								var msg="Error in assigned selected fields";
								alert(msg);
								
							}
						}
					});
					
					location.reload();
				}					
		
			}

		}else{
			var msg="Enter the field validation for selected field";
			alert(msg);
			 
		 }
      
    });


})