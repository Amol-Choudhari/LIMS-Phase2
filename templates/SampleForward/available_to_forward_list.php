<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">Available To Forward Sample List</li>
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
								<div class="card-header"><h3 class="card-title-new">List of All Samples available to Forward / Reject</h3></div>
								<table id="pages_list_table" class="table table-striped table-bordered table-hover">
									<thead class="tablehead">
										<tr>
											<th>SR.No</th>
											<th>Sample Code</th>
											<th>Received Date</th>
											<th>Sample Type</th>
											<th>Received From</th>
											<th>Commodity</th>
											<th>Action</th>
											</tr>
									</thead>
									<tbody>
										<?php if (!empty($res)) {
											$sr_no = 1;
											foreach($res as $each){ ?>
											<tr>
												<td><?php echo $sr_no; ?></td>
												<td><?php echo $each['org_sample_code']; ?></td>
												<td><?php echo $each['received_date'];  ?></td>
												<td><?php echo $each['sample_type_desc']; ?></td>
												<td><?php echo $each['ro_office']; ?></td>
												<td><?php echo $each['commodity_name']; ?></td>
												<td><?php echo $this->Html->link('', array('controller' => 'SampleForward', 'action'=>'redirect_to_forward', $each['org_sample_code']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'Forward Sample')); ?> |
													<?php echo $this->Html->link('', array('controller' => 'SampleForward', 'action'=>'redirect_to_reject', $each['org_sample_code']),array('class'=>'glyphicon glyphicon-remove','title'=>'Reject Sample')); ?> |
													<a href="../inward/get_sample_slip/<?php echo $each['org_sample_code']; ?>" title="Sample Slip" target="_blank" class="glyphicon glyphicon-list-alt"></a>
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
