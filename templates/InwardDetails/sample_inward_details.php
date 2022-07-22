<?php $sample_type=null; if (!empty($_SESSION['sample'])){ $sample_type = $_SESSION['sample']; } ?>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<?php
							if (empty($sample_Details_data['sample_type_code'])) {
								echo $this->Form->create(null, array('class'=>'form-group')); ?>
								<?php echo $this->Form->submit('Fetch Last Details', array('name'=>'fetch_common_details', 'id'=>'fetch_common_details', 'label'=>false,'class'=>'form-control btn btn bg-cyan')); ?>
								<?php echo $this->Form->end();
							}
						?>
					</ol>
				</div>
			</div>
		</div>

		<section class="content">
			<div class="container-fluid inner_form">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-12"><?php echo $this->element('/progress_bars/sample_registration_progress'); ?></div>
							<?php echo $this->Form->create(null, array('id'=>'frm_sample_inward', 'name'=>'sampleForm','class'=>'form-group')); ?>
								<div class="card card-lims">
									<div class="card-header"><h3 class="card-title-new">Sample Inward Details</h3></div>
									<div class="form-horizontal">
										<div class="card-body">
											<?php if (!empty($validate_err)) { ?><div class="textAlignCenter text-danger"><?php echo $validate_err; ?></div><?php } ?>
										<div class="row">
											<div class="col-md-12 row">
												<?php if (isset($_SESSION['org_sample_code'])) { ?>
													<div class="col-md-3">
														<label>Sample Code <span class="required-star">*</span></label>
														<?php echo $this->Form->control('org_sample_code', array('type'=>'text', 'id'=>'org_sample_code', 'value'=>$_SESSION['org_sample_code'], 'label'=>false,'class'=>'form-control','required'=>true,)); ?>
														<span id="error_org_sample_code" class="error invalid-feedback"></span>
													</div>
												<?php } ?>

												<div class="col-md-3">
													<label>Sample Type <span class="required-star">*</span></label>
													<?php echo $this->Form->control('sample_type_code', array('type'=>'select', 'id'=>'sample_type_code', 'options'=>$Sample_Type,'empty'=>'--Select--', 'value'=>$sample_type, 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_sample_type_code" class="error invalid-feedback"></span>
												</div>

												<div class="col-md-3">
													<label>Drawing Sample Date <span class="required-star">*</span></label>
													<?php echo $this->Form->control('smpl_drwl_dt', array('type'=>'text', 'id'=>'smpl_drwl_dt', 'value'=>$sample_Details_data['smpl_drwl_dt'], 'placeholder'=>'dd/mm/yyyy', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_smpl_drwl_dt" class="error invalid-feedback"></span>
												</div>

												<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d'); ?>">
												<?php if (date('m') <= 6) { $financial_year = (date('Y')-1) . '-' . date('Y'); } else { $financial_year = date('Y') . '-' . (date('Y') + 1); } ?>
												<input type="hidden" name="fin_year" id="fin_year" placeholder="Enter a fin year" class="form-control" value="<?php echo $financial_year; ?>">
												<input type="hidden" name="loc_id" id="loc_id" placeholder="Enter a letter no" class="form-control" value="<?php if(isset($_SESSION['posted_ro_office'])){ echo $_SESSION['posted_ro_office'];}?>">

												<div class="col-md-3">
													<label>Drawing Location <span class="required-star">*</span></label>
													<?php echo $this->Form->control('drawal_loc', array('type'=>'select', 'id'=>'drawal_loc', 'options'=>$drawal_locations,'empty'=>'--Select--', 'value'=>$sample_Details_data['drawal_loc'], 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_drawal_loc" class="error invalid-feedback"></span>
												</div>
											</div>

											<div class="clearfix"></div>

											<div class="col-md-12 row mt-3">
												<div class="col-md-3">
													<label>Shop Name <span class="required-star">*</span></label>
													<?php echo $this->Form->control('shop_name', array('type'=>'text', 'id'=>'shop_name', 'value'=>$sample_Details_data['shop_name'], 'placeholder'=>'Enter Shop Name', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_shop_name" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label>Shop Address <span class="required-star">*</span></label>
													<?php echo $this->Form->control('shop_address', array('type'=>'text', 'id'=>'shop_address', 'value'=>$sample_Details_data['shop_address'], 'placeholder'=>'Enter Shop Address', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_shop_address" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label>Manufacturer Name <span class="required-star">*</span></label>
													<?php echo $this->Form->control('mnfctr_nm', array('type'=>'text', 'id'=>'mnfctr_nm', 'value'=>$sample_Details_data['mnfctr_nm'], 'placeholder'=>'Enter Manufacturer Name', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_mnfctr_nm" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label >Manufacturer Address <span class="required-star">*</span></label>
													<?php echo $this->Form->control('mnfctr_addr', array('type'=>'text', 'id'=>'mnfctr_addr', 'value'=>$sample_Details_data['mnfctr_addr'], 'placeholder'=>'Enter Manufacturer Address', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_mnfctr_addr" class="error invalid-feedback"></span>
												</div>
											</div>

											<div class="clearfix"></div>

											<div class="col-md-12 row mt-3">
												<div class="col-md-3">
													<label >Packer Name <span class="required-star">*</span></label>
													<?php echo $this->Form->control('pckr_nm', array('type'=>'text', 'id'=>'pckr_nm', 'value'=>$sample_Details_data['pckr_nm'], 'placeholder'=>'Enter Packer Name', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_pckr_nm" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label >Packer Address <span class="required-star">*</span></label>
													<?php echo $this->Form->control('pckr_addr', array('type'=>'text', 'id'=>'pckr_addr', 'value'=>$sample_Details_data['pckr_addr'], 'placeholder'=>'Enter Packer Address', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_pckr_addr" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label >Grade <span class="required-star">*</span></label>
													<?php echo $this->Form->control('grade', array('type'=>'text', 'id'=>'grade', 'value'=>$sample_Details_data['grade'], 'placeholder'=>'Enter the Grade', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_grade" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label >TBL <span class="required-star">*</span></label>
													<?php echo $this->Form->control('tbl', array('type'=>'text', 'id'=>'tbl', 'value'=>$sample_Details_data['tbl'], 'placeholder'=>'Enter TBL', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_tbl" class="error invalid-feedback"></span>
												</div>
											</div>	
												
											<div class="clearfix"></div>

											<div class="col-md-12 row mt-3">
												<div class="col-md-3">
													<label >Packet Size <span class="required-star">*</span></label>
													<?php echo $this->Form->control('pack_size', array('type'=>'text', 'id'=>'pack_size', 'value'=>$sample_Details_data['pack_size'], 'placeholder'=>'Enter Pack Size', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_pack_size" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label >Lot Number <span class="required-star">*</span></label>
													<?php echo $this->Form->control('lot_no', array('type'=>'text', 'id'=>'lot_no', 'value'=>$sample_Details_data['lot_no'], 'placeholder'=>'Enter Lot No.', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_lot_no" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label >Number of Packets <span class="required-star">*</span></label>
													<?php echo $this->Form->control('no_of_packets', array('type'=>'text', 'id'=>'no_of_packets', 'value'=>$sample_Details_data['no_of_packets'], 'placeholder'=>'Number Of Packets', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_no_of_packets" class="error invalid-feedback"></span>
												</div>
												<div class="col-md-3">
													<label>Remark <span class="required-star">*</span></label>
													<?php echo $this->Form->control('remark', array('type'=>'textarea', 'id'=>'remark', 'value'=>$sample_Details_data['remark'], 'placeholder'=>'Enter Remark', 'label'=>false,'class'=>'form-control','required'=>true)); ?>
													<span id="error_remark" class="error invalid-feedback"></span>
												</div>
											</div>

											<div class="col-md-12 row mb-3" id="elementRow">
												<?php  if (isset($sample_Details_data['replica_serial_no'])) {
														$repArray = explode(',',$sample_Details_data['replica_serial_no']);
														for ($r=0;$r<count($repArray);$r++) {
															$incrmt = $r+1; ?>
															<div class="col-md-3" id="replica_serial_no_div<?php echo $incrmt; ?>" name="repDiv">
																<label>Replica Sr. No. <?php echo $incrmt;?> <span class="required-star">*</span></label>
																<?php echo $this->Form->control('replica_serial_no'.$incrmt, array('type'=>'text', 'id'=>'replica_serial_no'.$incrmt, 'value'=>$repArray[$r], 'label'=>false,'class'=>'form-control','required'=>true)); ?>
																<span id="error_replica_serial_no" class="error invalid-feedback"></span>
															</div>
												<?php } }?>
											</div>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="col-md-12">
										<!--if confirm then hide btns
										Added the PV flag condtion if sample is commercial - 30-06-2022  						
										-->
										<?php if (!(trim($sample_Details_data['status_flag'])=='S' || trim($sample_Details_data['status_flag'])=='PV')) { ?>
											<div class="col-md-1 float-left">
												<?php //if record exist
													if ($SaveUpdatebtn=='update') {
														echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false,'class'=>'btn btn-success'));
													} else {
														echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false,'class'=>'btn btn-success'));
													}
												?>
											</div>
											<?php if ($confirmBtnStatus=='show') { ?>
											<div class="col-md-1 float-left">
												<?php echo $this->Form->submit('Confirm', array('name'=>'confirm', 'id'=>'confirm', 'label'=>false,'class'=>'btn btn-success')); ?>
											</div>
										<?php } } ?>

										<?php // for payment  
											if ($_SESSION['is_payment_applicable']=='yes') { ?>
											<div class="col-md-2 float-left"><a href="../payment/payment_details" class="btn btn-primary">Next Section</a></div>
										<?php } ?>

										
										<div class="col-md-1 float-right"><a href="../Dashboard/home" class="btn btn-danger">Cancel</a></div>
										<div class="col-md-2 float-right"><a href="../Inward/sample_inward" class="btn btn-primary">Back Section</a></div>	
									</div>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
	</div>
	
<?php echo $this->Html->script('inward/sample_details'); ?>
<input type="hidden" id="sample_status" value="<?php echo trim($sample_Details_data['status_flag']); ?>">
<?php if(empty($sample_type)){ $sample_type = ''; }else{ $sample_type = $_SESSION['sample']; }?>
<input type="hidden" id="sample_type" value="<?php echo $sample_type; ?>">
<?php echo $this->Html->script('sample_reg_form'); ?>
