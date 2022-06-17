<?php ?>
<style>
	h4 {
		padding: 5px;
		font-family: times;
		font-size: 12pt;
	}	

	h5 {
		padding: 5px;
		font-family: times;
		font-size: 11pt;
	}

	table{
		padding: 5px;
		font-size: 9pt;
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
				Department of Agriculture Co-Operation & Farmers Welfare<br>
				Directorate of Marketing & Inspection</h4>				
			</td>
			<td width="12%" align="center">
				<img src="img/logos/agmarklogo.png">
			</td>				
		</tr>
	</table>
	
	
	<table width="100%" border="0">
		<h4 align="center">Chemist Test Report</h4>
	</table>
	
	<table width="100%" border="0">
		<tr>
			<td><b> Sample Code : </b><?php echo $sample_details['stage_smpl_cd']; ?></td>
			<td align="right"><b> Sample Type : </b><?php echo $sample_details['sample_type_desc']; ?></td>
		</tr>
		
		<tr>
			<td><b> Category : </b><?php echo $sample_details['category_name']; ?></td>
			<td align="right"><b> Commodity : </b><?php echo $sample_details['commodity_name']; ?></td>
		</tr>
		
		<tr>
			<td><b> Date of Receipt : </b><?php echo $sample_details['recby_ch_date']; ?></td>
			<td align="right"><b> Date of Submission : </b><?php echo $test_finalized_date; ?></td>
		</tr>
		<tr>
			<td><b> Chemist Name : </b><?php echo $sample_details['f_name'].' '.$sample_details['l_name'].', '.$sample_details['ro_office']; ?></td>					
		</tr>
	</table>
	<br><br>
	
	
		<?php if(isset($sample_allocated_test)){ $j=0;
			foreach($sample_allocated_test as $each_test){ $calculated_value = 'no'; ?>
						
			<table width="100%" border="0">

				<tr>
					<td><b><?php echo $j+1; ?>) Test Name : </b><?php echo $each_test['test_name']; ?></td>
				</tr>
				
				<tr>
					<td><b> Test Method : </b><?php echo $each_test['method_name']; ?></td>
				</tr>
				
				<tr>
					<td width="100%"><b> Test Formula : </b><?php echo $each_test['test_formula1']; ?></td>
				</tr>
				
				<tr>
					<td><b> Date of Analysis : </b><?php echo $each_test['test_perfm_date']; ?></td>
				</tr>

			</table>
			
			
			<table width="70%" border="1" align="center">

				<tr>
					<td><b>Field Name</b></td>
					<td><b>Test Value</b></td>
				</tr>
				<?php if($each_test['test_formula1'] =='PN'){ ?>
					<tr>							
						<td><?php echo 'Test Result for Positive/Negative'; ?></td>
						<td><?php echo $each_test['test_result']; ?></td>								
					</tr>
				<?php }elseif($each_test['test_formula1'] =='Temperature in Degree Celcius'){ ?>
						<tr>							
							<td><?php echo 'Temperature in Degree Celcius'; ?></td>
							<td><?php echo $each_test['test_result']; ?></td>								
						</tr>
				<?php }else{ ?>	
						<?php $i=0; 
						foreach($test_fields[$j] as $each_field){ if(!empty($each_field)){ ?>													
							<tr>							
								<td><?php echo $each_field['field_name']; ?></td>
							<?php if($each_field['field_value'] == '3') { ?>
								<td><?php echo $each_test['test_result']; ?></td>		
							<?php }elseif($each_field['field_value'] == '1') { ?>	
								<td><?php echo $each_test['test_result']; ?></td>	
							<?php }else{ $calculated_value = 'yes'; ?>	
								<td><?php echo $filled_test_values[$j][0][$each_field['field_value']]; ?></td>	
							<?php } ?>
							</tr>
				
						<?php } $i++; } ?>
				<?php } ?>	
				<?php if($calculated_value == 'yes'){ ?>	
					<tr>
						<td><?php echo 'Calculated Value'; ?></td>
						<td><?php echo $each_test['test_result']; ?></td>	
					</tr>
				<?php } ?>											
			</table>
			<br><br><br>
	<?php $j++; } }  ?>	
	
	<br><br>
	<table width="100%">
		<tr>
			<td align="right"><b> <?php echo $sample_details['role']; ?> </b><br><?php echo $sample_details['f_name'].' '.$sample_details['l_name'].', '.$sample_details['ro_office']; ?></td>					
		</tr>
	</table>
