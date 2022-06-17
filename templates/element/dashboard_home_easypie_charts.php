<?php ?>
	
		<div class="row">
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<div class="panel-body easypiechart-panel">
						<h4 style="text-align:center;">Site Inspection Done</h4>
						<div class="easypiechart" id="easypiechart-blue" data-percent="<?php echo $siteinspection_percentage; ?>" ><span class="percent"><?php echo round($siteinspection_percentage); ?>%</span>
						</div>
						<h5 style="text-align:right; padding:0 10px;"><?php echo $site_inspection_count; ?> Out of <?php echo $total_allocated_applications; ?></h5>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<div class="panel-body easypiechart-panel">
						<h4 style="text-align:center;">CA Applicants</h4>
						<div class="easypiechart" id="easypiechart-orange" data-percent="<?php echo $ca_percentage; ?>" ><span class="percent"><?php echo round($ca_percentage); ?>%</span>
						</div>
						<h5 style="text-align:right; padding:0 10px;"><?php echo $ca_applications_count; ?> Out of <?php echo $total_allocated_applications; ?></h5>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<div class="panel-body easypiechart-panel">
						<h4 style="text-align:center;">Printing Press Applicants</h4>
						<div class="easypiechart" id="easypiechart-teal" data-percent="<?php echo $printing_percentage; ?>" ><span class="percent"><?php echo round($printing_percentage); ?>%</span>
						</div>
						<h5 style="text-align:right; padding:0 10px;"><?php echo $printing_applications_count; ?> Out of <?php echo $total_allocated_applications; ?></h5>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<div class="panel-body easypiechart-panel">
						<h4 style="text-align:center;">Laboratory Applicants</h4>
						<div class="easypiechart" id="easypiechart-red" data-percent="<?php echo $lab_percentage; ?>" ><span class="percent"><?php echo round($lab_percentage); ?>%</span>
						</div>
						<h5 style="text-align:right; padding:0 10px;"><?php echo $lab_applications_count; ?> Out of <?php echo $total_allocated_applications; ?></h5>
					</div>
				</div>
			</div>
		</div><!--/.row-->
						
