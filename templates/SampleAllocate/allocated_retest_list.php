<?php ?>
<?php echo $this->Html->css('sampleAllocate/available_to_allocate'); ?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary float-left')); ?></div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
							<li class="breadcrumb-item active">Allocated List</li>
						</ol>
					</div>
				</div>
			</div>
			<section class="content form-middle">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<?php echo $this->Form->create(null,array('class'=>'form-group')); ?>
								<div class="masters_list card card-lims">
									<div class="panel panel-primary filterable">
										<div class="card-header"><h3 class="card-title-new">List of All Allocated/Forwarded Samples For Retest</h3></div>
											<div class="col-md-12 radio_options" >
												<label class="radio-inline "><input class="type radio" type="radio" name="type" value="A" checked>Allocated Samples</label>
												<label class="radio-inline "><input class="type radio" type="radio" name="type" value="F">Forwarded Samples</label>
											</div>

												<!-- list of Allocated Samples -->
													<div id="A_list">
														<table id="allocated_sample_table" class="table m-0 table-bordered table-hover table-striped">
															<thead class="tablehead">
																<tr>
																	<th>Sr No</th>
																	<th>Sample Allocation Date</th>
																	<th>Sample Code</th>
																	<th>Chemist / Division Code</th>
																	<th>Chemist Name</th>
																	<th>Tests Names</th>
																	<th width="60">Action</th>
																</tr>
															</thead>
															<tbody>
																<?php if (isset($allRes)) {

																		$srno = 1;

																		foreach ($allRes as $res1) { ?>

																		<tr>
																			<td><?php echo $srno; ?></td>
																			<td><?php echo $res1['alloc_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
																			<td><?php echo $res1['sample_code']; ?></td>
																			<td><?php echo $res1['chemist_code']; ?></td>
																			<td><?php echo $res1['cun_f_name'] ?></td>
																			<td><select class="form-control w-150px">
																				<?php foreach ($allRes1 as $data1) {

																					if( $res1['chemist_code']==$data1['chemist_code']){ ?>

																						<option value="<?php echo $data1['test_name'] ?>"><?php echo $data1['test_name'] ?></option>

																				<?php } } ?>
																				</select>
																			</td>
																			<td><a href="../SampleAllocate/redirect_to_sample_slip/<?php echo $res1['sample_code']; ?>" title="Sample Slip" target="_blank" class="glyphicon glyphicon-list-alt"></a></td>
																		</tr>
																<?php $srno++; } } ?>
															</tbody>
														</table>
													</div>

														<!-- list of Allocated Samples -->
														<div id="F_list">
															<table id="forwarded_sample_table"  class="table m-0 table-bordered table-hover table-striped">
																<thead class="tablehead">
																	<tr>
																		<th>Sr No</th>
																		<th>Sample Code</th>
																		<th>Forward To</th>
																		<th>Designation</th>
																	</tr>
																</thead>
																<tbody>
																	<?php if (isset($res)) {
																		$srno = 1;
																		foreach ($res as $res1) { ?>
																			<tr>
																				<td><?php echo $srno; ?></td>
																				<td><?php echo $res1['stage_smpl_cd']; ?></td>
																				<td><?php echo $res1['f_name'] ?></td>
																				<td><?php echo $res1['role'] ?></td>
																			</tr>
																	<?php $srno++; } } ?>
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
<?php echo $this->Html->script("sampleAllocate/allocated_retest_list"); ?>
