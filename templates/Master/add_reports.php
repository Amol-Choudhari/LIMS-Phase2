<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"><?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'all-reports'),array('class'=>'add_btn btn btn-secondary float-left')); ?></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('All Reports', array('controller' => 'master', 'action'=>'all-reports')); ?></li>
					<li class="breadcrumb-item active">Statistics Reports</li>
				</ol>
			</div>
		</div>
	</div>	
	<section class="content form-middle">
		<div class="container-fluid">
			<?php echo $this->Form->create(null, array('id'=>'save_report', 'class'=>'mb-0')); ?>
				<div class="card card-lims mb-0">
					<div class="card-header"><h3 class="card-title-new">Statistics Reports</h3></div>

					<div class="form-horizontal">
						<div class="card-body">
							<div class="col-md-12">
								<div class="row pt-2 pb-4">
									<div class="col-md-2 text-right">Label</div>
									<div class="col-md-4">
										<?php echo $this->Form->control('report_label', array('type'=>'select','options'=>$reportlebel, 'empty'=>'---select---' ,'id'=>'report_label','label'=>false,'class'=>'form-control','required'=>true)); ?>
									</div>
									<div class="col-md-2 text-right">Report Name</div>
									<div class="col-md-4">
										<?php echo $this->Form->control('report_name', array('type'=>'text','id'=>'report_name','label'=>false,'class'=>'form-control',
										'placeholder'=>'Enter Report Name','required'=>true)); ?>
									</div>
								</div>	
							</div>	
						</div>	
					</div>	
					<div class="card-footer">

						<?php echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'savebtn', 'label'=>false, 'class'=>'float-left btn btn-success')); ?>
						
						<a href="all-reports" class="btn btn-danger float-right">Cancel</a>

					</div>
				</div>	

			<?php echo $this->Form->end(); ?>
		</div>	
	</section>	
</div>	
