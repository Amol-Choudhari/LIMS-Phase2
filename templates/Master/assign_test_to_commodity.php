<?php ?>

<style>

.disable-btn {
	color: gray;
    cursor: not-allowed;
}

</style>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"> <?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'code-master-home'),array('class'=>'add_btn btn btn-secondary')); ?>
				<!--<h5 class="m-0 text-dark">Assign Test to Commodity</h5>--></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code-master-home')); ?></li>
					<li class="breadcrumb-item active">Assign Test to Commodity</li>
				</ol>
			</div>
		</div>
	</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">
                 <!-- <?php //echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'new_phy_appear', $phyAppear['action']),array('style'=>'float:left;','class'=>'add_btn btn btn-primary')); ?> -->
					</div>
					<div class="col-md-12">
						<div class="card card-lims">
							<?php echo $this->Form->create(); ?>
								<div class="card-header"><h3 class="card-title-new">List of Assign Test to Commodity</h3></div>
                           <div class="form-horizontal">
                              <div class="card-body">
                                 <div class="col-md-12">
                                    <div class="row">
                                       <div class="col-md-4">
                                          <div class="form-group">
                                             <label for="" class="col-md-6">Commodity <span class="required-star">*</span></label>
                                                <div class="custom-file col-sm-12">
                                                   <?php echo $this->Form->control('commodity_code', array('type'=>'select','options'=>$commodity, 'empty'=>'---select---' ,'label'=>false,'class'=>'form-control','required'=>true,'id'=>'commodity_code')); ?>	
                                                </div>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="form-group">
                                          <label for="" class="col-md-4">Test <span class="required-star">*</span></label>
                                             <div class="custom-file col-sm-12">
                                                <?php echo $this->Form->control('test_code', array('type'=>'select', 'empty'=>'---select---' ,'label'=>false,'class'=>'form-control','required'=>true,'id'=>'testlist')); ?>	
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-2">
                                       	<label for="" class="col-md-4"></label>
                                          <div class="form-group">                
                                             <div class="custom-file col-sm-8 mt-4">
                                                <?php echo $this->Form->control('Save', array('type'=>'submit', 'label'=>false,'class'=>'btn btn-primary','id'=>'testlist')); ?>	
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>

									<div class="form-horizontal">
										<div class="card-body">
											<div class="panel panel-primary filterable">
												<table id="pages_list_table" class="table table-bordered table-striped table-hover">
													<thead class="tablehead">
														<tr>
															<th>SR.No</th>
															<th>Test Name</th>
															<th>Test Name (hindi)</th>
															<th>Action</th>
														</tr>
													</thead>	
													<tbody>
                                       
													</tbody>
												</table>
											</div>
										</div>
									</div>
						</div>
						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
</div>


<?php //echo $this->Html->script("master/saved_category"); ?>
<?php echo $this->Html->script("master/assign_test_to_commodity"); ?>