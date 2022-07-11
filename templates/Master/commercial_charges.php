<?php ?> 
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><label class="badge badge-success">Commercial Charges</label></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
                        <li class="breadcrumb-item"><?php echo $this->Html->link('Reference', array('controller' => 'master', 'action'=>'reference_master_home')); ?></li>
                        <li class="breadcrumb-item active">All Charges</li>
                    </ol>
                </div>
            </div>
        </div>

        <section class="content form-middle">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <?php echo $this->Html->link('Add New', array('controller' => 'master', 'action'=>'add_commercial_charges'),array('class'=>'add_btn btn btn-primary float-left')); ?>
                        <?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'reference_master_home'),array('class'=>'add_btn btn btn-secondary float-right')); ?>
                    </div>

                    <div class="col-md-12">
                        <div class="card card-lims">
                            <?php echo $this->Form->create(); ?>
                                <div class="card-header"><h3 class="card-title-new">List of All Charges</h3></div>
                                <div class="form-horizontal">
                                    <div class="card-body">
                                        <div class="panel panel-primary filterable">
                                            <table id="category_list" class="table table-bordered table-hover table-striped">
                                                <thead class="tablehead">
                                                    <tr>
                                                        <th>SR.No</th>
                                                        <th>Category</th>
                                                        <th>Commodity</th>
                                                        <th>Charges</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        if(!empty($getAllCharges)){
                                                            $i=0;
                                                            $sr_no =1;
                                                            foreach($getAllCharges as $each){ 

                                                             if (!empty($commodity[$i]) && !empty($category[$i])) { ?>
                                                                <tr>
                                                                    <td><?php echo $sr_no; ?></td>
                                                                    <td><?php echo $category[$i]; ?></td>
                                                                    <td><?php echo $commodity[$i]; ?></td>
                                                                    <td><?php echo $each['charges'] ?></td>
                                                                    <td>
                                                                        <?php echo $this->Html->link('', array('controller' => 'master', 'action'=>'fetch_edit_id_for_charge', $each['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> |
                                                                        <?php echo $this->Html->link('', array('controller' => 'master', 'action'=>'delete_id_for_charge', $each['id']),array('class'=>'glyphicon glyphicon-trash','title'=>'Delete', 'id'=>'delete_record')); ?>
                                                                    </td>
                                                                </tr>
                                                        <?php $sr_no++; } $i=$i+1;	 } } ?>
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
