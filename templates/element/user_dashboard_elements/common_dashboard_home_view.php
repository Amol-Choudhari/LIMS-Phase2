

<?php
	//condition added on 08-12-2021 by Amol, separate option to get pending work status
	if(!empty($main_count_array)){			
		echo $this->element('user_dashboard_elements/dashboard_main_counts');	
	}else{
?>
		<div class="content-wrapper mt-30px">
			<section class="content">
				<div class="container-fluid">
					<div class="row">
						<section class="col-lg-7 connectedSortable">

							<div class="card direct-chat direct-chat-primary">

								<?php if($lab_status_count['office_type'] == 'RAL' || $lab_status_count['office_type'] == 'CAL'){ ?>
									<h6 class="alert alert-primary"><i class="fas fa-bahai"></i> My Laboratory Status</h6>
								<?php } elseif($lab_status_count['office_type'] == 'RO' || $lab_status_count['office_type'] == 'SO'){ ?>
									<h6 class="alert alert-primary">My Office Status</h6>
								<?php } ?>

								<?php echo $this->element('user_dashboard_elements/top_home_count_boxes_for_lab'); ?>

							</div>
							<div class="card direct-chat direct-chat-primary">

								<h6 class="alert alert-success"><i class="fas fa-dot-circle"></i> My Work Status</h6>
								<?php echo $this->element('user_dashboard_elements/top_home_count_boxes_for_user'); ?>

							</div>
						</section>

						<section class="col-lg-5 connectedSortable">
							<?php echo $this->element('user_dashboard_elements/dashboard_recent_activities'); ?>
						</section>
					</div>
				</div>
			</section>
		</div>

<?php 
	}
?>
		
	<div class="clearfix"></div>
