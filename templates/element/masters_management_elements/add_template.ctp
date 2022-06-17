<?php ?>

	<div class="add_sms_template">
	 
		<div class="col-md-8  col-md-offset-2">
			<h3>Enter Details</h3>
			<div class="col-md-6">			
				<h4>Short Description</h4>
				<?php echo $this->Form->control('description', array('type'=>'textarea', 'id'=>'description', 'label'=>false, 'placeholder'=>'Enter Short Description Here')); ?>	
				<div id="error_description"></div>
			</div>	

			<div class="col-md-6">
				<h4>SMS Message</h4>
				<?php echo $this->Form->control('sms_message', array('type'=>'textarea', 'id'=>'sms_message', 'label'=>false, 'placeholder'=>'Enter SMS Message Here')); ?>	
				<div id="error_sms_message"></div>
			</div>			
			
			<div class="col-md-6">
				<h4>Subject</h4>
				<?php echo $this->Form->control('email_subject', array('type'=>'text', 'id'=>'email_subject',  'label'=>false, 'placeholder'=>'Enter Subject Here')); ?>
				<div id="error_email_subject"></div>
			</div>
				
			<div class="col-md-6">
				<h4>Email Message</h4>
				<?php echo $this->Form->control('email_message', array('type'=>'textarea', 'id'=>'email_message',  'label'=>false, 'placeholder'=>'Enter Email Message Here')); ?>	
			</div>	

			<div class="col-md-12">
				<h4>Send To : 
				<?php 					
					$options=array('dmi'=>'DMI','lmis'=>'LIMS');
					$attributes=array('legend'=>false, 'id'=>'template_for');					
					echo $this->form->radio('template_for',$options,$attributes); 
				?>
				</h4>
			
				<div id="dmi_roles">
					<div class="col-md-3">
						<?php echo $this->Form->control('applicant', array('type'=>'checkbox', 'id'=>'applicant', 'label'=>'Applicant', 'escape'=>false)); ?>			
						<?php echo $this->Form->control('mo_smo', array('type'=>'checkbox', 'id'=>'mo_smo', 'label'=>'Scrutiny', 'escape'=>false)); ?>			
						<?php echo $this->Form->control('io', array('type'=>'checkbox', 'id'=>'io', 'label'=>'Site Inspection', 'escape'=>false)); ?>	
					</div>				
				
					<div class="col-md-3">						
						<?php echo $this->Form->control('ro_so', array('type'=>'checkbox', 'id'=>'ro_so', 'label'=>'RO/SO', 'escape'=>false)); ?>				
						<?php echo $this->Form->control('dy_ama', array('type'=>'checkbox', 'id'=>'dy_ama', 'label'=>'Dy AMA', 'escape'=>false)); ?>	
					</div>					
					
					<div class="col-md-3">			
						<?php echo $this->Form->control('jt_ama', array('type'=>'checkbox', 'id'=>'jt_ama', 'label'=>'Jt AMA', 'escape'=>false)); ?>	
						<?php echo $this->Form->control('ho_mo_smo', array('type'=>'checkbox', 'id'=>'ho_mo_smo', 'label'=>'Scrutiny(HO)', 'escape'=>false)); ?>					
					</div>					
					
					<div class="col-md-3">				
						<?php echo $this->Form->control('ama', array('type'=>'checkbox', 'id'=>'ama', 'label'=>'AMA', 'escape'=>false)); ?>					
						<?php echo $this->Form->control('accounts', array('type'=>'checkbox', 'id'=>'accounts', 'label'=>'Accounts', 'escape'=>false)); ?>				
					</div>
					<div class="clearfix"></div>
					
				</div>
				
				
				<div id="lmis_roles">
				
					<div class="col-md-3">					
						<?php echo $this->Form->control('lmis_applicant', array('type'=>'checkbox', 'id'=>'lmis_applicant', 'label'=>'Applicant', 'escape'=>false)); ?>					
						<?php echo $this->Form->control('ral_cal', array('type'=>'checkbox', 'id'=>'ral_cal', 'label'=>'RAL/CAL', 'escape'=>false)); ?>		
					</div>				
					
					<div class="col-md-3">			
						<?php echo $this->Form->control('chemist', array('type'=>'checkbox', 'id'=>'chemist', 'label'=>'Chemist', 'escape'=>false)); ?>					
						<?php echo $this->Form->control('chief_chemist', array('type'=>'checkbox', 'id'=>'chief_chemist', 'label'=>'Cheif Chemist', 'escape'=>false)); ?>													
					</div>					
					
					<div class="col-md-3">
						<?php echo $this->Form->control('lab_incharge', array('type'=>'checkbox', 'id'=>'lab_incharge', 'label'=>'Lab Incharge', 'escape'=>false)); ?>					
						<?php echo $this->Form->control('dol', array('type'=>'checkbox', 'id'=>'dol', 'label'=>'DOL', 'escape'=>false)); ?>		
					</div>				
					
					<div class="col-md-3">
						<?php echo $this->Form->control('inward_clerk', array('type'=>'checkbox', 'id'=>'inward_clerk', 'label'=>'Inward Clerk', 'escape'=>false)); ?>				
						<?php echo $this->Form->control('outward_clerk', array('type'=>'checkbox', 'id'=>'outword_clerk', 'label'=>'Outward Clerk', 'escape'=>false)); ?>	
					</div>
					<div class="clearfix"></div>
										
				</div>
				<div id="error_send_to"></div>
			</div>
			
	
	
			<div class="col-md-3">	
				<?php echo $this->form->submit('Add', array('name'=>'add_sms_template', 'id'=>'add_sms_template_btn', 'onclick'=>'sms_message_parameter_validation('.$masterId.');return false', 'label'=>false)); ?>
			</div>
			
			<div class="clearfix"></div>
		</div>
	</div>

		
<script type="text/javascript">

	$(document).ready(function(){

		
	//for Roles list	
		//for on clicked
		
		$("#dmi_roles").hide();
		$("#lmis_roles").hide();

			$('#template_for-dmi').click(function(){		
		
				$("#dmi_roles").show();
				$("#lmis_roles").hide();
									
			});
			
			$('#template_for-lmis').click(function(){		
		
				$("#lmis_roles").show();
				$("#dmi_roles").hide();
									
			});
		

		
	});
					
					
					
</script>
		