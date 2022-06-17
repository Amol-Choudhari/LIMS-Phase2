<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
						<li class="breadcrumb-item active">Tests</li>
				</ol>
			</div>
		</div>
	</div>
		<section class="content form-middle mt-2">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">
					<?php echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'add-test'),array('class'=>'add_btn btn btn-primary float-left')); ?>

					<?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'code_master_home'),array('class'=>'add_btn btn btn-secondary float-right')); ?>
					</div>
					<div class="col-md-12">
						<div class="card card-lims">
							<?php echo $this->Form->create(); ?>
								<div class="card-header"><h3 class="card-title-new">List of Tests</h3></div>
									<div class="form-horizontal">
										<div class="card-body">
											<div class="panel panel-primary filterable">
												<table id="pages_list_table" class="table table-bordered table-striped table-hover">
												<thead class="tablehead">
													<tr>
														<th>SR.No</th>
														<th>Test Name</th>
														<th>Test Name(हिंदी)</th>
														<th>Test Type</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
														<?php
														if(!empty($tests)){
															$sr_no = 1;
															foreach($tests as $each){ ?>

																<tr>
																	<td><?php echo $sr_no; ?></td>
																	<td><?php echo $test_name = $each['test_name']; ?></td>
																	<td><?php echo $each['l_test_name'];  ?></td>
																	<td><?php echo $each['mtt']['test_type_name'];  ?></td>
																	<td>
																		<?php echo $this->Html->link('', array('controller' => 'master', 'action'=>'editTest', $each['test_code']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> 
																		<?php //echo $this->Html->link('', array('controller' => 'master', 'action'=>'deleteTest', $each['test_code']),array('class'=>'glyphicon glyphicon-trash','title'=>'Delete', 'onclick'=>"return (confirm('Do you really want to delete ".$test_name." ?'))")); ?>
																	</td>
																</tr>

														<?php $sr_no++; } } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
						</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
</div>

<?php echo $this->Html->script("master/saved_category"); ?>
