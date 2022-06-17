
<?php echo $this->Html->script('report'); ?>
<?= $this->Form->create(null); ?>
<?php echo $this->Form->input('user_role_id', array('type' => 'hidden', 'id' => 'user_role_id', 'value' => $_SESSION['role'], 'label' => false,));?>

<div class="container">
    <legend class="heading">Title</legend>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div id="accordion">
                <div class="card">
                    <?php
                    foreach ($recordlabels as $recordlabel) {
                        if ($recordlabel != '') { ?>
                            <div class="card-header  bg-light">
                                <a class="card-link reportHeading" data-toggle="collapse" href="#collapse<?= $recordlabel['label_code']; ?>">
                                    <?= $recordlabel['label_desc']; ?>
                                </a>
                            </div>
                            <div id="collapse<?= $recordlabel['label_code']; ?>" class="collapse" data-parent="#accordion">
                                <div class="card-body"> 
                                    <?php
                                    foreach ($records as $record) {
                                        if ($recordlabel['label_code'] == $record['label_code']) {
                                            $labelR = strtolower(str_replace([' ', '/', '(', ')','&',',','.'], '-', $record['report_desc']));
                                            $report_desc = substr($labelR, -1, 1);
                                            if ($report_desc == '-') {
                                                $report_str = substr($labelR, 0, strlen($labelR) - 1);
                                    ?>
                                                <a href="<?= $this->Url->build(['action' => 'form-filter']); ?>" class="reportTitle labelName"><?= $record['report_desc']; ?></a> <br>
                                            <?php
                                            } else { ?>
                                                <a href="<?= $this->Url->build(['action' => 'form-filter']); ?>" class="reportTitle labelName"><?= $record['report_desc']; ?></a> <br>
                                    <?php

                                            }
                                        }
                                    } ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <h5 class="text-center">No Report for particular Role</h5>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<?= $this->Form->end(); ?>