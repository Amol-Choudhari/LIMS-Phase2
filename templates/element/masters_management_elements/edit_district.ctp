<?php ?>

	<div class="col-md-8  col-md-offset-2">
		<h3>Enter Details</h3>

		<div class="col-md-6">
		<h5>State</h5>

			<?php echo $this->Form->control('state_list', array('type'=>'select', 'id'=>'state_list','value'=>$selected_state_id, 'options'=>$state_list, 'label'=>false,'class'=>'form-control')); ?>	
			<div id="error_state_list"></div>
		</div>	
		
		
		<div class="col-md-6">
		<h5>District Name</h5>

			<?php echo $this->Form->control('district_name', array('type'=>'text', 'id'=>'district_name', 'label'=>false, 'value'=>$entered_district,'class'=>'form-control')); ?>	
			<div id="error_district_name"></div>
		</div>	
		
		<!-- Added below radio button block on 10-08-2018 FOR optional RO/SO office (one mandatory)-->
		<div class="col-md-6">
		<h5>District Office :
			<?php	
				$options=array('RO'=>'RO','SO'=>'SO');
				if(!empty($selected_so_office_id)){$dist_office_type='SO';}elseif(empty($selected_so_office_id)){$dist_office_type='RO';}
				$attributes=array('legend'=>false, 'value'=>$dist_office_type, 'id'=>'dist_office_type');					
				echo $this->Form->radio('dist_office_type',$options,$attributes); 
			?></h5>
		</div>
		
		
		<div id="ro_list_div" class="col-md-6">	
		<h5>RO Office</h5>

			<?php echo $this->Form->control('ro_offices_list', array('type'=>'select', 'id'=>'ro_offices_list','value'=>$selected_ro_office_id, 'options'=>$ro_offices_list,'label'=>false,'class'=>'form-control')); ?>	
			<div id="error_ro_offices_list"></div>
		</div>
		
		<!-- added on 06-03-2018 by Amol added id on 10-08-2018-->
		<div id="so_list_div" class="col-md-6">
		<h5>SO Office</h5>

			<?php  echo $this->Form->control('so_offices_list', array('type'=>'select', 'id'=>'so_offices_list','value'=>$selected_so_office_id, 'options'=>$so_offices_list,'label'=>false,'class'=>'form-control')); ?>	
			<div id="error_so_offices_list"></div>
		</div>

		<div class="col-md-3">

			<?php echo $this->element('masters_management_elements/edit_submit_common_btn'); ?>

		</div>
		
		<div class="clearfix"></div>
	</div>

				
<!-- below script added on 10-08-2018 to show hide RO/SO listing conditionally -->
<script>

	$(document).ready(function(){
		
		$("#ro_list_div").hide();
		$("#so_list_div").hide();
		
		if($('#dist_office_type-ro').is(":checked")){			
			$("#ro_list_div").show();
			
		}else if($('#dist_office_type-so').is(":checked")){
			$("#ro_list_div").show();
			$("#so_list_div").show();
		}
		
		$('#dist_office_type-ro').click(function(){
			$("#ro_list_div").show();
			$("#so_list_div").hide();
			
		});
		
		$('#dist_office_type-so').click(function(){
			$("#so_list_div").show();
			
		});
		
		
	});

</script>