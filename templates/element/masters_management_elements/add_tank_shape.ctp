<?php ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Enter Tank Shape</h3>
	<div class="col-md-7">
		<?php echo $this->Form->control('tank_shapes', array('type'=>'text', 'id'=>'tank_shapes','label'=>false, 'placeholder'=>'Enter Tank Shape Here','class'=>'form-control')); ?>	
		<div id="error_tank_shape"></div>
	</div>	


	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/add_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>