<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"><label class="badge badge-success">Homogenization Values</label></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
						<li class="breadcrumb-item active">Homogenization Values</li>
					</ol>
				</div>
			</div>
		</div>
		<section class="content form-middle mt-3">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">
					<?php echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'new_homo_value'),array('class'=>'add_btn btn btn-primary float-left')); ?>

					<?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'code-master-home'),array('class'=>'add_btn btn btn-secondary float-right')); ?>
					</div>
					<div class="col-md-12">
						<div class="card card-lims">
							<?php echo $this->Form->create(); ?>
								<div class="card-header"><h3 class="card-title-new">List of Homogenization Values</h3></div>
									<div class="form-horizontal">
										<div class="card-body">
											<div class="panel panel-primary filterable">
												<table id="pages_list_table" class="table table-bordered table-striped table-hover">
													<thead class="tablehead">
													<tr>
														<th>SR.No</th>
														<th>Homogenization Field</th>
														<th>Value of Homogenization </th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
												<?php
												if(!empty($homoValueArray)){
													$sr_no = 1;
													foreach($homoValueArray as $each){ ?>

														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $com_name = $each['m_sample_obs_desc']; ?></td>
															<td><?php $homo_value = $each['m_sample_obs_type_value']; echo $homo_value; ?></td>
															<td>
																<?php echo $this->Html->link('', array('controller' => 'master', 'action'=>'fetch_homo_value', $each['m_sample_obs_type_code']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?>
																<?php
																	if($homo_value=='Yes' || $homo_value=='No'){
																		$val_type = 'yesno';
																	} else {
																		$val_type = 'single';
																	}
																?> |
																<?php echo $this->Html->link('', array('controller' => 'master', 'action'=>'delete_homo_value', $each['m_sample_obs_type_code'], $each['m_sample_obs_code'], $val_type, $homo_value),array('class'=>'glyphicon glyphicon-trash','title'=>'Delete', 'id'=>"delete_record")); ?>
															</td>
														</tr>

												<?php $sr_no++; } } ?>
												</tbody>
												</table>
											</div>
										</div>
									</div>
						</div>
						<?php echo $this->Form->end() ?>
					</div>
				</div>
			</div>
		</section>
</div>
<?php echo $this->Html->script("master/saved_category"); ?>
