<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Reject Samples</li>
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
							<div class="form-horizontal">
								<div class="card-header"><h3 class="card-title-new">List of All Rejected Samples</h3></div>
									<table id="rejected_samples_list" class="table table-bordered table-hover table-striped">
										<thead class="tablehead">
											<tr>
												<th>Sr No</th>
												<th>Sample Code</th>
												<th>Commodity</th>
												<th>Type of Sample</th>
												<th>Rejected Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if(!empty($rejected_sample_list)) {

												$sr_no = 1;

												foreach ($rejected_sample_list as $rejected_sample) { ?>

													<tr>
														<td><?php echo $sr_no; ?></td>
														<td><?php echo $rejected_sample[0]['sample_code']; ?></td>
														<td><?php echo $rejected_sample[0]['commodity_name']; ?></td>
														<td><?php echo $rejected_sample[0]['sample_type_desc']; ?></td>
														<td><?php $date = explode(" ", $rejected_sample[0]['created']); echo $date[0]; ?></td>
														<td class="cursor-pointer"><i class="glyphicon glyphicon-refresh btn btn-dark" id="sample_undo"> Revert</i></td>
													</tr>
											<?php $sr_no++; } } ?>
										</tbody>
									</table>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
	</section>
</div>

<input type="hidden" id="sid_id" value="<?php if(!empty($rejected_sample_list)){ echo $rejected_sample[0]['sid_id']; } ?>">
<input type="hidden" id="sample_code" value="<?php if(!empty($rejected_sample_list)){ echo $rejected_sample[0]['sample_code']; } ?>">
<?php echo $this->Html->script("sampleForward/sample_reject") ?>
