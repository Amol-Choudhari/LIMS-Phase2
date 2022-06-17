<?php echo $this->Html->css("sampleAllocate/sample_slip"); ?>
<div class="content-header">
	<!--<div class="container-fluid">
		<div class="row mb-2">
      		<div class="col-sm-6"><?php //echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php //echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php //echo $this->Html->link('Available To Allocate', array('controller' => 'SampleAllocate', 'action'=>'available_to_allocate')); ?></li>
						<li class="breadcrumb-item active">Sample Allocate</li>
					</ol>
				</div>
			</div>
		</div>-->
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-lims">
							<div class="form-horizontal">
								<div class="card-body">
									<?php if(isset($testalloc) && $testalloc!='') { ?>

										<style> h4 {
														padding: 5px;
														font-family: times;
														font-size: 12pt;
												    }

												table{
														padding: 5px;
														font-size: 10pt;
														font-family: times;
													}
										</style>

										<table width="100%" border="1">
											<tr>
												<td align="center"><h4>Sample Slip</h4></td>
											</tr>
										</table>
										<table width="100%" border="1">
											<tr>
												<td style="padding:6px; vertical-align:top;">Sample Code</td>
												<td style="padding:6px; vertical-align:top;"><?php echo $testalloc[0]['chemist_code']; ?></td>
											</tr>
											<tr>
												<td style="padding:6px; vertical-align:top;">Commodity Name</td>
												<td style="padding:6px; vertical-align:top;"><?php echo $testalloc[0]['commodity_name']; ?></td>
											</tr>

											<tr>
												<td style="padding:6px; vertical-align:top;">User Name</td>
												<td style="padding:6px; vertical-align:top;"><?php echo $testalloc[0]['f_name'].' '.$testalloc[0]['l_name']; ?></td>
											</tr>

											<tr>
												<td style="padding:6px; vertical-align:top;">Date</td>
												<td style="padding:6px; vertical-align:top;"><?php echo $testalloc[0]['received_date']; ?></td>
											</tr>

											<tr>
											<td style="padding:6px; vertical-align:top;">Allocated Tests</td>
											<td style="padding:6px; vertical-align:top;">

											<table>
												<?php $tests = explode(",",$testalloc[0]['tests']);
													for($i=0;$i<count($tests);$i++){ ?>
														<tr>
															<td><?php echo $tests[$i];?></td>
														</tr>
												<?php }	?>
											</table>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<?php } else { ?>
							<?php echo $this->Form->create(null, array('id'=>'frm_sample_forward','class'=>'form-group')); ?>
								<div class="card-header"><h3 class="card-title-new">Generate Sample Slip</h3></div>
									<div class="form-horizontal">
										<div class="card-body">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group row">
														<label for="inputEmail3" class="col-sm-3 col-form-label">Sample Code <span class="required-star">*</span></label>
															<div class="custom-file col-sm-5">
																<?php echo $this->Form->control('sample', array('type'=>'select', 'id'=>'sample', 'options'=>$sample, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true)); ?>
																<span id="error_sample" class="error invalid-feedback"></span>
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group row">
															<label for="inputEmail3" class="col-sm-5 col-form-label">Chemist/Division Code <span class="required-star">*</span></label>
																<div class="custom-file col-sm-5">
																	<?php echo $this->Form->control('sample_code', array('type'=>'select', 'id'=>'sample_code', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
																	<span id="error_sample_code" class="error invalid-feedback"></span>
																</div>
															</div>
														</div>
														<div class="col-md-2 float-left" class="mt-px">
															<?php echo $this->Form->submit('Get Sample Slip', array('name'=>'save', 'id'=>'save', 'label'=>false,'class'=>'form-control btn btn-success')); ?>
														</div>
														<div class="col-md-2 float-right" class="mt-px">
															<a href="../Dashboard/home" class="form-control btn btn-danger">Cancel</a>
														</div>
													</div>
												</div>
											<?php echo $this->Form->end(); ?>
										</div>

										<script>

											</script>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</section>
				</div>

<?php echo $this->Html->script("sampleAllocate/sample_slip"); ?>
