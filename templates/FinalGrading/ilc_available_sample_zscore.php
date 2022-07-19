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
							
								<div class="card-header"><h3 class="card-title-new">List of Finalized Result Submited By RAL's/CAL's for sample code <?php echo $getcommodity['org_sample_code']; ?></h3></div>
								  
									<P class="p-2 text-center"><b> Org Sample Code : </b><?php echo $getcommodity['org_sample_code']; ?> <b> Category : </b><?php echo $getcommodity['category_name']; ?>  <b> Commodity :</b> <?php echo $getcommodity['category_name']; ?> <b> Sample Type : </b><?php echo $getcommodity['sample_type_desc']; ?></p>
								
									<div class="form-horizontal">
										<table id="finalized_samples_list" class="table table-striped table-bordered table-hover">
											<thead class="tablehead">
												<tr>
													<th>Sr No</th>
													<th>Sample Code</th>
													<th>Forwarded To</th>
													<th>Forwarded On(OF)</th>
													<th>Finalized On(FG)</th>
													<th>Report</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>

											<?php										
													if (isset($result)) {	

														$i=1;		
													
														foreach ($result as $res2) { ?>
														
														<tr>
															<td class="text-center"><?php echo $i; ?></td>                                     
															<td class="text-center"><?php echo $res2['ilc_org_sample_cd']; ?></td>
															<td class="text-center"><?php echo $res2['stage_smpl_cd']; ?> - <?php echo $res2['ro_office']; ?> </td>
														    <td class="text-center"><?php echo $res2['tran_date']; ?></td>
															<td class="text-center"><?php echo $final_reports[$i]; ?></td> 
															<td class="text-center"><a href="<?php echo $res2['report_pdf']; ?>" target='_blank' class="btn btn-outline-info">View</a></td>
															<td class="text-center"><a href="#" target='_blank' class="btn btn-outline-danger">Zscore</a></td>

														</tr>
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

<?php echo $this->Html->Script('finalGrading/finalized_sample_list'); ?>