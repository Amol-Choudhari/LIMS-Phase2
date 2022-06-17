<?php ?>

	<thead>
		<tr>
			<th>ID</th>
			<th>PAO/DDO Name</th>
			<th>Action</th>
		</tr>
	</thead>
		
	<tbody>
		<?php
		if(!empty($pao_name_list)){

			for ($i=0; $i<sizeof($pao_name_list); $i++){ ?>
				
				<tr>
					<td><?php echo $i+1;?></td>
					<td><?php echo $pao_name_list[$i];?></td>
					<td><?php echo $this->Html->link('', array('controller' => 'masters', 'action'=>'editfetchAndRedirect', $pao_id_list[$i]),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?>  
						<?php //echo $this->Html->link('', array('controller' => 'masters', 'action'=>'deleteMasterRecord', $pao_id_list[$i]['id']),array('class'=>'glyphicon glyphicon-remove','title'=>'Delete','confirm'=>'Are You Sure to Delete this Record?')); ?></td>
							
				</tr>
				
				
				
		<?php	} } ?>
		
	</tbody>
