$(document).ready(function(){
	
  	$(".grid_val_max_div" ).hide();
 	$(".grid_val_min_div").hide();
 	$(".rangeDiv").hide();

	$("#category_code").change(function(){
		get_commodity();
	});

	$("#commodity_code").change(function(){
		commodity_test();
	});

	$("#test_code").change(function(){
		get_test_methods();
	});

	$("#min_max").change(function(){
		enable_min_max();
	});



});



function get_commodity(){
	
	$("#method_code").find('option').remove();//added on 23-12-2019 by Amol															  
	$("#commodity_code").attr("disabled", false); 
    $("#commodity_code").find('option').remove();
    $("#commodity_code").append("<option value=''>-----Select---- </option>");
    $("#test_code").find('option').remove();
    $("#test_code").append("<option value=''>----Select----- </option>");
    var category_code = $("#category_code").val();

    if(category_code != ''){

    	$.ajax({
	        type: "POST",
	        url: 'get_commodity',
	        data: {category_code: category_code},
	        beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
	        success: function (data) {	
	            if(data==0)
				{					
						alert("Commodities are not available!!!");
					
				}
				else{
					$("#commodity_code").append(data);					
					//getData();//added on 19-01-2021 by Amol, and removed from inline element call
	            }
	        }
	    });
    }else{

    	alert("Category not selected");
    }

}



function commodity_test(){

	$("#method_code").find('option').remove();//added on 23-12-2019 by Amol	
	$("#test_code").attr("disabled", false); 
    $("#test_code").find('option').remove();
    $("#test_code").append("<option value=''>------Select----- </option>");
	
    var category_code = $("#category_code").val();

    if (category_code != "")
    {
        var commodity_code = $("#commodity_code").val();

        $.ajax({
            type: "POST",
            url: 'get_test_by_commodity_id',
            data: {commodity_code: commodity_code},
            beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
            success: function (data) {
					
					if(data==0)
					{
						alert("Tests are not available!!");						
					}
					else{
						$.each($.parseJSON(data), function (key, value) {

							$("#test_code").append("<option value='" + value['test_code'] + "'>" + value['test_name'] + "</option>");

	
						});

						//getData();//added on 19-01-2021 by Amol, and removed from inline element call
					}
			    }
        });
		
	}
   else {
	   alert("Select commodity category first!");       
   }					
   		
}


function get_test_methods(){

	var test_code = $("#test_code").val();
	$("#grd_standrd").val('');
	$("#min_max").val('');	

	if(test_code != ''){

		$.ajax({
	        type: "POST",
	        url: 'get_test_methods',
	        data: {test_code: test_code},
	        beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
	        success: function (data) {
					
				if(data ==	'0')
				{
					alert("Test not selected");			
				
				}else if(data == '1'){

					alert('No Formula Added for this Test. Please First Add Formula For This Test From Formula Master.');	
				}
				else{

					$.each($.parseJSON(data), function (key, value) {

						$("#method_code").append("<option value='" + value['method_code'] + "'>" + value['method_name'] + "</option>");

					});

					enable_standrd();

					//getData();//added on 19-01-2021 by Amol, and removed from inline element call
				}
		    }
	    });
	}

} 

function enable_standrd()
{

	$("#grade_value1").find('select').remove();
	$("#grade_value1").find('input').remove();
	var test_code = $("#test_code").val();

  	$.ajax({
		type: "POST",
		url: 'get_test_type',
		data: {test_code: test_code},
		beforeSend: function (xhr) { // Add this line
          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
    	},
		success: function (data) {
			
			$.each($.parseJSON(data), function (key, value) {
				
				if(value['test_type_name']=="PN"){
					$(".rangeDiv").hide();
					$(".grid_val_min_div").removeClass('pt-3');
					$(".grid_val_min_div").show();
					$("#grade_value1").append('<select name= "grade_value" class="form-control"><option vlaue="">----Select----</option><option value="Positive">Positive</option><option value="Negative">Negative</option></select>');
					$(".grid_val_max_div").hide(); 
					$( "#grid_val_max_subdiv" ).hide();
					//alert("PN");
				}
				else if(value['test_type_name']=="YN"){
					$(".rangeDiv").hide();
					$(".grid_val_min_div").show();
					$(".grid_val_min_div").removeClass('pt-3').addClass('pt-3');
					$("#grade_value1").append('<select name= "grade_value" class="form-control"><option vlaue="">----Select----</option><option value="Yes">Yes</option><option value="No">No</option></select>');	
					$(".grid_val_max_div").hide(); 	
					$( "#grid_val_max_subdiv" ).hide();			
					//alert("YN");									
				}
				else if(value['test_type_name']=="SV"){
					$(".rangeDiv").show();
					$("#min_max").val('Min');
					$(".grid_val_min_div").show();
					$(".grid_val_min_div").removeClass('pt-3').addClass('pt-3');
					$("#grade_value1").append('<input type="text" placeholder="Grade Value" class="form-control"  id="grade_value" name= "grade_value" >');		
					$(".grid_val_max_div").hide(); 
					$( "#grid_val_max_subdiv" ).hide();
					//alert("SV");

				}
				else{
					$(".rangeDiv").show();
					$(".grid_val_min_div").hide();					
				}
				
				getData();//added on 19-01-2021 by Amol, and removed from inline element call
			});
		}
  	});	

	$("#grd_standrd").attr("disabled", false); 
	$("#method_code").attr("disabled", false); 
	$("#grade_code").attr("disabled", false); 
}



