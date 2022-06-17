
<?php echo $this->Html->css('radiobuttons'); ?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Available To Allocate', array('controller' => 'SampleAllocate', 'action'=>'available_to_allocate')); ?></li>
						<li class="breadcrumb-item active">Sample Allocate</li>
					</ol>
				</div>
			</div>
		</div>
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Form->create(null, array('id'=>'frm_sample_allocate','class'=>'form-group')); ?>
						<div class="card card-lims">
							<div class="form-horizontal">
								<div class="card-header"><h3 class="card-title-new">Allocate Sample to Chemist</h3></div>
									<div class="form-horizontal">
										<?php if (!empty($validate_err)) { ?><div class="alert alert-danger textAlignCenter text-danger"><?php echo $validate_err; ?></div><?php } ?>
											<div class="card-body">
												<div class="row pb-2">
													<?php
														$current_date = date('Y-m-d');
														$from_date = strtotime($current_date);
														$to_date = strtotime($current_date.' -1 year');

														if (date('m') <= 6) {
															$financial_year = (date('Y')-1) . '-' . date('Y');
														} else {
															$financial_year = date('Y') . '-' . (date('Y') + 1);
														}
													?>

													<input type="hidden" name="fin_year" id="fin_year"  class="form-control" value="<?php echo $financial_year; ?>">
													<input type="hidden" name="test_n_r_no" id="test_n_r_no"  class="form-control" value="1">
													<input type="hidden" name="tests" id="tests"  class="form-control" value="">
													<input type="hidden" name="alloc_by_user_code" id="alloc_by_user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
													<input type="hidden" name="posted_ro_office" id="posted_ro_office"  class="form-control" value="<?php echo $_SESSION["posted_ro_office"];?>">
													<input type="hidden" name="result_dupl_flag" id="result_dupl_flag"  class="form-control" value="">
													<input type="hidden" name="chemist_code" id="chemist_code"  class="form-control" value="">
													<input type="hidden" name="li_code" id="li_code"  class="form-control" value="">
													<input type="hidden" name="alloc_cncl_flag" id="alloc_cncl_flag"  class="form-control" value="N">
													<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
													<input type="hidden" name="alloc_date" id="alloc_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
													<input type="hidden" name="button" id="button"  class="form-control" value="view">
													<input type="hidden" name="login_timestamp" id="login_timestamp"  class="form-control" value="<?php echo date('Y-m-d H:i:s'); ?>">
													<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
													<input type="hidden" name="rec_from_dt" id="rec_from_dt" value="<?php echo date('d/m/Y',$from_date); ?>"/>
													<input type="hidden" name="rec_to_dt"  id="rec_to_dt" value="<?php echo date('d/m/Y', $to_date); ?>">
													<input type="hidden" name="type"  id="type" value="A"><!--For allocation-->

												<div class="col-md-12">
													<div class="row">
														<div class="col-md-3">
															<label>Sample Code <span class="required-star">*</span></label>
																<?php echo $this->Form->control('stage_sample_code', array('type'=>'select', 'id'=>'stage_sample_code', 'options'=>$allocate_sample_cd, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true)); ?>
															<div id="error_sample_code"></div>
														</div>
														<div class="col-md-3">
															<label>Category <span class="required-star">*</span></label>
																<?php echo $this->Form->control('category_code', array('type'=>'select', 'id'=>'category_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
															<div id="error_category_code"></div>
														</div>
														<div class="col-md-3">
															<label>Commodity <span class="required-star">*</span></label>
																<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
															<div id="error_commodity_code"></div>
														</div>
														<div class="col-md-3">
															<label>Sample Type <span class="required-star">*</span></label>
																<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
															<div id="error_sample_type"></div>
														</div>
													</div>
												</div>

												<div class="col-md-12 mt-3">
													<div class="row">
														<div class="col-md-3">
															<label>User Type <span class="required-star">*</span></label>
																<?php echo $this->Form->control('user_type', array('type'=>'select', 'id'=>'user_type', 'options'=>$user_type, 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
															<div id="error_user_type"></div>
														</div>
														<div class="col-md-3">
															<label>User Name <span class="required-star">*</span></label>
																<?php echo $this->Form->control('alloc_to_user_code', array('type'=>'select', 'id'=>'alloc_to_user_code', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
															<div id="error_user_code"></div>
														</div>
														<div class="col-md-2">
															<label>Quantity <span class="required-star">*</span></label>
															<?php echo $this->Form->control('sample_qnt', array('type'=>'number', 'id'=>'sample_qnt', 'label'=>false,'class'=>'form-control','placeholder'=>"Qty",'required'=>true,)); ?>
														</div>
														<div class="col-md-2">
															<label>Unit <span class="required-star">*</span></label>
															<?php echo $this->Form->control('sample_unit', array('type'=>'select','options'=>$unit_desc, 'id'=>'sample_unit', 'label'=>false,'empty'=>'---','class'=>'form-control','required'=>true,)); ?>
														</div>
														<div class="col-md-2">
															<label>Test <span class="required-star">*</span></label>
															<div class="colmd-12">
																<label><input type="radio" id="test_n_r" name="test_n_r" value="N" required class='test_n_r_n_r' checked> Normal</label>
															</div>
														</div>
													</div>
												</div>

												<div class="col-md-12 mt-3">
													<div class="row">
														<div class="col-md-3">
															<label>Expected Completion <span class="required-star">*</span></label>
																<?php echo $this->Form->control('expect_complt', array('type'=>'text', 'id'=>'expect_complt', 'label'=>false,'class'=>'form-control','placeholder'=>"dd/mm/yyyy",'required'=>true,)); ?>
																<div id="error_expect_complt"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
										<div id="select_test_div">
											<label id="flg" class="flagForAllocate alert alert-success"></label>
											<label id="qty" class="quantityForAllocate alert alert-info"></label>
												<div class="card-header"><h3 class="card-title" id="labelalloc">Select Tests to Allocate</h3></div>
													<div class="form-horizontal">
														<div class="card-body">
															<div class="row">
																<div class="col-md-5">
																	<div class="form-group row">
																		<select autocomplete="off" class="form-control" multiple="multiple" id="test_select" name="test_select[]" align="center">
																			<option value="-1" disabled="">------Select----- </option>
																		</select>
																	</div>
																</div>
																<div class="col-md-2 p-0">
																	<button type="button" autocomplete="off" class="btn btn-primary" name="name" id="moveleft">&gt;&gt;</button>
																	<button type="button" disabled="disabled" autocomplete="off" class="btn btn-primary" name="name" id="moveright">&lt;&lt;</button>
																</div>
																<div class="col-md-5">
																	<div class="form-group row">
																		<select autocomplete="off" class="form-control" multiple="multiple" id="test_select1" name="test_select1" align="center" >
																			<option value="-1" disabled="">------Select----- </option>
																		</select>
																	</div>
																</div>
															</div>
														<div id="allo_all_test_msg" class="allo_all"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer">
											<div class="col-md-12">
												<div class="col-md-2 float-left">
													<?php echo $this->Form->submit('Allocate', array('name'=>'save', 'id'=>'save', 'label'=>false,'class'=>'form-control btn btn-success')); ?>
												</div>
												<div class="col-md-2 float-right">
													<a href="../SampleAllocate/available_to_allocate" class="form-control btn btn-danger">Cancel</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php echo $this->Html->Script('sample_forward_form'); ?>
<?php echo $this->Html->Script('sample_allocate_form'); ?>
<?php echo $this->Html->Script('sampleAllocate/sample-allocate'); ?>
