
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
									<div class="col-md-12 row p-2">
										<div class="col-md-3">
											<label>Sample Code <span class="required-star">*</span></label>
											<?php echo $this->Form->control('sample_code', array('type'=>'select', 'id'=>'sample_code', 'options'=>$samples_list, 'value'=>'', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
											<?php echo $this->Form->control('stage_sample_code', array('type'=>'hidden', 'id'=>'stage_sample_code','value'=>$stage_sample_code, 'label'=>false,'class'=>'form-control','required'=>true)); ?>
											<div id="error_sample_code"></div>
										</div>

										<div class="col-md-3">
											<label>Category <span class="required-star">*</span></label>
											<?php echo $this->Form->control('category_code', array('type'=>'select', 'id'=>'category_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
											<div id="error_commodity_code"></div>
											<input type="hidden" class="form-control" id="type" name="type"  hidden>
										</div>
										<div class="col-md-3">
											<label>Commodity <span class="required-star">*</span></label>
												<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
											<div id="error_commodity_code"></div>
										</div>
										<div class="col-md-3">
											<label>Sample Type <span class="required-star">*</span></label>
												<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true,)); ?>
											<div id="error_sample_type"></div>
										</div>
									</div>
									<div class="col-md-12 row">
										<div class="col-md-10">
											<P><b>Below is no. of RAL's/CAL's involved in this ILC sample processing.</b></p> 
										</div>
										<div class="col-md-2">
											<a href="#getmodal" target='_blank'  name="get_zscore"  id="get_zscore" data-toggle="modal" data-target="#getmodal" class="btn btn-outline-danger get_zscore" required>get zscore</a>
										</div>
									</div>
									
									
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
																<div class="clearfix"></div>
															</tr>

														<?php echo $this->Form->end(); ?>	
															
												<?php $i=$i+1;}} ?>				
											</tbody>
										</table>

									</div>
									<div class="row p-3">
										<div class="col-md-6"></div>
										<div class="col-md-6">
											<label>Remark</label>
											<?php echo $this->Form->control('remark', array('type'=>'textarea', 'id'=>'remark','label'=>false,'class'=>'form-control','required'=>true,)); ?>
										
										</div>
									</div>
									<div class="col-md-12 p-2">
											<button type="submit" class="btn btn-success" name="frd_to_oic" id="frd_to_oic" value="Save">Forward To OIC</button>
									</div>
								</div>
								
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>



<!-- added for get zscore model on 03/08/2022 by shreeya -->

<?php echo $this->element('ilc_zscore_modal_element');?>

 <?php echo $this->Html->Script('sample_grading_by_oic'); ?> 
<?php echo $this->Html->Script('finalGrading/finalized_sample_list'); ?> 
<?php echo $this->Html->Script('finalGrading/ilc_grading_by_inward'); ?>

