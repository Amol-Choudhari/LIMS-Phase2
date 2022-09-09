<?php 
	echo $this->Html->script('jquery_validationui');
	echo $this->Html->script('languages/jquery.validationEngine-en');
	echo $this->Html->script('jquery.validationEngine');

	echo $this->Html->css('validationEngine.jquery');
	echo $this->Html->css('master/create_formula');

	echo $this->Html->Script('bootstrap-datepicker.min');
	echo $this->Html->Script('bootstrap-datepicker.min');
	
?>


	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-3"><?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'code-master-home'),array('class'=>'add_btn btn btn-secondary float-left')); ?></div>
					<div class="col-sm-9">
						<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code-master-home')); ?></li>
						<li class="breadcrumb-item active">Formula Creationy</li>
					</ol>
				</div>
			</div>
		</div>

		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">
                 		<!-- <?php //echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'new_phy_appear', $phyAppear['action']),array('class'=>'add_btn btn btn-primary float-left')); ?> -->
					</div>
					
					<div class="col-md-12">
					   	<?php echo $this->Form->create(null, array('id'=>'save_formula', 'name'=>'create_formula','class'=>'mb-0')); ?>
							<div class="card card-lims mb-0">
								<div class="card-header"><h3 class="card-title-new">Create Formula</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="col-md-12">
											<div class="row" id="update_div">
												<input type="hidden" class="form-control" id="type" name="type" value="" >
												<input type="hidden" class="form-control" id="id" name="id" value="" >
												<input type="hidden" class="form-control" id="test_code" name="test_code" value="" >

												<label for="" class="col-md-2">Test <span class="required-star">*</span></label>
												<div class="col-md-3">
													<div class="form-group">		
														<div class="col-md-14">
															<?php echo $this->Form->control('test', array('type'=>'select','options'=>$test_names, 'empty'=>'---select---','label'=>false,'class'=>'form-control','required'=>true,'id'=>'select_test')); ?>	
														</div>
													</div>
												</div>	

												<label for="" class="col-md-2">Test Method <span class="required-star">*</span></label>			
												<div class="col-md-3">
													<div class="form-group">			
														<div class="col-md-14">
															<?php echo $this->Form->control('method_code', array('type'=>'select','options'=>$method, 'empty'=>'---select---' ,'id'=>'method_code','label'=>false,'class'=>'form-control','required'=>true)); ?>	
														</div>
													</div>
												</div>	
											</div>
										</div>
										<div class="col-md-12">
											<div class="row">
												<label class="control-label col-md-2">Test Type</label>
												<div class="col-md-3">
													<div class="form-group">				
														<div class="col-md-14">
															<input type="text" class="form-control validate[required]" id="test_type"  name="test_type"   placeholder="Test Type" value=""  disabled>
														</div>
													</div>
												</div>
													<label class="control-label col-md-2">Result Validation Range</label>
												<div class="col-md-3">
													<div class="form-group">				
														<div class="col-md-14">
															<input type="text" class="form-control validate[required,custom[onlyNumberSp]]" id="validation_range"  name="field_validation_range" pattern="[0-9]{1}" title="pattern should be numeric only" placeholder=" Number of digits after decimal eg:4 for  .9999" value=""  disabled>
														</div>
													</div>
												</div>	
											</div>
										</div>
										<div class="col-md-12">
											<div class="row">											
												<label  class="control-label col-md-2" for="sel1">From Date </label>
												<div class="col-md-3">
													<div class="form-group">				
														<div class="col-md-14 date">
															<div class="input-group input-append date" id="datePicker">
																<input type="text" class="form-control validate[required]" name="start_date" placeholder="dd/mm/yyyy" id="start_date" />
																<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
															</div>
														</div>
													</div>
												</div>
												<label class="control-label col-md-2">Unit</label>
												<div class="col-md-3">
													<div class="form-group">				
														<div class="col-md-14">
															<input type="text" class="form-control" id="unit"  name="unit"   placeholder="Unit" value=""  >
														</div>
													</div>
												</div>
											</div>
										</div>
					
										<div class="row">
											<div class="col-md-8 dnone" id="field_div">
												<label class="control-label" for="commodity_name">Fields</label>
												<ul id="fields" class="commul">
												</ul>
											</div>
											<div class="col-md-4">
												<div  id="operator_div" class="dnone">
													<label class="control-label " for="commodity_name">Operator</label>
													<ul id="operator">
														<table class="table1"  >
														<tbody class="oper1">
														<tr><td class="bgcolornumber calcuval">1</td><td class="bgcolornumber calcuval">2</td><td class="bgcolornumber calcuval">3</td><td class="bgcolornumber calcuval">4</td></tr>
														<tr><td class="bgcolornumber calcuval">5</td><td class="bgcolornumber calcuval">6</td><td class="bgcolornumber calcuval">7</td><td class="bgcolornumber calcuval">8</td></tr>
														<tr><td class="bgcolornumber calcuval">9</td><td class="bgcolornumber calcuval">0</td><td class="bgcolornumber calcuval">(</td><td class="bgcolornumber calcuval">)</td></tr>
														<tr><td class="bgcolornumber calcuval">-</td><td class="bgcolornumber calcuval">*</td><td class="bgcolornumber calcuval">+</td><td class="bgcolornumber calcuval">%</td></tr>
														<tr><td class="bgcolornumber calcuval">/</td><td class="bgcolornumber calcuval">.</td><td class="bgcolornumber getbackclk"><-</td><td class="bgcolornumber clearAlll">C</td></tr>
														</tbody> 
														</table>
													<p> </p>
													</ul>
												</div>
											</div>
										</div>
										
										<textarea id="formula1" class="dnone"  name="formula1" required></textarea>
										<label class="control-label col-md-3 dnone" id="formula_label" for="commodity_name">Formula</label>
										<div class="row dnone" id="formula_text"></div><br>
										<!---- <input type="number"required name="price" min="0" title="Currency" pattern="^\d+(?:\.\d{1,2})?$" onblur="this.parentNode.parentNode.style.backgroundColor = /^\d+(?:\.\d{1,2})?$/.test(this.value) ? 'inherit' : 'red'">--->
										<button id="finalize" class="btn btn-primary mb-2 ml350 dnone">Finalize</button>
										<button  class="btn btn-primary mb-2" id="testclick">Test Formula</button>
										
										<!-- //added by shankhpal shende on 06/09/2022 -->
										<div class="card-footer">
											<?php echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'savebtn', 'label'=>false, 'class'=>'float-left btn btn-success')); ?>
											<?php //echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success')); ?>
											<a href="saved-test-type" class="btn btn-danger float-right">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						<?php echo $this->Form->end(); ?>
					</div>

					<!-- /#page-content-wrapper -->
					<br>

					<table id="form1" class="dnone"></table>
					<table id="form2" class="dnone"></table>

					<div class="col-md-12">
						<div class="card-body">
							<div class="col-xs-12 col-sm-12 col-md-12">
								<div class="table-responsive" id="avb">
									<table class="table table-striped avbtable">
										<thead>
											<tr>
												<th>Sr No</th>
												<th>Test Name</th>
												<th>Method Name</th>
												<th>From Date</th>
												<th>Test Formula</th>
												<th>Finalize</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
								<input type="hidden" id="type" value="" class="hidden" />
							</div>
						</div>
					</div>

					<div id="myModal" class="modal" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" id="test_title">Test Formula</h4>
									<button type="button" class="close" id="close" data-dismiss="modal">&times;</button>
								</div>           
								<div class="modal-body">
									<input type="hidden" class="form-control" id="sample" name="chemist_code">
									<input type="hidden" class="form-control" id="test_v" name="test_code">
									<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
											<input type="hidden" name="test_perfm_date" id="test_perfm_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
									<div class="row">							
										<div class="form-group w-100" id="input_parameter_text"></div>
									</div>
								</div>
								<div class="modal-footer bt0">
									<button id="calculate" class="btn btn-primary" >Calculate</button>
									<button type="button" class="btn btn-default" id="close1" data-dismiss="modal">Close</button>
								</div>
								
							</div>
						</div>
					</div> 
				</div>
			</div>
		</section>
	</div>
	
<?php echo $this->Html->Script('master/create_formula'); ?>
