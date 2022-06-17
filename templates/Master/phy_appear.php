
<?php echo $this->Html->Script('sample_reg_form'); ?>
<?php echo $this->Html->Script('input_validation'); ?>

<style>
	
.error-msg {
	color: red;
}
.invalid-fld {
	border: 1px solid red!important;
    box-shadow: 0 0 2px red!important;
}
.valid-fld {
	border: 1px solid #747474!important;
    box-shadow: 0 0 2px green!important;
}

</style>

<?php 

// assign field's default value
if(isset($_SESSION['phy_appear_code']) && isset($_SESSION['phy_appear_data'])){
	$textbox_1 = $phy_appear_data[$phy_appear_module['textbox_1']];
	$textbox_2 = $phy_appear_data[$phy_appear_module['textbox_2']];
	$title = 'Edit ' . $phy_appear_module['title'];
	$phy_appear_code = $phy_appear_data[$phy_appear_module['phy_appear_code']];
} else {
	$textbox_1 = '';
	$textbox_2 = '';
	$title = 'Add ' . $phyAppear['title'];
	$phy_appear_code = '';
}

// add JS input validation for financial year
$table_nm = $phyAppear['table_name'];
if($table_nm=='m_fin_year'){
	$textOnly = 'finYear';
	$hindiFont = 'finYear';
} else {
	$textOnly = 'txtOnly';
	$hindiFont = 'hindiFont';
}

?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active"><?php echo $title; ?></li>
				</ol>
			</div>
		</div>
	</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-10">
						<?php echo $this->Form->create(null, array('id'=>'frm_phy_appear', 'name'=>'categoryForm','class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new"><?php echo $title; ?></h3></div>
									<div class="form-horizontal">
										<?php if(!empty($validate_err)){ ?><div class="badge badge-danger p-2"><?php echo $validate_err; ?></div><?php } ?>
											<div class="card-body">
												<div class="row">
													<?php echo $this->Form->control('phy_appear_code', array('type'=>'hidden', 'id'=>'phy_appear_code', 'value'=>$phy_appear_code, 'label'=>false)); ?>
													<?php echo $this->Form->control('phy_appear_code_name', array('type'=>'hidden', 'id'=>'phy_appear_code', 'value'=>$phyAppear['phy_appear_code'], 'label'=>false)); ?>
													<?php echo $this->Form->control('textbox_1', array('type'=>'hidden', 'label'=>false, 'value'=>$phyAppear['textbox_1'])); ?>
													<?php echo $this->Form->control('textbox_2', array('type'=>'hidden', 'label'=>false, 'value'=>$phyAppear['textbox_2'])); ?>
													<?php echo $this->Form->control('table_name', array('type'=>'hidden', 'label'=>false, 'value'=>$phyAppear['table_name'])); ?>
													<?php echo $this->Form->control('action', array('type'=>'hidden', 'label'=>false, 'value'=>$phyAppear['action'])); ?>
													<?php echo $this->Form->control('title', array('type'=>'hidden', 'label'=>false, 'value'=>$phyAppear['title'])); ?>
													<?php echo $this->Form->control('label_1', array('type'=>'hidden', 'label'=>false, 'value'=>$phyAppear['label_1'])); ?>
													<?php echo $this->Form->control('label_2', array('type'=>'hidden', 'label'=>false, 'value'=>$phyAppear['label_2'])); ?>

	
													<div class="col-md-6">
														<label><?php echo $phyAppear['label_1']; ?> <span class="required-star">*</span></label>				
															<?php echo $this->Form->control($phyAppear['textbox_1'], array('type'=>'text', 'id'=>'textbox_1', 'value'=>$textbox_1, 'label'=>false, 'class'=>'form-control ' . $textOnly, 'placeholder'=>$phyAppear['label_1'], 'maxLength'=>'70', 'required'=>true)); ?>	
														<div class="error-msg" id="error_textbox_1"></div>
													</div>
				
													<div class="col-md-6">
														<label><?php echo $phyAppear['label_2']; ?> <span class="required-star">*</span></label>				
															<?php echo $this->Form->control($phyAppear['textbox_2'], array('type'=>'text', 'id'=>'textbox_2', 'value'=>$textbox_2, 'label'=>false, 'class'=>'form-control ' . $hindiFont, 'placeholder'=>$phyAppear['label_2'], 'maxLength'=>'70', 'required'=>true)); ?>	
														<div class="error-msg" id="error_textbox_2"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="card-footer mt-4">
											<?php 
											if(isset($_SESSION['phy_appear_code']) && isset($_SESSION['phy_appear_data'])){

												echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));

											} else {

												echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));

											} ?>

											<a href="../saved-phy-appear/<?php echo $phyAppear['action']; ?>" class="float-right btn btn-danger">Cancel</a>
										</div>
									</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>