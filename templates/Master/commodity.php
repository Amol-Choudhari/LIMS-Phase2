
<?php echo $this->Html->Script('sample_reg_form'); ?>
<?php echo $this->Html->Script('input_validation'); ?>
<?php date_default_timezone_set('Asia/Kolkata'); ?>
<?php echo $this->Html->css("master/commodity"); ?>

<?php


// assign field's default value
if(isset($_SESSION['commodity_code']) && isset($_SESSION['commodity_data'])){
	$commodity_code = $commodity_data['commodity_code'];
	$commodity_name = $commodity_data['commodity_name'];
	$l_commodity_name = $commodity_data['l_commodity_name'];
	$category_code = $commodity_data['category_code'];
	$title = 'Edit Commodity';
} else {
	$commodity_code = '';
	$commodity_name = '';
	$l_commodity_name = '';
	$category_code = '';
	$title = 'Add Commodity';
}

?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'saved_commodity'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Code Master', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Commodites', array('controller' => 'master', 'action'=>'saved_commodity')); ?></li>
						<li class="breadcrumb-item active"><?php echo $title; ?></li>
					</ol>
				</div>
			</div>
		</div>

		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(null, array('id'=>'frm_commodity', 'name'=>'commodityForm','class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new"><?php echo $title; ?></h3></div>
								<div class="form-horizontal">
									<?php if(!empty($validate_err)){ ?><div class="badge badge-danger p-2"><?php echo $validate_err; ?></div><?php } ?>
									<div class="card-body">
										<div class="row">
											<?php echo $this->Form->control('commodity_code', array('type'=>'hidden', 'id'=>'commodity_code', 'value'=>$commodity_code, 'label'=>false)); ?>
											<?php echo $this->Form->control('login_timestamp', array('type'=>'hidden', 'id'=>'login_timestamp', 'label'=>false, 'value'=>date('Y-m-d H:i:s'))); 
											
											// this condition used for readonly Category when user edit commodity
											// added by shankhpal shende on 02/09/2022
											if ($commodity_code !='') {
												$disbaled_status = 'disabled';
												$readonly_status = 'readonly';
											} else {
												$disbaled_status = '';
												$readonly_status = '';
											}
											
											?>


											<div class="col-md-4">
												<label>Category <span class="required-star">*</span></label>
												<select name="category_code" id="category_code" class="form-control" readonly ="<?php echo $readonly_status; ?>">
														<option value="">--Select--</option>
														<?php foreach($categoryArray as $catData){ ?>
															<option value="<?php $cat_code = $catData['category_code']; echo $cat_code; ?>" <?php if($cat_code==$category_code){ echo 'selected'; }else{ echo $disbaled_status;} ?>><?php echo $catData['category_name']; ?></option>
														<?php } ?>
													</select>
												<div class="error-msg" id="error_category_code"></div>
											</div>
											<div class="col-md-4">
												<label>Commodity Name <span class="required-star">*</span></label>
													<?php echo $this->Form->control('commodity_name', array('type'=>'text', 'id'=>'commodity_name', 'value'=>$commodity_name, 'label'=>false, 'class'=>'form-control txtOnly', 'placeholder'=>'Commodity Name', 'minLength'=>'4', 'maxLength'=>'50', 'required'=>true)); ?>
												<div class="error-msg" id="error_commodity_name"></div>
											</div>
											<div class="col-md-4">
												<label>Commodity Name (हिन्दी) <span class="required-star">*</span></label>
													<?php echo $this->Form->control('l_commodity_name', array('type'=>'text', 'id'=>'l_commodity_name', 'value'=>$l_commodity_name, 'label'=>false, 'class'=>'form-control hindiFont', 'placeholder'=>'Commodity Name (हिन्दी)', 'maxLength'=>'50', 'required'=>true)); ?>
												<div class="error-msg" id="error_l_commodity_name"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer mt-4">
									<?php
										if (isset($_SESSION['commodity_code']) && isset($_SESSION['commodity_data'])) {
											echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));
										} else {
											echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));
										}
									?>
									<a href="saved_commodity" class="btn btn-danger float-right">Cancel</a>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
	</div>
<?php echo $this->Html->script("master/category"); ?>
