<?php ?>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Accepted Sample List</li>
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
									<div class="card-header">
										<?php if (!empty($from_dt) && !empty($to_dt)) { ?>
												<h3 class="card-title-new">Given Below is Sample Accepted List from <?php echo $from_dt; ?> to <?php echo $to_dt; ?></h3>
										<?php } else { ?>
												<h3 class="card-title-new">Given Below is Sample Accepted List for Last 1 Month</h3>
										<?php } ?>
									</div>
									<div class="card-body">
										<?php echo $this->element('date_filter'); ?>
										<table id="accepted_samples_list" class="table table-bordered table-hover table-striped">
											<thead class="tablehead">
												<tr>
													<th>Sr No</th>
													<th>Registered Code</th>
													<th>Commodity</th>
													<th>Type of Sample</th>
													<th>Accepted Date</th>
												</tr>
											</thead>
											<tbody>
												<?php if (!empty($res3)) {

													$sr_no = 1;

													foreach ($res3 as $each) { ?>

													<tr>
														<td><?php echo $sr_no; ?></td>
														<td><?php echo $each['stage_smpl_cd']; ?></td>
														<td><?php echo $each['commodity_name']; ?></td>
														<td><?php echo $each['sample_type_desc']; ?></td>
														<td><?php echo $each['tran_date']; ?></td>
													</tr>

												<?php $sr_no++; } } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
	</div>
<?php echo $this->Html->script('sampleAccept/gnrt_smpl_frwd_ltr'); ?>
