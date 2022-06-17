<?php ?>

	<div class="col-md-8  col-md-offset-2">
		<!-- added on 26-07-2018 to show radio btns to add RO or RAL -->
		<div class="col-md-12">
		<h5>Office Type :
		<?php $options=array('RO'=>'RO','RAL'=>'RAL','SO'=>'SO');
			$attributes=array('legend'=>false, 'value'=>'RO', 'id'=>'office_type');					
			echo $this->form->radio('office_type',$options,$attributes); ?>
		</div>
		</h5>
		<div class="col-md-6">
		<h5>Office Name</h5>

					<?php echo $this->Form->control('ro_office', array('type'=>'text', 'id'=>'ro_office', 'class'=>'form-control', 'label'=>false, 'placeholder'=>'Enter Office Name')); ?>	
					<div id="error_ro_office"></div>
		</div>	
		
		
		<div class="col-md-6">
		<h5>Address</h5>

					<?php echo $this->Form->control('ro_office_address', array('type'=>'text', 'id'=>'ro_office_address', 'class'=>'form-control', 'label'=>false, 'placeholder'=>'Enter Office Address')); ?>	
					<div id="error_ro_office_address"></div>
		</div>	
		
		
		<!-- added id on 26-07-2018 ---->
		<div id="ro_email_list" class="col-md-6">
		<h5>Officer Id (default id is 'dmiqc@nic.in')</h5>

					<?php echo $this->Form->control('ro_email_id', array('type'=>'select', 'id'=>'ro_email_id', 'class'=>'form-control', 'label'=>false, 'options'=>$all_ro_list, 'value'=>'dmiqc@nic.in')); ?>	
					<div id="error_ro_email_id"></div>
		</div>
		
		<!-- added on 26-07-2018 to show LIMS users list when RAL radio btn selected -->
		<div id="ral_email_list" class="col-md-6">
		<h5>Officer Id (default id is 'dmiqc@nic.in')</h5>

					<?php echo $this->Form->control('ral_email_id', array('type'=>'select', 'id'=>'ral_email_id', 'class'=>'form-control', 'label'=>false, 'options'=>$all_ral_list, 'value'=>'dmiqc@nic.in')); ?>	
					<div id="error_ral_email_id"></div>
		</div>
		
		<!-- added on 11-03-2019 to show SO incharge users list when SO radio btn selected -->
		<div id="so_email_list" class="col-md-6">
		<h5>Officer Id (default id is 'dmiqc@nic.in')</h5>

					<?php echo $this->Form->control('so_email_id', array('type'=>'select', 'id'=>'so_email_id', 'class'=>'form-control', 'label'=>false, 'options'=>$all_so_list, 'value'=>'dmiqc@nic.in')); ?>	
					<div id="error_so_email_id"></div>
		</div>
		
		
		<!-- added below RO offices dropdown on 11-03-2019 for SO offices-->
		<div id="ro_office_list" class="col-md-6">
		<h5>RO Office</h5>

					<?php echo $this->Form->control('ro_office_id', array('type'=>'select', 'id'=>'ro_office_id', 'class'=>'form-control', 'label'=>false, 'options'=>$ro_office_list, 'empty'=>'---Select---')); ?>	
					<div id="error_ro_office_id"></div>
		</div>
		<div class="col-md-6">
		<h5>Phone No.</h5>

					<?php echo $this->Form->control('ro_office_phone', array('type'=>'text', 'id'=>'ro_office_phone', 'class'=>'form-control', 'label'=>false, 'placeholder'=>'Enter Office Phone NO.')); ?>	
					<div id="error_ro_office_phone"></div>
		</div>	
		
		
		<!-- added id on 26-07-2018 ---->
		<div id="short_code_div" class="col-md-6">
		<h5>Office District Code</h5>

					<?php echo $this->Form->control('short_code', array('type'=>'text', 'id'=>'short_code', 'class'=>'form-control', 'label'=>false, 'placeholder'=>'Enter Office District Short Code')); ?>	
					<div id="error_short_code" style="color:red;"><?php if(!empty($duplicate_short_code)){echo "This Short code already exist";}?></div>
		</div>	


		<div class="col-md-3">

			<?php echo $this->element('masters_management_elements/add_submit_common_btn'); ?>

		</div>
		
		<div class="clearfix"></div>
	</div>

<script>

	//for already checked		
	if($('#office_type-ro').is(":checked")){		

		$("#ro_email_list").show();
		$("#short_code_div").show();
		$("#ral_email_list").hide();
		$("#so_email_list").hide();		
		$("#ro_office_list").hide();												  
	}
			
	
	//when clicked
	$('#office_type-ro').click(function(){		

		$("#ro_email_list").show();
		$("#short_code_div").show();
		$("#ral_email_list").hide();
		$("#so_email_list").hide();
		$("#ro_office_list").hide();					  
									
	});
			
	$('#office_type-ral').click(function(){		

		$("#ro_email_list").hide();
		$("#short_code").val('');
		$("#short_code_div").hide();
		$("#ral_email_list").show();
		$("#so_email_list").hide();
		$("#ro_office_list").hide();				  
							
	});
	$('#office_type-so').click(function(){		

		$("#ro_email_list").hide();
		$("#short_code_div").show();
		$("#ral_email_list").hide();
		$("#so_email_list").show();
		$("#ro_office_list").show();
									
	});
</script>