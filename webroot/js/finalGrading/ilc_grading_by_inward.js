$(document).ready(function(){

	
	$("#frd_to_oic").click(function (e) {
		
	   e.preventDefault();
	   var get_zscore=$("#get_zscore").val();
	   var remark=$("#remark").val();
	  

	   if(get_zscore==''){
		var msg="Please calculate zscore!!";
		$.alert(msg);
		return;
		}
	   else if(remark==''){
		   var msg="Please enter remark!!";
		   $.alert(msg);
		   return;
	   }

	   


   });

	//added for click to show modal for zscore
	// $('#get_zscore').on('show.bs.modal', function(e) {
	// 	var id = $(e.relatedTarget).data('id');
	// 	$(e.currentTarget).find('button[id="save"]').onclick = function() { alert(id); };
	// });

	$("#get_zscore").click(function() {
		alert(this.id);
	  });

	
	
	$.ajax({

		type:'POST',
		url:'../FinalGrading/ilc-available-sample-zscore',
		data:{id:id},
		async:true,
		cache:false,
		beforeSend: function (xhr) {
		xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		},
		success: function (data) {
		alert('Successfully Added'); 
		},
		
		
	});
  
  


	//03-08-2022//
   // jQuery button click event to remove a row
	// $('#tbody').on('click', '.get_zscore', function () {
	
	// 	// Getting all the rows next to the 
	// 	// row containing the clicked button
	// 	var child = $(this).closest('tr').nextAll();
	
	// 	// Iterating across all the rows 
	// 	// obtained to change the index
	// 	child.each(function () {
			
	// 		// Getting <tr> id.
	// 		var id = $(this).attr('id');

	// 	});
	
	// 	// Removing the current row.
	// 	// $(this).closest('tr').remove();
	
	// 	// Decreasing the total number of rows by 1.
	// 	// rowIdx--;
	// });

  

});




