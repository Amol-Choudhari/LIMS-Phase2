
<?php echo $this->Html->Script('sample_reg_form'); ?>
<?php echo $this->Html->Script('input_validation'); ?>
<?php // assign field's default value
	if (isset($_SESSION['category_code']) && isset($_SESSION['category_data'])) {

		$category_code = $category_data['category_code'];
		$category_name = $category_data['category_name'];
		$l_category_name = $category_data['l_category_name'];
		$min_quantity = $category_data['min_quantity'];
		$title = 'Edit Category';

	} else {

		$category_code = '';
		$category_name = '';
		$l_category_name = '';
		$min_quantity = '';
		$title = 'Add Category';
	}

?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Categories', array('controller' => 'master', 'action'=>'saved_category')); ?></li>
						<li class="breadcrumb-item active"><?php echo $title; ?></li>
					</ol>
				</div>
			</div>
		</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(null, array('id'=>'frm_category', 'name'=>'categoryForm','class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new"><?php echo $title; ?></h3></div>
									<div class="form-horizontal">
										<div class="card-body">
											<div class="row">
											<?php echo $this->Form->control('category_code', array('type'=>'hidden', 'id'=>'category_code', 'value'=>$category_code, 'label'=>false)); ?>
												<?php if(!empty($validate_err)){ ?>
													<div class="text-center;text-dange"><?php echo $validate_err; ?></div>
												<?php } ?>

											<div class="col-md-4">
												<label>Category Name <span class="required-star">*</span></label>
												<?php echo $this->Form->control('category_name', array('type'=>'text', 'id'=>'category_name', 'value'=>$category_name, 'label'=>false,'class'=>'form-control txtOnly', 'placeholder'=>'Category Name', 'minLength'=>'4', 'maxLength'=>'50', 'required'=>true)); ?>
												<div class="error-msg" id="error_category_name"></div>
											</div>

											<div class="col-md-4">
												<label>Category Name (हिन्दी) <span class="required-star">*</span></label>
												<?php echo $this->Form->control('l_category_name', array('type'=>'text', 'id'=>'l_category_name', 'value'=>$l_category_name, 'label'=>false,'class'=>'form-control hindiFont', 'placeholder'=>'Category in Hindi', 'maxLength'=>'150', 'required'=>true)); ?>
												<div class="error-msg" id="error_l_category_name"></div>
											</div>

											<div class="col-md-4">
												<label>Minimum Quantity To Be Graded <span class="required-star">*</span></label>
												<?php echo $this->Form->control('min_quantity', array('type'=>'text', 'id'=>'min_quantity', 'value'=>$min_quantity, 'label'=>false,'class'=>'form-control numOnly', 'placeholder'=>'Minimum Quantity(Numbers Only)', 'maxLength'=>'150', 'required'=>true)); ?>
												<div class="error-msg" id="error_min_quantity"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer mt-4">

									<?php
										if(isset($_SESSION['category_code']) && isset($_SESSION['category_data'])){

											echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));

										} else {

											echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));

										} ?>
									
									<a href="saved_category" class="btn btn-danger float-right">Cancel</a>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php echo $this->Html->Script("master/category.js"); ?>
