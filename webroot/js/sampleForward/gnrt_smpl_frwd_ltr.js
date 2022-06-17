//$('#stage_sample_code').select2();
$(document).ready(function(){

	$('#stage_sample_code').change(function(){
		
		var sample_code = $('#stage_sample_code').val();
		$('#generate_letter_btn').attr('href','../SampleForward/frd_letter_pdf/'+sample_code);
	});
	
	$("#stage_sample_code").change();
	
});