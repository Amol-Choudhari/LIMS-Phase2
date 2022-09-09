<?php echo $this->Html->css("sampleAllocate/available_to_allocate"); ?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">List of All Allocated Samples</li>
					</ol>
				</div>
			</div>
		</div>

		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(null,array('id'=>'modal_test','class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">List of All Allocated Samples (Accept / Send Back)</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<input type="hidden" class="form-control" id="sample" name="chemist_code">
										<input type="hidden" class="form-control" id="test_v" name="test_code">
										<div class="panel panel-primary filterable">
											<table  class="table table-bordered table-striped table-hover" id="input_parameter_text" >
												<thead class="tablehead">
													<tr>
														<th>Select</th>
														<th>Sample </th>
														<th>Commodity</th>
														<th width="250">Test Names</th>
													</tr>
												</thead>
												<tbody>
													<?php if (isset($testalloc)) {
															foreach ($testalloc as $data) { ?>
															<tr id="">
																<td><input type="checkbox" class="check_id" id="<?php echo $data['sr_no'] ?>" name="checkboxArray[]" value=""/></td>
																<td><label  class="control-label " for="sel1"><?php echo $data['chemist_code'] ?></td>
																<td><label  class="control-label " for="sel1"><?php echo $data['commodity_name'] ?></td>
																<td><select class="form-control w-200">
																		<?php foreach ($testalloc1 as $data1) {
																				if ($data['chemist_code']==$data1['chemist_code']){ ?>
																					<option value="<?php echo $data1['test_name'] ?>"><?php echo $data1['test_name'] ?></option>
																		<?php } } ?>
																	</select>
																</td>
															</tr>
													<?php } } ?>
												</tbody>
											</table>
										</div>

										<?php if (isset($testalloc)) { ?>

											<div class="col-md-12 mb-3">
												<div class="row">
													<div class="col-md-12">
														<div class="radio_options">
															<label class="radio-inline "><input class="radio action" type="radio" name="action" value="A" required >Accept Selected Samples</label>
															<label class="radio-inline "><input class="radio action" type="radio" name="action" value="R" required >Send Back Selected Samples</label>
														</div>
													</div>
													<div class="col-md-12">
														<div class="col-md-6 float-right mt-3">
															<div class="row"></div>
														</div>
													</div>
													<div class="col-md-12">
														<div class="add_master">
															<div id="abc">
																<div class="col-md-6">
																	<label>Reason to Send Back <span class="required-star">*</span></label>
																	<textarea class="form-control" name="sendback_remark" id="sendback_remark" required  placeholder="Enter Reason" ></textarea>
																</div>
															</div>

														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="card-footer">
										<div class="col-md-1 float-left"><a href="#" id="save_a" title="Accept to Peform Test" class="btn btn-success">Accept</a></div>
										<div class="col-md-2 float-left"><a href="#" id="save_r_n" title="To Cancel Allocation" class="btn btn-primary">Send Back</a></div>
										<div class="col-md-1 float-right"><a href="../Dashboard/home" class="btn btn-danger">Cancel</a></div>
									</div>
									<?php } ?>
							</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
	</div>
<?php echo $this->Html->script('test/accept_sample'); ?>
