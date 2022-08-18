<?php ?>
  <aside class="main-sidebar sidebar-dark-primary elevation-4 ovfl">

  	<!-- Brand Logo / Profile Photo -->
	<a href="index3.html" class="brand-link">
		<?php echo $this->Html->image('AdminLTELogo.png', array('alt'=>'AQCMS Logo', 'class'=>'brand-image img-circle elevation-3 op8')); ?>
		<span class="brand-text">LIMS</span>
	</a>
	<div class="user-panel mt-3 pb-3 mb-1 d-flex">
		<?php
		//	$rootdir = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
			$profile_pic = $_SESSION['profile_pic']; //added on 06-05-2021 for profile pic
			echo $this->Html->image('../../'.$profile_pic, array("alt"=>"User Image", "width"=>"200", "class"=>"img-circle")); ?>

		<div class="info">
			<a href="#" class="d-block"><?php echo $_SESSION["f_name"];?> <?php echo $_SESSION["l_name"];?></a>
			<span class="right badge badge-light"><?php echo base64_decode($_SESSION["username"]);/*for email encoding*/ ?></span>
		</div>
	</div>
	<!-- Brand Logo / Profile Photo ends -->

	<!-- Sidebar Menu-->
	<nav class="mt-2">
		<ul class="nav nav-pills nav-sidebar flex-column p-0" data-widget="treeview" role="menu" data-accordion="false">
			<li class="nav-item">
				<a href="<?php echo $this->request->getAttribute("webroot");?>dashboard/home" class="nav-link">
					<i class="nav-icon fas fa-tachometer-alt"></i>
					<p class="nav-icon-p">Dashboard</p>
				</a>
			</li>


			
			<li class="nav-item">
				<a href="<?php echo $this->request->getAttribute('webroot');?>dashboard/my_team" class="nav-link">
				<i class="fas fa-users nav-icon"></i>
					<p class="nav-icon-p">My Team</p>
				</a>
			</li>
		
			<!--Sidebar Menu for Sample Inward/Sample Inward Details-->

				<?php if (!empty($current_user_roles)) { ?>

					<?php if ($current_user_roles['sample_inward'] == 'yes') { ?>

						<li class="nav-item has-treeview">
							<a href="#" class="nav-link">
								<i class="nav-icon far fa-user"></i>
								<p>Sample Inward<i class="nav-icon fas fa-angle-right right"></i></p>
							</a>

							<ul class="nav nav-treeview dnone">
								<li class="nav-item has-treeview">
									<li class="nav-item">
										<a href="<?php echo $this->request->getAttribute('webroot');?>Inward/fresh_registration" class="bg-cyan nav-link">
											<i class="nav-icon glyphicon glyphicon-plus"></i>
											<p class="nav-icon-p">New Sample</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="<?php echo $this->request->getAttribute('webroot');?>Inward/saved_samples" class="bg-cyan nav-link">
											<i class="nav-icon glyphicon glyphicon-remove-circle"></i>
											<p class="nav-icon-p">Unconfirmed List</p>
										</a>
									</li>
									<li class="nav-item">
										<a href="<?php echo $this->request->getAttribute('webroot');?>Inward/confirmed_samples" class="bg-cyan nav-link">
											<i class="nav-icon glyphicon glyphicon-ok-circle"></i>
											<p class="nav-icon-p">Confirmed List</p>
										</a>
									</li>
								</li>
							</ul>
						</li>


					<!--Sidebar Menu for Sample Accept Details-->

					<?php if ($_SESSION['role']=="Inward Officer") { ?>

						<li class="nav-item has-treeview">
							<a href="#" class="nav-link">
								<i class="nav-icon fas fa-vial"></i>
								<p>Accept Sample<i class="nav-icon fas fa-angle-right right"></i></p>
							</a>
							<ul class="nav nav-treeview dnone">
								<li class="nav-item">
									<a href="<?php echo $this->request->getAttribute('webroot');?>SampleAccept/available_to_accept_list" class="bg-cyan nav-link">
										<i class="nav-icon glyphicon glyphicon-hand-right"></i>
										<p class="nav-icon-p">Accept Sample <i class="nav-icon fas fa-angle-right right"></i></p>
									</a>
								</li>
								<li class="nav-item">
									<a href="<?php echo $this->request->getAttribute('webroot');?>SampleAccept/accepted_list" class="bg-cyan nav-link">
										<i class="nav-icon fas fa-vote-yea"></i>
										<p class="nav-icon-p">Accepted Samples</p>
									</a>
								</li>
							</ul>
						</li>

					<?php } ?>


					<!--Sidebar Menu for Sample Forward Details-->

					<?php } if ($current_user_roles['sample_forward'] == 'yes') { ?>

						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon far fa-share-square"></i>
									<p>Sample Forward<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
								<ul class="nav nav-treeview dnone">
									<li class="nav-item has-treeview">
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>SampleForward/available_to_forward_list" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-share-alt"></i>
												<p class="nav-icon-p">Forward / Reject</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>SampleForward/forwarded_list" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-ok"></i>
												<p class="nav-icon-p">Forwarded Samples</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>SampleForward/rejected_list" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-remove-circle"></i>
												<p class="nav-icon-p">Rejected Samples</p>
											</a>
										</li>
									</li>
								</ul>
							</li>
						</li>


					<!--Sidebar Menu for Sample Allocation Details-->

					<?php } if ($current_user_roles['sample_allocated'] == 'yes') { ?>

						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-thumbtack"></i>
									<p>Allocation<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
								<ul class="nav nav-treeview dnone">
									<li class="nav-item has-treeview">
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>SampleAllocate/available_to_allocate" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-pushpin"></i>
												<p class="nav-icon-p">Allocate Sample</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>SampleAllocate/allocated_list" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-saved"></i>
												<p>Allocated Samples</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>SampleAllocate/available_to_allocate_retest" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-repeat"></i>
												<p>	Allocate for Retest</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>SampleAllocate/allocated_retest_list" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-ok-sign"></i>
												<p>Allocated Retest</p>
											</a>
										</li>
									</li>
								</ul>
							</li>
						</li>


					<!--Sidebar Menu for Sample Test Details-->

					<?php } if ($current_user_roles['sample_testing_progress'] == 'yes') { ?>

						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-vials"></i>
									<p>Test<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
								<ul class="nav nav-treeview dnone">
									<li class="nav-item has-treeview">
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>Test/accept_sample" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-ok"></i>
												<p>Accept For Test</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>Test/available_to_enter_reading" class="bg-cyan nav-link">
												<i class="nav-icon far fa-clipboard"></i>
												<p>Enter Readings</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>Test/view_prfm_test" class="bg-cyan nav-link">
												<i class="nav-icon fas fa-book"></i>
												<p>Performed Test</p>
											</a>
										</li>
									</li>
								</ul>
							</li>
						</li>


					<!--Sidebar Menu for Sample Test Results -->

					<?php } if ($current_user_roles['sample_result_approval'] == 'yes') { ?>

						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-poll-h"></i>
									<p>Test Results<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
								<ul class="nav nav-treeview dnone">
									<li class="nav-item has-treeview">
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>ApproveReading/available_for_approve_reading" class="bg-cyan nav-link">
												<i class="nav-icon fas fa-poll-h"></i>
												<p>Approve Results</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>ApproveReading/approved_results" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-tasks"></i>
												<p>Approved Results</p>
											</a>
										</li>
									</li>
								</ul>
							</li>
						</li>

					<?php }

					/*if($current_user_roles['verify_sample'] == 'yes') { ?>

						<li class="dropdown">
							<a href="<?php echo $this->request->getAttribute('webroot');?>FinalGrading/available_for_grading_to_inward">Finalize Result</a>
						</li>

					<?php }*/


				/* <!--Sidebar Menu for Sample Finalization  --> */

					if ($current_user_roles['finalized_sample'] == 'yes') { ?>

						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-stamp"></i>
									<p>Finalization<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
									<?php if($_SESSION['role']=="Inward Officer") {

										$action_name = 'available_for_grading_to_inward';

										}else{

											$action_name = 'available_for_grading_to_Oic';

										} ?>

									<ul class="nav nav-treeview dnone">
										<li class="nav-item has-treeview">
											<li class="nav-item">
												<a href="<?php echo $this->request->getAttribute('webroot');?>FinalGrading/<?php echo $action_name; ?>" class="bg-cyan nav-link">
													<i class="nav-icon fas fa-poll-h"></i>
													<p>Finalize Result</p>
												</a>
											</li>

											<?php if($_SESSION['role']=="Inward Officer") { ?>

												<li class="nav-item">
														<a href="<?php echo $this->request->getAttribute('webroot');?>FinalGrading/ilc_finalized_samples" class="bg-cyan nav-link">
															<i class="nav-icon fas fa-poll-h"></i>
															<p>ILC Final Z-score</p>
														</a>
												</li>

												<?php } else { ?>

													<li class="nav-item">
														<a href="<?php echo $this->request->getAttribute('webroot');?>FinalGrading/finalized_samples" class="bg-cyan nav-link">
															<i class="nav-icon fas fa-poll-h"></i>
															<p>Finalized Samples</p>
														</a>
													</li>

													<li class="nav-item">
														<a href="<?php echo $this->request->getAttribute('webroot');?>FinalGrading/ilc_finalized_zscore" class="bg-cyan nav-link">
														<!-- ilc_finalized_zscore   ilc_grading-by-oic-->
															<i class="nav-icon fas fa-poll-h"></i>
															<p>ILC Finalized Z-score</p>
														</a>
													</li>

											<?php } ?>
										</li>
									</ul>
								</li>
							</li>


					<!-- Sidebar Menu for Sample Test Reports -->

					<?php }if($current_user_roles['reports'] == 'yes') { ?>

						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-file-signature"></i>
									<p>Reports<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>

								<ul class="nav nav-treeview dnone">
								<li class="nav-item has-treeview">
									<?php if($_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Inward Officer') { ?>

									<li class="nav-item">
										<a href="<?php echo $this->request->getAttribute('webroot');?>Report/final_sample_test_reports" class="bg-cyan nav-link">
											<i class="nav-icon glyphicon glyphicon-list-alt"></i>
											<p>Test Report</p>
										</a>
									</li>

									<?php }if($_SESSION['username']=='agmarkonline.dmi@gmail.com') { ?>

										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>applicationjourney/sample_list" class="bg-cyan nav-link">
												<i class="nav-icon glyphicon glyphicon-list-alt"></i>
												<p>Sample Current Status</p>
											</a>
										</li>

									<?php } ?>

									<li class="nav-item">
										<!-- <a href="<?php //echo $this->request->getAttribute('webroot');?>Report/count_sample" class="bg-cyan nav-link">
											<i class="nav-icon glyphicon glyphicon-briefcase"></i>
											<p>Statistics Reports</p>
										</a> -->

										<!-- Change done by Shweta Apale 20-10-2021 -->
										<a href="<?php echo $this->request->getAttribute('webroot');?>Report/index" class="bg-cyan nav-link">
											<i class="nav-icon glyphicon glyphicon-briefcase"></i>
											<p>Statistics Reports</p>
										</a>
									</li>
								</li>
							</ul>
						</li>
					</li>


					<!-- master sample start -->

					<?php } if($current_user_roles['administration'] == 'yes') { ?>

						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-poll-h"></i>
									<p>Masters<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
								<ul class="nav nav-treeview dnone">
									<li class="nav-item has-treeview">
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>master/code-master-home" class="bg-cyan nav-link">
												<i class="nav-icon fas fa-poll-h"></i>
												<p>Code Files</p>
											</a>
										</li>
										<li class="nav-item">
											<a href="<?php echo $this->request->getAttribute('webroot');?>master/reference-master-home" class="bg-cyan nav-link">
												<i class="nav-icon fas fa-poll-h"></i>
												<p>Reference Files</p>
											</a>
										</li>
									</li>
								</ul>
							</li>
						</li>
						
						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="<?php echo $this->request->getAttribute('webroot'); ?>users/user_pending_work_transfer" class="nav-link">
									<i class="nav-icon fas fa-poll-h"></i>
									<p>User Work Transfer<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
							</li>
						</li>
						
						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-poll-h"></i>
									<p>Report Management<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
								<ul class="nav nav-treeview dnone">
									<li class="nav-item"><a class="nav-link bg-cyan" href="<?php echo $this->request->getAttribute('webroot');?>master/all-reports"><span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span>Reports</a></li>

									<li class="nav-item"><a class="nav-link bg-cyan" href="<?php echo $this->request->getAttribute('webroot');?>master/set-report"><span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span>Set Reports</a></li>
								</ul>
							</li>
						</li>
						
						<li class="nav-item">
							<li class="nav-item has-treeview">
								<a href="#" class="nav-link">
									<i class="nav-icon fas fa-poll-h"></i>
									<p>NABL<i class="nav-icon fas fa-angle-right right"></i></p>
								</a>
								<ul class="nav nav-treeview dnone">
									<li class="nav-item"><a class="nav-link bg-cyan" href="<?php echo $this->request->getAttribute('webroot');?>nablAccreditation/nabldetail-list"><span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span>NABL accreditation</a></li>    
									<!-- <li class="nav-item"><a class="nav-link bg-cyan" href="<?php //echo $this->request->getAttribute('webroot');?>nablAccreditation/add-nabl"><span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span>NABL accreditation</a></li>     -->

								</ul>  
							</li>
						</li>

				<!--	<li class="nav-item">
						<li class="nav-item has-treeview">
							<a href="#" class="nav-link">
								<i class="nav-icon fas fa-poll-h"></i>
								<p>Reports Master<i class="nav-icon fas fa-angle-right right"></i></p>
							</a>
							<ul class="nav nav-treeview dnone">
								<li class="nav-item">
									<a class="bg-cyan nav-link" href="<?php //echo $this->request->getAttribute('webroot');?>Report/ref_reports">
										<span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span> Reference
									</a>
								</li>
								<li class="nav-item">
									<a class="bg-cyan nav-link" href="<?php //echo $this->request->getAttribute('webroot');?>Report/code_reports">
										<span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span> Code
									</a>
								</li>
							</ul>
						</li>
					</li> -->
				

				<!--	<li class="nav-item">
						<li class="nav-item has-treeview">
							<a href="#" class="nav-link">
								<i class="nav-icon fas fa-poll-h"></i>
								<p>Audit Trail Reports<i class="nav-icon fas fa-angle-right right"></i></p>
							</a>
							<ul class="nav nav-treeview dnone">
								<li class="nav-item"><a class="nav-link bg-cyan" href="<?php //echo $this->request->getAttribute('webroot');?>Audit/total_login_trail"><span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span>Online Users</a></li>

								<li class="nav-item"><a class="nav-link bg-cyan" href="<?php //echo $this->request->getAttribute('webroot');?>Audit/task_track"><span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span>Visited User History</a></li>

								<li class="nav-item"><a class="nav-link bg-cyan" href="<?php //echo $this->request->getAttribute('webroot');?>Audit/hitcount"><span class="nav-icon glyphicon glyphicon-arrow-right float-right"></span>User Hit Count</a></li>
							</ul>
						</li>
					</li> -->

				<?php } } ?>

					

					<li class="nav-item">
						<li class="nav-item has-treeview">
							<a href="<?php echo $this->request->getAttribute('webroot'); ?>users/logout" class="nav-link">
								<i class="nav-icon fas fa-power-off"></i>
								<p>Logout</p>
							</a>
						</li>
					</li>
				</ul>
			</nav>
		</aside>
