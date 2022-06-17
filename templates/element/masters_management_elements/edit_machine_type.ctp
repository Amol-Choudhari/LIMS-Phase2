<?php ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Enter Machine Type</h3>
	<div class="col-md-7">
		<?php echo $this->Form->control('machine_types', array('type'=>'text', 'id'=>'machine_types','label'=>false, 'value'=>$record_details['machine_types'],'class'=>'form-control')); ?>	
		<div id="error_machine_type"></div>
	</div>

	<div class="col-md-4">
	<h5>Application Type</h5>
			<?php 					
			$options=array('ca'=>'CA','printing'=>'Printing');
			$attributes=array('legend'=>false, 'value'=>$record_details['application_type'], 'id'=>'application_type');		
			echo $this->form->radio('application_type',$options,$attributes); ?>
			<div id="error_application_type"></div>
	</div>


	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>