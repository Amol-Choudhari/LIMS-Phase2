<?php  ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Update Application Charges</h3>

		<div class="col-md-7">
				<?php echo $this->Form->control('application_type', array('type'=>'textarea', 'id'=>'application_type', 'value'=>$record_details['application_type'], 'readonly'=>true, 'escape'=>false)); ?>	
		</div>	
		
		<div class="col-md-4">
				<?php echo $this->Form->control('charge', array('type'=>'text', 'id'=>'charge', 'value'=>$record_details['charge'], 'escape'=>false,'required'=>true)); ?>	
		</div>
		
		<div id="error_business_type"></div>

	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>