<?php echo $this->Html->css('master/set_report'); ?>


<div class="content-header">

	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active"><?php echo 'Set Report'; ?></li>
				</ol>
			</div>
		</div>
	</div>
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card card-lims ">
						<div class="card-header"><h3 class="card-title-new"><?php echo 'Set Report'; ?></h3></div>
						<?php echo $this->Form->create(null, array('id'=>'add_test', 'name'=>'testForm','class'=>'form-group')); ?>
						<div class="form-horizontal">
							<div class="card-body">
								<div class="row">
									<div class="col-md-3">
										<label>User Role <span class="required-star">*</span></label>
										<?php echo $this->Form->control('userrole', array('type'=>'select', 'id'=>'userrole', 'options'=>$user_roles,'empty'=>'-- Select --','label'=>false,'class'=>'form-control', 'required'=>true)); ?>
									</div>										
									<div class="col-md-4">
										<label>Report Category <span class="required-star">*</span></label>
										<?php echo $this->Form->control('reportcategory', array('type'=>'select', 'id'=>'reportcategory', 'options'=>$report_category,'empty'=>'-- Select --','label'=>false,'class'=>'form-control', 'required'=>true)); ?>
									</div>
									<div class="col-md-4">
										<label>Reports List<span class="required-star">*</span></label>
										<?php echo $this->Form->control('reportnames', array('type'=>'select', 'id'=>'reportnames', 'label'=>false,'class'=>'form-control', 'multiple'=>'multiple')); ?>
									</div>
									<div class="col-md-1">
										<label>&nbsp;</label>
										<?php echo $this->Form->submit('Save', array('name'=>'submit', 'id'=>'sbtn', 'label'=>false,'class'=>'btn btn-success')); ?>
									</div>										
								</div>	
								<div>
									<div class="table-responsive mt-5" id="setreports">
									<!--	<table class='table table-striped' id='setreportrecord'>
											<thead>
												<tr><th class='w-25-p'>Sr.No</th>
													<th>Assigned Reports</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>-->
									</div>
								</div>
							</div>	
						</div>	
						<?php echo $this->Form->end(); ?>
					</div>		
				</div>	
			</div>	
		</div>	
	</section>	

</div>	

<?php echo $this->Html->script('master/set_reports'); ?>