function enable_min_max(){
			 
	var min_max = $("#min_max").val();
	
	if(min_max=="Range"){
		$(".grid_val_min_div").show();
		$(".grid_val_max_div").show();
		$("#grid_val_max_subdiv").show();
		$("#grid_val_max_subdiv" ).empty();
		$("#grade_value1" ).empty();
		$("#grade_value1").append('<input type="text" placeholder="Min Grade Value" class="form-control"  id="grade_value"  name= "grade_value" >');
		$("#grid_val_max_subdiv").append('<input type="text" placeholder="Max Grade Value" class="form-control"  id="max_grade_value"  name= "max_grade_value" >');
		$("#min_max").attr("disabled", false); 		
	 }
	 else if(min_max=="Min"){
		 $(".grid_val_max_div").hide(); 
		 $(".grid_val_min_div").show(); 
		 $("#grade_value1").show();
		$("#grade_value1" ).empty();
		$("#grade_value1").append('<input type="text" placeholder="Min Grade Value" class="form-control"  id="grade_value"  name= "grade_value" >');				 
		 $(".grid_val_max_div").hide();
		 $( "#grid_val_max_subdiv" ).empty();
	 }
	else if(min_max=="Max"){
		 $(".grid_val_min_div").hide(); 
		 $(".grid_val_max_div").show();
		$("#grade_value1").empty();
		$("#grid_val_max_subdiv").empty();
		$("#grid_val_max_subdiv").show(); 
		$("#grid_val_max_subdiv").append('<input type="text" placeholder="Max Grade Value" class="form-control"  id="max_grade_value"  name= "max_grade_value" >');
		
	}
	// alert(min_max);
 }


$("#viewrecords").click(function(){

	var category_code	= $("#category_code").val();
	var commodity_code	= $("#commodity_code").val();
	var test_code		= $("#test_code").val();

	if(category_code=='')
		category_code=0;
	if(commodity_code=='')
		commodity_code=0;
	if(test_code=='')
		test_code=0;

	$("#avb").prop("hidden", false);
	
	var i=1;
	
	  $("#check_div tbody").empty();
	  $('#check_div').DataTable().clear().destroy();
	 $.ajax({
            type: "POST",
            url: 'viewCommGradeList',
            data: {category_code:category_code,commodity_code:commodity_code,test_code:test_code},
            beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
            success: function (data) {


				$.each($.parseJSON(data), function (key, value){
					console.log(value);
					//$.each( value,function (key1, value1){
						var rowcontent="<tr><td>"+i+"</td>";
						rowcontent=rowcontent+"<td>"+value['category_name']+"</td>";
						rowcontent=rowcontent+"<td>"+value['commodity_name']+"</td>";
						rowcontent=rowcontent+"<td>"+value['test_name']+"</td>";
						rowcontent=rowcontent+"<td>"+ value['grade_desc']+"</td>";
						if(value['grade_value']){
						rowcontent=rowcontent+"<td>"+  value['grade_value']    +"</td>";
						}
						else{
							rowcontent=rowcontent+"<td>-</td>";
						}
						if(value['max_grade_value']){
						rowcontent=rowcontent+"<td>"+  value['max_grade_value']    +"</td>";
						}
						else{
							rowcontent=rowcontent+"<td>-</td>";
						}
						//rowcontent=rowcontent+"<td>"+  value1['max_grade_value'] +"</td>";
						if(value['singleval']){
						rowcontent=rowcontent+"<td>"+  value['singleval']    +"</td>";
						}
						else{
							rowcontent=rowcontent+"<td>-</td>";
						}
						
						$("#check_div tbody").append(rowcontent);
					//});
					i++;
				});
				$('#check_div').DataTable();
				$("#myModal").modal('show');

			}
        });
})








