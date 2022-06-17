<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>		
						<li class="breadcrumb-item">Generate Letter For Multiple Samples</li>		
										
					</ol>
				</div>
			</div>
		</div>
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-8">
					<?php echo $this->Form->create(null, array('id'=>'modal_test','name'=>'modal_test','class'=>'form-group')); ?>
						<div class="card card-lims">
							<div class="card-header"><h3 class="card-title-new">Generate Letter For Multiple Samples</h3></div>
								<div class="form-horizontal mb-3">
									<div class="card-body">
										<div class="row">
											<div class="col-md-3">
												<label>Select Sample Codes <span class="required-star">*</span></label>
													<?php echo $this->Form->control('stage_sample_code_s', array('type'=>'select', 'multiple'=>'multiple', 'id'=>'stage_sample_code_s', 'options'=>$samples_list, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true)); ?>
													<div id="error_sample_code"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<?php echo $this->Form->submit('Generate', array('name'=>'generate', 'id'=>'generate_letter_btn', 'label'=>false,'class'=>'float-left btn btn-success')); ?>
									<a href="../Dashboard/home" class="float-right btn btn-danger">Cancel</a>
								</div>
							</div>
						</div>
					</div>
				<?php echo $this->Form->end(); ?>
			</div>
	</section>
</div>


<?php echo $this->Html->Script('sample_forward_form'); ?>
