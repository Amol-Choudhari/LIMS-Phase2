
<?php echo $this->Html->Script('sample_reg_form'); ?>
<?php echo $this->Html->Script('input_validation'); ?>
<?php date_default_timezone_set('Asia/Kolkata'); ?>
<?php echo $this->Html->css('master/assign_homo'); ?>

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
			<div class="col-sm-3"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
					</ol>
				</div>
			</div>
		</div>
		<section class="content form-middle mb-3 pt-2">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(null, array('id'=>'frm_commodity', 'name'=>'commodityForm','class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">Assign Homogenization to Commodity</h3></div>
									<div class="form-horizontal">
										<?php if(!empty($validate_err)){ ?><div class="badge badge-danger p-2"><?php echo $validate_err; ?></div><?php } ?>
											<div class="card-body">
												<div class="row">
													<?php echo $this->Form->control('commodity_code', array('type'=>'hidden', 'id'=>'commodity_code', 'value'=>$commodity_code, 'label'=>false)); ?>
													<?php echo $this->Form->control('login_timestamp', array('type'=>'hidden', 'id'=>'login_timestamp', 'label'=>false, 'value'=>date('Y-m-d H:i:s'))); ?>

													<div class="col-md-6">
														<label>Category</label>
															<select name="category_code" id="category" class="form-control" required>
																<?php if(empty($homocategory)){ ?>
																	<option value="">--Select--</option>
																<?php } ?>
																<?php foreach($categories as $catData){ ?>
																	<option value="<?php $cat_code = $catData['category_code']; echo $cat_code; ?>" <?php if($cat_code==$homocategory){ echo 'selected'; } ?>><?php echo $catData['category_name']; ?></option>
																<?php } ?>
															</select>
														<div class="error-msg" id="error_category_code"></div>
													</div>

													<div class="col-md-6">
														<label>Commodity</label>
															<select name="commodity_code" id="commodity" class="form-control">
																<?php if(empty($homocommodity)){ ?>
																	<option value="">--Select--</option>
																<?php } ?>
																<?php foreach($mcommodity as $comData){ ?>
																	<option value="<?php $com_code = $comData['commodity_code']; echo $com_code; ?>" <?php if($com_code==$homocommodity){ echo 'selected'; } ?>><?php echo $comData['commodity_name']; ?></option>
																<?php } ?>
															</select>
														<div class="error-msg" id="error_commodity_name"></div>
													</div>

													<div class="clear sec_divi"></div>

													<h3 class="card-title-new pt-2">Select Homogenization Fields</h3>
														<div class="col-md-12 chk-div">
															<table class="table table-striped table-bordered table-hover">
																<thead class="tablehead">
																	<tr>
																		<th scope="col">Select</th>
																		<th scope="col">Homogenization Name</th>
																		<th scope="col">Homogenization Name (हिंदी)</th>
																	</tr>
																</thead>
																<tbody>
																	<?php
																	if(!empty($homo_fields)){
																		foreach($homo_fields as $homoData){ ?>
																			<tr>
																				<td><input type="checkbox" name="homocheck[]" id="<?php echo $homoData['m_sample_obs_code'] ?>" value="<?php echo $homoData['m_sample_obs_code'] ?>" ></td>
																				<td><?php echo $homoData['m_sample_obs_desc'] ?></td>
																				<td><?php echo $homoData['l_m_sample_obs_desc'] ?></td>
																			</tr>
																	<?php } } ?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="card-footer mt-4">
												<?php if (isset($_SESSION['commodity_code']) && isset($_SESSION['commodity_data'])) {
													  echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));
												} else {
													echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));
												} ?>
												<a href="saved-assign-homo" class="float-right btn btn-danger">Cancel</a>
											</div>
										</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>
<input type="hidden" id="homocategory" value="<?php echo $homocategory; ?>">
<input type="hidden" id="homocommodity" value="<?php echo $homocommodity; ?>">

<?php echo $this->Html->script("master/assign_homo"); ?>
