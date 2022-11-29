
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Available Sample List', array('controller' => 'SampleAccept', 'action'=>'available_to_accept_list')); ?></li>
					<li class="breadcrumb-item active">Accept Sample</li>
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
							<div class="card-header"><h3 class="card-title-new">Sample Accept</h3></div>
							<div class="form-horizontal mb-3">
								<div class="card-body">
									<?php if (!empty($validate_err)) { ?><div class="alert alert-danger tac"><?php echo $validate_err; ?></div><?php } ?>
									<div class="row">
										<input type="hidden" id="tran_date" name="tran_date" value="<?php echo date('Y-m-d');?> "/>
										<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
										<input type="hidden" id="src_loc_id" name="src_loc_id" value=""/>
										<input type="hidden" id="homCnt" name="homCnt" value=""/>
										<select class="dnone" class="form-control" id="category_code"  name="category_code" ></select>

										<div class="col-sm-6">
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-4 col-form-label">Sample Code <span class="required-star">*</span></label>
												<div class="custom-file col-sm-6">
													<?php echo $this->Form->control('stage_sample_code', array('type'=>'select', 'id'=>'stage_sample_code', 'options'=>$samples_list, 'value'=>'', 'label'=>false,/*'empty'=>'--Select--',*/'class'=>'form-control','required'=>true)); ?>
													<span id="error_sample_code" class="error invalid-feedback"></span>
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-4 col-form-label">Commodity Name <span class="required-star">*</span></label>
												<div class="custom-file col-sm-6">
													<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
													<span id="error_commodity_code" class="error invalid-feedback"></span>
													<input type="hidden" class="form-control" id="type" name="type"  hidden>
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-4 col-form-label">Sample Type <span class="required-star">*</span></label>
												<div class="custom-file col-sm-6">
													<?php echo $this->Form->control('sample_type', array('type'=>'select', 'id'=>'sample_type', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
													<span id="error_sample_type" class="error invalid-feedback"></span>
												</div>
											</div>

											<div class="form-group">
												<label for="inputEmail3" class="col-sm-4 col-form-label">Select Office <span class="required-star">*</span></label>
												<div class="custom-file col-sm-6">
													<?php if ($_SESSION["user_flag"]=="RAL") { ?>
														<label class="radio-inline"><input class="validate[required] radio" type="radio" id="acc_rej_flg" name="ral_cal" value="RAL" required> <label class="badge badge bg-purple">RAL</label></label>
													<?php } if ($_SESSION["user_flag"]=="CAL") { ?>
														<label class="radio-inline "><input class="validate[required] radio" type="radio" id="acc_rej_flg1"  name="ral_cal" value="CAL"  required > CAL</label>
													<?php } if ($_SESSION['user_flag']=="HO") { ?>
														<label class="radio-inline "><input class="validate[required] radio" type="radio" id="acc_rej_flg2"  name="ral_cal" value="HO"  required> HO</label>
													<?php } ?>
													<input type="hidden" id="inward_id"  name="inward_id">
													<input type="hidden" name="acceptstatus_date" id="acceptstatus_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
												</div>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-4 col-form-label">Accepted By <span class="required-star">*</span></label>
												<div class="custom-file col-sm-6">
													<?php echo $this->Form->control('dst_loc_id', array('type'=>'select', 'id'=>'dst_loc_id', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
													<span id="error_dst_loc_id" class="error invalid-feedback"></span>
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-4 col-form-label">User Name <span class="required-star">*</span></label>
												<div class="custom-file col-sm-6">
													<?php echo $this->Form->control('dst_usr_cd', array('type'=>'select', 'id'=>'dst_usr_cd', 'options'=>'', 'value'=>'', 'label'=>false,'empty'=>'--Select--','class'=>'form-control','required'=>true)); ?>
													<span id="error_dst_usr_cd" class="error invalid-feedback"></span>
												</div>
											</div>
											<div class="form-group">
												<label for="inputEmail3" class="col-sm-4 col-form-label">For Analysis <span class="required-star">*</span></label>
												<label class="radio-inline "><input  type="radio" id="result_dupl_single_flag"  name="result_dupl_flag" value="S" required checked> Single</label>
												<label class="radio-inline "><input type="radio" id="result_dupl_duplicate_flag" name="result_dupl_flag" value="D" required > Duplicate</label>
											</div>
											<div class="form-group">
												<div class="col-md-6">
													<span  class="badge bg-info">Registered Quantity :  <span class="actualquantity"> 
														<!-- aded if condition for sample type ILC show save selected quantity done by shreeya on 16-11-2022 -->
														<?php if($sampleTypeCode == 9){
														 		echo $selectedqty[0]['qty'];
															}else{
																echo $getqty[0]['sample_total_qnt'];
															}
														?>  
													</span> - <?php echo $unit ?></span>
													
												</div>
												<label for="inputEmail3" class="col-sm-5 col-form-label">Actual Recieved Qty <span class="required-star">*</span></label>
												<div class="custom-file col-sm-6">
													<?php echo $this->Form->control('actual_received_qty', array('type'=>'text', 'id'=>'actual_received_qty','label'=>false,'class'=>'form-control allow_decimal','required'=>true,)); ?>
													<span id="error_actual_received_qty" class="error invalid-feedback"></span>
												</div>
											</div>
										</div>

										<div class="marginauto">
											<div class="card-body">
												<div class="row">
													<div class="col-sm-12">
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group row">
																	<label class="radio-inline marginauto"><input type="radio" id="acc_accepted_flag" name="acceptstatus_flag" value="A" required > Accept</label>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group row">
																	<label class="radio-inline marginauto"><input  type="radio" id="acc_rej_flg_rejected1"  name="acceptstatus_flag" value="R" required > Reject</label>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-12">
														<div id="abc">
															<div class="form-group row">
																<label for="inputEmail3" class="col-sm-3 col-form-label">Remark <span class="required-star">*</span></label>
																<?php echo $this->Form->control('acceptstatus_remark', array('type'=>'textarea', 'id'=>'acceptstatus_remark','label'=>false,'class'=>'form-control')); ?>
																<span id="error_rej_reason" class="error invalid-feedback"></span>
															</div>
														</div>
													</div>
													<div class="col-md-8 marginauto" id="homgen">
														<div class="card-header"><h3 class="card-title-new" >Sample Homogenization & Observations</h3></div>
														<div class="card-body boxformenus">
															<div class="row " id="homgen1"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<div class="col-md-12">
									<div class="col-md-1 float-left"><?php echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'ral', 'label'=>false,'class'=>'btn btn-success')); ?></div>
									<div class="col-md-1 float-right"><a href="../Dashboard/home" class="btn btn-danger">Cancel</a></div>
								</div>
							</div>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>
</div>		

	<?php
		unset($_SESSION['sample']);
		unset($_SESSION['stage_sample_code']);
	?>
	<input type="hidden" id="actualqty" value="<?php echo $getqty[0]['sample_total_qnt']; ?>">

	<?php echo $this->Html->Script('sampleAccept/sample_accept'); ?>
	<?php echo $this->Html->Script('sample_forward_form'); ?>
