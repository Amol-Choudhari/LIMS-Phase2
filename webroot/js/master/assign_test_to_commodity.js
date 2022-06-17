$(document).ready(function(){


	$("#commodity_code").change(function(){

		var commodity_code = $(this).val();

		 $("#testlist").find('options').remove();

		if(commodity_code != ''){


			$.ajax({
		    	type: 'POST',         
				url: 'get-list-unassigned-test-to-comm',
		     	data:{commodity_code:commodity_code},
		     	async:true,
			    cache:false,
			    beforeSend: function (xhr) { // Add this line
			          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			    },
		    	success: function (data) {
				
					if(data != 0 ){

						$.each($.parseJSON(data), function (key1, val1) {

							var test_code = val1['test_code'];
							var test_name = val1['test_name'];
							$("#testlist").append("<option value='"+test_code+"'>"+test_name+"</option>");
								
						});
					}

					getListAassignedTestToComm(commodity_code);
				}
			});


		}else{

			alert("Select Commodity first");
		}

	});



});


function getListAassignedTestToComm(commodity_code){


	$.ajax({

    	type: 'POST',         
		url: 'get-list-assigned-test-to-comm',
     	data:{commodity_code:commodity_code},
     	async:true,
	    cache:false,
	    beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    },
    	success: function (data) {
		
			$(".filterable").empty();

			if(data != 0 ){


				var tableHtml = '<table id="pages_list_table" class="table table-bordered table-striped table-hover">';
				tableHtml += '<thead class="tablehead">';
				tableHtml += '<tr><th>SR.No</th><th>Test Name</th></tr>';
				tableHtml += '</thead>';

				var i = 1;
				$.each($.parseJSON(data), function (key1, val1) {

					tableHtml += '<tr><td>'+i+'</td><td>'+val1['a']['test_name']+'</td></tr>';
					i++;

				})

				tableHtml += '<tbody>';
				tableHtml += '</tbody></table>';
													
				$(".filterable").html(tableHtml);

				$('#pages_list_table').DataTable();

				console.log(tableHtml)					
			}


		}
	});


}