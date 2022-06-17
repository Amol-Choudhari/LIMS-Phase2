<?php ?>
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
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(null,array('class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">List of All Samples for which Test Reading Submitted</h3></div>		
									<div class="panel panel-primary filterable">
									<!-- list of sample to enter reading -->
										<table id="view_perform_sample" class="table table-bordered table-hover table-active"> 
											<thead class="tablehead">
												<tr>
													<th>Sr No</th>
													<th>Sample Code</th>
													<th>Category</th>
													<th>Commodity</th>
													<th>Sample Type</th>							
													<th>Action</th>
												</tr>
											</thead>	
											<tbody>
												<?php $i=1; foreach($list_of_finalized_test as $each_record){ ?>
												
													<tr><td><?php echo $i; ?></td>
													<td><?php echo $each_record['stage_smpl_cd']; ?></td>
													<td><?php echo $each_record['category_name']; ?></td>
													<td><?php echo $each_record['commodity_name']; ?></td>
													<td><?php echo $each_record['sample_type_desc']; ?></td>							
													<td><a class='glyphicon glyphicon-eye-open' href='chemist_test_report_code/<?php echo trim($each_record['stage_smpl_cd']);?>/<?php echo trim($each_record['commodity_code']);?>' target='_blank'></a></td></tr>							
												<?php $i++; } ?>
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


			
<?php echo $this->Html->Script('test/view_prfm_test'); ?>