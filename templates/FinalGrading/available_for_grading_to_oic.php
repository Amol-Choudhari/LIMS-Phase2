<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">List of All Samples available for Final Grading</li>
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
								<div class="card-header"><h3 class="card-title-new">List of All Samples available for Final Grading</h3></div>
									<!-- added  radio button  for ilc flow 07-07-2022 -->
									<div class="col-md-12 radio_options text-center">

									<label class="radio-inline"><input class="type validate[required] radio" type="radio" id="type"  name="type" value="all" checked="checked">&nbsp;All Samples List</label>
									<label class="radio-inline"><input class="type validate[required] radio" type="radio" id="type" name="type" value="ilc">&nbsp;ILC Samples List</label>

									</div>
									<div class="clear"></div>
									<div class="form-horizontal">
										<!-- list of sample  -->
										<div id="ALL_list">
											<table id="avai_to_verify" class="table table-striped table-bordered table-hover">
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
												<?php
													if (!empty($sample_codes)) {

													$sr_no = 1;

													foreach ($sample_codes as $each) { ?>

														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $each['stage_smpl_cd']; ?></td>
															<td><?php echo $each['commodity_name'];  ?></td>
															<td><?php echo $each['sample_type_desc']; ?></td>
															<td><?php echo $each['ro_office']; ?></td>
															<td><?php echo $each['submitted_on']; ?></td>
															<td><?php echo $this->Html->link('', array('controller' => 'FinalGrading', 'action'=>'redirect_to_grade', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'To Final Grading')); ?></td>
														</tr>
													<?php $sr_no++; } } ?>
												</tbody>
											</table>
										</div>
										<!-- list of sample ILC -->
										<div id="ILC_list">
										<table id="ilc_avai_to_verify" class="table table-striped table-bordered table-hover">
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
												<?php
													if (!empty($sample_codes1)) {

													$sr_no = 1;

													foreach ($sample_codes1 as $each) { ?>

														<tr>
															<td><?php echo $sr_no; ?></td>
															<td><?php echo $each['stage_smpl_cd']; ?></td>
															<td><?php echo $each['commodity_name'];  ?></td>
															<td><?php echo $each['sample_type_desc']; ?></td>
															<td><?php echo $each['ro_office']; ?></td>
															<td><?php echo $each['submitted_on']; ?></td>
															<td><?php echo $this->Html->link('', array('controller' => 'FinalGrading', 'action'=>'redirect_to_grade_ilc', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share-alt','title'=>'To Final Grading')); ?></td>
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

<?php echo $this->Html->script("sampleAllocate/allocated_list"); ?>
