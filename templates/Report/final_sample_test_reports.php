<?php echo $this->Html->script('report'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action' => 'home'), array('class' => 'add_btn btn btn-secondary')); ?>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action' => 'home')); ?></li>
                    <li class="breadcrumb-item active">Finalized Sample Test Reports</li>
                </ol>
            </div>
        </div>
    </div>
    <section class="content form-middle">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-lims">
                        <div class="card-header">
                            <h3 class="card-title-new">Finalized Sample Test Reports</h3>
                        </div>
                        <div class="form-horizontal">
                            <div class="card-body" id="avb">
                                <table class="table table-striped table-hover table-bordered" id="commodityReport">
                                    <thead class="tableHead">
                                        <tr>
                                            <th>Sr No</th>
                                            <th>Finalized Sample Code</th>
                                            <th>Finalized Date</th>
                                            <th>Category</th>
                                            <th>Commodity</th>
                                            <th>Sample Type</th>
                                            <th>Report Pdf</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tableBody">
                                        <?php

                                        if (isset($final_reports)) {
                                            $i = 0;
                                            foreach ($final_reports as $res2) :
                                                $i++; ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $res2[0]['stage_smpl_cd']; ?></td>
                                                    <td><?php echo $res2[0]['tran_date']; ?></td>
                                                    <td><?php echo $res2[0]['category_name']; ?></td>
                                                    <td><?php echo $res2[0]['commodity_name']; ?></td>
                                                    <td><?php echo $res2[0]['sample_type_desc'] ?></td>
                                                    <td><a class="btn btn-info" href="<?php echo $this->request->getAttribute('webroot'); ?>FinalGrading/sample_test_report_code/<?php echo trim($res2[0]['stage_smpl_cd']) . '/' . $res2[0]['commodity_code']; ?>" target='_blank'>View</a></td>
                                                </tr>
                                        <?php endforeach;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php echo $this->Html->script('report/final_sample_test_report'); ?>