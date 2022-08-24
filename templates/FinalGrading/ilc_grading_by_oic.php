<?php echo $this->Html->css("finalGrading/grading_by_inward"); ?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
							<li class="breadcrumb-item active">Verify Results</li>
						</ol>
					</div>
				</div>
			</div>
			<section class="content form-middle">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<?php echo $this->Form->create(null, array('id'=>'frm_final_grading','class'=>'form-group')); ?>
								<div class="card card-lims">
									<div class="card-header"><h3 class="card-title-new">Finalized Test Report</h3></div>
										<div class="form-horizontal">
											<div class="card-body">
												<div class="row">
													<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
													<input type="hidden" name="button" id="button"  class="form-control" value="view">
													<input type="hidden" name="array" id="array"  class="form-control" value="">
													<input type="hidden" name="login_timestamp" id="login_timestamp"  class="form-control" value="<?php echo date('Y-m-d');?>">
													<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">

													<div class="col-md-12">
														<div class="row">
															<div class="col-md-3">
																<label>Sample Code <span class="required-star">*</span></label>
																	<?php echo $this->Form->control('sample_code', array('type'=>'select', 'id'=>'sample_code', 'options'=>$samples_list, 'value'=>'', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
																	<?php echo $this->Form->control('stage_sample_code', array('type'=>'hidden', 'id'=>'stage_sample_code','value'=>$stage_sample_code, 'label'=>false,'class'=>'form-control','required'=>true)); ?>
																<div id="error_sample_code"></div>
															</div>
															<div class="col-md-3">
															<label>Category <span class="required-star">*</span></label>
																<?php echo $this->Form->control('category_code', array('type'=>'select', 'id'=>'category_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
																<div id="error_commodity_code"></div>
															<input type="hidden" class="form-control" id="type" name="type"  hidden>
														</div>
														<div class="col-md-3">
															<label>Commodity <span class="required-star">*</span></label>
																<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
															<div id="error_commodity_code"></div>
														</div>
														<div class="col-md-3">
															<label>Sample Type <span class="required-star">*</span></label>
																<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true,)); ?>
															<div id="error_sample_type"></div>
														</div>
														</div>
													</div>
													<div class="clear"></div>
													<div class="clear"></div>

													<div class="col-md-12">
														<div class="row pt-3">
															<div class="col-md-3">
															<div class="col-md-3 d-inline"><a target="blank" id="clickpreview" href="../FinalGrading/sampleTestReportCode/<?php echo trim($stage_sample_code).'/'.$smple_commdity_code; ?>" >Preview Report</a></div>
															</div>
															<div class="col-md-5">
															<?php echo $this->Form->control('gradeListChecked', array('type'=>'checkbox', 'id'=>'reportlink', 'label'=>'I have previewed the report & agree to proceed')); ?>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label class="radio-inline"><input type="radio" id="result_flg"  name="result_flg" value="R"  class='test_p_f_m' required >Re-test</label>
																	<label class="radio-inline"><input type="radio" id="result_flg"  name="result_flg" value="N"  class='test_p_f_m' required >None</label>
																</div>
															</div>
														</div>
													</div>

													<div class="col-md-12">
														<div id="abc" class="row">
															<div class="col-md-6">
																<div class="form-group" id="byINW">
																	<label>Remark by Inward Officer</label>
																	<?php echo $this->Form->control('remark', array('type'=>'textarea', 'id'=>'remark','label'=>false,'class'=>'form-control','required'=>true, 'disabled'=>true)); ?>
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group" id="byDOL">
																	<label>Remark by Office Incharge</label>
																		<?php echo $this->Form->control('remark_new', array('type'=>'textarea', 'id'=>'remark_new','label'=>false,'class'=>'form-control','required'=>true,)); ?>
																	</div>
																</div>
															</div>
															<!--create hidden field, to get user role value, -->
															<input type="hidden" id="user_role_x" name="user_role_x" value="<?php echo $_SESSION["role"]; ?>">
													</div>
													<div class="col-md-12">
														<div class="col-md-2 float-left p-3">
															<?php echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false,'class'=>'form-control btn btn-success')); ?>
														</div>
														<div class="col-md-2 float-right p-3">
															<a href="../Dashboard/home" class="form-control btn btn-danger">Cancel</a>
														</div>
													</div>

												</div>
											</div>
										</div>
								</div>
								<?php echo $this->element('esign_consent/consent_for_esign'); ?>
								<?php echo $this->Form->end(); ?>
						</div>
					</div>
				</div>
			</section>
</div>
<?php echo $this->Html->Script('sample_grading_by_oic'); ?>

 <?php echo $this->Html->Script('finalGrading/ilc_grading_by_oic'); ?> 
