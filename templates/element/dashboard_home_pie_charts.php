<?php ?>
	
	
<!-- taking chart data values in hidden fields -->
<?php //echo $this->Form->create(); ?>


<?php 
// To show month name

	$i=1;
	foreach($month_name as $month){

		echo $this->Form->control('month_name', array('label'=>false, 'id'=>'month_name'.$i, 'type'=>'hidden', 'value'=>$month)); 

	$i=$i+1;
	}
	

?>




<?php 
// To show month wise allocated applications

	$i=1;
	foreach($month_allocated_data as $data_value){

		echo $this->Form->control('month_allocated_data', array('label'=>false, 'id'=>'month_allocated_data'.$i, 'type'=>'hidden', 'value'=>$data_value)); 

	$i=$i+1;
	}
	

?>




<?php 
// To show month wise approved applications

	$i=1;
	foreach($month_approved_data as $data_value){

		echo $this->Form->control('month_approved_data', array('label'=>false, 'id'=>'month_approved_data'.$i, 'type'=>'hidden', 'value'=>$data_value)); 
		
	$i=$i+1;
	}

?>






<?php //echo $this->Form->end(); ?>
	

		
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">Commodity Wise</div>
					<div class="panel-body">
						<div class="canvas-wrapper">
							<canvas class="chart" id="pie-chart" ></canvas>
						</div>
					</div>
				</div>
			</div>
		</div><!--/.row-->
						
