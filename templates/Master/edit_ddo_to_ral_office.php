<?php ?>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2"><div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'ddo_for_labs'),array('class'=>'add_btn btn btn-secondary')); ?></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
                        <li class="breadcrumb-item"><?php echo $this->Html->link('Reference Master', array('controller' => 'master', 'action'=>'reference_master_home')); ?></li>
                    </ol>
                </div>
            </div>
        </div>

        <section class="content form-middle">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $this->Form->create(null, array('id'=>'edit_ddo', 'name'=>'edit_ddo','class'=>'form-group')); ?>
                            <div class="card card-lims">
                                <div class="card-header"><h3 class="card-title-new">Add DDO for Laboratory</h3></div>
                                <div class="form-horizontal">
                                    <?php if(!empty($validate_err)){ ?><div class="badge badge-danger p-2"><?php echo $validate_err; ?></div><?php } ?>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Laboratories <span class="required-star">*</span></label>
                                                <?php echo $this->Form->control('ral_office_id', array('type'=>'select', 'id'=>'ral_office_id', 'label'=>false, 'options'=>$get_labs,'value'=>$lab_id,'class'=>'form-control','empty'=>'---Select---')); ?>
                                                <div class="error-msg" id="error_category_code"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>DDO List <span class="required-star">*</span></label>
                                                <?php echo $this->Form->control('ddo_id', array('type'=>'select', 'id'=>'ddo_id', 'label'=>false, 'options'=>$ddolist,'value'=>$ddo_id,'class'=>'form-control','empty'=>'---Select---')); ?>
                                                <div class="error-msg" id="error_commodity_name"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Posted Office : </label>
                                                <div id="posted_office"></div>
                                                <div class="error-msg" id="error_l_commodity_name"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Reason : </label>
                                                <?php echo $this->Form->control('reason_to_change', array('type'=>'textarea', 'id'=>'reason_to_change', 'label'=>false,'class'=>'form-control')); ?>
                                                <div class="error-msg" id="error_l_commodity_name"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer mt-4">
                                    <?php if (isset($_SESSION['commodity_code']) && isset($_SESSION['commodity_data'])) {
                                            echo $this->Form->submit('Update', array('name'=>'update', 'id'=>'update', 'label'=>false, 'class'=>'float-left btn btn-success'));
                                        } else {
                                            echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success'));
                                        }
                                    ?>
                                    <a href="saved_commodity" class="btn btn-danger float-right">Cancel</a>
                                </div>
                            </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php echo $this->Html->script("master/add_edit_ddo_to_ral_office"); ?>
