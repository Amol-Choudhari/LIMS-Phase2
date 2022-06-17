<?php echo $this->Html->css("sampleAllocate/available_to_allocate"); ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Available To Enter Reading</li>
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
							<div class="card-header"><h3 class="card-title-new">List of All Samples Available to Enter Test Readings</h3></div>
								<div class="card-horizontal">
									<div class="card-body">
										<div class="panel panel-primary filterable">
										<!-- list of sample to enter reading -->
											<table id="avai_for_test_reading" class="table table-bordered table-hover table-striped">
												<thead class="tablehead">
													<tr>
														<th>Sr No</th>
														<th>Sample Code</th>
														<th>Commodity</th>
														<th>Type of Sample</th>
														<th>Office</th>
														<th>Accepted On</th>
														<th>Action</th>
													</tr>
													</thead>
													<tbody>
														<?php if (!empty($chemist_codes_list)) {

															$sr_no = 1;

															foreach ($chemist_codes_list as $each) { ?>

																<tr>
																	<td><?php echo $sr_no; ?></td>
																	<td><?php echo $each['chemist_code']; ?></td>
																	<td><?php echo $each['commodity_name'];  ?></td>
																	<td><?php echo $each['sample_type_desc']; ?></td>
																	<td><?php echo $each['ro_office']; ?></td>
																	<td><?php echo $each['accepted_on']; ?></td>
																	<td><?php echo $this->Html->link('', array('controller' => 'Test', 'action'=>'redirect_to_enter_reading', $each['chemist_code']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'To Enter Test Readings')); ?></td>
																</tr>
														<?php $sr_no++; } } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
	</section>
</div>
<?php echo $this->Html->script("test/available_to_enter_reading"); ?>
