<?php 
	echo $this->Html->script('jquery.dataTables.min');
	echo $this->Html->css('jquery.dataTables.min');
	echo $this->Html->css('master/commodity_grad');
?>


<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"><?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'reference-master-home'),array('class'=>'add_btn btn btn-secondary float-left')); ?></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Reference Files', array('controller' => 'master', 'action'=>'reference-master-home')); ?></li>
					<li class="breadcrumb-item active">Granding Standards</li>
				</ol>
			</div>
		</div>
	</div>	
		
	<section class="content form-middle">
		<div class="container-fluid">
			<div class="col-md-12">  
				<?php echo $this->Form->create(null, array('id'=>'save_formula', 'name'=>'create_formula','class'=>'mb-0')); ?>

				<input type="hidden" name="user_code" id="user_code" value="<?php echo $_SESSION['user_code']; ?>">					
				

				<div class="card card-lims mb-0">
					<div class="card-header"><h3 class="card-title-new">Granding Standards
						<a class="btn btn-danger float-right" id="viewrecords">view Records</a>
					</h3>

					</div>
					<div class="form-horizontal">
						<div class="card-body">
							<div class="col-md-12">
								<div class="row pt-2 pb-2">
									<div class="col-md-2 text-right">Category</div>
									<div class="col-md-2">
										<?php echo $this->Form->control('category_code', array('type'=>'select','options'=>$commodity_category, 'empty'=>'---select---' ,'id'=>'category_code','label'=>false,'class'=>'form-control','required'=>true)); ?>
									</div>

									<div class="col-md-2 text-right">Commodity</div>
									<div class="col-md-2">
										<?php echo $this->Form->control('commodity_code', array('type'=>'select','empty'=>'---select---' ,'id'=>'commodity_code','label'=>false,'class'=>'form-control','required'=>true)); ?>
									</div>
									<div class="col-md-2 text-right">Tests</div>
									<div class="col-md-2">
										<?php echo $this->Form->control('test_code', array('type'=>'select','empty'=>'---select---' ,'id'=>'test_code','label'=>false,'class'=>'form-control','required'=>true)); ?>
									</div>
								</div>	
							</div>

							<div class="col-md-12">
								<div class="row pt-2 pb-2">
									<div class="col-md-2 text-right">Test methods</div>
									<div class="col-md-2">
										<?php echo $this->Form->control('method_code', array('type'=>'select','empty'=>'---select---' ,'id'=>'method_code','label'=>false,'class'=>'form-control','required'=>true,'disabled'=>true)); ?>
									</div>

									<div class="col-md-2 text-right">Standard</div>
									<div class="col-md-2">
										<?php echo $this->Form->control('grd_standrd', array('type'=>'select','options'=>$grades_strd,'empty'=>'---select---' ,'id'=>'grd_standrd','label'=>false,'class'=>'form-control','required'=>true,'disabled'=>true)); ?>
									</div>
									<div class="col-md-2 text-right rangeDiv">Min/Max</div>
									<div class="col-md-2 rangeDiv">
										<?php echo $this->Form->control('min_max', array('type'=>'select','empty'=>'---select---' ,'options'=>$minmax,'id'=>'min_max','label'=>false,'class'=>'form-control')); ?>
									</div>
									
									<div class="col-md-2 pt-3 text-right grid_val_min_div">Grade Value</div>
									<div class="col-md-2 pt-3 grid_val_min_div" id="grade_value1">	
									</div>

									<div class="col-md-2 pt-3 text-right pl-0 grid_val_max_div">Max Grade Value</div>
									<div class="col-md-2 pt-3 grid_val_max_div" id="grid_val_max_subdiv">
									</div>																		
								</div>	
							</div>	

							<table class="table table-striped mt-4">
								<thead>
									<tr class="h-tab">
										<th class="w-10">Select</th>
										<th class="w-45">Grade</th>
										<th class="w-45">Order</th>
									</tr>
								</thead>
							</table>				
							<div class="table-responsive gord">

								<table class="table table-striped ">
									<tbody>
											<?php $i=1;
												foreach ($grades as $row1):
											?>
										<tr>
											<td class="w-10"><input type='checkbox'  id="<?php echo $row1['grade_desc']; ?>" name='grade_code[]' value="<?php echo $row1['grade_code']; ?>" /></td>
											<td class="w-45"><?php echo $row1['grade_desc']; ?></td>
											<td class="w-45">	
													<label class="radio-inline "><input class=" validate[required] radio" type="radio" id="Higher1_<?php echo $row1['grade_code']; ?>" name="grade_order[<?php echo $row1['grade_code']; ?>]" value="1">Higher</label>
													<label class="radio-inline "><input class="validate[required] radio" type="radio"  id="Higher2_<?php echo $row1['grade_code']; ?>" name="grade_order[<?php echo $row1['grade_code']; ?>]" value="2" >Middle</label>
													<label class="radio-inline "><input class="validate[required] radio" type="radio"  id="Higher3_<?php echo $row1['grade_code']; ?>" name="grade_order[<?php echo $row1['grade_code']; ?>]" value="3" >Lower</label>	
											</td>
										</tr>
											<?php $i++;
												endforeach; ?>	
									</tbody>		
								</table>
							</div>
						</div>	
					</div>	
					<div class="card-footer">

						<?php echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'savebtn', 'label'=>false, 'class'=>'float-left btn btn-success')); ?>

						<?php //echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success')); ?>
						
						<a href="reference-master-home" class="btn btn-danger float-right">Cancel</a>

					</div>
				</div>	
				<?php echo $this->Form->end(); ?> 
			</div>	
		</div>	
	</section>			
</div>	

<div id="myModal" class="modal" role="dialog" data-target=".bs-example-modal-lg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close float-right" id="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title float-left" id="test_title">Allocated Commodity Grades</h4>
			</div>	
			<div class="modal-body">
				<div class="table-responsive" id="avb">
					<table class="table table-striped" id="check_div">
						<thead>
                            <tr>
                                <th>Sr No</th>
								<th>Category Name</th>
								<th>Commodity Name</th>
								<th>Tests</th>
								<th>Grade </th>
                                <th>Grade Min Value</th>
                                <th>Grade Max Value</th>
								<th>Grade Value</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
					</table>	
				</div>	
			</div>	
		</div>	
	</div>	
</div>	
<?php echo $this->Html->script("master/commodity_grade"); ?>
