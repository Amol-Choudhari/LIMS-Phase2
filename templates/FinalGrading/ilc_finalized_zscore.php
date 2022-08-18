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
							
								<div class="card-header"><h3 class="card-title-new">List of Finalized Result Submited by Z-score</h3></div>
								  
								<!-- <P class="p-2 text-center"><b> Org Sample Code : </b><?php echo $getresult['org_sample_code']; ?> <b> Category : </b><?php echo $getresult['category_name']; ?>  <b> Commodity :</b> <?php echo $getresult['category_name']; ?> <b> Sample Type : </b><?php echo $getresult['sample_type_desc']; ?> <?php echo $getresult['org_sample_code']; ?></p>  -->
								
								<div class="form-horizontal">

									<table id="finalized_samples_list" class="table table-striped table-bordered table-hover">
										<thead class="tablehead">
										<tr>
											<th>Sr No</th>
											<th>Finalized Sample Code</th>
											<th>Commodity</th>
											<th>Office</th>
											<th>Sample Type</th>
											<th>Finalized Date</th>
											<th>Action</th>
										</tr>
										</thead>
										<tbody>
											<?php										
											if (isset($result)) {	

												$i=1;		
											
												foreach ($result as $res2) { ?>
												
												<?php echo $this->Form->create(); ?>

													<tr class="text-center">
													
														<td><?php echo $i; ?></td>                                     
														<td><?php echo $res2['stage_smpl_cd']; ?></td>
														<td class="text-center"><?php echo $res2['commodity_name']; ?></td>	
														<td class="text-center"><?php echo $res2['ro_office']; ?></td>									
														<td class="text-center"><?php echo $res2['sample_type_desc'] ?></td>
														<td class="text-center"><?php echo $res2['tran_date']; ?></td>
														<td><?php echo $this->Html->link('', array('controller' => 'FinalGrading', 'action'=>'redirect_to_grade_ilc', trim($res2['stage_smpl_cd'])),array('class'=>'glyphicon glyphicon-share','title'=>'Finalized Zscore')); ?></td>
														<!-- ilc_non_grading_by_oic  $res2['org_sample_code'] --> 
														<div class="clearfix"></div>
													</tr>
												<?php echo $this->Form->end(); ?>	
													
											<?php $i=$i+1;}} ?>
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
</div>
<?php echo $this->Html->script("sampleAllocate/allocated_list"); ?>
<!-- <?php echo $this->Html->Script('finalGrading/finalized_sample_list'); ?> -->