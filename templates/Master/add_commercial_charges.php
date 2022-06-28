<?php ?>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2"><div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'master', 'action'=>'commercial_charges'),array('class'=>'add_btn btn btn-secondary')); ?></div>
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
                    <div class="col-md-10">
                        <?php echo $this->Form->create(null, array('id'=>'add_charges', 'name'=>'add_charges','class'=>'form-group')); ?>
                            <div class="card card-lims">
                                <div class="card-header"><h3 class="card-title-new">Add Commercial Charges</h3></div>
                                <div class="form-horizontal">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Type <span class="required-star">*</span></label>
                                                <?php echo $this->Form->control('charges_type', array('type'=>'text', 'id'=>'charges_type', 'label'=>false,'class'=>'form-control')); ?>
                                                <div class="error invalid-feedback" id="error_charges_type"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Charge <span class="required-star">*</span></label>
                                                <?php echo $this->Form->control('charges', array('type'=>'text', 'id'=>'charges', 'label'=>false,'class'=>'form-control')); ?>
                                                <div class="error invalid-feedback" id="error_charges"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer mt-4">
                                    <?php echo $this->Form->submit('Save', array('name'=>'save', 'id'=>'save', 'label'=>false, 'class'=>'float-left btn btn-success')); ?> 
                                    <a href="commercial_charges" class="btn btn-danger float-right">Cancel</a>
                                </div>
                            </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php echo $this->Html->script('master/commercial_charges'); ?>
