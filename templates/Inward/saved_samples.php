
<?php ?>
<?php echo $this->Html->css('inward/saved_sample'); ?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'Inward', 'action'=>'sample_inward'),array('class'=>'add_btn btn btn-secondary float-sm-left')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Saved Sample</li>
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
								<div class="card-header"><h3 class="card-title-new">List of All Unconfirmed Samples</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="panel panel-primary filterable">
											<?php if ($user_flag=='RO' || $user_flag=='SO') { ?>

												<table id="pages_list_table" class="table table-stripped table-bordered table-hover">
													<thead class="tablehead">
														<tr>
															<th>SR.No</th>
															<th>Sample Code</th>
															<th>Received Date</th>
															<th>Drawing Date</th>
															<th>Inward</th>
															<th>Details</th>
															<th>Payment</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
														<?php if(!empty($sampleArray)){
															$sr_no = 1;
															foreach($sampleArray as $each){ ?>
																<tr>
																	<td><?php echo $sr_no; ?></td>
																	<td><?php echo $each['org_sample_code']; ?></td>
																	<td><?php if (isset($each['received_date'])) { 
																				echo $each['received_date']; 
																			} else { 
																				echo '<i class="fas fa-minus mleft"></i>'; 
																			} 
																		?>
																	</td>
																	<td><?php if (isset($each['smpl_drwl_dt'])) { 
																				echo $each['smpl_drwl_dt']; 
																			} else { 
																				echo '<i class="fas fa-minus mleft"></i>'; 
																			} 
																		?>
																	</td>
																	<td><?php if ($each['inward_section'] == 'Y') { 
																				echo '<i class="fas fa-check cpcg mleft"></i>'; 
																			} elseif ($each['inward_section'] == null || $each['inward_section'] == '') {
																				echo '<i class="fas fa-minus mleft"></i>'; 
																			}
																		 ?>
																	</td>
																	<td><?php if ($each['details_section'] == 'Y') { 
																				echo '<i class="fas fa-check cpcg mleft"></i>'; 
																			} elseif ($each['details_section'] == null || $each['details_section'] == '') {
																				echo '<i class="fas fa-minus mleft"></i>'; 
																			}
																		?>
																	</td>
																	<td><?php if ($each['sample_type_code'] == '3' && $each['payment_section'] == 'Y') {
																				echo '<i class="fas fa-check cpcg mleft"></i>'; 
																			} elseif ($each['sample_type_code'] == '3' && $each['payment_section'] == null) { 
																				echo '<i class="fas fa-minus mleft"></i>'; 
																			} else {
																				echo '<span class="badge badge-info mleft28">N/A</span>'; 
																			} 
																		?>
																	</td>
																	<td>
																		<?php
																			if (!empty($each['received_date'])) {
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

											<?php } else { ?>

												<table id="pages_list_table" class="table table-striped table-bordered table-hover">
													<thead class="tablehead">
														<tr>
															<th>SR.No</th>
															<th>Sample Code</th>
															<th>Received Date</th>
															<th>Sample Type</th>
															<th>Received From</th>
															<th>Commodity</th>
															<th>Inward</th>
															<th>Payment</th>
															<th>Status</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
													<?php
														if (!empty($sampleArray)) {
															$sr_no = 1;
															foreach ($sampleArray as $each) { ?>
															<tr>
																<td><?php echo $sr_no; ?></td>
																<td><?php echo $each['org_sample_code'] ?></td>
																<td><?php echo $each['received_date']  ?></td>
																<td><?php echo $each['sample_type_desc'] ?></td>
																<td><?php echo $each['ro_office'] ?></td>
																<td><?php echo "<span class='badge'>". $each['commodity_name']. "</span>" ?></td>
																<td><?php 
																	if ($each['inward_section'] == 'Y') { 
																		echo '<i class="fas fa-check cpcg mleft"></i>'; 
																	} elseif ($each['inward_section'] == null || $each['inward_section'] == '') {
																		echo '<i class="fas fa-minus mleft"></i>'; 
																	}
																	?>
																</td>
																<td><?php
																	if ($each['sample_type_desc'] == 'Commercial' && $each['payment_section'] == 'Y') { 
																		echo '<i class="fas fa-check cpcg mleft"></i>'; 
																	} elseif ($each['sample_type_desc'] == 'Commercial' && $each['payment_section'] == null) { 
																		echo '<i class="fas fa-minus mleft"></i>'; 
																	} else { 
																		echo '<span class="badge badge-info mleft28">N/A</span>'; 
																	}
																	?>
																</td>
																<td><?php if ($each['acc_rej_flg']=="A") { 
																			echo "Accepted";
																		} elseif ($each['acc_rej_flg']=="P") { 
																			echo "Pending";
																		} else {
																			echo "Rejected";
																		} 
																	?>
																</td>
																<td><?php echo $this->Html->link('', array('controller' => 'inward', 'action'=>'fetch_inward_id', $each['inward_id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?></td>
															</tr>
														<?php $sr_no++; } } ?>
													</tbody>
												</table>
											<?php } ?>
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
