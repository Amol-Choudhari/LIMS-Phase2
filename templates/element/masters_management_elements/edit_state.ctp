<?php  ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Enter State Name</h3>
	<div class="col-md-7">
		<?php echo $this->Form->control('state_name', array('type'=>'text', 'id'=>'state_name','label'=>false, 'value'=>$record_details['state_name'],'class'=>'form-control')); ?>	
		<div id="error_state_name"></div>
	</div>	


	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>