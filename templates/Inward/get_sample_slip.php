<?php ?>
<style>
	h4 {
		padding: 5px;
		font-family: times;
		font-size: 13pt;
	}					 

	table{
		padding: 5px;
		font-size: 11pt;
		font-family: times;
	}
				
</style>
	
	
	<table width="60%" border="1">
		<tr>				
			<td align="center"><h4>Sample Slip</h4></td>				
		</tr>
	</table>

	<table width="60%" border="1">
		<tr>
			<td style="padding:6px; vertical-align:top;">Sample Code</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['stage_sample_code']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Sample Location</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['ro_office']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Designation</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['designation']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Letter Date</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['letr_date']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Received Date</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['received_date']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Letter Ref. No.</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['letr_ref_no']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Commodity Category</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['category_name']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Commodity</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['commodity_name']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Container Type</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['container_desc']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Package Condition</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['par_condition_desc']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Type of Sample</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['sample_type_desc']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Physical Appearance</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['phy_appear_desc']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Sample Condition</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['sam_condition_desc']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Quantity</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['sample_total_qnt'].' '.$sample_data[0]['unit_weight']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Ref. Src. Code</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['ref_src_code']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Expiry Month</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['expiry_month']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Expiry Year</td>
			<td style="padding:6px; vertical-align:top;"><?php echo $sample_data[0]['expiry_year']; ?></td>
		</tr>
		
		<tr>
			<td style="padding:6px; vertical-align:top;">Status</td>
			<td style="padding:6px; vertical-align:top;"><?php if($sample_data[0]['status_flag']=='A'){echo "Accepted";} ?></td>
		</tr>
	</table>