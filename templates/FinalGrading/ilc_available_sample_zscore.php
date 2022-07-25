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
							
								<div class="card-header"><h3 class="card-title-new">List of Finalized Result Submited By RAL's/CAL's for sample code </h3></div>
<!-- 								  
									<P class="p-2 text-center"><b> Org Sample Code : </b><?php echo $getcommodity['org_sample_code']; ?> <b> Category : </b><?php echo $getcommodity['category_name']; ?>  <b> Commodity :</b> <?php echo $getcommodity['category_name']; ?> <b> Sample Type : </b><?php echo $getcommodity['sample_type_desc']; ?>  <?php echo $getcommodity['org_sample_code']; ?></p> -->
								
									<div class="form-horizontal">

										<table id="finalized_samples_list" class="table table-striped table-bordered table-hover">
											<thead class="tablehead">
												<tr>
													<th>Sr No</th>
													<th>Sample Code</th>
													<th>Forwarded To</th>
													<th>Forwarded On(OF)</th>
													<th>Finalized On(FG)</th>
													<th>View Report</th>
													<th>Calculated Z-score</th>
													<th>Put Finalized Z-score</th>
													<th colspan="2">Action</th>
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
																<td><input type="text" class="form-control" name="ilc_org_sample_cd" value="<?php echo $res2['ilc_org_sample_cd']; ?>" readonly></td>
																<td><?php echo $res2['stage_smpl_cd']; ?> - <?php echo $res2['ro_office']; ?> </td>
																<td><?php echo $res2['tran_date']; ?></td>
																<td><?php echo $final_reports[$i]; ?></td> 
																<td><a href="<?php echo $res2['report_pdf']; ?>" target='_blank' class="btn btn-outline-info">View</a></td>

																<td>2</td>
																
																<td><input type="text"  class="form-control text-center" id="zscore" name="zscore" value="<?php echo $res2['zscore']; ?>" required></td>
															
																<td><a href="<?php echo $res2['ilc_org_sample_cd']; ?>"><button type="submit" class="btn btn-outline-success" name="save_zscore"  id="save_zscore">Save</button></a></td>
																<td><?php echo $this->Html->link('', array('controller' => 'FinalGrading', 'action'=>'ilc_gnrt_ltr_pdf', $res2['ilc_org_sample_cd']),array('class'=>'glyphicon glyphicon-share','title'=>'Generate Letter for Single Sample')); ?></td>
																<div class="clearfix"></div>
															</tr>

															

														<?php echo $this->Form->end(); ?>	
															
														
														
												<?php $i=$i+1;}} ?>

																
											</tbody>
										</table>
									</div>
									
								</div>
								<button type="submit" class="btn btn-success" id="frdoic">Forward To OIC</button>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>

<?php echo $this->Html->Script('finalGrading/finalized_sample_list'); ?>