<?php ?>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'Inward', 'action'=>'sample_inward'),array('class'=>'add_btn btn btn-secondary float-sm-left')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Commercial Sample</li>
					</ol>
				</div>
			</div>
		</div>
		
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">List of Sample Payment Status</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="panel panel-primary filterable">
											<table id="pages_list_table" class="table table-stripped table-bordered table-hover">
												<thead class="tablehead">
													<tr>
														<th>SR.No</th>
														<th>Sample Code</th>
														<th>Payment Status</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
													<?php if(!empty($res)){
														$sr_no = 1;
														foreach($res as $each){ ?>
														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $each['sample_code']; ?></td>
															<td><?php 
																if (trim($each['payment_confirmation']) == 'not_confirmed') {
																	echo 'Referred Back from the DDO'; 
																} elseif (trim($each['payment_confirmation']) == 'replied') { 
																	echo 'Replied Payment'; 
																} ?>
															</td>
															<td>
																<?php
																	if (trim($each['payment_confirmation']) == 'not_confirmed') {
																		echo $this->Html->link('', array('controller' => 'inward', 'action'=>'fetch_inward_id', $each['inward_id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit'));
																	} else {
																		echo $this->Html->link('', array('controller' => 'InwardDetails', 'action'=>'fetch_inward_id', $each['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit'));
																	}
																?>
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
<?php echo $this->Html->script("sampleForward/available_to_forward_list") ?>
