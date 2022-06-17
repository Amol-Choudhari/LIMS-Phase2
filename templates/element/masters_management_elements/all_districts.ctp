<?php ?>

<thead>
	<tr>
		<th>SR.No</th>
		<th>District Name</th>
		<th>State Name</th>
		<th>Action</th>
	</tr>
</thead>

<tbody>
	
	<?php
	if(!empty($all_records)){
		$sr_no =1;	
		$i=0;
		foreach($all_records as $each_record){ ?>
			
			<tr>
				<td><?php echo $sr_no; ?></td>
				<td><?php echo $each_record['district_name'];?></td>
				<td><?php echo $state_name[$i];?></td>
				<td>
					<?php echo $this->Html->link('', array('controller' => 'masters', 'action'=>'editfetchAndRedirect', $each_record['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> | 
					<?php echo $this->Html->link('', array('controller' => 'masters', 'action'=>'deleteMasterRecord', $each_record['id']),array('class'=>'glyphicon glyphicon-remove','title'=>'Delete','confirm'=>'Are You Sure to Delete this Record?')); ?>				
				</td>	
			</tr>

	<?php $sr_no++; $i=$i+1; }} ?>
	
</tbody>