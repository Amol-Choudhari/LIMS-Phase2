<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<!-- <?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary float-sm-left')); ?> -->
					
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">NABL Scope accreditation</li>
				</ol>
			</div>
		</div>
	</div>
		
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 mb-2">
					<?php echo $this->Html->link('Add New', array('controller' => 'nablAccreditation', 'action'=>'add-nabl'),array('class'=>'add_btn btn btn-primary float-left')); ?>
				</div>
				<div class="col-md-12">
					<?php echo $this->Form->create(); ?>
						<div class="card card-lims">
							<div class="card-header"><h3 class="card-title-new">List of All NABL Scope accreditation</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="panel panel-primary filterable">
											<table id="pages_list_table" class="table table-striped table-bordered table-hover">
												<thead class="tablehead">
													<tr>
														<th>Sr.No</th>
														<th>RAL/CAL</th>
														<th>Commodity</th>										
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
														<td><?php echo $each['ro_office'] ?></td>
														<td><?php echo $each['commodity_name'] ?></td>						
														<td><?php echo $this->Html->link('', array('controller' => 'nablAccreditation', 'action'=>'fetch_nabl_id', $each['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?></td>
													</tr>
												<?php $sr_no++; } } ?>
												</tbody>
											</table>
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
