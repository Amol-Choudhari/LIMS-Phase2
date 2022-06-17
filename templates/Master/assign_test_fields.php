<?php echo $this->Html->css('master/homo_value'); ?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"></div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Test', array('controller' => 'master', 'action'=>'test-fields')); ?></li>
					<li class="breadcrumb-item active">Assign Test Fields</li>
				</ol>
			</div>
		</div>
	</div>

	<section class="content form-middle">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->Form->create(null, array('id'=>'assigntestform', 'name'=>'testForm','class'=>'form-group')); ?>

						<div class="card card-lims">
							<div class="card-header"><h3 class="card-title-new">Assign Test Fields</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="row">
											<div class="col-md-5"></div>											
											<div class="col-md-3">
												<select class='form-control mb-3' id="test_code">
													<?php foreach ($test_names as $key => $value) { ?>
														<option value="<?php echo $value['test_code']; ?>"> <?php echo $value['test_name']; ?></option>														
													<?php } ?>
												</select>
											</div>											
										</div>
										<div class="row">											
											<div class="col-md-12 col-xs-10 col-sm-10  col-md-offset-0">
					                            <div class="table-responsive">
					                                <table  class="table table-striped" id="input_parameter_text" >
					                                    <thead >
				                                    		<tr>
				                                    			<th class='text-center'>Select</th>
				                                    			<th class='text-left'>Field Name</th>
				                                    			<th class='text-left'>Field Name(Hindi)</th>
				                                    			<th class='text-center'>Field validation</th>
				                                    			<th class='text-center'>Unit</th>
				                                    			<th class='text-center'>Dependent</th>
				                                    		</tr>
				                                    	</thead>
				                                    	<tbody>
				                                    		<tr>	
				                                    			<td colspan="4" class="text-right pr-3"><b>Eg:for 99.99(2,2)</b></td>
				                                    		</tr>
				                                    		<?php foreach ($test_fields as $key => $value) {  ?>

				                                    			<tr  id="tr_<?php echo $value['field_code']; ?>">
				                                    				<td class='text-left' >
				                                    					<input type='checkbox'  id='<?php echo $value["field_code"]; ?>' name='checkboxArray[]' value='<?php echo $value["field_code"]; ?>' />
				                                    				</td>
				                                    				<td >
				                                    					<div class='col-md-12 '>
				                                    						<label  class=' control-label text-left col-md-12' for='sel1'><?php echo $value['field_name']; ?></label>
				                                    					</div>
				                                    				</td>
				                                    				<td class='text-left'>
				                                    					<div class='col-md-12'>
				                                    						<label  class='control-label col-md-12' for='sel1'><?php echo $value['l_field_name']; ?></label>
				                                    					</div>
				                                    				</td>
				                                    				<td>
				                                    					<div class='col-md-12'>
				                                    						<input class='form-control' type='text' pattern='[0-9]{1},[0-9]{1}' title='Range should be in format eg: (2,4)' maxlength='3'id='text_<?php echo $value["field_code"]; ?>' />
				                                    					</div>
				                                    				</td>
				                                    				<td>
				                                    					<div class='col-md-12'>
				                                    						<input class='form-control' type='text' id='unit_<?php echo $value["field_code"]; ?>' />
				                                    					</div>
				                                    				</td>
				                                    				<td class='text-center' >
				                                    					<input type='checkbox' id='dep_<?php echo $value["field_code"]; ?>' name='checkboxArray1[]' />
				                                    				</td>
				                                    			</tr>
				                                    		<?php } ?>
				                                    	</tbody>
					                                </table>
					                            </div>
					                        </div>											
									</div>
								</div>

								<div class="card-footer mt-4">

									<?php
										if(!empty($test_name)){

											echo $this->Form->submit('Update', array('name'=>'save', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));

										} else {

											echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));

										}

										if(array_key_exists("assigntestcodestatus",$_SESSION) && $_SESSION['assigntestcodestatus'] == 'edit')
										{
											echo $this->Form->submit('Finalize', array('name'=>'finalize', 'id'=>'finalize', 'label'=>false, 'class'=>'btn btn-success ml-2'));
										}

									?>
									
									<a href="test-fields" class="btn btn-danger float-right">Cancel</a>
								</div>
							</div>
						</div>						
					<?php echo $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</section>					
</div>

<?php echo $this->Html->script('master/assign_test_fields'); ?>
