<?php ?>
<style>
	h4 {
		padding: 5px;
		font-family: times;
		font-size: 13pt;
	}					 

	table{
		padding: 3px 5px;
		font-size: 10pt;
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
			<td>Subject: Analysis of samples with following details</td><br><br>
		</tr>
		
		<tr>
			<td>Sir,</td><br>
		</tr>

		<tr>
			<td>With reference to subject cited above, I am sending herewith these samples of following bearing Codes for analysis.
					The details for each sample are listed below.<br>
					It is requested that these samples may be analyzed for all parameters and analytical report may be sent to
					this office within stipulated time.</td>
		</tr>
		
	</table>
	
	<br><br>
	<table border="1" width="90%" align="center">
	
			<tr>
				<td><b>Sample Code</b></td>
				<td><b>Commodity</b></td>
				<td><b>Sample Type</b></td>
				<td><b>Quantity</b></td>
				<td><b>Container Type</b></td>
			</tr>
		<?php foreach($result_array as $each){ ?>
			
			<tr>
				<td><?php echo $each['sample_code']; ?></td>
				<td><?php echo $each['commodity']; ?></td>
				<td><?php echo $each['sample_type']; ?></td>
				<td><?php echo $each['quantity']; ?></td>
				<td><?php echo $each['container_type']; ?></td>				
			</tr>
	
		<?php } ?>
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