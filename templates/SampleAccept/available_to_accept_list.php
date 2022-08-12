<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item">	<?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">Available Sample to Accept</li>
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
							<div class="card-header"><h3 class="card-title-new">List of All Samples available to Accept</h3></div>
							<div class="card-body">
								<table id="avai_to_accpt_list" class="table table-bordered table-hover table-striped">
									<thead class="tablehead">
										<tr>
											<th>Sr No</th>
											<th>Sample Code</th>
											<th>Commodity</th>
											<th>Type of Sample</th>
											<th>Office</th>
											<th>Forwarded On</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (!empty($res)) {
											$sr_no = 1;
											foreach ($res as $each) { ?>
											<tr>
												<td><?php echo $sr_no; ?></td>
												<td><?php echo $each['stage_smpl_cd']; ?></td>
												<td><?php echo $each['commodity_name'];  ?></td>
												<td><?php echo $each['sample_type_desc']; ?></td>
												<td><?php echo $each['ro_office']; ?></td>
												<td><?php $trimmed = explode(".",$each['forwarded_on']); if (!empty($trimmed)){ echo $trimmed[0]; } else { echo $each['forwarded_on']; } ?></td>
												<td>
													<?php echo $this->Html->link('', array('controller' => 'SampleAccept', 'action'=>'redirect_to_accept', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'Accept Sample')); ?> |
													<?php echo $this->Html->link('', array('controller' => 'SampleAccept', 'action'=>'redirect_to_gnrt_ltr', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-list-alt','title'=>'Sample Letter')); ?>
												</td>
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

	<?php echo $this->Html->script("sampleForward/available_to_forward_list"); ?>
	
