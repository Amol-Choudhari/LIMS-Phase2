<?php ?>
<style>
.highlight_btn{border:2px solid #747474;font-weight:bold;font-size:16px;color:#000 !important;}
</style>

<div class="admin-main-page">
	<h5>
	<a class="highlight_btn" id="ca_btn" href="#">CA Business Years</a>
	<a id="printing_btn" href="#">Printing Business Years</a>
	<a id="crush_ref_btn" href="#">Crushing & Refining Periods</a>
	</h5>
</div>


	<thead>
		<tr>
			<th>ID</th>
			<th>Business Years</th>
			<th>Action</th>
		</tr>
	</thead>	
	
	<tbody id="ca_view">
		<?php
		if(!empty($ca_business_years)){
			foreach($ca_business_years as $each_year){ ?>
				
				<tr>
					<td><?php echo $each_year['id'];?></td>
					<td><?php echo $each_year['business_years'];?></td>
					<td><?php echo $this->Html->link('', array('controller' => 'masters', 'action'=>'editfetchAndRedirect', $each_year['id'],'0'),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> 
						<?php //echo $this->Html->link('', array('controller' => 'masters', 'action'=>'deleteMasterRecord', $each_year['id']),array('class'=>'glyphicon glyphicon-remove','title'=>'Delete','confirm'=>'Are You Sure to Delete this Record?')); ?></td>
							
				</tr>

		<?php	} } ?>
		
	</tbody>								

	
	
	<tbody id="printing_view">
		<?php
		if(!empty($pp_business_years)){
			foreach($pp_business_years as $each_year){ ?>
				
				<tr>
					<td><?php echo $each_year['id'];?></td>
					<td><?php echo $each_year['business_years'];?></td>
					<td><?php echo $this->Html->link('', array('controller' => 'masters', 'action'=>'editfetchAndRedirect', $each_year['id'],'1'),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> 
						<?php //echo $this->Html->link('', array('controller' => 'masters', 'action'=>'deleteMasterRecord', $each_year['id']),array('class'=>'glyphicon glyphicon-remove','title'=>'Delete','confirm'=>'Are You Sure to Delete this Record?')); ?></td>
							
				</tr>

		<?php	} } ?>
		
	</tbody>								


	
	<tbody id="crush_ref_view">	
		<?php
		if(!empty($crush_refine_years)){
			foreach($crush_refine_years as $each_period){ ?>
				
				<tr>
					<td><?php echo $each_period['id'];?></td>
					<td><?php echo $each_period['crushing_refining_periods'];?></td>
					<td><?php echo $this->Html->link('', array('controller' => 'masters', 'action'=>'editfetchAndRedirect', $each_period['id'],'2'),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?> 
						<?php //echo $this->Html->link('', array('controller' => 'masters', 'action'=>'deleteMasterRecord', $each_period['id']),array('class'=>'glyphicon glyphicon-remove','title'=>'Delete','confirm'=>'Are You Sure to Delete this Record?')); ?></td>										
				</tr>

		<?php	} } ?>
		
	</tbody>								


<script>

$(document).ready(function(){
	$("#printing_view").hide();
	$("#crush_ref_view").hide();

	$("#ca_btn").click(function(){
		
		$("#ca_view").show();
		$("#printing_view").hide();
		$("#crush_ref_view").hide();
		$("#ca_btn").addClass('highlight_btn');
		$("#printing_btn").removeClass('highlight_btn');
		$("#crush_ref_btn").removeClass('highlight_btn');
		
		
	});
	
	$("#printing_btn").click(function(){
		
		$("#printing_view").show();
		$("#ca_view").hide();
		$("#crush_ref_view").hide();
		$("#ca_btn").removeClass('highlight_btn');
		$("#printing_btn").addClass('highlight_btn');
		$("#crush_ref_btn").removeClass('highlight_btn');
		
	});
	
	$("#crush_ref_btn").click(function(){
		
		$("#crush_ref_view").show();
		$("#printing_view").hide();
		$("#ca_view").hide();
		$("#ca_btn").removeClass('highlight_btn');
		$("#printing_btn").removeClass('highlight_btn');
		$("#crush_ref_btn").addClass('highlight_btn');
		
	});
});
</script>