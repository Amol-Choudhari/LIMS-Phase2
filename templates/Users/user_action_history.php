<?php $username = $_SESSION['username']; ?>

   <div class="content-wrapper">
      <div class="content-header">
         <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6"><label class="badge badge-info">Action Logs</label></div>
            <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
                  <li class="breadcrumb-item active">Action Log History</li>
               </ol>
            </div>
            </div>
         </div>
      </div>

      <section class="content form-middle">
         <div class="container-fluid">
            <div class="row">
               <div class="col-md-12">
                  <?php echo $this->Form->create(); ?>
                     <div class="card card-Lightblue">
                        <div class="card-header"><h4 class="card-title-new">Given Below is Action Point history</h4></div>
                        <div class="form-horizontal">
                           <div class="card-body">
                              <table id ="user_logs_table" class="table table-striped table-hover table-bordered">
                                 <thead class="tablehead">
                                    <tr>
                                       <th>Sr.No</th>
                                       <th>User Id</th>
                                       <th>IP Address</th>
                                       <th>Date and Time</th>
                                       <th>Action Performed</th>
                                       <th>Status</th>
                                    </tr>
                                 </thead>
                                 <tbody>

                                    <?php if (!empty($get_user_actions)) {

                                       $i=0;
                                       $sr = 1;
                                       foreach ($get_user_actions as $get_user_actions) { ?>
                                          <tr>
                                             <td><?php echo $sr; ?></td>
                                             <td><?php echo base64_decode($get_user_actions['user_id']);?></td>
                                             <td><?php echo $get_user_actions['ipaddress'];?></td>
                                             <td><?php echo $get_user_actions['created']; ?></td>
                                             <td><?php echo ucwords($get_user_actions['action_perform']);?></td>
                                             <td><?php
                                                $remark = ucwords($get_user_actions['status']);
                                                if($remark == 'Success')
                                                   $badge = "success";
                                                elseif ($remark == 'Failed')
                                                   $badge = "danger";
                                                else
                                                   $badge = "info";
                                                echo "<span class='badge badge-".$badge."'>".$remark."</span>";
                                                ?>
                                          </td>
                                          </tr>
                                    <?php	$i=$i+1; $sr++; } }?>

                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  <?php echo $this->Form->end(); ?>
               </div>
            </div>
         </div>
      </section>
   </div>
<?php echo $this->Html->script("users/user_profile"); ?>
