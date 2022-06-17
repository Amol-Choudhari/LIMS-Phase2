<?php ?>
	<div class="content-wrapper">
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2"><div class="col-sm-6"><label class="badge badge-info">User Profile</label></div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home'));?></a></li>
							<li class="breadcrumb-item active">User Profile</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->Form->create(null, array('id'=>'updateprofiledetails', 'enctype'=>'multipart/form-data')); ?>
			<div class="form-style-3 content form-middle">
				<div class="card card-cyan">
					<?php foreach ($user_data as $user_data_value) { ?>
						<div class="card-header"><h3 class="card-title">Name</h3></div>
							<div class="form-horizontal">
								<div class="card-body">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="field3"><span>First Name <span class="required-star">*</span></span></label>
													<?php echo $this->Form->control('f_name', array('label'=>'', 'escape'=>false, 'id'=>'f_name', 'value'=>$user_data_value['f_name'], 'class'=>'form-control')); ?>
												<div id="error_f_name"></div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="field3"><span>Last Name <span class="required-star">*</span></span></label>
													<?php echo $this->Form->control('l_name', array('label'=>'', 'escape'=>false, 'id'=>'l_name', 'value'=>$user_data_value['l_name'], 'class'=>'form-control')); ?>
												<div id="error_l_name"></div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="uneditable" for="field3"><span>Email <span class="required-star">*</span></span></label>
													<?php echo $this->Form->control('email', array('label'=>'', 'escape'=>false, 'id'=>'email', 'value'=>$user_data_value['email'], 'class'=>'form-control', 'readonly'=>true)); ?>
												<div id="error_email"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<div class="card-header mt-2"><h3 class="card-title">Other Details</h3></div>
							<div class="form-horizontal">
								<div class="card-body">
									<div class="row">

										<!-- commented on 15-06-2018 by Amol, no provision to store aadhar	-->
										<!--<label for="field3"><span>Aadhar card No. <span class="required">*</span></span>
												<?php //echo $this->Form->control('once_card_no', array('type'=>'text', 'escape'=>false, 'id'=>'once_card_no', 'value'=>$decrypted_aadhar, 'readonly'=>true, 'label'=>false)); ?>
												<div id="error_aadhar_card_no"></div>
											</label>-->

											<div class="col-md-4">
												<label class="uneditable" for="field3"><span>Mobile No. <span class="required-star">*</span></span></label>
													<?php echo $this->Form->control('phone', array('label'=>'', 'escape'=>false, 'id'=>'phone', 'class'=>'form-control','value'=>$user_data_value['phone'], 'readonly'=>true)); ?>
												<div id="error_phone"></div>
											</div>
											<div class="col-md-4">
												<!-- added on 12-05-2017 by Amol(for landline no.) -->
												<label for="field3"><span>Landline No. <span class="required"></span></span></label>
													<?php echo $this->Form->control('landline', array('label'=>'', 'value'=>base64_decode($user_data_value['landline']),'class'=>'form-control', 'escape'=>false, 'id'=>'landline_phone')); ?>
												<div id="error_landline_phone"></div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<div class="form-group row">
														<label for="field3"><span>Profile Picture
															<?php if(!empty($user_data_value['profile_pic'])){ ?>
																	<a  target="_blank" href="<?php echo str_replace("D:/xampp/htdocs","",$user_data_value['profile_pic']); ?>">: Preview</a>
																<?php } ?>
															<span class="required-star">*</span></span>
														</label>
														<div class="custom-file">
															<input type="file" class="custom-file-input" id="profile_pic" name="profile_pic" multiple='multiple'>
															<label class="custom-file-label" for="customFile">Choose file</label>
															<span id="error_profile_pic" class="error invalid-feedback"></span>
															<span id="error_size_profile_pic" class="error invalid-feedback"></span>
															<span id="error_type_profile_pic" class="error invalid-feedback"></span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
									<div class="card-header"><h3 class="card-title">Assigned Roles</h3></div>
										<div class="form-horizontal">
											<div class="card-body">
												<div class="row">

													<?php foreach($assigned_old_roles as $each_role){ ?>
														<div class=" master_home col-md-4">
															<?php if($each_role['add_user']=='yes'){ ?><label>Add User</label><?php } ?>

															<?php if($each_role['page_draft']=='yes'){ ?><label>Page (Draft only)</label><?php } ?>

															<?php if($each_role['page_publish']=='yes'){ ?><label>Page Publish</label><?php } ?>

															<?php if($each_role['menus']=='yes'){ ?><label>Menus</label><?php } ?>

															<?php if($each_role['mo_smo_inspection']=='yes'){ ?><label>MO/SMO</label><?php } ?>

															<?php if($each_role['io_inspection']=='yes'){ ?><label>Inspection Officer</label><?php } ?>

															<?php if($each_role['ro_inspection']=='yes'){ ?><label>RO In-Charge</label><?php } ?>

															<?php if($each_role['allocation_mo_smo']=='yes'){ ?><label>Allocate to MO/SMO</label><?php } ?>

															<?php if($each_role['allocation_io']=='yes'){  ?><label>Allocate to IO</label><?php } ?>

															<?php if($each_role['reallocation']=='yes'){  ?><label>Re-Allocate</label><?php } ?>

															<?php if($each_role['super_admin']=='yes'){ ?><label>Super Admin</label><?php } ?>

															<?php if($each_role['sample_inward']=='yes'){ ?><label>Sample Inward</label><?php } ?>

															<?php if($each_role['sample_forward']=='yes'){ ?><label>Sample Forward</label><?php } ?>

															<?php if($each_role['sample_allocated']=='yes'){ ?><label>Allocate Sample</label><?php } ?>

														</div>
														<div class=" master_home col-md-4">

															<?php if($each_role['form_verification_home']=='yes'){ ?><label>Form Scrutiny Home</label><?php } ?>

															<?php if($each_role['allocation_home']=='yes'){ ?><label>Allocation Home</label><?php } ?>

															<?php if($each_role['view_reports']=='yes'){  ?><label>View Reports</label><?php } ?>

															<?php if($each_role['file_upload']=='yes'){  ?><label>Upload Files</label><?php } ?>

															<?php if($each_role['dy_ama']=='yes'){  ?><label>Dy. AMA (QC)</label><?php } ?>

															<?php if($each_role['ho_mo_smo']=='yes'){  ?><label>HO MO/SMO</label><?php } ?>

															<?php if($each_role['jt_ama']=='yes'){  ?><label>Jt. AMA</label><?php } ?>

															<?php if($each_role['ama']=='yes'){  ?><label>AMA</label><?php } ?>

															<?php if($each_role['allocation_dy_ama']=='yes'){  ?><label>Forward to Dy. AMA</label><?php } ?>

															<?php if($each_role['allocation_ho_mo_smo']=='yes'){  ?><label>Allocate to HO MO/SMO</label><?php } ?>

															<?php if($each_role['sample_testing_progress']=='yes'){ ?><label>Test Progress</label><?php } ?>

															<?php if($each_role['sample_result_approval']=='yes'){ ?><label>Approve Results</label><?php } ?>

															<?php if($each_role['finalized_sample']=='yes'){ ?><label>Finalized Results</label><?php } ?>

															<?php if($each_role['administration']=='yes'){ ?><label>Administration</label><?php } ?>

														</div>

														<div class=" master_home col-md-4">

															<?php if($each_role['allocation_jt_ama']=='yes'){   ?><label>Forward to Jt. AMA</label><?php } ?>

															<?php if($each_role['allocation_ama']=='yes'){  ?><label>Forward to AMA</label><?php } ?>

															<?php if($each_role['masters']=='yes'){  ?><label>Masters</label><?php } ?>

															<?php if($each_role['super_admin']=='yes'){  ?><label>Super Admin</label><?php } ?>

															<?php if($each_role['renewal_verification']=='yes'){  ?><label>Renewal Scrutiny</label><?php } ?>

															<?php if($each_role['renewal_allocation']=='yes'){  ?><label>Renewal Allocation</label><?php } ?>

															<?php if($each_role['pao']=='yes'){  ?><label>PAO/DDO</label><?php } ?>

															<?php if($each_role['once_update_permission']=='yes'){  ?><label>Aadhar update Permission</label><?php } ?>

															<?php if($each_role['old_appln_data_entry']=='yes'){  ?><label>Old Applications Data Entry</label><?php } ?>

															<?php if($each_role['so_inspection']=='yes'){  ?><label>SO In-Charge</label><?php } ?>

															<?php if($each_role['smd_inspection']=='yes'){  ?><label>SMD In-Charge</label><?php } ?>

															<?php if($each_role['verify_sample']=='yes'){ ?><label>Result Verification</label><?php } ?>

															<?php if($each_role['reports']=='yes'){ ?><label>View Reports</label><?php } ?>

															<?php if($each_role['out_forward']=='yes'){ ?><label>Out Forward</label><?php } ?>
														</div>
													<?php } ?>
												</div>
											</div>
										</div>
										<div class="form-buttons card-footer">
											<?php echo $this->Form->control('Back', array('type'=>'submit', 'name'=>'ok', 'label'=>false,'class'=>'btn btn-primary float-right')); ?>
											<?php echo $this->Form->control('Update', array('type'=>'submit', 'name'=>'update', 'label'=>false, 'id'=>"updateprofile", 'class'=>'btn btn-success float-left')); ?>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Html->script("users/user_profile"); ?>
