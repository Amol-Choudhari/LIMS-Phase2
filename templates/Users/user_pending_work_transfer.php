<?php 
	echo $this->Html->Script('bootstrap-datepicker.min');
		echo $this->Html->script('jquery.dataTables.min');
	echo $this->Html->css('jquery.dataTables.min');
	//print  $this->Session->flash("flash", array("element" => "flash-message_new")); 
?>
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2"><div class="col-sm-6"><label class="badge badge-info">User Work Transfer</label></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home'));?></a></li>
						<li class="breadcrumb-item active">User Work Tranfer</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	
<?php echo $this->Form->create(null,array('id'=>'user_work_tranfer')); ?>
	<div class="form-middle">
		<div class="card card-lims">
				<div class="card-header"><h3 class="card-title-new">User Work Transfer</h3></div>
					<div class="form-horizontal">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<p class="alert alert-success">Please Note: <br>1. The purpose of this module is to transfer user overall work to another user. <br>
										2. The list of users will be available only for the office selected from the dropdown. <br>
										3. The work will be transfer to the officer with same role and same office only, as per the selected user. <br>
										4. The work once transfered will not be reverted, Only the option is to transfer again.<br>
									</p>
								</div>
								<div class="col-md-12">
									<div class="row">
										<div class="form-group col-md-4">				
											<label>From Office <span class="required-star">*</span></label>
											<!-- newly added by Amol to sort users list office wise on 04-12-2020 -->
											<?php echo $this->Form->control('from_office', array('type'=>'select', 'options'=>$ral_cal_list, 'empty'=>'--Select--', 'id'=>'from_office', 'label'=>false, 'escape'=>false, 'class'=>'form-control')); ?>
											<span id="error_from_office" class="error invalid-feedback"></span>
										</div>
										<div class="form-group col-md-4">				
											<label>From User <span class="required-star">*</span></label>
											<?php echo $this->Form->control('from_user', array('type'=>'select', 'options'=>'', 'empty'=>'--Select--', 'id'=>'from_user', 'label'=>false, 'escape'=>false, 'class'=>'form-control')); ?>
											<span id="error_from_user" class="error invalid-feedback"></span>
										</div>
										<div class="form-group col-md-4">	
											<label>To User <span class="required-star">*</span></label>
											<?php echo $this->Form->control('to_user', array('type'=>'select', 'empty'=>'--Select--', 'id'=>'to_user', 'label'=>false, 'escape'=>false, 'class'=>'form-control mt')); ?>
											<span id="error_to_user" class="error invalid-feedback"></span>
										</div>
									</div>
								</div>
								<div class="form-group col-md-6">	
									<label>Reason/Remark <span class="required-star">*</span></label>
									<?php echo $this->Form->control('reason', array('type'=>'textarea',  'label'=>false, 'escape'=>false, 'class'=>'form-control mt')); ?>
									<span id="error_reason" class="error invalid-feedback"></span>	
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<button class="btn btn-primary float-left" id="view" name="transfer">Transfer</button>
						<button class="btn btn-secondary float-right">Back</button>
					</div>
				</div>
			</div>	
		<?php echo $this->Form->end(); ?>
	</div>

<?php echo $this->Html->script('user_work_transfer'); ?>  