
<?php echo $this->Html->css('approve_reading_form'); ?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">View Perform Test</li>
				</ol>
			</div>
		</div>
	</div>

	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Form->create(null, array('id'=>'frm_approve_reading','class'=>'form-group')); ?>
						<div class="card card-lims">
							<div class="card-header"><h3 class="card-title-new">Approve Test Results</h3></div>
							<div class="form-horizontal">
								<div class="card-body">
									<div class="row">
										<input type='hidden' value='<?php echo $_SESSION['posted_ro_office'] ; ?>' name='posted_ro_office' id='posted_ro_office'/>
										<input type='hidden' value='<?php echo $_SESSION['user_flag'] ; ?>' name='user_flag' id='user_flag'/>
										<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
										<input type="hidden" name="button" id="button"  class="form-control" value="view">
										<input type='hidden' id='test_code' name='test_code'  class="form-control"  value=""   >
										<input type='hidden' id='chemist_code' name='chemist_code'  class="form-control"  value=""   >
										<input type='hidden' id='final_result' name='final_result'  class="form-control"  value=""   >
										<input type="hidden" name="login_timestamp" id="login_timestamp"  class="form-control" value="<?php echo date('Y-m-d');?>">
										<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">

										<div class="col-md-12 row">
											<div class="col-md-3">
												<label>Sample Code <span class="required-star">*</span></label>
												<?php echo $this->Form->control('sample_code', array('type'=>'select', 'id'=>'sample_code', 'options'=>$samples_list, 'value'=>'', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
												<?php echo $this->Form->control('stage_sample_code', array('type'=>'hidden', 'id'=>'stage_sample_code','value'=>$stage_sample_code, 'label'=>false,'class'=>'form-control','required'=>true)); ?>
												<span id="error_sample_code" class="error invalid-feedback"></span>
											</div>
											<div class="col-md-3">
												<label>Category <span class="required-star">*</span></label>
												<?php echo $this->Form->control('category_code', array('type'=>'select', 'id'=>'category_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
												<span id="error_commodity_code" class="error invalid-feedback"></span>
												<input type="hidden" class="form-control" id="type" name="type"  hidden>
											</div>
											<div class="col-md-3">
												<label>Commodity <span class="required-star">*</span></label>
												<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
												<span id="error_commodity_code" class="error invalid-feedback"></span>
											</div>
											<div class="col-md-3">
												<label>Sample Type <span class="required-star">*</span></label>
												<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true,)); ?>
												<span id="error_sample_type" class="error invalid-feedback"></span>
											</div>
										</div>

										<!--Below code is Added in the fieldset for dispalying three color blocks. That is used for inform to user. BY Akash.-->
										<div class="col-md-12">
											<div class="progress_code middle">
												<span class="badge badge-pill pill_color_test_finalized"><b class="pill_color_test_finalized">w</b></span> - <strong>Test are Finalised.</strong>
												<span class="badge badge-pill pill_color_to_be_finalized"><b class="pill_color_to_be_finalized">w</b></span> - <strong>Test To be Finalised.</strong>
												<span class="badge badge-pill pill_color_selected"><b class="pill_color_selected">w</b></span> - <strong>Currently Selected.</strong>
											</div>
										</div>

										<div class="col-md-12 row">
											<div id="d1div" class="col-lg-6 col-md-6 col-sm-12 center-block">
												<div class="fsStyle1 dnone">
													<div class="table-responsive ap_rd_test_list">
														<table class="table table-bordered dnone " id="d1">
															<thead class="bgbe">
																<tr>
																	<th>S.No</th>
																	<th>Test Name</th>
																	<th>Final Result</th>
																</tr>
															</thead>
															<tbody></tbody>
														</table>
													</div>
												</div>
											</div>

											<div id="d2div" class="col-lg-6 col-md-6 col-sm-12 center-block ">
												<div class="fsStyle2 dnone">
													<div class="table-responsive">
														<table class="table table-bordered table-hover dnone" id="d2" >
															<thead class="bgbe">
																<tr>
																	<th></th>
																	<th></th>
																	<th></th>
																	<th></th>
																</tr>
															</thead>
															<tbody></tbody>
														</table>
													</div>

													<div class="col-md-10 mt-2 dnone"  id="button_id">
														<div class="row">
															<div class="col-sm-2"><input type='button' name='Final' id='final' class="btn btn-primary"   value='Accept' disabled></div>
															<div class="col-sm-3"><input type='button' name='Retest' id='re_test' class="btn btn-primary"   value='Re-test' disabled></div>
															<?php  if($_SESSION['user_flag']=="CAL"){?>
																<!--<label class="control-label">Duplicate Analysis</label>
																<input type="checkbox" name="duplicate_record" id="duplicate_record" value="DF" >-->
															<?php } ?>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="col-lg-12 col-md-12 col-sm-12 textAlignCenter mt-4">
											<span id="finalise_id" class="fiz" >
												<input type='button' name='Finalize' id='finalize' class="btn btn-primary"   disabled value='Finalize' title='This finalised button will be active once all test reading final' >
											</span>
											<span id="ral_id" class="fiz" >
												<input type='button' name='Send to RAL' id='ral' class="btn btn-primary"  disabled  value='Approve Result' >
											</span>
											<span id="cal_id" class="fiz" >
												<input type='button' name='Send to CAL' id='cal' class="btn btn-primary"  disabled  value='Send to Inward' >
											</span>
											<span id="ho_id" class="fiz" >
												<input type='button' name='Send to HO' id='ho' class="btn btn-primary"  disabled  value='Send to HO' >
											</span>
											<span id="closediv" class="pt-2">
												<a href="../ApproveReading/available_for_approve_reading" class="btn btn-primary">Cancel</a>
											</span>
										</div>

										<div class="row" id="menubuttons">
											<div class="col-lg-12 text-center" >
												<span><button class="btn btn-primary" disabled id="save">Save</button></span>
												<span></span>
												<span></span>
											</div>
										</div>
									</div> 
								</div>
							</div>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>

<input type="hidden" id="sess_user_flag" value="<?php echo $_SESSION['user_flag']; ?>">
<input type="hidden" id="sess_role" value="<?php echo $_SESSION['role']; ?> ">

<?php echo $this->Html->script('sample_forward_form'); ?>
<?php echo $this->Html->script('approve_reading_form'); ?>
<?php echo $this->Html->script('approveReading/approve_reading'); ?>
