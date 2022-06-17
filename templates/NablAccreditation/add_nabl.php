<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"><?php echo $this->Html->link('Back', array('controller' => 'nablAccreditation', 'action'=>'nabldetail-list'),array('class'=>'add_btn btn btn-secondary float-left')); ?></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item active">NABL Scope accreditation</li>
				</ol>
			</div>
		</div>
	</div>	
	<section class="content form-middle">
		<div class="container-fluid">
			<?php echo $this->Form->create(null, array('id'=>'save_report', 'class'=>'mb-0')); ?>
				<div class="card card-lims mb-0">
					<div class="card-header"><h3 class="card-title-new">NABL Scope accreditation</h3></div>
					<div class="form-horizontal">
						<div class="card-body">
							<div class="col-md-12">
								<div class="row pt-2 pb-4">
									<div class="form-group row">
										<div class="col-md-2 text-right">For RAL/CAL <span class="required-star">*</span></div>
										<div class="col-md-4">
											<?php echo $this->Form->control('office', array('type'=>'select','options'=>$office,'value'=>$get_nabl_details['lab_id'], 'id'=>'office', 'label'=>false,'empty'=>'--Select--','class'=>'form-control')); ?>
											<span id="error_office" class="error invalid-feedback"></span>    
										</div> 
										<div class="col-md-2 text-right">Certificate No. <span class="required-star">*</span></div>
										<div class="col-md-4">
											<?php echo $this->Form->control('nabl_certificate', array('type'=>'text','id'=>'nabl_certificate', 'value'=>$get_nabl_details['accreditation_cert_no'],'label'=>false,'class'=>'form-control','placeholder'=>'NABL Certificate No.')); ?>
											<span id="error_nabl_certificate" class="error invalid-feedback"></span>
										</div>
										<div class="col-md-2 text-right"> Category <span class="required-star">*</span></div>
										<div class="col-md-4">
											<?php echo $this->Form->control('category_code', array('type'=>'select','options'=>$commodity_category,'value'=>$get_nabl_details['category_id'], 'id'=>'category_code', 'label'=>false,'empty'=>'--Select--','class'=>'form-control')); ?>
											<span id="error_category_code" class="error invalid-feedback"></span>
										</div>
										<div class="col-md-2 text-right">Date of Validity <span class="required-star">*</span></div>
										<div class="col-md-4">
											<?php echo $this->Form->control('date_validity', array('type'=>'text', 'id'=>'date_validity','value'=>$get_nabl_details['valid_upto_date'], 'label'=>false,'class'=>'form-control','placeholder'=>"Date of Validity")); ?>
											<span id="error_date_validity" class="error invalid-feedback"></span>
										</div>
										<div class="col-md-2 text-right"> Commodity <span class="required-star">*</span></div>
										<div class="col-md-4">
											<?php echo $this->Form->control('commodity_code', array('type'=>'select', 'id'=>'commodity_code','options'=>$commodity_list,'value'=>$get_nabl_details['commodity'], 'label'=>false,'empty'=>'--Select--','class'=>'form-control')); ?>
											<span id="error_commodity_code" class="error invalid-feedback"></span>
										</div>

										
										<div class="col-md-2 text-right"> Test Parameters <span class="required-star">*</span></div>
										<div class="col-md-4">
											<?php echo $this->Form->control('test_parameters', array('type'=>'select', 'id'=>'test_parameters','options'=>$test_list,'value'=>$get_nabl_details['tests'], 'label'=>false,'multiple'=>'multiple','class'=>'form-control test_parameters')); ?>
											<span id="error_test_parameters" class="error invalid-feedback"></span>
										</div>

										  

									</div>	

									<!-- ===================== -->

									

									
									<!-- ===================== -->
								</div>	
							</div>	
						</div>	
					</div>	
					<div class="card-footer">

						<?php echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'savebtn', 'label'=>false, 'class'=>'float-left btn btn-success')); ?>
						
						<a href="nabldetail-list" class="btn btn-danger float-right">Cancel</a>

					</div>
				</div>	
			<?php echo $this->Form->end(); ?>
		</div>	
	</section>	
</div>	
<?php echo $this->Html->Script('nablAccreditation/add_nabl'); ?>

