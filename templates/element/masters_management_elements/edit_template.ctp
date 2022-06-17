<?php ?>

	<div class="add_sms_template">
	 
		<div class="col-md-8  col-md-offset-2">
			<h3>Edit Details</h3>
			<div class="col-md-6">
			
				<h4>Short Description</h4>

				<?php echo $this->Form->control('description', array('type'=>'textarea', 'id'=>'description', 'value'=>$record_details['description'], 'label'=>false, 'placeholder'=>'Enter Short Description Here')); ?>	
				<div id="error_description"></div>
			</div>	
			
			
			
			<div class="col-md-6">
			<h4>SMS Message</h4>

				<?php echo $this->Form->control('sms_message', array('type'=>'textarea', 'id'=>'sms_message', 'value'=>$record_details['sms_message'], 'label'=>false)); ?>	
				<div id="error_sms_message"></div>
			</div>	
			
			
			<div class="col-md-6">
				<h4>Subject</h4>
				<?php echo $this->Form->control('email_subject', array('type'=>'text', 'id'=>'email_subject', 'value'=>$record_details['email_subject'], 'label'=>false)); ?>
				<div id="error_email_subject"></div>
			</div>
				
			<div class="col-md-6">
				<h4>Email Message</h4>
				<?php echo $this->Form->control('email_message', array('type'=>'textarea', 'id'=>'email_message', 'value'=>$record_details['email_message'], 'label'=>false)); ?>	
			</div>	
			
			
			
			<div class="col-md-12">
				<h4>Send To :
					<?php 					
						$options=array('dmi'=>'DMI','lmis'=>'LMIS');
						$attributes=array('legend'=>false, 'value'=>$record_details['template_for'], 'id'=>'template_for');					
						echo $this->form->radio('template_for',$options,$attributes); 
					?>				
				</h4>
				
				<div id="dmi_roles">			
					<div class="col-md-3">
						<?php 
							if( in_array(0,$existed_destination_array))
							{	echo $this->Form->control('applicant', array('type'=>'checkbox', 'checked'=>true, 'id'=>'applicant', 'label'=>'Applicant'));
							}else{									
								echo $this->Form->control('applicant', array('type'=>'checkbox', 'checked'=>false, 'id'=>'applicant', 'label'=>'Applicant'));
							}
						?>						
						
						<?php 
							if( in_array(1,$existed_destination_array))
							{ 	echo $this->Form->control('mo_smo', array('type'=>'checkbox', 'checked'=>true, 'id'=>'mo_smo', 'label'=>'Scrutiny'));
							}else{									
								echo $this->Form->control('mo_smo', array('type'=>'checkbox', 'checked'=>false, 'id'=>'mo_smo', 'label'=>'Scrutiny'));
							}
						?>
						
						
						<?php 
							if( in_array(2,$existed_destination_array))
							{	echo $this->Form->control('io', array('type'=>'checkbox', 'checked'=>true, 'id'=>'io', 'label'=>'Site Inspection'));
							}else{									
								echo $this->Form->control('io', array('type'=>'checkbox', 'checked'=>false, 'id'=>'io', 'label'=>'Site Inspection'));
							}
						?>
					
						
					</div>
				
				
					<div class="col-md-3">
					
						<?php 
							if( in_array(3,$existed_destination_array))
							{	echo $this->Form->control('ro_so', array('type'=>'checkbox', 'checked'=>true, 'id'=>'ro_so', 'label'=>'RO/SO'));
							}else{									
								echo $this->Form->control('ro_so', array('type'=>'checkbox', 'checked'=>false, 'id'=>'ro_so', 'label'=>'RO/SO'));
							}
						?>
						
						
						<?php 
							if( in_array(4,$existed_destination_array))
							{	echo $this->Form->control('dy_ama', array('type'=>'checkbox', 'checked'=>true, 'id'=>'dy_ama', 'label'=>'Dy AMA'));
							}else{									
								echo $this->Form->control('dy_ama', array('type'=>'checkbox', 'checked'=>false, 'id'=>'dy_ama', 'label'=>'Dy AMA'));
							}
						?>
						
		
					</div>
					
					
					<div class="col-md-3">
					
						<?php 
							if( in_array(5,$existed_destination_array))
							{	echo $this->Form->control('jt_ama', array('type'=>'checkbox', 'checked'=>true, 'id'=>'jt_ama', 'label'=>'Jt AMA'));
							}else{									
								echo $this->Form->control('jt_ama', array('type'=>'checkbox', 'checked'=>false, 'id'=>'jt_ama', 'label'=>'Jt AMA'));
							}
						?>
						
					
						<?php 
							if( in_array(6,$existed_destination_array))
							{	echo $this->Form->control('ho_mo_smo', array('type'=>'checkbox', 'checked'=>true, 'id'=>'ho_mo_smo', 'label'=>'Scrutiny(HO)'));
							}else{									
								echo $this->Form->control('ho_mo_smo', array('type'=>'checkbox', 'checked'=>false, 'id'=>'ho_mo_smo', 'label'=>'Scrutiny(HO)'));
							}
						?>
					
					
					</div>
					
					
					<div class="col-md-3">
					
						<?php 
							if( in_array(7,$existed_destination_array))
							{	echo $this->Form->control('ama', array('type'=>'checkbox', 'checked'=>true, 'id'=>'ama', 'label'=>'AMA'));
							}else{									
								echo $this->Form->control('ama', array('type'=>'checkbox', 'checked'=>false, 'id'=>'ama', 'label'=>'AMA'));
							}
						?>
						
						
						<?php 
							if( in_array(8,$existed_destination_array))
							{	echo $this->Form->control('accounts', array('type'=>'checkbox', 'checked'=>true, 'id'=>'accounts', 'label'=>'Accounts'));
							}else{									
								echo $this->Form->control('accounts', array('type'=>'checkbox', 'checked'=>false, 'id'=>'accounts', 'label'=>'Accounts'));
							}
						?>
					</div>
					<div class="clearfix"></div>
				</div>
				
				
				
				<div id="lmis_roles">
				
					<div class="col-md-3">
					
						<?php 
							if( in_array(101,$existed_destination_array))
							{	echo $this->Form->control('lmis_applicant', array('type'=>'checkbox', 'checked'=>true, 'id'=>'lmis_applicant', 'label'=>'Applicant'));
							}else{
								echo $this->Form->control('lmis_applicant', array('type'=>'checkbox', 'checked'=>false, 'id'=>'lmis_applicant', 'label'=>'Applicant'));
							}
						?>
						
						
						<?php 
							if( in_array(102,$existed_destination_array))
							{	echo $this->Form->control('ral_cal', array('type'=>'checkbox', 'checked'=>true, 'id'=>'ral_cal', 'label'=>'RAL/CAL'));
							}else{
								echo $this->Form->control('ral_cal', array('type'=>'checkbox', 'checked'=>false, 'id'=>'ral_cal', 'label'=>'RAL/CAL'));
							}					
						?>
					</div>
					
					
					
					<div class="col-md-3">
						<?php 
							if( in_array(103,$existed_destination_array))
							{	echo $this->Form->control('chemist', array('type'=>'checkbox', 'checked'=>true, 'id'=>'chemist', 'label'=>'Chemist'));
							}else{
								echo $this->Form->control('chemist', array('type'=>'checkbox', 'checked'=>false, 'id'=>'chemist', 'label'=>'Chemist'));
							}
						?>
						
						
						<?php 
							if( in_array(104,$existed_destination_array))
							{	echo $this->Form->control('chief_chemist', array('type'=>'checkbox', 'checked'=>true, 'id'=>'chief_chemist', 'label'=>'Cheif Chemist'));
							}else{
								echo $this->Form->control('chief_chemist', array('type'=>'checkbox', 'checked'=>false, 'id'=>'chief_chemist', 'label'=>'Cheif Chemist'));
							}						
						?>
					</div>
					

					<div class="col-md-3">
						<?php 
							if( in_array(105,$existed_destination_array))
							{	echo $this->Form->control('lab_incharge', array('type'=>'checkbox', 'checked'=>true, 'id'=>'lab_incharge', 'label'=>'Lab Incharge'));
							}else{
								echo $this->Form->control('lab_incharge', array('type'=>'checkbox', 'checked'=>false, 'id'=>'lab_incharge', 'label'=>'Lab Incharge'));
							}
						?>
						
						
						<?php 
							if( in_array(106,$existed_destination_array))
							{	echo $this->Form->control('dol', array('type'=>'checkbox', 'checked'=>true, 'id'=>'dol', 'label'=>'DOL'));
							}else{
								echo $this->Form->control('dol', array('type'=>'checkbox', 'checked'=>false, 'id'=>'dol', 'label'=>'DOL'));
							}					
						?>
					</div>	
						
					

					<div class="col-md-3">
						<?php 
							if( in_array(107,$existed_destination_array))
							{	echo $this->Form->control('inward_clerk', array('type'=>'checkbox', 'checked'=>true, 'id'=>'inward_clerk', 'label'=>'Inward Clerk'));
							}else{
								echo $this->Form->control('inward_clerk', array('type'=>'checkbox', 'checked'=>false, 'id'=>'inward_clerk', 'label'=>'Inward Clerk'));
							}
						?>
						
						
						<?php 
							if( in_array(108,$existed_destination_array))
							{	echo $this->Form->control('outward_clerk', array('type'=>'checkbox', 'checked'=>true, 'id'=>'outword_clerk', 'label'=>'Outward Clerk'));
							}else{
								echo $this->Form->control('outward_clerk', array('type'=>'checkbox', 'checked'=>false, 'id'=>'outword_clerk', 'label'=>'Outward Clerk'));
							}					
						?>
						
					</div>
					<div class="clearfix"></div>
										
				</div>

				<div id="error_send_to"></div>
			</div>
	
			<div class="col-md-3">
	
				<?php echo $this->form->submit('Edit', array('name'=>'edit_sms_template', 'id'=>'edit_sms_template_btn', 'onclick'=>'sms_message_parameter_validation('.$masterId.');return false', 'label'=>false)); ?>

			</div>
			
			<div class="clearfix"></div>
		</div>
	</div>


<script type="text/javascript">

	$(document).ready(function(){

		//for Roles list

			$("#dmi_roles").hide();
			$("#lmis_roles").hide();
		
			//for already checked
		
			if($('#template_for-dmi').is(":checked")){		
		
						$("#dmi_roles").show();
						$("#lmis_roles").hide();
									
			}else if($('#template_for-lmis').is(":checked")){

					$("#lmis_roles").show();
					$("#dmi_roles").hide();
				
			}
			
			
		
			//for on clicked

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
