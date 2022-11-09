$(document).ready(function(){

  $('#rejected_samples_list').DataTable();

	// For date picker option
  $('#publish_date').datepicker({
    format: "dd/mm/yyyy"
  });


  $('#end_date').datepicker({
    format: "dd/mm/yyyy"
  });

});

$("#sample_undo").click(function(e){

  var sid_id = $("#sid_id").val();
  var sample_code = $("#sample_code").val();

	if(sample_undo(sid_id,sample_code)==false){
		e.preventDefault();
	}

});

//to revert the reject samples on sample forward window by OIC.
//and again available for forwarding
function sample_undo(sid_id,sample_code){

	$.ajax({
		type: "POST",
		url: 'rejected_list',
		data: {sid_id:sid_id,sample_code: sample_code,sample_reject_undo:'sample_reject_undo'},
		beforeSend: function (xhr) { // Add this line
				xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		},
		success: function (data) {

			var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
			if(resArray==1)
			{
				var msg="Sample Reverted Successfully!!!";
			}else{
				var msg="Sorry...Sample Not Reverted, Please check!!!";
			}

			alert(msg);

			location.reload();


		}
	});


}
