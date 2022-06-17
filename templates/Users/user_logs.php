<?php $username = $_SESSION['username'];?>
<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2"><div class="col-sm-6"><label class="badge badge-info">Log History</label></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Log History</li>
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
							<div class="card-header"><h4 class="card-title-new">Given Below is your log history</h4></div>
								<div class="form-horizontal">
									<div class="card-body">
										<table class="table table-bordered table-stripped table-hover" id="log_table">
											<thead class="tablehead">
												<tr>
													<th>Date</th>
													<th>TimeIn</th>
													<th>TimeOut</th>
													<th>Duration</th>
													<th>Remark</th>
													<th>IP Address</th>
												</tr>
											</thead>
											<?php if (!empty($user_logs)) {

												foreach ($user_logs as $user_log) { ?>

													<?php
														$time_in = strtotime($user_log['time_in']);
														$time_out = strtotime($user_log['time_out']);
														$login_duration = $time_out - $time_in;
													?>

													<tr>
														<td><?php echo $user_log['date'];?></td>
														<td><?php echo $user_log['time_in'];?></td>
														<td><?php echo $user_log['time_out'];?></td>
														<td><?php if (!empty($user_log['time_out'])) { echo round($login_duration/60)." min ".($login_duration%60)." sec";} else { echo "Current Session";} ?></td>
														<td><?php $remark = $user_log['remark'];
																if($remark == 'Success'){
																	$badge = "success";
																} elseif ($remark == 'Failed') {
																	$badge = "danger";
																} else {
																	$badge = "info";
																}
																echo "<span class='badge badge-".$badge."'>".$remark."</span>";
															?>
														</td>
														<td><?php echo $user_log['ip_address'];?></td>

													</tr>
													<?php } }?>
												</thead>
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
