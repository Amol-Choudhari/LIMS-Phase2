<?php ?>
<?php echo $this->Html->css("master/commodity"); ?>

<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"><label class="badge badge-success"><?php echo $phyAppear['title']; ?></label></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Reference Master', array('controller' => 'master', 'action'=>'reference_master_home')); ?></li>
					<li class="breadcrumb-item active"><?php echo $phyAppear['title']; ?></li>
				</ol>
			</div>
		</div>
	</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">
						<?php echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'new_phy_appear', $phyAppear['action']),array('class'=>'add_btn btn btn-primary float-left')); ?>
						<?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'reference_master_home'),array('class'=>'add_btn btn btn-secondary float-right')); ?>
					</div>
					<div class="col-md-12">
						<div class="card card-lims">
							<?php echo $this->Form->create(); ?>
								<div class="card-header"><h3 class="card-title-new">List of All <?php echo $phyAppear['title']; ?></h3></div>
									<div class="form-horizontal">
										<div class="card-body">
											<div class="panel panel-primary filterable">
												<table id="pages_list_table" class="table table-bordered table-striped table-hover">
													<thead class="tablehead">
														<tr>
															<th>SR.No</th>
															<th><?php echo $phyAppear['name_1']; ?></th>
															<th><?php echo $phyAppear['name_2']; ?></th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
													<?php
													if(!empty($phyAppearArray)){
														$sr_no = 1;
														foreach($phyAppearArray as $each){ ?>

															<tr>
																<td><?php echo $sr_no; ?></td>
																<td><?php echo $each[$phyAppear['textbox_1']]; ?></td>
																<td><?php echo $each[$phyAppear['textbox_2']]  ?></td>
																<td>
																	<?php
																		if(isset($each['iseditable'])){

																			echo "<span class='glyphicon glyphicon-edit disable-btn'></span>";

																		} else  {

																			echo $this->Html->link('', array('controller' => 'master', 'action'=>'fetch_phy_appear', $phyAppear['phy_appear_code'], $phyAppear['table_name'], $each[$phyAppear['phy_appear_code']]),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit'));

																		} ?>
																			|
																	<?php echo $this->Html->link('', array('controller' => 'master', 'action'=>'delete_phy_appear', $phyAppear['phy_appear_code'], $phyAppear['table_name'], $phyAppear['action'], $each[$phyAppear['phy_appear_code']]),array('class'=>'glyphicon glyphicon-trash','title'=>'Delete','id'=>'delete_record')); ?>
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
