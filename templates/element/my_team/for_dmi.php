<?php echo $this->Html->css('line_tracking'); ?>
<?php $status = ''; ?>
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
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 offset-3">
                                                <div class="row">
                                                    <span class="col-md-4"><label>Office Name : <span id="officeName" class="badge badge-primary"><?php echo $office_name; ?></span></label></span>
                                                    <span class="col-md-4"><label>Office Type : <span id="officeType" class="badge badge-info"><?php echo $office_type; ?></span></label></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <div class="p-4">
                                    <p class="badge">Officers Involved In the <?php echo $office_name; ?> Office</p>
                                    <table class="table table-bordered track_tbl">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>User Role</th>
                                            <th>User Email</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="active">
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>DDO</td>
											<td><?php if (!empty($getPao)) { ?>
                                                    <?php  if (count($get_io_list) > 1) { ?>
                                                        <p class="badge"><u>This is the List of Users with role of <b>DDO</b> for this office</u></p></br>
                                                   <?php } ?>
                                                   <?php foreach ($getPao as $each_record) { ?>
                                                        <?php echo $each_record['f_name']." ".$each_record['l_name'].  " ( ".base64_decode($each_record['email'])." )"; ?>
                                                        </br>
                                                <?php } } ?>
                                            </td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>SO In-charge</td>
                                            <td>
                                                <?php if(empty($soInchargeEmail)) { ?>
                                                    <?php echo "N/A"; ?>
                                                <?php  } else { ?>
                                                    <?php echo $soInchargeName." ( ".base64_decode($soInchargeEmail)." )" ; ?>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>RO In-charge</td>
                                            <td><?php echo $roInchargeName." ( ".base64_decode($roInchargeEmail)." )" ; ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>Scrutinizer</td>
                                            <td><?php if (!empty($get_scrutinizers_list)) { ?>
                                                      <?php  if (count($get_scrutinizers_list) > 1) { ?>
                                                            <p class="badge"><u>This is the List of Users with role of <b>Scrutinizer</b> for this office</u></p></br>
                                                       <?php } ?>
                                                   <?php foreach ($get_scrutinizers_list as $each_record) { ?>
                                                        <?php echo $each_record['scrutinizers_name'].  " ( ".base64_decode($each_record['scrutinizers_email'])." )"; ?>
                                                        </br>
                                                <?php } } ?>

                                            </td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>Inspection Officer</td>
                                            <td><?php if (!empty($get_io_list)) { ?>
                                                      <?php  if (count($get_io_list) > 1) { ?>
                                                            <p class="badge"><u>This is the List of Users with role of <b>Inspection</b> for this office</u></p></br>
                                                       <?php } ?>
                                                   <?php foreach ($get_io_list as $each_record) { ?>
                                                        <?php echo $each_record['io_name'].  " ( ".base64_decode($each_record['io_email'])." )"; ?>
                                                        </br>
                                                <?php } } ?>

                                            </td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>Dy. AMA</td>
                                            <td><?php echo $dy_ama_name." ( ".base64_decode($dy_ama)." )" ; ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>Jt. AMA</td>
                                            <td><?php echo $jt_ama_name." ( ".base64_decode($jt_ama)." )" ; ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>AMA</td>
                                            <td><?php echo $ama_name." ( ".base64_decode($ama)." )" ; ?></td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="track_dot">
                                                <span class="track_line"></span>
                                            </td>
                                            <td>Scrutinizer (HO)</td>
                                            <td><p class="badge"><u>This is the List of Users with role of <b>Scrutinizer (HO)</u></b></p></br>
                                             <?php
                                                if (!empty($ho_scrutinizers_list)) {

                                                    foreach ($ho_scrutinizers_list as $each_record) { ?>
                                                        <?php echo $each_record['ho_scrutinizers_name'].  " ( ".base64_decode($each_record['ho_scrutinizers_email'])." )"; ?>
                                                        </br>
                                                <?php } } ?>

                                            </td>
                                            <td><?php echo $status; ?></td>
                                        </tr>
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

    <?php echo $this->Html->script('othermodules/my_team'); ?>
