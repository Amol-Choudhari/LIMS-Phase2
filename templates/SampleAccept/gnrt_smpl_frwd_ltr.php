<?php echo $this->Html->Script('sample_forward_form'); ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Available Sample List', array('controller' => 'SampleAccept', 'action'=>'available_to_accept_list')); ?></li>
				<li class="breadcrumb-item active">Generate Sample Forward Letter</li>
			</ol>
		</div>
	</div>
</div>
<section class="content form-middle">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<?php echo $this->Form->create(null, array('id'=>'modal_test','name'=>'modal_test','class'=>'form-group')); ?>
					<div class="card card-lims">
						<div class="card-header"><h3 class="card-title-new">View Sample Letter</h3></div>
							<div class="form-horizontal">
								<div class="card-body">
									<div class="row p-2 pb-2">
										<div class="col-md-3">
											<label>Sample Code <span class="required-star">*</span></label>
											<?php echo $this->Form->control('stage_sample_code', array('type'=>'select', 'id'=>'stage_sample_code', 'options'=>$samples_list, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true,'onchange'=>'getsampledetails();return false',)); ?>
											<span id="error_sample_code" class="error invalid-feedback"></span>
										</div>
										<div class="col-md-3">
											<label>Category <span class="required-star">*</span></label>
											<?php echo $this->Form->control('category_code', array('type'=>'select', 'id'=>'category_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
											<span id="error_category_code" class="error invalid-feedback"></span>
										</div>
										<div class="col-md-3">
											<label>Commodity <span class="required-star">*</span></label>
											<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
											<span id="error_commodity_code" class="error invalid-feedback"></span>
										</div>
										<div class="col-md-3">
											<label>Sample Type <span class="required-star">*</span></label>
											<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
											<span id="error_sample_type" class="error invalid-feedback"></span>
										</div>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<a id="generate_letter_btn" target="_blank" class="btn btn-success float-left">View Letter</a>
								<a href="../Dashboard/home" class="btn btn-danger float-right">Cancel</a>
							</div>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>
<?php echo $this->Html->script("sampleAccept/gnrt_smpl_frwd_ltr"); ?>
