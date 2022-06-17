<?php ?>

<div class="masters_list">
	<?php echo $this->Html->link('Back', array('controller' => 'InwardDetails', 'action'=>'sample_inward_details'),array('style'=>'float:right;','class'=>'add_btn')); ?>
	<div class="clearfix"></div>

	<?php echo $this->Form->create('SampleInward'); ?>

		<div class="panel panel-primary filterable">

			<div class="panel-heading">			
                <h3 class="panel-title">Given Below is list of All Saved Sample Details</h3>				
				<div class="clearfix"></div>
            </div>

			<table id="pages_list_table">
				<thead>
					<tr>
						<th>SR.No</th>
						<th>Sample Code</th>
						<th>Drawing Date</th>
						<th>Received From</th>														
						<th>Action</th>
					</tr>
				</thead>	
				
				<tbody>
					<?php
					if(!empty($res)){
						$sr_no = 1;		
						foreach($res as $each){ ?>
							
							<tr>
								<td><?php echo $sr_no; ?></td>
								<td><?php echo $each['org_sample_code'] ?></td>
                                <td><?php echo $each['smpl_drwl_dt']  ?></td>
								<td><?php echo $each['ro_office'] ?></td>														
								<td> 
									<?php echo $this->Html->link('', array('controller' => 'InwardDetails', 'action'=>'fetch_inward_id', $each['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?>
								</td>
							 </tr>

					<?php $sr_no++; } } ?>					
				</tbody>
				
			</table>
		</div>
</div>
	
	<?php echo $this->Form->end(); ?>


<script type="text/javascript">
		
	$(document).ready(function(){

		$('#pages_list_table').DataTable();

	// For date picker option
		$('#publish_date').datepicker({
			format: "dd/mm/yyyy"
		});
		
		
		$('#end_date').datepicker({
			format: "dd/mm/yyyy"
		});
			

				
	});
			
			
	</script>