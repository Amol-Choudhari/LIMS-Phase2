<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><label class="badge badge-info">Reports</label></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">All Reports</li>
				    </ol>
			    </div>
		    </div>
	    </div>

		<section class="content form-middle mt-3">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">
						<?php echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'add_reports'),array('class'=>'add_btn btn btn-primary float-left')); ?>
					
						<?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'code-master-home'),array('class'=>'add_btn btn btn-secondary float-right')); ?>
					</div>
					<div class="col-md-12">
						<div class="card card-lims">
							<?php echo $this->Form->create(); ?>
								<div class="card-header"><h3 class="card-title-new">List of All Reports</h3></div>
									<div class="form-horizontal">
										<div class="card-body">
                                            <div class="panel panel-primary filterable">		
                                                <table id="pages_list_table" class="table table-bordered table-hover table-striped">
                                                    <thead class="tablehead">
                                                        <tr>
                                                            <th>SR.No</th>
                                                            <th>Reports</th>                 		
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>	
                                                    <tbody>
                                                        <?php
                                                        
                                                         if (!empty($label)) {

                                                            $sr_no = 1;		
                                                            
                                                            foreach ($label as $each) { ?>
                                                                    
                                                                <tr>
                                                                    <td><?php echo $sr_no; ?></td>
                                                                    <td><?php echo $each['report_desc'] ?></td>
                                                                    
                                                                    </td>
                                                                    <td> 
                                                                    <a id="<?php echo $each['report_code']; ?>" class="glyphicon glyphicon glyphicon-remove dercord text-info cursor-pointer btn" title="delete"></a>	
                                                                        
                                                                    <?php //echo $this->Html->link('', array('controller' => 'master', 'action'=>'delete_category', $each['category_code']),array('class'=>'glyphicon glyphicon-trash','title'=>'Delete', 'onclick'=>"return (confirm('Are you sure you want to delete this record ?'))")); ?>
                                                                    </td>
                                                                </tr>

                                                            <?php $sr_no++; } } ?>					
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

<?php echo $this->Html->script("master/saved_category"); ?>
<?php echo $this->Html->script("master/all_reports"); ?>