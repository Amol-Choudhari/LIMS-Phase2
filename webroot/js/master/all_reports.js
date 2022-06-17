$(document).ready(function(){

	
	$('#pages_list_table').on('click','.dercord',function(){

		if(confirm('Are you sure you want to delete this record ?')){

			reportid = $(this).attr('id');

			$.ajax({

				type:'POST',
				url:'delete-report',
				data:{reportid:reportid},
				beforeSend: function (xhr) { // Add this line
		          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		    	},
		    	success: function (data){

		    		if(data==0){
		    			alert('Record not deleted');
		    		}else{

		    			alert('Record deleted successfully');
		    			location.reload();
		    		}



		    	}

			});
		}		

	});

})