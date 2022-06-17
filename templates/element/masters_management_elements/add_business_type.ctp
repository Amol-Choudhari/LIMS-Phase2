<?php ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Enter Business Type</h3>
	<div class="col-md-7">
		<?php echo $this->Form->control('business_type', array('type'=>'text', 'id'=>'business_type','label'=>false, 'placeholder'=>'Enter Business Type Here','class'=>'form-control')); ?>	
		<div id="error_business_type"></div>
	</div>	


	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/add_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>