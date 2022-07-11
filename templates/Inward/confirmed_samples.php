
<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Confirmed Sample</li>
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
								<div class="card-header"><h3 class="card-title-new">List of All Confirmed Samples</h3></div>
									<table id="pages_list_table" class="table table-bordered table-hover table-striped">
										<thead class="tablehead">
											<tr>
												<th>SR.No</th>
												<th>Sample Code</th>
												<th>Received Date</th>
												<th>Sample Type</th>
												<th>Received From</th>
												<th>Commodity</th>
												<th>Status</th>
												<th width="60" >Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if(!empty($res)){
												$sr_no = 1;
												foreach($res as $each){ ?>
												<tr>
													<td><?php echo $sr_no; ?></td>
													<td><?php echo $each['org_sample_code']; ?></td>
													<td><?php echo $each['received_date'];  ?></td>
													<td><?php echo $each['sample_type_desc']; ?></td>
													<td><?php echo $each['ro_office']; ?></td>
													<td><?php echo $each['commodity_name']; ?></td>
													<td><?php if(trim($each['acc_rej_flg'])=='A'){ 
																echo "Accepted";
															}elseif(trim($each['acc_rej_flg'])=='P'){ 
																echo "Pending";
															}elseif(trim($each['acc_rej_flg'])=='PS'){
																echo "Payment is Saved & Pending with DDO";
															}elseif(trim($each['acc_rej_flg'])=='PR'){
																echo "Payment is Referred Back";
															}else{
																echo "Rejected";
															} ?>
													</td>
													<td>
														<?php echo $this->Html->link('', array('controller' => 'inward', 'action'=>'fetch_inward_id', $each['inward_id']),array('class'=>'glyphicon glyphicon-eye-open','title'=>'Sample Inward Details')); ?> |
														<a href="../inward/get_sample_slip/<?php echo $each['org_sample_code']; ?>" title="Sample Slip" target="_blank" class="glyphicon glyphicon-list-alt"></a>
													</td>
												</tr>
											<?php $sr_no++; } } ?>
										</tbody>
									</table>
								</div>
							<?php echo $this->Form->end(); ?>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php echo $this->Html->script("sampleForward/available_to_forward_list") ?>
