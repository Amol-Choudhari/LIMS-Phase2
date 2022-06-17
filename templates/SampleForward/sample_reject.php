
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Sample Reject</li>
					</ol>
				</div>
			</div>
		</div>
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Form->create(null, array('id'=>'frm_sample_reject','class'=>'form-group')); ?>
						<div class="card card-lims">
							<div class="card-header"><h3 class="card-title-new">Sample Reject</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<?php if(!empty($validate_err)){ ?><div class="alert alert-danger textAlignCenter text-danger"><?php echo $validate_err; ?></div><?php } ?>
											<div class="row">
												<div class="col-md-3">
													<label>Sample Code <span class="required-star">*</span></label>
													<?php echo $this->Form->control('stage_sample_code', array('type'=>'select', 'id'=>'stage_sample_code', 'options'=>$res, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true)); ?>
													<div id="error_sample_code"></div>
												</div>
												<div class="col-md-3">
													<label>Commodity Name <span class="required-star">*</span></label>
													<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
													<div id="error_commodity_code"></div>
													<input type="hidden" class="form-control" id="type" name="type"  hidden>
												</div>
												<div class="col-md-3">
													<label>Sample Type <span class="required-star">*</span></label>
													<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
													<div id="error_sample_type"></div>
												</div>
												<div class="col-md-3" id="reject_box_field">
													<label>Reason to Reject Sample <span class="required-star">*</span></label>
													<?php echo $this->Form->control('sample_reject_reason', array('type'=>'textarea', 'id'=>'sample_reject_reason', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<div id="error_sample_reject_reason"></div>
												</div>
											</div>
										</div>
									</div>
									<div class="card-footer mt-2">
										<div class="col-md-2 float-left">
											<?php echo $this->Form->submit('Reject Sample', array('name'=>'sample_reject', 'id'=>'sample_reject', 'label'=>false,'class'=>'form-control btn btn-success')); ?>
										</div>

										<div class="col-md-2 float-right">
											<a href="../SampleForward/available_to_forward_list" class="form-control btn btn-danger">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
				</div>
		</section>
</div>
<?php
	unset($_SESSION['sample']);
	unset($_SESSION['stage_sample_code']);
?>
<?php echo $this->Html->Script('sample_forward_form'); ?>
