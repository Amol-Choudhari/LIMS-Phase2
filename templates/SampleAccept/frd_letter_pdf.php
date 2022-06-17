<?php ?>
<style>
	h4 {
		padding: 5px;
		font-family: times;
		font-size: 13pt;
	}					 

	table{
		padding: 5px;
		font-size: 12pt;
		font-family: times;
	}
				
</style>
	
	
	<table width="100%" border="1">
		<tr>				
			<td width="12%" align="center">
				<img width="35" src="img/logos/emblem.png">
			</td>
			<td width="76%" align="center">
				<h4>Government of India <br> Ministry of Agriculture and Farmers Welfare<br>
				Department of Agriculture & Farmers Welfare<br>
				Directorate of Marketing & Inspection</h4>				
			</td>
			<td width="12%" align="center">
				<img src="img/logos/agmarklogo.png">
			</td>				
		</tr>
	</table>
	
	
	<!--<table width="100%" border="1">
		<?php //if($_SESSION['user_flag']=="RO"){ ?>
				<h5 class="text-center">Regional Office,<?php //echo $user_data[0]['ro_office']; ?></h5>
		<?php //} ?>
	</table>-->
	
	
	<table width="100%">
		<tr><td></td><br></tr>		
		<tr>
			<td><br>To,</td><br>
		</tr>	
	</table>
	
	
	<table  width="100%">
	
		<tr>
			<td><?php echo $user_data[0]['f_name'].' '.$user_data[0]['l_name']; ?>,<br>
				<?php echo $user_data[0]['role_name']; ?>,<br>
				<?php echo $user_data[0]['user_flag'].','.$user_data[0]['ro_office'];?><br>
			</td><br>
		</tr>
		
		<tr>
			<td>Subject: Analysis of <?php echo $str_data[0]['sample_type_desc']; ?> sample of <?php echo $str_data[0]['commodity_name'];?> bearing Code No.<?php echo $str_data[0]['stage_smpl_cd'];?></td><br><br>
		</tr>
		
		<tr>
			<td>Sir,</td><br>
		</tr>

		<tr>
			<td>With reference to subject cited above, I am sending herewith <?php echo $str_data[0]['sample_total_qnt']; ?>
					<?php echo $str_data[0]['unit_weight']; ?> of <?php echo $str_data[0]['sample_type_desc']; ?> sample of <?php echo $str_data[0]['commodity_name'];?> bearing Code No.<?php echo $str_data[0]['stage_smpl_cd']; ?> for analysis.
					The quantity of sample is <?php echo $str_data[0]['sample_total_qnt'];?> <?php echo $str_data[0]['unit_weight']; ?>,
					packed in <?php echo $str_data[0]['container_desc'];?>.<br>
					It is requested that the sample may be analyzed for all parameters and analytical report may be sent to
					this office within stipulated time.</td>
		</tr>
		
	</table>
	
	
	<table>
			<tr><td></td><br></tr>
			<tr><td></td><br></tr>
			<tr>
				<td  align="left">
					Encl:as above,<br>
					Place: <?php echo $src_user_data[0]['ro_office']; ?><br>
					Date: <?php echo date('d/m/Y');?>
				</td>
			</tr>
	</table>
	
	<?php //if($show_esigned_by=='yes'){ ?><!-- Condition added on 27-03-2018 by Amol -->
	<table  align="right">	
			
			<tr>
			<td>Your's Faithfully,<br> 
				<?php echo $src_user_data[0]['f_name'];?> <?php echo $src_user_data[0]['l_name'];?>,<br>
				<?php echo $src_user_data[0]['role'];?>,
				<?php echo $src_user_data[0]['user_flag'].','.$src_user_data[0]['ro_office'];?>.
			</td>
			</tr>
	</table>
	<?php //} ?>

<!-- FORM B portion end here -->		
	
	
	<!--<br pagebreak="true" />-->