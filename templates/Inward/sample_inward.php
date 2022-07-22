<?php echo $this->Html->css('sampleinward'); ?>
<?php if (!empty($_SESSION['sample'])){ $sample_type_code = $_SESSION['sample']; } else { $sample_type_code=''; } ?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><h1 class="m-0 text-dark"></h1></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">New Sample</li>
					</ol>
				</div>
			</div>
		</div>

		<section class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mt-2">
						<?php echo $this->element('/progress_bars/sample_registration_progress'); ?>
						<?php echo $this->Form->create(null, array('id'=>'frm_sample_inward', 'name'=>'sampleForm','class'=>'form-group')); ?>
						<div class="card card-lims">
							<div class="form-horizontal">
								<div class="card-header"><h3 class="card-title-new">Sample Inward</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<?php if(!empty($validate_err)){ ?><div class="alert alert-danger textAlignCenter"><?php echo $validate_err; ?></div><div class="clearfix"></div><?php } ?>
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Sample Location <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('loc_id', array('type'=>'select', 'id'=>'loc_id', 'options'=>$users, 'value'=>$default_loc, 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
														<span id="error_loc_id" class="error invalid-feedback"></span>
													</div>
												</div>

												<div id="desDiv">
													<div class="form-group row marginB26">
														<label for="inputEmail3" class="col-sm-3 col-form-label">User Designation <span class="required-star">*</span></label>
														<div class="custom-file col-sm-9">
															<?php echo $this->Form->control('designation', array('type'=>'select', 'id'=>'designation','options'=>$desig_list, 'label'=>false, 'value'=>$default_desig,'empty'=>'--Select--','class'=>'form-control', 'required'=>true,)); ?>
															<span id="error_designation" class="error invalid-feedback"></span>
														</div>
													</div>
												</div>

												<div id="xyz">
													<div class="form-group row marginB26">
														<label for="inputEmail3" class="col-sm-3 col-form-label">Received From <span class="required-star">*</span></label>
															<div class="custom-file col-sm-9">
																<select class='form-control' name='users' id='users' ><option value='' >---Select---</option></select>
															<span id="error_loc_id" class="error invalid-feedback"></span>
														</div>
													</div>
												</div>
											</div>

											<div class="col-sm-6">
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Letter Reference Number <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('letr_ref_no', array('type'=>'text', 'id'=>'letr_ref_no','value'=>$sample_inward_data['letr_ref_no'], 'label'=>false,'class'=>'form-control','placeholder'=>'Enter a Letter No','required'=>true,)); ?>
														<span id="error_letr_ref_no" class="error invalid-feedback"></span>

														<!--Some required hidden fields -->
														<?php echo $this->Form->control('user_code', array('type'=>'hidden', 'id'=>'user_code', 'label'=>false,'value'=>$_SESSION['user_code'])); ?>
														<?php echo $this->Form->control('tran_date', array('type'=>'hidden', 'id'=>'tran_date', 'label'=>false,'value'=>date('Y-m-d'))); ?>

														<?php if (date('m') <= 6) { $financial_year = (date('Y')-1) . '-' . date('Y');
															} else { $financial_year = date('Y') . '-' . (date('Y') + 1); }
														?>
														<?php echo $this->Form->control('fin_year', array('type'=>'hidden', 'id'=>'fin_year', 'label'=>false,'value'=>$financial_year)); ?>

														<?php if (isset($sample_code)) {
																echo $this->Form->control('stage_sample_code', array('type'=>'hidden', 'id'=>'stage_sample_code', 'label'=>false,'value'=>$sample_code));
															} ?>

														<?php //echo $this->Form->control('login_timestamp', array('type'=>'hidden', 'id'=>'login_timestamp', 'label'=>false,'value'=>$timezone)); ?>
														<?php echo $this->Form->control('homCnt', array('type'=>'hidden', 'id'=>'homCnt', 'label'=>false,'value'=>'')); ?>
														<?php echo $this->Form->control('reject_date', array('type'=>'hidden', 'id'=>'reject_date','value'=>$sample_inward_data['reject_date'], 'label'=>false)); ?>
													</div>
												</div>

												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Letter Date <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('letr_date', array('type'=>'text', 'id'=>'letr_date','value'=>$sample_inward_data['letr_date'], 'label'=>false,'class'=>'form-control','placeholder'=>"dd/mm/yyyy",'required'=>true,)); ?>
														<span id="error_letr_date" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Inward Date <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('received_date', array('type'=>'text', 'id'=>'received_date','value'=>$sample_inward_data['received_date'], 'label'=>false,'class'=>'form-control','placeholder'=>"dd/mm/yyyy",'required'=>true,)); ?>
														<span id="error_received_date" class="error invalid-feedback"></span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div id="sample_code_div" hidden>
									<div class="card-header"><h3 class="card-title-new">Registered Sample</h3></div>
									<div class="form-horizontal">
										<div class="card-body">
											<div class="row">
												<div class="col-sm-6">
													<div class="form-group row marginB26">
														<div class="custom-file col-sm-9">
															<?php echo $this->Form->control('sample_code', array('type'=>'text', 'id'=>'sample_code', 'label'=>false,'class'=>'form-control','placeholder'=>"Registered Sample")); ?>
															<span id="error_sample_code" class="error invalid-feedback"></span>
														</div>
														<div class="clear sec_divi"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div class="card-header"><h3 class="card-title-new">Sample Condition & Parcel Condition</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Type Of Sample <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('sample_type_code', array('type'=>'select','options'=>$Sample_Type,'value'=>$sample_type_code, 'id'=>'sample_type_code', 'label'=>false,'empty'=>'--Select Sample--','class'=>'form-control','required'=>true,)); ?>
														<span id="error_Sample_Type" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Container Type <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('container_code', array('type'=>'select','options'=>$con,'value'=>$sample_inward_data['container_code'], 'id'=>'container_code', 'label'=>false,'empty'=>'--Select Container--','class'=>'form-control','required'=>true,)); ?>
														<span id="error_container_code" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Physical Appearance <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('entry_flag', array('type'=>'select','options'=>$phy_app,'value'=>$sample_inward_data['entry_flag'], 'id'=>'entry_flag', 'label'=>false,'empty'=>'--Select Physical Appearance--','class'=>'form-control','required'=>true,)); ?>
														<span id="error_entry_flag" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Package Condition <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('par_condition_code', array('type'=>'select','options'=>$parcel_condition,'value'=>$sample_inward_data['par_condition_code'], 'id'=>'par_condition_code', 'label'=>false,'empty'=>'--Select Package Condition--','class'=>'form-control','required'=>true,)); ?>
														<span id="error_parcel_condition" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Sample Condition <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('sam_condition_code', array('type'=>'select','options'=>$sample_condition,'value'=>$sample_inward_data['sam_condition_code'], 'id'=>'sam_condition_code', 'label'=>false,'empty'=>'--Select Sample Condition--','class'=>'form-control','required'=>true,)); ?>
														<span id="error_sample_condition" class="error invalid-feedback"></span>
													</div>
												</div>
											</div>

											<div class="col-sm-6">
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Quantity <span class="required-star">*</span></label>
													<div class="custom-file col-sm-3">
														<?php echo $this->Form->control('sample_total_qnt', array('type'=>'number', 'id'=>'sample_total_qnt','value'=>$sample_inward_data['sample_total_qnt'], 'label'=>false,'class'=>'form-control','placeholder'=>"Enter the Quanity",'required'=>true,)); ?>
														<span id="error_sample_total_qnt" class="error invalid-feedback"></span>
													</div>
													<div class="col-md-6">
														<label for="inputEmail3" class="col-sm-3 col-form-label">Unit <span class="required-star">*</span></label>
														<div class="custom-file col-sm-5">
															<?php echo $this->Form->control('parcel_size', array('type'=>'select','options'=>$grade_desc,'value'=>$sample_inward_data['parcel_size'], 'id'=>'parcel_size', 'label'=>false,'empty'=>'--Select Units--','class'=>'form-control','required'=>true,)); ?>
															<span id="error_parcel_size" class="error invalid-feedback"></span>
														</div>
													</div>
												</div>

												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Commodity Category <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('category_code', array('type'=>'select','options'=>$commodity_category,'value'=>$sample_inward_data['category_code'], 'id'=>'category_code', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true,)); ?>
														<span id="error_category_code" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Commodity <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code','options'=>$commodity_list,'value'=>$sample_inward_data['commodity_code'], 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true,)); ?>
														<span id="error_commodity_code" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Reference Source Code <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('ref_src_code', array('type'=>'text', 'id'=>'ref_src_code','value'=>$sample_inward_data['ref_src_code'], 'label'=>false,'class'=>'form-control','placeholder'=>"Enter Ref. Code",'required'=>true,)); ?>
														<span id="error_ref_src_code" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Expiry Month <span class="required-star">*</span></label>
													<div class="custom-file col-sm-4">
														<?php echo $this->Form->control('expiry_month', array('type'=>'select','options'=>$monthArray,'value'=>$sample_inward_data['expiry_month'], 'id'=>'expiry_month', 'label'=>false,'class'=>'form-control','required'=>true,'empty'=>'--Select Month--')); ?>
														<span id="error_expiry_month" class="error invalid-feedback"></span>
													</div>

													<label for="inputEmail3" class="col-sm-2 col-form-label">Year <span class="required-star">*</span></label>
													<div class="custom-file col-sm-3">
														<?php echo $this->Form->control('expiry_year', array('type'=>'text', 'id'=>'expiry_year','value'=>$sample_inward_data['expiry_year'], 'label'=>false,'class'=>'form-control','placeholder'=>'Select year','required'=>true,)); ?>
														<span id="error_expiry_year" class="error invalid-feedback"></span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
				
								<div class="card-header"><h3 class="card-title-new">Customer's Details</h3></div>
								<div class="form-horizontal">
									<div class="card-body marginB10">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Customer's Name <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('customer_name', array('type'=>'text','id'=>'customer_name','escape'=>false,'class'=>'form-control input-field','label'=>false,'placeholder'=>'Enter the customer full name','value'=>$customer_details['customer_name'])); ?>
														<span id="error_customer_name" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Email Id <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('customer_email_id', array('type'=>'text', 'id'=>'customer_email_id', 'escape'=>false,'class'=>'form-control input-field','label'=>false,'placeholder'=>'Enter the email id','value'=>base64_decode($customer_details['customer_email_id']))); ?>
														<span id="error_customer_email_id" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Address <span class="required-star">*</span></label>
													<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('street_address', array('type'=>'textarea', 'id'=>'street_address', 'escape'=>false, 'class'=>'form-control input-field','label'=>false,'placeholder'=>'Enter the customer address','value'=>$customer_details['street_address'])); ?>
														<span id="error_street_address" class="error invalid-feedback"></span>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">State/Region <span class="required-star">*</span></label>
														<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('state', array('type'=>'select', 'id'=>'state','label'=>false,'class'=>'form-control','options'=>$states,'empty'=>'--Select State--','value'=>$customer_details['state'])); ?>
														<span id="error_state" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">District <span class="required-star">*</span></label>
														<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('district', array('type'=>'select', 'id'=>'district','label'=>false, 'class'=>'form-control','options'=>array(),'empty'=>'--Select District--','value'=>$customer_details['district'])); ?>
														<span id="error_district" class="error invalid-feedback"></span>

													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Pin Code <span class="required-star">*</span></label>
														<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('postal_code', array('type'=>'text', 'id'=>'postal_code', 'escape'=>false,'class'=>'form-control input-field','label'=>false,'placeholder'=>'Enter the pincode','value'=>$customer_details['postal_code'])); ?>
														<span id="error_postal_code" class="error invalid-feedback"></span>

													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Mobile No. <span class="required-star">*</span></label>
														<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('customer_mobile_no', array('type'=>'text', 'id'=>'customer_mobile_no', 'escape'=>false,'class'=>'form-control input-field','label'=>false,'placeholder'=>'Enter the customer mobile number','value'=>base64_decode($customer_details['customer_mobile_no']))); ?>
														<span id="error_customer_mobile_no" class="error invalid-feedback"></span>
													</div>
												</div>
												<div class="form-group row marginB26">
													<label for="inputEmail3" class="col-sm-3 col-form-label">Phone No.</label>
														<div class="custom-file col-sm-9">
														<?php echo $this->Form->control('customer_fax_no', array('type'=>'text', 'id'=>'customer_fax_no', 'escape'=>false,'class'=>'form-control input-field','label'=>false,'placeholder'=>'Enter the phone number','value'=>$customer_details['customer_fax_no'])); ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							

								<div class="col-md-6 offset-3">
									<div class="card">
										<div class="card-body">
											<div class="row">
												<div class="col-sm-12">
													<div class="row">
														<div class="col-sm-4">
															<div class="form-group">
																<label class="radio-inline "><input class=" validate[required] radio" type="radio" id="acc_rej_flg" name="acc_rej_flg" value="A" required <?php if(trim($sample_inward_data['acc_rej_flg'])=='A' || trim($sample_inward_data['acc_rej_flg'])=='PS'){ echo 'checked';} ?> > Accepted</label>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="form-group">
																<label class="radio-inline "><input class="validate[required] radio" type="radio" id="acc_rej_flg1"  name="acc_rej_flg" value="R" required <?php if(trim($sample_inward_data['acc_rej_flg'])=='R'){ echo 'checked';} ?> > Rejected</label>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="form-group">
																<label class="radio-inline "><input class="validate[required] radio" type="radio" id="acc_rej_flg2"  name="acc_rej_flg" value="P" required <?php if(trim($sample_inward_data['acc_rej_flg'])=='P'){ echo 'checked';} ?> > Pending</label>
															</div>
														</div>
														<input type="hidden" id="inward_id"  name="inward_id">
													</div>
												</div>

												<div class="col-sm-12">
													<div id="abc">
														<div class="form-group">
															<label for="inputEmail3" class="col-sm-3 col-form-label">Reason <span class="required-star">*</span></label>
															<?php echo $this->Form->control('rej_code', array('type'=>'select','options'=>$rej,'value'=>$sample_inward_data['rej_code'], 'empty'=>'--Select--', 'id'=>'rej_code', 'label'=>false,'class'=>'form-control')); ?>
															<div id="error_rej_code" class="error invalid-feedback"></div>
														</div>
														<div class="form-group">
															<label for="inputEmail3" class="col-sm-3 col-form-label">Remark <span class="required-star">*</span></label>
															<?php echo $this->Form->control('rej_reason', array('type'=>'textarea', 'id'=>'rej_reason','value'=>$sample_inward_data['rej_reason'], 'label'=>false,'class'=>'form-control')); ?>
															<span id="error_rej_reason" class="error invalid-feedback"></span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="card-footer">
								<div class="col-md-12">
									<!--if confirm then hide btns Added the PV flag condtion if sample is commercial - 30-06-2022 -->
									<?php if (!(trim($sample_inward_data['status_flag'])=='S' || trim($sample_inward_data['status_flag'])=='PV')) { ?>

										<div class="col-md-1 float-left">
											<?php if (!(trim($sample_inward_data['status_flag'])=='')){
												echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false,'class'=>'btn btn-success'));
											} else {
												echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false,'class'=>'btn btn-success'));
											} ?>
										</div>

										<?php if ($confirmBtnStatus=='show') { ?>
											<div class="col-md-1 float-left">
												<?php echo $this->Form->submit('Confirm', array('name'=>'confirm', 'id'=>'confirm', 'label'=>false,'class'=>'btn btn-success')); ?>
											</div>
										<?php }
									} ?>

									<div class="col-md-1 float-right"><a href="../Dashboard/home" class="btn btn-danger">Cancel</a></div>
								
									<?php if ($_SESSION['user_flag']=='RO' || $_SESSION['user_flag']=='SO') { ?>
										<?php if (!(trim($sample_inward_data['status_flag'])=='')) { ?>
											<div class="col-md-2 float-left"><a href="../InwardDetails/sample_inward_details" class="btn btn-primary">Next Section</a></div>
										<?php	} ?>
									<?php } ?>

									<?php if ($_SESSION['user_flag']=='RAL' || $_SESSION['user_flag']=='CAL') { ?>
										<?php if (trim($_SESSION['is_payment_applicable'] =='yes')) { ?>
											<div class="col-md-2 float-left"><a href="../payment/payment_details" class="btn btn-primary">Next Section</a></div>
										<?php	} ?>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
	</div>

<?php  if(empty($sample_inward_data['users'])){ $receivedfrom = $_SESSION['user_code']; }else{ $receivedfrom = $sample_inward_data['users']; } ?>
<?php if (isset($_SESSION['org_sample_code'])) { $org_sample_code = $_SESSION['org_sample_code']; }else{ $org_sample_code = '' ; } ?>
<input type="hidden" id="acc_rej_flg" value="<?php echo $sample_inward_data['acc_rej_flg']; ?> ">
<input type="hidden" id="org_sample_code" value="<?php echo $org_sample_code; ?> ">
<input type="hidden" id="sample_status" value="<?php echo trim($sample_inward_data['status_flag']); ?>">
<input type="hidden" id="receivedfrom" value="<?php echo $receivedfrom; ?>">
<?php if(empty($sample_type_code)){ $sample_type = ''; }else{ $sample_type = $sample_type_code; } ?>
<input type="hidden" id="sample_type" value="<?php echo $sample_type; ?>">
<?php echo $this->Html->Script('inward/sample_inward'); ?>
<?php echo $this->Html->Script('sample_reg_form'); ?>
