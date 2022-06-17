<?php ?>
<?php echo $this->Html->css('sampleAllocate/available_to_allocate'); ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Available to Allocate</li>
					</ol>
				</div>
			</div>
		</div>
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Form->create(null,array('class'=>'form-group')); ?>
						<div class="card card-lims">
							<div class="form-horizontal">
								<div class="card-header">
								<?php
									if (!empty($from_dt) && !empty($to_dt)) { ?>

											<h3 class="card-title-new">Samples available to Allocate for Retest / Forward to Lab Incharge List From <?php echo $from_dt; ?> to <?php echo $to_dt; ?></h3>
									<?php } else { ?>

											<h3 class="card-title-new">Samples available to Allocate for Retest / Forward to Lab Incharge List for Last 1 Month</h3>
									<?php } ?>
								</div>

								<div class="col-md-12"><?php echo $this->element('date_filter'); ?></div>
								
								<!--<div class="card-header"><h3 class="card-title-new">List of All Samples available to Allocate for Retest / Forward to Lab Incharge</h3></div>-->
									<div class="col-md-12 radio_options">
										<label class="radio-inline "><input class="type validate[required] radio" type="radio" id="type"  name="type" value="A" checked="checked">Allocate for Retest</label>
										<label class="radio-inline "><input class="type validate[required] radio" type="radio" id="type" name="type" value="F"> Forward to Lab Incharge</label>
										<label class="radio-inline "><input class="type validate[required] radio" type="radio" id="type" name="type" value="RC"> Samples Returned By Chemist</label>
									</div>
									<!-- list of sample to allocate for test -->
									<div id="A_list">
										<table id="avai_to_allocate_list" class="table m-0 table-bordered table-hover table-striped">
											<thead class="tablehead">
												<tr>
													<th>Sr No</th>
													<th>Sample Code</th>
													<th>Commodity</th>
													<th>Type of Sample</th>
													<th>Office</th>
													<th>Requested On</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php if (!empty($avail_to_allocate)) {

													$sr_no = 1;

													foreach ($avail_to_allocate as $each) { ?>

														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $each['stage_smpl_cd']; ?></td>
															<td><?php echo $each['commodity_name'];  ?></td>
															<td><?php echo $each['sample_type_desc']; ?></td>
															<td><?php echo $each['ro_office']; ?></td>
															<td><?php echo $each['requested_on']; ?></td>
															<td><?php echo $this->Html->link('', array('controller' => 'SampleAllocate', 'action'=>'redirect_to_allocate_retest', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'Allocate For Retest')); ?></td>
														</tr>
												<?php $sr_no++; } } ?>
											</tbody>
										</table>
									</div>
									<!-- list of sample to Forward to lab incharge -->
									<div id="F_list">
										<table id="avai_to_forward_list" class="table m-0 table-bordered table-hover table-striped">
											<thead class="tablehead">
												<tr>
													<th>Sr No</th>
													<th>Sample Code</th>
													<th>Commodity</th>
													<th>Type of Sample</th>
													<th>Office</th>
													<th>Requested On</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php if (!empty($avail_to_forward)) {

													$sr_no = 1;

													foreach ($avail_to_forward as $each) { ?>

														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $each['stage_smpl_cd']; ?></td>
															<td><?php echo $each['commodity_name'];  ?></td>
															<td><?php echo $each['sample_type_desc']; ?></td>
															<td><?php echo $each['ro_office']; ?></td>
															<td><?php echo $each['requested_on']; ?></td>
															<td><?php echo $this->Html->link('', array('controller' => 'SampleAllocate', 'action'=>'redirect_to_forward_retest', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'Forward to Lab Incharge')); ?></td>
														</tr>
												<?php $sr_no++; } } ?>
											</tbody>
										</table>
									</div>
									<!-- list of samples returned by chemist and ready to allocate again -->
									<div id="RC_list">
										<table id="returned_by_chem_list" class="table m-0 table-bordered table-hover table-striped">
											<thead class="tablehead">
												<tr>
													<tr>
													<th>Sr No.</th>
													<th>Sample </th>
													<th>Commodity</th>
													<th>Chemist Code</th>
													<th>Remark/Reason</th>
													<th>Send Back Date</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php if (isset($sendback)) {

													$i = 1;

													foreach ($sendback as $data) { ?>
														<tr>
															<td><?php echo $i; ?></td>
															<td><?php echo $data['sample_code'] ?></td>
															<td><?php echo $data['commodity_name'] ?></td>
															<td><?php echo $data['chemist_code'] ?></td>
															<td><?php echo $data['sendback_remark'] ?></td>
															<td><?php echo $data['recby_ch_date'] ?></td>
															<td><?php echo $this->Html->link('', array('controller' => 'SampleAllocate', 'action'=>'redirect_to_allocate_retest', $data['sample_code']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'Re-Allocate For Retest')); ?> </td>
														</tr>
												<?php $i++;} } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</section>
	</div>
<?php echo $this->Html->script("sampleAllocate/available_to_allocate_retest"); ?>
