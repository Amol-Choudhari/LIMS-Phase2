<?php //echo $this->Html->css('line_tracking'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><label class="badge badge-primary">My Team</label></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home'));?></a></li>
                        <li class="breadcrumb-item active">My Team</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content form-middle ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10">
                    <?php echo $this->Form->create(null, array('id'=>'myTeam')); ?>
                        <div class="card card-cyan">
                            <div class="card-header"><h3 class="card-title-new">My Team</h3></div>
                            <div class="form-horizontal marginBottom33">
                            <div class="col-md-6 offset-3">
                                <div class="row mt-2">
                                    <span class="col-md-6"><label>Office Name : <span id="officeName" class="badge badge-primary"><?php echo $office_name; ?></span></label></span>
                                    <span class="col-md-6"><label>Office Type : <span id="officeType" class="badge badge-info"><?php echo $_SESSION['user_flag']; ?></span></label></span>
                                </div>
                            </div>

                            <div class="p-4">
                                <p class="badge">Officers Involved</p>
                                <table class="table table-bordered track_tbl">
                                    <thead>
                                        <tr>
                                            <th>User Role</th>
                                            <th>User Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Inward Officer</td>
                                            <td>
                                                <?php
                                                if (!empty($getInward)) {
                                                    foreach ($getInward as $each_record) { ?>
                                                    <?php echo $each_record['inward_name'].  " ( ".base64_decode($each_record['inward_email'])." )"; ?>
                                                    </br>
                                            <?php } } else { echo "N/A"; } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Jr. Chemist</td>
                                            <td>
                                                <p class="badge"><u>This is the List of Users with role of <b>Jr. Chemist</b> for this office</u></p></br>
                                                <?php
                                                    if (!empty($getJrChemist)) {
                                                        foreach ($getJrChemist as $each_record) { ?>
                                                        <?php echo $each_record['jr_chemist_name'].  " ( ".base64_decode($each_record['jr_chemist_email'])." )"; ?>
                                                        </br>
                                                <?php } } else { echo "N/A"; } ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sr. Chemist</td>
                                            <td>
                                                <p class="badge"><u>This is the List of Users with role of <b>Sr. Chemist</b> for this office</u></p></br>
                                                <?php
                                                    if (!empty($getSrChemist)) {
                                                        foreach ($getSrChemist as $each_record) { ?>
                                                        <?php echo $each_record['sr_chemist_name'].  " ( ".base64_decode($each_record['sr_chemist_email'])." )"; ?>
                                                        </br>
                                                <?php } } else { echo "N/A"; } ?>
                                            </td>
                                        </tr>
                                        <?php if($postedRoOfficeId == '55'){ ?>
                                            <tr>
                                                <td>Lab Incharge</td>
                                                <td><?php if (!empty($getLabIncharge)) { ?>
                                                         <?php echo $getLabIncharge['lab_inchrage_name'].  " ( ".base64_decode($getLabIncharge['lab_inchrage_email'])." )"; ?>
                                                    <?php } else { echo "N/A"; } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        
                                        <tr>
                                            <td>RAL/CAL OIC</td>
                                            <td><?php if (!empty($inchargeName)) { ?>
                                                    <?php echo $inchargeName.  " ( ".base64_decode($officeIncharge)." )"; ?>
                                                <?php } else { echo "N/A"; } ?>
                                            </td>
                                        </tr>
                                        
                                        <?php if($postedRoOfficeId == '55'){ ?>
                                        <tr>
                                            <td>DOL</td>
                                            <td><?php if (!empty($getDol)) { ?>
                                                    <?php echo $getDol['dol_name'].  " ( ".base64_decode($getDol['dol_email'])." )"; ?>
                                                <?php } else { echo "N/A"; } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </section>
</div>
