<?php ?>
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><label class="badge badge-info">Masters</label></div>
          		<div class="col-sm-6">
            		<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home'));?></li>
              			<li class="breadcrumb-item active">Code Files Home</li>
            		</ol>
				</div>
			</div>
		</div>
	</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-Lightblue">
							<div class="card-header"><h3 class="card-title-new">Code Files</h3></div>
								<?php echo $this->Form->create(); ?>							
									<div class="form-horizontal">
										<div class="card-body">
											<div class="row">
												<div class="col-sm-3">
													<div class="form-group">
														<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved_category">Category</a>
													</div>
												</div>
													<div class="col-sm-3">
														<div class="form-group">
															<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved_commodity">Commodity</a>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Sample_Obs">Add Homegenization</a>
														 </div>
													</div>
														<div class="col-md-3">
															<div class="form-group">
																<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-homo-value">Add Value to Homogenization</a>
															</div>
														</div>
													</div>
												</div>
											</div>
						          	<div class="form-horizontal">
											<div class="card-body">
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-assign-homo">Assign Homo to Commodity</a>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Test_type">Test Type</a>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-test-type">Test</a>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Master_Test_Field">Test Fields</a>
														</div>
													</div>
												</div>
											 </div>
										 </div>
											<div class="form-horizontal">
												<div class="card-body">
													<div class="row">
														<div class="col-md-3">
															<div class="form-group">
																<a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>master/test-fields/">Assign Test Fields in Test</a>
															</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>master/assign-test-to-commodity">Assign Test to Commodity</a>
																</div>	
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>master/saved-phy-appear/Test_Method">Test Method</a>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>master/create-formula">Create Formula For Test</a>
																</div>
															</div>
														</div>
													</div>
												</div>					
											<?php echo $this->Form->end(); ?>
										</div>
									</div>
								</div>
							</div>
						</section>
				</div>

