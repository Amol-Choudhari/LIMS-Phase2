<?php ?>

	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><label class="badge badge-success">Commodities</label></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item"><?php echo $this->Html->link('Code Master', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
						<li class="breadcrumb-item active">Commodities</li>
					</ol>
				</div>
			</div>
		</div>

		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">
						<!-- added action code-master-home by shankhpal shende on 06/09/2022 -->
						<?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'code-master-home'),array('class'=>'add_btn btn btn-secondary float-right')); ?>
						<?php echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'new_commodity'),array('class'=>'add_btn btn btn-primary float-left')); ?>
					</div>
					<div class="col-md-12">
						<div class="card card-lims">
							<?php echo $this->Form->create(); ?>
								<div class="card-header"><h3 class="card-title-new">List of All Commodities</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="panel panel-primary filterable">
											<table id="pages_list_table" class="table table-bordered table-striped table-hover ">
												<thead class="tablehead">
													<tr>
														<th>SR.No</th>
														<th>Commodity</th>
														<th>Commodity (हिन्दी)</th>
														<th>Category</th>
														<!-- <th>Action</th> --> 
														<!-- This field commented by shankhpal shende on 05/09/02022 for user can not be update any commodity -->
													</tr>
												</thead>
												<tbody>
													<?php
													if(!empty($commodityArray)){
														$sr_no = 1;
														foreach($commodityArray as $each){ ?>

															<tr>
																<td><?php echo $sr_no; ?></td>
																<td><?php echo $com_name = $each['commodity_name']; ?></td>
																<td><?php echo $each['l_commodity_name'];  ?></td>
																<td><?php echo $each['category_name']; ?></td>
																<!-- <td class="text-center"> -->
																	<?php //echo $this->Html->link('', array('controller' => 'master', 'action'=>'fetch_commodity', $each['commodity_code']),array('class'=>'glyphicon glyphicon-edit ','title'=>'Edit')); ?> 
																	<?php //echo $this->Html->link('', array('controller' => 'master', 'action'=>'delete_commodity', $each['commodity_code']),array('class'=>'glyphicon glyphicon-trash','title'=>'Delete','id'=>'delete_record')); ?>
																     <!-- commented by shankhpal shende on 02/09/2022  -->
																<!-- </td> -->
															</tr>

													<?php $sr_no++; } } ?>
												</tbody>
											</table>
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
<?php echo $this->Html->script("master/saved_category"); ?>
