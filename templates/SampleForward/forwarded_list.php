<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Forwarded Samples</li>
					</ol>
				</div>
			</div>
		</div>
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Form->create(); ?>
						<div class="masters_list card card-lims">
							<div class="panel panel-primary filterable">
								<div class="card-header"><h3 class="card-title-new">List of All Forwarded Samples</h3></div>
								<div class="col-md-12 radio_options text-center">

								<label class="radio-inline"><input class="type validate[required] radio" type="radio" id="type"  name="type" value="all" checked="checked">&nbsp;Forward List</label>
								<label class="radio-inline"><input class="type validate[required] radio" type="radio" id="type" name="type" value="ilc">&nbsp;ILC Forward List</label>
								
								</div>
								<div class="clear"></div>
								<!-- list of sample to Forward -->
								<div id="ALL_list">
									<table id="forwarded_samples_list" class="table table-bordered table-hover table-striped m-0">
										<thead class="tablehead">
											<tr>
												<th>Sr No</th>
												<th>Registered Code</th>
												<th>Forwarded Code</th>
												<th>Forwarded To</th>
												<th>Commodity</th>
												<th>Type of Sample</th>
												<th>Forwarded Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($res3)) {

												$sr_no = 1;

												foreach($res3 as $each){ ?>

													<tr>
														<td><?php echo $sr_no; ?></td>
														<td><?php echo $each['stage_sample_code']; ?></td>
														<td><?php echo $each['stage_smpl_cd']; ?></td>
														<td><?php echo $each['f_name'].' '.$each['l_name'].' ('.base64_decode($each['email']).')'; ?></td> <!--for email encoding-->
														<td><?php echo $each['commodity_name']; ?></td>
														<td><?php echo $each['sample_type_desc']; ?></td>
														<td><?php echo $each['tran_date']; ?></td>
														<td><?php echo $this->Html->link('', array('controller' => 'Sample-forward', 'action'=>'redirect_to_gnrt_ltr', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share','title'=>'Generate Letter for Single Sample')); ?> |
															<?php echo $this->Html->link('', array('controller' => 'Sample-forward', 'action'=>'gnrt_multiple_smpl_frwd_ltr'),array('class'=>'glyphicon glyphicon-list-alt','title'=>'Generate Letter for Multiple Samples')); ?> 
															<?php // echo $this->Html->link('', array('controller' => 'Sampleforward', 'action'=>'editForwardedSample',$each['stage_smpl_cd']),array('class'=>'fas fa-edit','title'=>'Edit Forwarded Sample')); ?>
														</td>
														
													</tr>
											<?php $sr_no++; } } ?>
										</tbody>
									</table>
								</div>
								<!-- list of sample to Forward ILC-->
								<div id="ILC_list">
									<table id="ilc_forwarded_samples_list" class="table table-bordered table-hover table-striped m-0">
										<thead class="tablehead">
											<tr>
												<th>Sr No</th>
												<th>Registered Code</th>
												<th>Forwarded Code</th>
												<th>Forwarded To</th>
												<th>Commodity</th>
												<th>Type of Sample</th>
												<th>Forwarded Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if (!empty($result)) {

												$sr_no = 1;
												$i = 1;
												foreach($result as $each){ ?>

													<tr>
														<td><?php echo $sr_no; ?></td>
														<td><?php echo $each['stage_sample_code']; ?></td>
														<td><?php echo $this->Form->control('stage_smpl_cd', array('type'=>'select','options'=>$subsamplelist[$i], 'label'=>false,'class'=>'form-control')); ?></td>
														<td><?php echo $this->Form->control('f_name', array('type'=>'select', 'options'=>$userdetailslist[$i],  'label'=>false,'class'=>'form-control')); ?></td>
														<td><?php echo $each['commodity_name']; ?></td>
														<td><?php echo $each['sample_type_desc']; ?></td>
														<td><?php echo $each['tran_date']; ?></td>
														<td><?php echo $this->Html->link('', array('controller' => 'Sample-forward', 'action'=>'redirect_to_gnrt_ltr_ilc', $each['stage_smpl_cd']),array('class'=>'glyphicon glyphicon-share','title'=>'Generate Letter for Single Sample')); ?> |
															<?php echo $this->Html->link('', array('controller' => 'Sample-forward', 'action'=>'gnrt_multiple_smpl_frwd_ltr'),array('class'=>'glyphicon glyphicon-list-alt','title'=>'Generate Letter for Multiple Samples')); ?> 
															<?php // echo $this->Html->link('', array('controller' => 'Sampleforward', 'action'=>'editForwardedSample',$each['stage_smpl_cd']),array('class'=>'fas fa-edit','title'=>'Edit Forwarded Sample')); ?>
														</td>
														
													</tr>
											<?php $sr_no++; $i++;} } ?>
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

<?php echo $this->Html->script("sampleForward/available_to_forward_list") ?>

