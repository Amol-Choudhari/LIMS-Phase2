$(document).ready(function(){

	$('#approvedresulttable').DataTable();

	$("#approvedresulttable").on('click', '.stage_sample_id', function() {

		var stage_sample_modal_view = $(this).attr('id');
		var currentRow=$(this).closest("tr");
		var col0=currentRow.find("td:eq(1)").text();
		var col1=currentRow.find("td:eq(4)").text();
		var col2=currentRow.find("td:eq(3)").text();
		var col3=currentRow.find("td:eq(2)").text();

	   $("label[for='samplecodelabel']").text(col0);
		 $("label[for='sampletypelabel']").text(col1);
		 $("label[for='commoditylabel']").text(col2);
		 $("label[for='categorylabel']").text(col3);

		 $("#modalView tbody").empty();

		  var i=1;

		$.ajax({
			type: "POST",
			url: 'view_approved_result',
			data: {stage_sample_modal_view: stage_sample_modal_view},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data){

				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween## from response
				resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

				$.each(resArray, function (key, value) {

				    $('#modalView tbody').append('<tr class="child"><td>'+i+++'</td><td>'+value['test_name']+'</td><td>'+value['final_result']+'</td></tr>');
				});

				$('#resultModal').modal('show');
			}
		 });
	});


});
