<?php ?>

	<div class="col-md-8  col-md-offset-2">
		<h3>Enter Details</h3>				
		
		<!-- added on 26-07-2018 to show office type is RO/RAL -->
		<div class="col-md-6">
		<h5>Office Type: </h5>
			<?php echo $this->Form->control('office_type', array('type'=>'text', 'value'=>$record_details['office_type'],'label'=>false,'class'=>'form-control','readonly'=>true)); ?>
		</div>
		
		<div class="col-md-6">
		<h5>Office Name</h5>

					<?php echo $this->Form->control('ro_office', array('type'=>'text', 'value'=>$record_details['ro_office'], 'id'=>'ro_office', 'label'=>false,'class'=>'form-control')); ?>	
					<div id="error_ro_office"></div>
		</div>	
		
		
		<div class="col-md-6">
		<h5>Address</h5>

					<?php echo $this->Form->control('ro_office_address', array('type'=>'text', 'value'=>$record_details['ro_office_address'], 'id'=>'ro_office_address', 'label'=>false,'class'=>'form-control')); ?>	
					<div id="error_ro_office_address"></div>
		</div>	
		
		<!-- to show when office type is RO -->
		<?php if($record_details['office_type']=='RO' || $record_details['office_type']=='SO'){ ?>
			<div class="col-md-6">
			<h5>Office District Code</h5>

				<?php echo $this->Form->control('short_code', array('type'=>'text', 'value'=>$record_details['short_code'], 'label'=>false, 'readonly'=>true,'class'=>'form-control')); ?>	
				<div id="error_short_code"></div>
			</div>
		<?php } ?>
							
		<div class="col-md-6">
		<h5>Phone No.</h5>

			<?php echo $this->Form->control('ro_office_phone', array('type'=>'text', 'value'=>$record_details['ro_office_phone'], 'id'=>'ro_office_phone', 'label'=>false,'class'=>'form-control')); ?>	
			<div id="error_ro_office_phone"></div>
		</div>

		<!-- to show when office type is RAL -->
		<?php if($record_details['office_type']=='RAL'){ ?>
		
			<div id="ral_email_list" class="col-md-6">
			<h5>Officer Email Id</h5>

				<?php echo $this->Form->control('ral_email_id', array('type'=>'select', 'id'=>'ral_email_id', 'label'=>false, 'options'=>$all_ral_list, 'value'=>$record_details['ro_email_id'],'class'=>'form-control')); ?>	
				<div id="error_ral_email_id"></div>
			</div>
			
		<?php } ?>
		
		<!-- added below RO offices dropdown for SO offices-->
		<?php if($record_details['office_type']=='SO'){ ?>
		
			<div id="ro_office_list" class="col-md-6">
			<h5>RO Office</h5>
				<?php echo $this->Form->control('ro_office_id', array('type'=>'select', 'id'=>'ro_office_id', 'label'=>false, 'options'=>$ro_office_list, 'value'=>$record_details['ro_id_for_so'],'class'=>'form-control')); ?>	
				<div id="error_ro_office_id"></div>
			</div>
			
		<?php } ?>
		<div class="col-md-3">
			<?php echo $this->Form->submit('Edit', array('name'=>'edit_ro_office', 'onclick'=>'masters_validation('.$masterId.');return false',  'label'=>false,'class'=>'form-control','style'=>'margin-top: 15px;')); ?>
		</div>
		
		
		<!-- to show when office type is RO -->
		<?php if($record_details['office_type']=='RO' || $record_details['office_type']=='SO'){ ?>
		
			<!-- Show current ro incharge details -->
			<div class="col-md-12">
			<h3>In-charge Details</h3>
			</div>
			
			<div class="col-md-4">
			<h5>In-charge Email Id</h5>

						<?php echo $this->Form->control('ro_email_id', array('type'=>'text', 'value'=>$record_details['ro_email_id'], 'label'=>false, 'readonly'=>true,'class'=>'form-control')); ?>	
						<div id="error_ro_email_id"></div>
			</div>
			
			<div class="col-md-4">
			<h5>In-charge Name</h5>

						<?php echo $this->Form->control('incharge_name', array('type'=>'text', 'value'=>$ro_incharge_name, 'label'=>false, 'readonly'=>true,'class'=>'form-control')); ?>	
						<div id="error_incharge_name"></div>
			</div>
			
			<div class="col-md-4">
			<h5>In-charge Mobile No.</h5>

						<?php echo $this->Form->control('incharge_mobile_no', array('type'=>'text', 'value'=>$ro_incharge_mobile_no, 'label'=>false, 'readonly'=>true,'class'=>'form-control')); ?>	
						<div id="error_incharge_mobile_no"></div>
			</div>
			
			<div class="clearfix"></div>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
			
	<!-- to show when office type is RO -->
	<?php if($record_details['office_type']=='RO' || $record_details['office_type']=='SO'){ ?>
		<!--  Reallocated the ro incharge to current ro office  (Done by pravin 01-09-2017)-->

			<div class="col-md-8  col-md-offset-2" style="margin-top: 30px;">
				<h3>Reallocate In-charge</h3>
					<div class="col-md-3">					
						<h5>All In-charge List</h5>					
					</div>					
					<div class="col-md-6">
							<?php echo $this->Form->control('ro_name_list', array('type'=>'select', 'options'=>$ro_incharge_name_list, 'label'=>false,'class'=>'form-control')); ?>	
							<div id="error_ro_name_list"></div>
					</div>			
					<div class="col-md-3">			
							<?php echo $this->Form->submit('Reallocate', array('name'=>'ro_reallocate', 'id'=>'ro_reallocate_btn', 'onclick'=>'masters_validation('.$masterId.');return false',  'style'=>'margin-top: -2px;', 'label'=>false,'class'=>'form-control')); ?>								
					</div>	
				<div class="clearfix"></div>	
			</div>

	<?php } ?>
	
<script>

$("#ro_reallocate_btn").click(function(){
	
	if(confirm('Please confirm you are sure to Reallocate Office In-charge? \nOnce Reallocated, All applications of this office will be avaiiable to newly appointed office In-charge.')){
		
	}else{return false;}
});
</script>
