<?php ?>
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"></div>
          	<div class="col-sm-6">
            	<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home'));?></li>
              	<li class="breadcrumb-item active">Statistics Reports</li>
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
							<div class="card-header"><h3 class="card-title-new">Statistics Reports</h3></div>
								<?php echo $this->Form->create(); ?>							
									<div class="form-horizontal">
										<div class="card-body">
											<div class="row">
												<div class="col-sm-3">
													<div class="form-group">
														<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved_category">Sample Inward/Dispatch</a>
															</div>
														</div>
														<div class="col-sm-3">
															<div class="form-group">
																<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved_commodity">Sample Allotment (RAL/CAL)</a>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Sample_Obs">Sample Test</a>
														 </div>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-homo-value">RAL/CAL Monthly Performance Report</a>
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
															   <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-assign-homo">Annexure Report</a>
																</div>
															 </div>
                                        </div>
                                 </div>
                            </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
</div>
					

<script type="text/javascript">
		
	$(document).ready(function(){




				$('#search_btn').click(function(){
					
						
						
						var search_file=$("#search_file").val();
						
	
						if(start_date==""){
							
								alert("Sorry...All search Fields are empty");
								
							return false;
							}
							
							
				});
				
				
				
	});
			
			
</script>