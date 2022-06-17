<?php echo $this->Html->css("test/enter_test_reading"); ?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Sample Test Readings</li>
					</ol>
				</div>
			</div>
		</div>

		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(null, array('id'=>'test','action'=>'finalizeSampleResult','class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">Sample Test Readings</h3></div>
									<div class="card-horizontal">
										<div class="card-body">
											<div class="row">
												<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
												<input type="hidden" name="test_perfm_date" id="test_perfm_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
												<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
												<input type="hidden" name="posted_ro_office" id="posted_ro_office"  class="form-control" value="<?php echo $_SESSION["posted_ro_office"];?>">

												<div class="col-md-3">
													<label class="float-left">Sample Code <span class="required-star">*</span></label>
														<?php echo $this->Form->control('sample_code', array('type'=>'select', 'id'=>'sample_code', 'options'=>$chemist_code, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true)); ?>
														<?php echo $this->Form->control('stage_sample_code', array('type'=>'hidden', 'id'=>'stage_sample_code', 'value'=>$stage_sample_code, 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<div id="error_sample_code"></div>
												</div>
												<div class="col-md-3">
													<label class="float-left">Category <span class="required-star">*</span></label>
														<?php echo $this->Form->control('category_code', array('type'=>'select', 'id'=>'category_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
														<div id="error_commodity_code"></div>
													<input type="hidden" class="form-control" id="type" name="type"  hidden>
												</div>
												<div class="col-md-3">
													<label class="float-left">Commodity <span class="required-star">*</span></label>
														<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
														<div id="error_commodity_code"></div>
													<input type="hidden" class="form-control" id="type" name="type"  hidden>
												</div>
												<div class="col-md-3">
													<label class="float-left">Sample Type <span class="required-star">*</span></label>
														<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
													<div id="error_sample_type"></div>
												</div>
												<div class="clear"></div>

												<div class="col-md-12 col-xs-8 col-sm-6 mt-3">
													<div class="row">
														<label class="control-label col-md-4 col-xs-6 col-sm-4 tleft" for="sel1" id="sample_alloc"></label>
														<label class="control-label col-md-4 col-xs-6 col-sm-4 tcenter" for="sel1" id="sample_acc"></label>
														<label class="control-label col-md-4 col-xs-6 col-sm-4" for="sel1" id="expect_cmpl"></label>
													</div>

													<label class="control-label col-md-12 col-xs-12 col-sm-12 text-danger mt-2 tcenter" for="sel1" id="method_error_message"></label>
												</div>
											</div>
											<!--<div class="row">
												<div class="col-xs-6 col-sm-6 col-md-6">
													<div class="form-group" id="test_select_div dnone">
														<label class="control-label col-md-6 col-xs-6 col-sm-6" for="sel1">Test</label>
														<div class="col-md-6">
															<select class="form-control" id="test_select" name="test_select">
																<option value="">-----Select----- </option>
															</select>
														</div>
													</div>
												</div>
											</div>-->
										</div>
								</div>
							</div>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</section>
	</div>

	<?php echo $this->element("chemist_enter_test_reading"); ?>
	<?php echo $this->element("chemist_sample_tests_list"); ?>

</div>

<?php echo $this->Html->Script('sample_enter_reading'); ?>
<?php echo $this->Html->script("test/enter_test_reading"); ?>
