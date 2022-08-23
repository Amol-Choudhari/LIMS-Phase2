<?php echo $this->Html->script('report'); ?>

<div class="container">

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">

            <?= $this->Form->create(null, [
                'id' => 'rpt_comm_sample',
                'url' => [
                    'controller' => 'report',
                    'action' => $title,
                    'autocomplete' => "off",
                ]
            ]); ?>

            <?php 
            switch ($title) {
                case "rejected-samples":
                case "samples-pending-for-dispatch":
                case "coding-decoding-section":
                case "samples-alloted-to-chemist-for-re-testing":
                case "re-tested-samples":
                case "re-tested-samples-submitted-by-chemist":
                case "no--of-check--private---research-samples-analyzed-by-rals":
                case "samples-alloted-analyzed-pending-report-ral-cal":
                case "time-taken-for-analysis-of-samples":
                
                
            ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name ?></legend><br>

                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                    </div>
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label> </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>

                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php

                    break;
                case  "sample-register":
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">

                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>

                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row" id="paralab1">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label> </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="checkbox">
                                            <label class="labelForm"><span class="compulsoryField">*</span> Sample Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('sample_type', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'options' => $samples,
                                            'empty' => '-----Select-----',
                                            'required' => true,
                                            'id' => 'sample_type'
                                        ]); ?>

                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="checkbox">
                                            <label class="labelForm"><span class="compulsoryField">*</span> Category</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('Category', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'Category',
                                            'empty' => '-----Select-----',
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="checkbox">
                                            <label class="labelForm"><span class="compulsoryField">*</span> Commodity</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('Commodity', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'Commodity',
                                            'empty' => '-----Select-----',
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?>

                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php

                    break;
                case "sample-registration-details":
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>

                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <select class="form-control" id="ral_lab_list" name="ral_lab" hidden>
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php

                    break;
                case  "sample-received-from-ro-so-ral-cal":
                ?>
                    <input type="hidden" class="form-control" name="labelName" id="labelName" value="Sample received from RO/SO/RAL/CAL">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>

                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span>Offices</label>

                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"></label>

                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Category</label>

                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('Category', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'Category',
                                            'empty' => '-----Select-----',
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">

                                        <label class="labelForm"><span class="compulsoryField">*</span> Commodity</label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('Commodity', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'Commodity',
                                            'empty' => '-----Select-----',
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?>

                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php

                    break;
                case  "samples-accepted-by-chemist-for-testing":
                case "samples-alloted-to-chemist-for-testing":
                case "sample-analyzed-by-chemist":
               
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <input type="hidden" class="form-control" name="user_flag" value="<?= $_SESSION['user_flag']; ?>">
                    <input type="hidden" class="form-control" name="ro_office" value="<?= $_SESSION['ro_office']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>
                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label> </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> User</label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('user', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'empty' => '-----Select-----',
                                            'required' => true,
                                            'id' => 'users'
                                        ]); ?>

                                    </div>
                                </div>
                            <?php } ?>

                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php
                    break;
                case  "samples-analyzed-count":
                case "performance-report-of-ral-cal":
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>
                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"></label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <?php if ($_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="<?php echo $_SESSION['user_flag'] . ',' . $_SESSION['ro_office']; ?>" selected><?php echo $_SESSION['user_flag'] . ',' . $_SESSION['ro_office']; ?></option>
                                            <?php } ?>
                                            <!-- To get list of office on change lab -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == 'HO') {  ?>
                                                <option value=""></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Sample Type</label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('sample_type', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'options' => $samples,
                                            'empty' => '-----Select-----',
                                            'required' => true,
                                            'id' => 'sample_type'
                                        ]); ?>

                                    </div>
                                </div>
                            <?php } ?>
                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php
                    break;
                case  "tested-samples":
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>
                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date</label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"></label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Sample Type</label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('sample_type', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'options' => $samples,
                                            'empty' => '-----Select-----',
                                            'required' => true,
                                            'id' => 'sample_type'
                                        ]); ?>

                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Commodity </label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('Commodity', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'Commodity',
                                            'empty' => '-----Select-----',
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php
                    break;
                case  "test-result-submitted-by-chemist":
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>
                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' ||  $_SESSION['role'] == 'DOL'  || $_SESSION['role'] == 'RO/SO OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"></label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' ||  $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Sample Code Available at Chemist </label>

                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('chemist_code', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'chemist_code',
                                            'empty' => '-----Select----- '
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'DOL' ||  $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO Officer') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Sample Code </label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('sample_code', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'sample_code',
                                            'empty' => '-----Select----- '
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php
                    break;

                case  "consolidated-statement-of-brought-forward-and-carried-forward-of-samples":
                case "chemist-–wise-sample-analysis":
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>
                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL') {  ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Month </label>
                                    </div>
                                    <div class="col-md-8">
                                        <?php
                                        $data = ['01' => 'Janaury', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                                        ?>
                                        <?= $this->Form->control('month', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'required' => true,
                                            'label' => false,
                                            'options' => $data,
                                            'empty' => '----Select---- ',
                                            'id' => 'month',
                                            'required' => true
                                        ]); ?>
                                    </div>
                                </div>
                            <?php } ?><br>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="lab" name="lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- Get User flag list for getLabName() in ReportController -->
                                            <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                                foreach ($user_flags as $user_flag) { ?>
                                                    <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"> </label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" id="ral_lab_list" name="ral_lab">
                                            <option hidden="hidden" value=''>-----Select-----</option>
                                            <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                            <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                                <option value="" selected></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?><br>
                            <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'DOL') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> Users </label>
                                    </div>
                                    <div class="col-md-8">
                                        <?= $this->Form->control('user', [
                                            'type' => 'select',
                                            'class' => 'form-control',
                                            'label' => false,
                                            'empty' => '-----Select-----',
                                            'required' => true,
                                            'id' => 'users'
                                        ]); ?>

                                    </div>
                                </div>
                            <?php } ?>
                        </fieldset><br>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="row parameters">
                        <div class="col-md-12 text-center">
                            <span>
                                <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                            </span>
                            <span>
                                <button class="btn btn-primary" name="close" id="close">Close</button>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                <?php
                    break;
                case  "test-report-for-commodity":
                ?>
                    <input type="hidden" class="form-control" name="label_name" id="label_name">
                    <input type="hidden" class="form-control" name="posted_ro_office" id="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
                    <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
                    <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
                    <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
                    <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
                    <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <legend class="heading"><?= $report_name; ?></legend><br>
                        <fieldset class="fsStyle">
                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('from_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'from_date',
                                                        'placeholder' => 'From(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                            <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <div class="form-group">
                                            <div class="input-group input-append date" id="datePicker1">
                                                <div class="col-md-12">
                                                    <?= $this->Form->control('to_date', [
                                                        'type' => 'text',
                                                        'class' => 'form-control glyphicon glyphicon-calendar',
                                                        'label' => false,
                                                        'required' => true,
                                                        'id' => 'to_date',
                                                        'placeholder' => 'To(dd/mm/yyyy)'
                                                    ]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                            <?php }    ?>

                            <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                                <div class="row" id="loading_con">
                                    <div class="col-md-12">
                                        <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                                    </div>
                                </div>

                                <div class="row"">
                                    <div class=" col-md-4">
                                    <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="lab" name="lab">
                                        <option hidden="hidden" value=''>-----Select-----</option>
                                        <!-- Get User flag list for getLabName() in ReportController -->
                                        <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                            foreach ($user_flags as $user_flag) { ?>
                                                <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"></label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="ral_lab_list" name="ral_lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Commodity </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('Commodity', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'id' => 'Commodity',
                                'empty' => '-----Select-----',
                            ]); ?>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'DOL' ||  $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO Officer') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Sample Code </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('sample_code', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'id' => 'sample_code',
                                'empty' => '-----Select----- '
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
                </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    <?php
                    break;
                case  "brought-forward-analysed-and-carried-forward-of-samples":
    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">
                <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL') {  ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Month </label>
                        </div>
                        <div class="col-md-8">
                            <?php
                            $data = ['01' => 'Janaury', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            ?>
                            <?= $this->Form->control('month', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'required' => true,
                                'label' => false,
                                'options' => $data,
                                'empty' => '----Select---- ',
                                'id' => 'month',
                                'required' => true
                            ]); ?>
                        </div>
                    </div>
                <?php } ?><br>

                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                    <div class="row" id="loading_con">
                        <div class="col-md-12">
                            <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="lab" name="lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- Get User flag list for getLabName() in ReportController -->
                                <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                    foreach ($user_flags as $user_flag) { ?>
                                        <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"> </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="ral_lab_list" name="ral_lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    <?php
                    break;
                case  "category-wise-received-sample":
    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="input-group input-append date" id="datePicker">
                                    <div class="col-md-12">
                                        <?= $this->Form->control('from_date', [
                                            'type' => 'text',
                                            'class' => 'form-control glyphicon glyphicon-calendar',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'from_date',
                                            'placeholder' => 'From(dd/mm/yyyy)'
                                        ]); ?>
                                    </div>
                                </div>
                                <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="input-group input-append date" id="datePicker1">
                                    <div class="col-md-12">
                                        <?= $this->Form->control('to_date', [
                                            'type' => 'text',
                                            'class' => 'form-control glyphicon glyphicon-calendar',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'to_date',
                                            'placeholder' => 'To(dd/mm/yyyy)'
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><br>
                <?php }    ?>

                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                    <div class="row" id="loading_con">
                        <div class="col-md-12">
                            <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="lab" name="lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- Get User flag list for getLabName() in ReportController -->
                                <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                    foreach ($user_flags as $user_flag) { ?>
                                        <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"> </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="ral_lab_list" name="ral_lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Sample Type </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('sample_type', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'options' => $samples,
                                'empty' => '-----Select-----',
                                'required' => true,
                                'id' => 'sample_type'
                            ]); ?>

                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Category </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('Category', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'id' => 'Category',
                                'empty' => '-----Select-----',
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    <?php
                    break;
                case  "commodity-wise-consolidated-report-of-lab":
    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">
                <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL') {  ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Month </label>
                        </div>
                        <div class="col-md-8">
                            <?php
                            $data = ['01' => 'Janaury', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            ?>
                            <?= $this->Form->control('month', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'required' => true,
                                'label' => false,
                                'options' => $data,
                                'empty' => '----Select---- ',
                                'id' => 'month',
                                'required' => true
                            ]); ?>
                        </div>
                    </div>
                <?php } ?><br>

                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                    <div class="row" id="loading_con">
                        <div class="col-md-12">
                            <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="lab" name="lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- Get User flag list for getLabName() in ReportController -->
                                <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                    foreach ($user_flags as $user_flag) { ?>
                                        <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"> </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="ral_lab_list" name="ral_lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Sample Type </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('sample_type', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'options' => $samples,
                                'empty' => '-----Select-----',
                                'required' => true,
                                'id' => 'sample_type'
                            ]); ?>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Commodity </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('Commodity', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'options' => $commodity,
                                'id' => 'Commodity',
                                'empty' => '-----Select-----',
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    <?php
                    break;
                case  "commodity-wise-check---challenged-samples-analysed":
    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="input-group input-append date" id="datePicker">
                                    <div class="col-md-12">
                                        <?= $this->Form->control('from_date', [
                                            'type' => 'text',
                                            'class' => 'form-control glyphicon glyphicon-calendar',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'from_date',
                                            'placeholder' => 'From(dd/mm/yyyy)'
                                        ]); ?>
                                    </div>
                                </div>
                                <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="input-group input-append date" id="datePicker1">
                                    <div class="col-md-12">
                                        <?= $this->Form->control('to_date', [
                                            'type' => 'text',
                                            'class' => 'form-control glyphicon glyphicon-calendar',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'to_date',
                                            'placeholder' => 'To(dd/mm/yyyy)'
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><br>
                <?php }    ?>

                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                    <div class="row" id="loading_con">
                        <div class="col-md-12">
                            <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="lab" name="lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- Get User flag list for getLabName() in ReportController -->
                                <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                    foreach ($user_flags as $user_flag) { ?>
                                        <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"> </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="ral_lab_list" name="ral_lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Commodity </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('Commodity', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'id' => 'Commodity',
                                'empty' => '-----Select-----',
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    <?php
                    break;

                case  "commodity-wise-research---private-samples-analysed":
    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">

                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                    <div class="row" id="loading_con">
                        <div class="col-md-12">
                            <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="lab" name="lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- Get User flag list for getLabName() in ReportController -->
                                <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                    foreach ($user_flags as $user_flag) { ?>
                                        <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select class="form-control" id="ral_lab_list" name="ral_lab" hidden>
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Commodity </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('Commodity', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'options' => $commodity,
                                'id' => 'Commodity',
                                'empty' => '-----Select-----',
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    <?php
                    break;
                case  "all-offices-statistics-counts":
    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> From Date </label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="input-group input-append date" id="datePicker">
                                    <div class="col-md-12">
                                        <?= $this->Form->control('from_date', [
                                            'type' => 'text',
                                            'class' => 'form-control glyphicon glyphicon-calendar',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'from_date',
                                            'placeholder' => 'From(dd/mm/yyyy)'
                                        ]); ?>
                                    </div>
                                </div>
                                <label class="control-label" for="sel1"> (Select Range of 'Sample Registration' Date)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> To Date </label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="input-group input-append date" id="datePicker1">
                                    <div class="col-md-12">
                                        <?= $this->Form->control('to_date', [
                                            'type' => 'text',
                                            'class' => 'form-control glyphicon glyphicon-calendar',
                                            'label' => false,
                                            'required' => true,
                                            'id' => 'to_date',
                                            'placeholder' => 'To(dd/mm/yyyy)'
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><br>
                <?php }    ?>

                <div class="row">
                    <div class="col-md-4">
                        <label class="labelForm"><span class="compulsoryField">*</span> Office Type </label>
                    </div>
                    <div class="col-md-8">
                        <?php $data = ['RAL' => 'RAL', 'RO' => 'RO']; ?>
                        <?= $this->Form->control('office_type', [
                            'type' => 'select',
                            'class' => 'form-control',
                            'label' => false,
                            'required' => true,
                            'options' => $data,
                            'empty' => '-----Select-----',
                        ]); ?>
                    </div>
                </div><br>

                <?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Commodity </label>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->control('Commodity', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'label' => false,
                                'required' => true,
                                'options' => $commodity,
                                'empty' => '-----Select-----',
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
    <?php echo $this->Form->end();
                    break;
                case "details-of-sample-analyzed-by-chemist":
                case "monthly-report-of-carry-forward-and-brought-forward":
                case "information-of-annexure-e-along-with-mpr-division-wise":
				case "no--of-remnent-research-other-ilc-samples-analyzed--commodity-wise":
				case "performa-regarding-monthly-report-of-ral-annexure-a":
                case "details-of-samples-analyzed-by-rals-annexure-b":
                case "bifercation-of-samples-analyzed-by-ral":
                case "monthly-status-of-analyzed-of-check-samples-and-pending-samples-of-ral-annexure-e":
                case "commodity-wise-details-of-samples-analyzed-by-ral-annexure-e":
                case "statement-of-check-samples-brought-forward--carry-forward-annexure-i":
                case "time-taken-report":
                case "sample-allotment-sheet-of-coding-section-to-the-i-c-analytical-section-of-cal--nagpur":
                case "sample-allotment-sheet-of-i-c-analytical-section-issued-to-the-chemist-for-analysis":
                case "perticulars-of-samples-received-and-analyzed-by-ral-annexure-d":
                case "consolidated-report-analysed-by-chemist":
    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">
                <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL') {  ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Date </label>
                        </div>
                        <div class="col-md-4">
                            <?php
                            $data = ['01' => 'Janaury', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            ?>
                            <?= $this->Form->control('month', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'required' => true,
                                'label' => false,
                                'options' => $data,
                                'empty' => '--Select Month-- ',
                                'id' => 'month',
                            ]); ?>
                        </div>
                        <div class="col-md-4">
                            <?= $this->Form->year('year', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'required' => true,
                                'label' => false,
                                'empty' => '--Select Year-- ',
                                'id' => 'year',
                                'min' => 2011,
                                'max' => date('Y')
                            ]); ?>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                    <div class="row" id="loading_con">
                        <div class="col-md-12">
                            <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="lab" name="lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- Get User flag list for getLabName() in ReportController -->
                                <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                    foreach ($user_flags as $user_flag) { ?>
                                        <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"> </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="ral_lab_list" name="ral_lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    <?php

      break;
      case "common-report":

    ?>
        <input type="hidden" class="form-control" name="label_name" id="label_name">
        <input type="hidden" class="form-control" name="posted_ro_office" value="<?= $_SESSION['posted_ro_office']; ?>">
        <input type="hidden" class="form-control" name="fname" value="<?= $_SESSION['f_name']; ?>">
        <input type="hidden" class="form-control" name="lname" value="<?= $_SESSION['l_name']; ?>">
        <input type="hidden" class="form-control" name="email" value="<?= $_SESSION['username']; ?>">
        <input type="hidden" class="form-control" name="role" value="<?= $_SESSION['role']; ?>">
        <input type="hidden" class="form-control" name="user_code" value="<?= $_SESSION['user_code']; ?>">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <legend class="heading"><?= $report_name; ?></legend><br>
            <fieldset class="fsStyle">
                <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL') {  ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Date </label>
                        </div>
                        <div class="col-md-4">
                            <?php
                            $data = ['01' => 'Janaury', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            ?>
                            <?= $this->Form->control('month', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'required' => true,
                                'label' => false,
                                'options' => $data,
                                'empty' => '--Select Month-- ',
                                'id' => 'month',
                            ]); ?>
                        </div>
                        <div class="col-md-4">
                            <?= $this->Form->year('year', [
                                'type' => 'select',
                                'class' => 'form-control',
                                'required' => true,
                                'label' => false,
                                'empty' => '--Select Year-- ',
                                'id' => 'year',
                                'min' => 2011,
                                'max' => date('Y')
                            ]); ?>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

                    <div class="row" id="loading_con">
                        <div class="col-md-12">
                            <?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Offices </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="lab" name="lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- Get User flag list for getLabName() in ReportController -->
                                <?php if ($_SESSION['user_flag'] == 'CAL' || $_SESSION['user_flag'] == 'HO' || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {
                                    foreach ($user_flags as $user_flag) { ?>
                                        <option value="<?php echo $user_flag['user_flag']; ?>" selected><?php echo $user_flag['user_flag']; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"> </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="ral_lab_list" name="ral_lab">
                                <option hidden="hidden" value=''>-----Select-----</option>
                                <!-- To get list of office on change lab from getRallabByLab() in DmiUsers Model -->
                                <?php if ($_SESSION['user_flag'] == "CAL" || $_SESSION['user_flag'] == "HO" || $_SESSION['user_flag'] == "RAL" || $_SESSION['user_flag'] == 'RO' || $_SESSION['user_flag'] == 'SO') {  ?>
                                    <option value="" selected></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php } ?><br>
                <?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="labelForm"><span class="compulsoryField">*</span> Sample Type </label>
                        </div>
                        <div class="col-md-8">
                            <select name = "sample_type[]" id="sample_type_dropdown" multiple="multiple" class="form-control">
                                <?php foreach ($samples_type as $samples_type) { ?>
                                    <option value="<?php echo $samples_type['sample_type_code']; ?>"><?php echo $samples_type['sample_type_desc']; ?></option>
                                <?php } ?>

                            </select>
                        </div>
                    </div>
                <?php } ?>
            </fieldset><br>
        </div>
        <div class="col-md-1"></div>
        <div class="row parameters">
            <div class="col-md-12 text-center">
                <span>
                    <button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
                </span>
                <span>
                    <button class="btn btn-primary" type="reset" name="cancel" id="cancel">Cancel</button>
                </span>
                <span>
                    <button class="btn btn-primary" name="close" id="close">Close</button>
                </span>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
        <?php

                    break;
                default: {
        ?>
            <h4 class='noReport'>Report Not Found...</h4>
<?php
                    }
            }
?>


    </div>
    <div class="col-md-2"></div>
</div>

</div>
