<?php  ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Enter Packing Type</h3>
	<div class="col-md-7">
		<?php echo $this->Form->control('packing_type', array('type'=>'text', 'id'=>'packing_type','label'=>false, 'value'=>$record_details['packing_type'],'class'=>'form-control')); ?>	
		<div id="error_packing_type"></div>
	</div>	


	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>