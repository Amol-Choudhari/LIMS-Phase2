$(document).ready(function(){

	$('#pages_list_table').DataTable();

	// For date picker option
	$('#publish_date').datepicker({
		format: "dd/mm/yyyy"
	});

	$('#end_date').datepicker({
		format: "dd/mm/yyyy"
	});


  //$("#delete_record").click(function(e){
	$('#pages_list_table').on('click','#delete_record', function(e){

  	if(confirm('Are you sure you want to delete this record ?')==false){
  		e.preventDefault();
  	}

  });

});
