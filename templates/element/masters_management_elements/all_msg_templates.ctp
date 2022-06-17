<?php ?>

	<thead>
		<tr>
			<th>SR.No</th>
			<th>SMS Message</th>
			<th>Created By</th>
			<th>Status</th>
			<th width="50">Action</th>
		</tr>
	</thead>	
	
	<tbody>
		<?php
		if(!empty($all_records)){
			$sr_no=1;
			foreach($all_records as $each_record){ ?>
				
				<tr>
					<td><?php echo $sr_no; ?></td>
					<td><?php echo $each_record['sms_message'];?></td>
					<td><?php echo $each_record['user_email_id'];?></td>
					<td><?php echo $each_record['status'];?></td>
					<td><?php echo $this->Html->link('', array('controller' => 'masters', 'action'=>'editfetchAndRedirect', $each_record['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> | 
						<?php 
						if($each_record['status'] == 'active')
						{
							echo $this->Html->link('', array('controller' => 'masters', 'action'=>'change_template_status_redirect', $each_record['id']),array('class'=>'glyphicon glyphicon-remove','title'=>'Deactivate','confirm'=>'Are you sure to Deactivate this template?'));
						}
						else{
							
							echo $this->Html->link('', array('controller' => 'masters', 'action'=>'change_template_status_redirect', $each_record['id']),array('class'=>'glyphicon glyphicon-ok','title'=>'Activate','confirm'=>'Are you sure to Activate this template?'));
						} ?>
					</td>
				</tr>
				
				
				
		<?php	$sr_no++; } } ?>
	</tbody>
								