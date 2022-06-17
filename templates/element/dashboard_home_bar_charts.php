<?php ?>
	
	
<!-- taking chart data values in hidden fields -->
<?php //echo $this->Form->create(); ?>


<?php 
// To show month wise allocated applications

	$i=1;
	foreach($month_name as $month){

		echo $this->Form->input('month_name', array('label'=>false, 'id'=>'month_name'.$i, 'type'=>'hidden', 'value'=>$month)); 

	$i=$i+1;
	}
	

?>








<?php 
// To show month wise allocated applications

	$i=1;
	foreach($month_allocated_data as $data_value){

		echo $this->Form->input('month_allocated_data', array('label'=>false, 'id'=>'month_allocated_data'.$i, 'type'=>'hidden', 'value'=>$data_value)); 

	$i=$i+1;
	}
	

?>





<?php 
// To show month wise approved applications

	$i=1;
	foreach($month_approved_data as $data_value){

		echo $this->Form->input('month_approved_data', array('label'=>false, 'id'=>'month_approved_data'.$i, 'type'=>'hidden', 'value'=>$data_value)); 
		
	$i=$i+1;
	}

?>



<?php //echo $this->Form->end(); ?>	


		
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">Your Total Applicants vs Accepted Overview(Last 12 Months)</div>
					<div class="panel-body">
						
						<div id="regular_devices" class="canvas-wrapper">
							<canvas class="main-chart" id="bar-chart" width="600"></canvas>
						</div>
					
					
					<!--	<div id="small_devices" class="canvas-wrapper">
							<canvas class="main-chart" id="bar-chart" height="350" width="600"></canvas>
						</div>
						-->
					</div>
				</div>
			</div>
		</div><!--/.row-->
	

<script>
var canvas = document.getElementById("bar-chart");
if(screen.width < 400){

	canvas.height = 340;
	draw();

}else if(screen.width > 400 && screen.width < 1000){

	canvas.height = 250;
	draw();

}else{
	canvas.height = 120;
	draw();
}

</script>