<?php  ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Enter Laboratory Type</h3>
	<div class="col-md-7">
		<?php echo $this->Form->control('laboratory_type', array('type'=>'text', 'id'=>'laboratory_type','label'=>false, 'value'=>$record_details['laboratory_type'],'class'=>'form-control')); ?>	
		<div id="error_laboratory_type"></div>
	</div>	


	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>