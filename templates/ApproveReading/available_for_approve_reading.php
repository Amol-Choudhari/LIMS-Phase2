<?php ?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Available For Approve Reading</li>
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
								<div class="card-header"><h3 class="card-title-new">Available For Approve Reading</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="panel panel-primary filterable">
											<table id="avai_to_appr_results" class="table table-bordered table-hover table-striped">
												<thead class="tablehead">
													<tr>
														<th>Sr No</th>
														<th>Sample Code</th>
														<th>Commodity</th>
														<th>Type of Sample</th>
														<th>Office</th>
														<th>Submitted On</th>
														<th>Action</th>
													</tr>
												</thead>
												<tbody>
													<?php if (!empty($approve_reading_sample)) {
														$sr_no = 1;
														foreach ($approve_reading_sample as $each) { ?>
														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $each['stage_smpl_cd']; ?></td>
															<td><?php echo $each['commodity_name'];  ?></td>
															<td><?php echo $each['sample_type_desc']; ?></td>
															<td><?php echo $each['ro_office']; ?></td>
															<td><?php $trimmed = explode(".",$each['submitted_on']); if (!empty($trimmed)){ echo $trimmed[0]; } else { echo $each['submitted_on']; } ?></td>
															<td><?php echo $this->Html->link('', array('controller' => 'ApproveReading', 'action'=>'redirect_to_approve_reading', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'To Approve Results')); ?></td>
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

<?php echo $this->Html->script("sampleAllocate/allocated_list"); ?>
