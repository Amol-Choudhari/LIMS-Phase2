<?php  ?>

<div class="col-md-8  col-md-offset-2">

	<h3>Enter Feedback Type</h3>
	<div class="col-md-7">
		<?php echo $this->Form->control('title', array('type'=>'text', 'id'=>'title','label'=>false, 'value'=>$record_details['title'],'class'=>'form-control', 'required'=>true)); ?>	
		<div id="error_title"></div>
	</div>	


	<div class="col-md-3">			
		<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>
	</div>
	
	<div class="clearfix"></div>
	
</div>