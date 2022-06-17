
<?php if(null!==($_SESSION['username'])){ ?>
    <nav class="navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
            <li class="nav-item d-none d-sm-inline-block"><a href="#" class="nav-link">Last Login: <?php echo $this->element('user_last_login'); ?> [IP: <?php echo $_SESSION["ip_address"];?>]</a></li>
        </ul>
    <!-- SEARCH FORM -->
        <?php echo $this->element('user_dashboard_elements/common_sample_search_element'); ?>
		
		 <div class="input-group input-group-sm form-inline ml-3 search-bx">
		<?php 
			
			if($this->getRequest()->getParam('controller')=='Dashboard' &&
				$this->getRequest()->getParam('action')=='home'){

					echo $this->Form->create(null,array('id'=>'pending_work_btn'));
					echo $this->Form->Submit('Overall Pending Work Status',array('name'=>'get_pending_work','class'=>'btn btn-warning form-control','label'=>false));
					echo $this->Form->End(); 

				}
		?>
		</div>

        <!--RIGHT NAVIGATION BAR MENU-->
            <ul class="nav navbar-nav navbar-right">
                <li id="user" class="dropdown"><a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Profile<span class="caret"></span></a>
                    <ul class="dropdown-menu" style="min-width:165px;">
                        <li><li><a class="nav-link" href="<?php echo $this->getRequest()->getAttribute('webroot');?>users/user_profile"><i class="fas fa-address-book"></i> <span class="badge">View Profile</span></a></li>
                            <li><a class="nav-link" href="<?php echo $this->getRequest()->getAttribute('webroot');?>users/change_password"><i class="fas fa-key"></i> <span class="badge">Change Password</span></a></li>
                            <li><a class="nav-link" href="<?php echo $this->getRequest()->getAttribute('webroot');?>users/user_logs"><i class="fas fa-clock"></i> <span class="badge">Log History</span></a></li>
                            <li><a class="nav-link" target="_blank" href="/writereaddata/DMI/manuals/User Manual LIMS.pdf"><i class="fas fa-book-open"></i> <span class="badge">User Manual</span></a></li>

                        <?php if($current_user_division['division'] == 'BOTH') { ?>
                            <li class="dropdown"><a class="nav-link" href="../../DMI/users/common_user_redirect_login/<?php echo $current_user_division['id']; ?>"><i class="fas fa-arrow-circle-right"></i> <span class="badge">Go To DMI</span></a></li>
                        <?php } ?>

                        <?php if($current_user_division['role'] == 'Admin' || $current_user_division['role'] == 'Head Office' || $current_user_division['role'] == 'RAL/CAL OIC') { ?>						
                            <li><a class="nav-link" href="<?php echo $this->getRequest()->getAttribute('webroot'); ?>users/all_users_logs"><i class="fas fa-book-reader"></i> <span class="badge">All Users Logs</span></a></li>
                        <?php } ?>

                        <?php if($current_user_division['role'] == 'Admin' || $current_user_division['role'] == 'Head Office'){ ?>
                            <li><a class="nav-link" href="<?php echo $this->getRequest()->getAttribute('webroot'); ?>users/admin_logs"><i class="fas fa-arrow-circle-right"></i> <span class="badge">Admin Logs</span></a></li>
                        <?php } ?>

                        <li><a class="nav-link" href="<?php echo $this->getRequest()->getAttribute('webroot'); ?>users/user_action_history"><i class="fas fa-arrow-circle-right"></i> <span class="badge">Action Logs</span></a></li>
                        <li><a class="nav-link" href="<?php echo $this->getRequest()->getAttribute('webroot');?>users/logout"><i class="fas fa-power-off"></i>  <span class="badge">Logout</span></a></li>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <?php } ?>