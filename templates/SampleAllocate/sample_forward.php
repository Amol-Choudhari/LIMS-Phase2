<?php echo $this->Html->Script('sample_forward_form'); ?>
<?php echo $this->Html->Script('sample_allocate_form'); ?>
<?php echo $this->Html->css('sample_allocate_forward');?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'btn btn-secondary')); ?></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">Forward Sample to Lab Incharge</li>
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
							<div class="card-header"><h3 class="card-title-new">Forward Sample to Lab Incharge</h3></div>
							<div class="form-horizontal">
								<?php if (!empty($validate_err)) { ?><div class="alert alert-danger"><?php echo $validate_err; ?></div><?php } ?>
									<div class="card-body">
										<div class="row p-2">
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
											<input type="hidden" name="alloc_by_user_code" id="alloc_by_user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
											<input type="hidden" name="posted_ro_office" id="posted_ro_office"  class="form-control" value="<?php echo $_SESSION["posted_ro_office"];?>">
											<input type="hidden" name="result_dupl_flag" id="result_dupl_flag"  class="form-control" value="">
											<input type="hidden" name="li_code" id="li_code"  class="form-control" value="">
											<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
											<input type="hidden" name="button" id="button"  class="form-control" value="view">
											<input type="hidden" name="login_timestamp" id="login_timestamp"  class="form-control" value=""> 
											<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
											<input type="hidden" name="type"  id="type" value="F"><!--For Forwarding to Lab incharge-->
													
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-3">
														<label>Sample Code <span class="required-star">*</span></label>				
															<?php echo $this->Form->control('stage_sample_code', array('type'=>'select', 'id'=>'stage_sample_code', 'options'=>$forward_sample_cd, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true)); ?>	
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
												</div>
											</div>
											<div class="clear"></div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="col-md-12">
										<div class="float-left"><?php echo $this->Form->submit('Forward', array('name'=>'save', 'id'=>'save', 'label'=>false,'class'=>'btn btn-success')); ?></div>
										<div class="float-right"><a href="../SampleAllocate/available_to_allocate" class="btn btn-danger">Cancel</a></div>
									</div>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
	</div>
<?php echo $this->Html->script('sampleAllocate/sample_forward');?>