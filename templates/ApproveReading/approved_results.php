<?php echo $this->Html->css("approveReading/approved_results"); ?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">View Perform Test</li>
				</ol>
			</div>
		</div>
	</div>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-10 ma">
					<?php echo $this->Form->create(); ?>
						<div class="masters_list card card-lims">
							<div class="card-header"><h3 class="card-title-new">List of All Samples with Approved Test Results</h3></div>
							<div class="panel panel-primary filterable">
								<table id="approvedresulttable" class="table table-stripped table-hover table-bordered">
									<thead class="tablehead">
										<tr>
											<th>Sr No.</th>
											<th>Sample Code</th>
											<th>Category Name</th>
											<th>Commodity Name</th>
											<th>Sample Type</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php if (isset($showapprovedresult)) {
												$j = 1;
												foreach ($showapprovedresult as $data) { ?>
													<tr id="">
													<td><?php echo $j; ?></td>
													<td><?php echo $data['stage_smpl_cd'] ?></td>
													<td id="catam<?php echo $data['stage_smpl_cd'] ?>"><?php echo $data['category_name'] ?></td>
													<td id="comm<?php echo $data['stage_smpl_cd'] ?>"><?php echo $data['commodity_name'] ?></td>
													<td id="st<?php echo $data['stage_smpl_cd'] ?>"><?php echo $data['sample_type_desc'] ?></td>
													<td><button type="button" id = "<?php echo $data['stage_smpl_cd'] ?>" class="btn btn-info stage_sample_id vbtn"><strong>View</strong></td>
										<?php $j++; } } ?>
									</tbody>
								</table>
							</div>
							<div class="clear"></div>
						</div>

						<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-body">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-6">
													<label>Sample Code : <label for="samplecodelabel" class="badge badge-info"></label></label>
												</div>
												<div class="col-md-6">
													<label>Sample Type : <label for="sampletypelabel" class="badge badge-success"></label><label>
												</div>
												<div class="col-md-6">
													<label>Category : <label for="categorylabel"></label></label>
												</div>
												<div class="col-md-6">
													<label>Commodity : <label for="commoditylabel"></label></label>
												</div>
											</div>
										</div>

										<!--Added by Akash for modal view.-->
										<div class="row">
											<div class="table-responsive">
												<table class="table table-striped table-bordered" id="modalView">
													<thead>
														<tr>
															<th>Sr No.</th>
															<th>Test Name</th>
															<th>Final Result</th>
															</tr>
														</thead>
													<tbody></tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-info cbtn" data-dismiss="modal">Close</button>
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

<?php echo $this->Html->script("approveReading/approved_results"); ?>
