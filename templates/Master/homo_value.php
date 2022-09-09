
<?php echo $this->Html->Script('sample_reg_form'); ?>
<?php date_default_timezone_set('Asia/Kolkata'); ?>
<?php echo $this->Html->css("master/homo_value"); ?>

<?php

	$single_status = '';
	$yesno_status = 'checked';

	// assign field's default value
	if(isset($_SESSION['homo_value_code']) && isset($_SESSION['homo_value_data'])){

		$m_sample_obs_type_code = $homo_value_code;
		$m_sample_obs_code = $homo_value_data['m_sample_obs_code'];
		$m_sample_obs_type_value = $homo_value_data['m_sample_obs_type_value'];
		$title = 'Edit Homogenization Value';
		$dropdown_status = 'readonly';

		// checked single or yes no option by default
		if($homo_value_data['m_sample_obs_type_value']!='Yes' && $homo_value_data['m_sample_obs_type_value']!='No'){

			$single_status = 'checked';
			$yesno_status = '';
			$old_val_type = 'single';
			$single_val_req_status = 'required';

		} else {
			
			$m_sample_obs_type_value = 'No';  //added by shankhpal shende on 05/09/2022 
			$old_val_type = 'yesno';
			$single_val_req_status = '';
		}

	} else {

		$m_sample_obs_type_code = '';
		$m_sample_obs_code = '';
		$m_sample_obs_type_value = '';
		$title = 'Add Homogenization Value';
		$dropdown_status ='';
		$old_val_type = '';
		$single_val_req_status = '';
	}

?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
				</ol>
			</div>
		</div>
	</div>

	<section class="content form-middle mt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-10">
					<?php echo $this->Form->create(null, array('id'=>'frm_homo_value', 'name'=>'homoValueForm','class'=>'form-group')); ?>
						<div class="card card-lims">
							<div class="card-header"><h3 class="card-title-new"><?php echo $title; ?></h3></div>
							<div class="form-horizontal">
								<?php if(!empty($validate_err)){ ?><div class="badge badge-danger p-2"><?php echo $validate_err; ?></div><?php } ?>
								<div class="card-body">
									<div class="row">
										<?php echo $this->Form->control('m_sample_obs_type_code', array('type'=>'hidden', 'id'=>'m_sample_obs_type_code', 'value'=>$m_sample_obs_type_code, 'label'=>false)); ?>
										<?php echo $this->Form->control('login_timestamp', array('type'=>'hidden', 'id'=>'login_timestamp', 'label'=>false, 'value'=>date('Y-m-d H:i:s'))); ?>
										<?php echo $this->Form->control('old_val_type', array('type'=>'hidden', 'id'=>'old_val_type', 'label'=>false, 'value'=>$old_val_type)); ?>

										<div class="col-md-4">
											<label>Homogenization Field <span class="required-star">*</span></label>
												<select name="m_sample_obs_code" id="m_sample_obs_code" class="form-control" required="" <?php echo $dropdown_status; ?>>
													<option value="">--Select--</option>
														<?php foreach($homoValueArray as $valData){ ?>
															<option value="<?php $cat_code = $valData['m_sample_obs_code']; echo $cat_code; ?>" <?php if($cat_code==$m_sample_obs_code){ echo 'selected'; } ?>><?php echo $valData['m_sample_obs_desc']; ?></option>
														<?php } ?>
												</select>
											<div class="error-msg" id="error_m_sample_obs_code"></div>
										</div>

										<div class="col-md-8">
											<div class="col-md-10">
												<label>Select Value Type</label>
													<input type="radio" name="val_type" id="val_type_one" class="val_type" value="single" <?php echo $single_status; ?>> Single Value
													<input type="radio" name="val_type" id="val_type_two" class="val_type" value="yesno" <?php echo $yesno_status; ?>> Yes / No
												<div class="error-msg" id="error_val_type"></div>
											</div>

											<div class="col-md-4" id="val_type_one_div">
												<label>Value</label>
													<?php echo $this->Form->control('m_sample_obs_type_value', array('type'=>'text', 'id'=>'m_sample_obs_type_value', 'value'=>$m_sample_obs_type_value, 'label'=>false, 'class'=>'form-control', 'placeholder'=>'Enter Value', 'maxLength'=>'70', $single_val_req_status)); ?>
												<div class="error-msg" id="error_m_sample_obs_type_value"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-footer mt-4">
								<?php if (isset($_SESSION['homo_value_code']) && isset($_SESSION['homo_value_data'])) {
									echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));
								} else {
									echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));
								} ?>

								<a href="saved-homo-value" class="float-right btn btn-danger">Cancel</a>
							</div>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>
<input type="hidden" id="single_status" value="<?php echo $single_status; ?>">
<?php echo $this->Html->script("master/homo_value"); ?>
