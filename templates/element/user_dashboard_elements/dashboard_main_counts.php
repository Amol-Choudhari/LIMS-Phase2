<?php echo $this->Html->css("element/user_dashboard_elements/dashboard_main_counts"); ?>

 <div class="modal" id="dasboard_main_count_popop">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Status Of Pending Work</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">

		<?php $pending_work = 'no'; ?>

		<?php

			if(!empty($check_user_role)){

				if($check_user_role['sample_inward'] == 'yes'){ ?>

				<?php if($main_count_array['saved_samples'] != 0 || $main_count_array['samples_to_accept'] != 0){ $pending_work = 'yes'; ?>
					<div class="role_section">
						<h6>Inward Process</h6>
						<table>
							<tr>

							<?php if($main_count_array['saved_samples'] != 0){ ?>

									<td><a href="../Inward/saved_samples">Samples Need to Confirm ( <?php echo $main_count_array['saved_samples']; ?> )</a></td>

							<?php } ?>

							<?php if($_SESSION['role']=="Inward Officer") { ?>

								<?php if($main_count_array['samples_to_accept'] != 0){ ?>

										<td><a href="../SampleAccept/available_to_accept_list">Samples available to Accept ( <?php echo $main_count_array['samples_to_accept']; ?> )</a></td>

								<?php }
							} ?>
							</tr>
						</table>
					</div>
				<?php	}
				}
				if($check_user_role['sample_forward'] == 'yes'){ ?>

					<?php if($main_count_array['samples_to_forward'] != 0){ $pending_work = 'yes'; ?>

						<div class="role_section">
							<h6>Forward Process</h6>
								<table>
									<tr>
										<td><a href="../SampleForward/available_to_forward_list">Samples available to Forward ( <?php echo $main_count_array['samples_to_forward']; ?> )</a></td>
									</tr>
								</table>

						</div>

					<?php }
				}
				if($check_user_role['sample_allocated'] == 'yes'){ ?>

					<?php if($main_count_array['samples_to_allocate'] != 0 || $main_count_array['forward_to_lab_incharge'] != 0 || $main_count_array['returned_by_chemist'] != 0){
							$pending_work = 'yes'; ?>

						<div class="role_section">
							<h6>Allocation For Test</h6>
							<table>
								<tr>
									<?php if($main_count_array['samples_to_allocate'] != 0){ ?>

										<td><a href="../SampleAllocate/available_to_allocate">Allocate for Test ( <?php echo $main_count_array['samples_to_allocate']; ?> )</a></td>

									<?php }if($main_count_array['forward_to_lab_incharge']){ ?>

										<td><a href="../SampleAllocate/available_to_allocate">Forward to Lab Incharge ( <?php echo $main_count_array['forward_to_lab_incharge']; ?> )</a></td>

									<?php }if($main_count_array['returned_by_chemist']){ ?>

										<td><a href="../SampleAllocate/available_to_allocate">Rejected by Chemist ( <?php echo $main_count_array['returned_by_chemist']; ?> )</a></td>

									<?php }?>
								</tr>
							</table>

						</div>

					<?php } ?>


					<?php if($main_count_array['samples_to_allocate_retest'] != 0 || $main_count_array['forward_to_lab_incharge_retest'] != 0 || $main_count_array['returned_by_chemist_retest'] != 0){
								$pending_work = 'yes'; ?>

						<div class="role_section">
							<h6>Allocation For Re-Test</h6>
							<table>
								<tr>
									<?php if($main_count_array['samples_to_allocate_retest'] != 0){ ?>

										<td><a href="../SampleAllocate/available_to_allocate_retest">Allocate for Re-Test ( <?php echo $main_count_array['samples_to_allocate_retest']; ?> )</a></td>

									<?php }if($main_count_array['forward_to_lab_incharge_retest']){ ?>

										<td><a href="../SampleAllocate/available_to_allocate_retest">Forward to Lab Incharge ( <?php echo $main_count_array['forward_to_lab_incharge_retest']; ?> )</a></td>

									<?php }if($main_count_array['returned_by_chemist_retest']){ ?>

										<td><a href="../SampleAllocate/available_to_allocate_retest">Rejected by Chemist ( <?php echo $main_count_array['returned_by_chemist_retest']; ?> )</a></td>

									<?php }?>
								</tr>
							</table>

						</div>

					<?php } ?>

			<?php	}
				if($check_user_role['sample_testing_progress'] == 'yes'){ ?>

					<?php if($main_count_array['to_accept_by_chemist'] != 0 || $main_count_array['enter_reading_by_chemist'] != 0){ $pending_work = 'yes'; ?>

						<div class="role_section">
							<h6>Testing Process</h6>
							<table>
								<tr>
									<?php if($main_count_array['to_accept_by_chemist'] != 0){ ?>

										<td><a href="../Test/accept_sample">Samples to Accept for Test ( <?php echo $main_count_array['to_accept_by_chemist']; ?> )</a></td>

									<?php }if($main_count_array['enter_reading_by_chemist']){ ?>

										<td><a href="../Test/available_to_enter_reading">Samples to Enter Test Reading ( <?php echo $main_count_array['enter_reading_by_chemist']; ?> )</a></td>

									<?php } ?>
								</tr>
							</table>

						</div>

					<?php } ?>

			<?php	}
				if($check_user_role['sample_result_approval'] == 'yes'){ ?>

					<?php if($main_count_array['to_approve_readings'] != 0){ $pending_work = 'yes'; ?>

						<div class="role_section">
							<h6>Approval Process</h6>
							<table>
								<tr>
									<td><a href="../ApproveReading/available_for_approve_reading">Test Results to Approve ( <?php echo $main_count_array['to_approve_readings']; ?> )</a></td>
								</tr>
							</table>
						</div>

					<?php }

				}
				if($check_user_role['finalized_sample'] == 'yes'){ ?>

				<?php if($main_count_array['to_grade_by_inward'] != 0 || $main_count_array['to_grade_by_oic'] != 0){ $pending_work = 'yes'; ?>

					<div class="role_section">
						<h6>Grading Process</h6>
						<table>
							<tr>

						<?php if($_SESSION['role']=="Inward Officer"){

								 if($main_count_array['to_grade_by_inward'] != 0){ ?>

										<td><a href="../FinalGrading/available_for_grading_to_inward">Available for Grading ( <?php echo $main_count_array['to_grade_by_inward']; ?> )</a></td>

								<?php }

							}else{

								 if($main_count_array['to_grade_by_oic'] != 0){ ?>

										<td><a href="../FinalGrading/available_for_grading_to_Oic">Available for Grading ( <?php echo $main_count_array['to_grade_by_oic']; ?> )</a></td>

								<?php }

							} ?>
							</tr>
						</table>
					</div>

				<?php	}
				}

			}

		?>

		<?php if($pending_work == 'no'){ ?>No Pending Work<?php } ?>

        </div>

        <!-- Modal footer -->
       <!-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div> -->

      </div>
    </div>
  </div>

<?php echo $this->Html->script("element/user_dashboard_elements/dashboard_main_counts"); ?>
