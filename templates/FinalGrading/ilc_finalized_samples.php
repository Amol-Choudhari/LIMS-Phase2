<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">List of Non Graded Samples for ILC</li>
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
								<div class="card-header"><h3 class="card-title-new">List of Available samples to get Z-score</h3></div>
									<div class="form-horizontal">
										<table id="finalized_samples_list" class="table table-striped table-bordered table-hover">
											<thead class="tablehead">
												<tr>
													<th>Sr No</th>
													<th>Finalized Sample Code</th>
													<th>Finalized Date</th>
													<th>Category</th>
													<th>Commodity</th>
													<th>Sample Type</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php										
													if (isset($ilc_sample_reports)) {	

														$i = 1;										
														
														foreach ($ilc_sample_reports as $res2) { ?>

														<tr>
														
															<td class="text-center"><?php echo $i; ?></td>                                        
															<td class="text-center"><?php echo $res2['stage_smpl_cd']; ?></td>
															<td class="text-center"><?php echo $res2['tran_date']; ?></td>
															<td class="text-center"><?php echo $res2['category_name']; ?></td>
															<td class="text-center"><?php echo $res2['commodity_name']; ?></td>										
															<td class="text-center"><?php echo $res2['sample_type_desc'] ?></td>
															<td class="text-center"><?php echo $this->Html->link('', array('controller' => 'FinalGrading', 'action'=>'ilc_available_sample_zscore', $res2['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share','title'=>'finalized result submited by RAL/CAL')); ?></td>

														</tr>
												<?php $i=$i+1; }} ?>				
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

<?php echo $this->Html->Script('finalGrading/finalized_sample_list'); ?>