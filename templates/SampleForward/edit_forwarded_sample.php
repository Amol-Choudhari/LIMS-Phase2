<?php echo $this->Html->Script('sample_forward_form'); ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('class'=>'add_btn btn btn-secondary')); ?></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Edit Sample</li>
					</ol>
				</div>
			</div>
		</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Form->create(null, array('id'=>'frm_sample_forward','class'=>'form-group')); ?>
							<div class="card card-lims">
								<div class="card-header"><h3 class="card-title-new">Edit Sample</h3></div>
									<div class="form-horizontal mb-3">
										<div class="card-body">
											<?php if(!empty($validate_err)){ ?><div style="text-align:center;color:red;" class="alert alert-danger"><?php echo $validate_err; ?></div><?php } ?>
												<div class="row">
													<div class="col-md-3">
														<label>Sample Code <span class="required-star">*</span></label>				
															<?php echo $this->Form->control('stage_sample_code', array('type'=>'select', 'id'=>'stage_sample_code', 'options'=>$res, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true,'onchange'=>'getsampledetails();return false',)); ?>	
														<div id="error_sample_code"></div>
													</div>

													<div class="col-md-3">
														<label>Commodity Name <span class="required-star">*</span></label>				
															<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>	
															<div id="error_commodity_code"></div>
														<input type="hidden" class="form-control" id="type" name="type"  hidden>
													</div>

													<div class="col-md-3">
														<label>Sample Type <span class="required-star">*</span></label>				
															<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>	
														<div id="error_sample_type"></div>
													</div>

													<div class="col-md-3">
														<label>Select Office <span class="required-star">*</span></label>
														<div class="colmd-12">
															<label class="radio-inline "><input type="radio" id="acc_rej_flg" name="ral_cal" value="RAL" required > RAL</label>
															<label class="radio-inline "><input type="radio" id="acc_rej_flg1"  name="ral_cal" value="CAL" required > CAL</label>
												
															<?php if ($_SESSION['user_flag']=='HO' || $_SESSION['user_flag']=='RO/SO OIC' ||  $_SESSION['user_flag']=='RAL' || $_SESSION['user_flag']=='CAL' || 
																	  $_SESSION['user_flag']=='RO' || $_SESSION['user_flag']=='SO' || $_SESSION['user_flag']=='RAL/CAL OIC') { ?>
																
																<label class="radio-inline "><input type="radio" id="acc_rej_flg2"  name="ral_cal" value="HO" required > HO</label>
															<?php } ?>
											
															<input type="hidden" id="inward_id"  name="inward_id">
														</div>
													</div>

													<div class="col-md-3 mt-3">
														<label>Forward To <span class="required-star">*</span></label>
															<?php echo $this->Form->control('dst_loc_id', array('type'=>'select', 'id'=>'dst_loc_id', 'options'=>$office, 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true,'onchange'=>'get_user_name();return false',)); ?>	
														<div id="error_dst_loc_id"></div>
													</div>

													<div class="col-md-3 mt-3">
														<label id="dst_usr_lbl" >User Name <span class="required-star">*</span></label>				
															<?php echo $this->Form->control('dst_usr_cd', array('type'=>'select', 'id'=>'dst_usr_cd', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>	
														<div id="error_dst_usr_cd"></div>
													</div>			
											</div>
										</div>
									</div>
									<div class="card-footer">
										<?php echo $this->Form->submit('Forward Sample', array('name'=>'forward_sample', 'id'=>'ral', 'label'=>false,'class'=>'btn btn-success float-left')); ?>
										<a href="../SampleForward/available_to_forward_list" class="btn btn-danger float-right">Cancel</a>
									</div>
							</div>
					</div>
				</div>
			</div>
		</section>
</div>

<?php echo $this->Html->script('sample_forward/edit_forwarded_sample'); ?>
<?php
	unset($_SESSION['sample']);
	unset($_SESSION['stage_sample_code']);
?>
