//$('#stage_sample_code').select2();
$(document).ready(function(){

	$('input[name="ral_cal"]').prop('disabled', true);
	$('#stage_sample_code').change();//default to load details
	$('#forwarded_samples_list').DataTable();
	$("#type").hide();
		
		
	$('input[name=ral_cal]').click(function() {
			
		var ral=$('input[name=ral_cal]:checked').val();
		
		$.ajax({
			type:"POST",
			url:"get_office",
			data:{ral:ral},
			async:true,
			cache:false,
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success : function(data){					
				$("#dst_loc_id").empty();
				$("#dst_loc_id").append(data);
				$("#dst_usr_cd").empty();						  
				$("#dst_usr_cd").append("<option>-----Select-----</option>");
				$("#dst_loc_id").change();
			}
		});
	});
	
});