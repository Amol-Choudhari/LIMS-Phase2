
var single_status = $("#single_status").val();

if(single_status!='checked')
{
  $("#val_type_one_div").hide();
}


$(document).ready(function(){

	$('.val_type').change(function(){
		var value = $('.val_type:checked').val();
		if(value=='single'){
			$("#val_type_one_div").show();
			$('#m_sample_obs_type_value').attr('required','');
		}else{
			$("#val_type_one_div").hide();
			$('#m_sample_obs_type_value').removeAttr('required');
		}
	});

});
