<?php 
//taking 'years for' value from option session variable
//conditional column name due to 3 diff. tables.
$years_for = $_SESSION['edit_optional_param'];
$options = array('0'=>'CA','1'=>'Printing Press','2'=>'Crushing & Refining');
if($years_for=='0' || $years_for=='1'){
	$business_years='business_years';
}elseif($years_for=='2'){
	$business_years='crushing_refining_periods';
}
?>

<div class="col-md-8  col-md-offset-2" style="margin-bottom:20px;">
	<div class="col-md-4">
	<h5 style="font-weight:bold;">Enter value Here</h5>
			<?php echo $this->Form->control('business_years', array('type'=>'text', 'id'=>'business_years','label'=>false, 'value'=>$record_details[$business_years],'class'=>'form-control')); ?>	
			<div id="error_business_year"></div>
	</div>
	
	<div class="col-md-4">
	<h5 style="font-weight:bold;">Select Business Years for:</h5>
			<?php echo $this->Form->control('business_years_for', array('type'=>'select', 'id'=>'business_years_for','label'=>false, 'options'=>$options, 'value'=>$years_for,'class'=>'form-control','readonly'=>true)); ?>	
			<div id="error_business_years_for"></div>
	</div>
	

	<div class="col-md-3">
			<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>
	</div>
	<div class="clearfix"></div>

</div>

<script>

	$("#add_business_year_btn").click(function (){
		
		var value_return = 'true';
		
		if($("#business_years").val() == ''){
			
			$("#error_business_year").show().text("Please Enter value here");
			$("#error_business_year").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
			//setTimeout(function(){ $("#error_business_year").fadeOut();},8000);
			$("#business_years").click(function(){$("#error_business_year").hide().text;});
			value_return = 'false';
		}
		
		if(value_return == 'false')
		{
			return false;
		}
		else{
			exit();			
		}
		
	});


</script>