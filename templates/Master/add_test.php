<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code-master-home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Test', array('controller' => 'master', 'action'=>'saved-test-type')); ?></li>
					<li class="breadcrumb-item active"><?php echo $title; ?></li>
				</ol>
			</div>
		</div>
	</div>

	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Form->create(null, array('id'=>'add_test', 'name'=>'testForm','class'=>'form-group')); ?>

						<div class="card card-lims">
							<div class="card-header"><h3 class="card-title-new"><?php echo $title; ?></h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="row">

											<div class="col-md-4">
												<label>Test Type <span class="required-star">*</span></label>
												<?php echo $this->Form->control('test_type', array('type'=>'select', 'id'=>'test_type', 'options'=>$test_types, 'value'=>$test_type_code, 'empty'=>'-- Select --','label'=>false,'class'=>'form-control', 'required'=>true)); ?>
												<div class="error-msg" id="error_category_name"></div>
											</div>

											<div class="col-md-4">
												<label>Test Name (Eng) <span class="required-star">*</span></label>
												<?php echo $this->Form->control('test_name', array('type'=>'text', 'id'=>'test_name', 'value'=>$test_name, 'label'=>false,'class'=>'form-control', 'placeholder'=>'Enter test name', 'maxLength'=>'150', 'required'=>true)); ?>
												<div class="error-msg" id="error_test_name"></div>
											</div>

											<div class="col-md-4">
												<label>Test Name(हिंदी) </label>
												<?php echo $this->Form->control('l_test_name', array('type'=>'text', 'id'=>'l_test_name', 'value'=>$l_test_name, 'label'=>false,'class'=>'form-control ', 'placeholder'=>'Enter test name', 'maxLength'=>'150', 'required'=>true)); ?>
												<div class="error-msg" id="error_min_quantity"></div>
											</div>

										</div>
									</div>
								</div>

								<div class="card-footer mt-4">

									<?php
										if(!empty($test_name)){

											echo $this->Form->submit('Update', array('name'=>'save', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));

										} else {

											echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));

										} ?>
									
									<a href="saved-test-type" class="btn btn-danger float-right">Cancel</a>
								</div>
							</div>
						</div>						
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>					
</div>