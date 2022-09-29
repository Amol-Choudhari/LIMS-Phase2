<?php ?>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><label class="badge badge-info">Masters</label></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home'));?></li>
                        <li class="breadcrumb-item active">Reference</li>
                    </ol>
                </div>
            </div>
        </div>

        <section class="content form-middle">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-Lightblue">
                            <div class="card-header"><h3 class="card-title-new">Reference</h3></div>
                            <div class="form-horizontal">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Phy_Apperance">Physical Appearance</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Sample_Condition">Sample Condition</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Parcel_Condition">Package Condition</a>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Unit_Desc">Unit Description</a>
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
                                                <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Sample_Type">Type Of Sample</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Grade_Desc">Grade</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Fin_Year">Financial Year</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <a class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/saved-phy-appear/Reject_reason">Reject Reason</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-horizontal">
                                <div class="card-body">
                                    <div class="row">
                                       <!-- <div class="col-md-3">
                                            <div class="form-group">
                                                <a  class="btn btn-block btn-outline-secondary" href="<?php // echo $this->getRequest()->getAttribute('webroot');?>Master/commodityGrade">Sample Wise Inward Fields</a>
                                            </div>
                                        </div>-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/commodityGrade">Grading Standards</a>
                                            </div>
                                        </div>
                                        <!--<div class="col-md-3">
                                            <div class="form-group">
                                                <a  class="btn btn-block btn-outline-secondary" href="<?php //echo $this->getRequest()->getAttribute('webroot');?>Master/ddo_for_labs">PAO/DDO</a>
                                            </div>
                                        </div>-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <a  class="btn btn-block btn-outline-secondary" href="<?php echo $this->getRequest()->getAttribute('webroot');?>Master/commercial_charges">Commercial Charges</a>
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
