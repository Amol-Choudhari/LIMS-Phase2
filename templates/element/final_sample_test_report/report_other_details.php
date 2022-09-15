<table width="100%" border="1">
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">fji¨VZ la[;k</span> /  Report No</td>
			<td><?php if(isset($test_report)) { echo $test_report[0]['report_no']; } ?></td>
		</tr>
		<?php if($showNablLogo=='yes'){ ?>
			<tr>
				<td><span style="font-family: krutidev010; font-size:10px;"></span>  ULR No.</td>
				<td><?php echo $urlNo; ?></td>
			</tr>
		<?php } ?>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">xzkgd dk uke v©j irk</span> / Name and Address of customer</td>
			<td><?php  #this code block is added for the commercial customer address and name by Akash [15-09-2022]
				if (isset($test_report)) { 
					if ($test_report[0]['sample_type_desc'] == 'Commercial' && $customer_details != null) {

						echo $customer_details['customer_name']."<br>". 
						"Email:- ".  base64_decode($customer_details['customer_email_id'])."<br>".
						"Mobile:- ". base64_decode($customer_details['customer_mobile_no'])."<br>".
						"Address:- ". $customer_details['street_address'].", ".$stateAndDistrict['district_name'].", ".$stateAndDistrict['state_name']." - ".$customer_details['postal_code'];

					} else { ?>
						<b><?php if(!empty($sample_forwarded_office)) { echo $sample_forwarded_office[0]['user_flag']." - ".$sample_forwarded_office[0]['ro_office']; } ?></b>
				<?php } } ?>
			</td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">i.; dk uke v©j uewus dh izd`fr</span> / Name of Commodity and Nature of Sample</td>
			<td> <?php if(isset($test_report)) { echo $test_report[0]['commodity_name']."(".$test_report[0]['category_name'].")"; ?> <b><?php echo $test_report[0]['sample_type_desc']; } ?></b></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">uewuk d®M la[;k</span> / Sample Code No</td>
			<td><?php if(isset($test_report)) { echo $Sample_code_as;} ?></td>
		</tr>
		
		<tr> 
			<td><span style="font-family: krutidev010; font-size:10px;">xzkgd dh lanHkZ la[;k</span> / Reference No of customer</td>
			<td><?php if(isset($test_report)) { echo $test_report[0]['letr_ref_no']."(".$test_report[0]['letr_date'].")";} ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">daVsuj dk çdkj</span> / Type of Container</td>
			<td><?php if(isset($test_report)) { echo $test_report[0]['container_desc'];} ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">iSdst dh voLFkk</span> / State of Package</td>
			<td><?php if(isset($test_report)) { echo $test_report[0]['par_condition_desc'];} ?></td>
			
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">uewus dh çkIr ek=k</span> / Quantum of Sample Received</td>
			<td><?php if(isset($test_report)) {echo $test_report[0]['sample_total_qnt']." ".$test_report[0]['unit_weight'];} ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">ç;ksx'kkyk esa uewuk dh çkfIr dh frfFk</span> / Date of receipt of sample in the laboratory</td>
			<td><?php if(isset($test_report)) { echo $test_report[0]['phy_accept_sample_date']; } ?></td>
		</tr>

        <?php echo $this->element('/final_sample_test_report/sample_free_from_list'); ?>
										

		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">fo'ys"k.k ds çkjaHk gksus fd frFkh</span> / Date of commencement of analysis</td>
			<td><?php if(isset($comm_date)) {echo $comm_date;} ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">fo'ys"k.kkRed ifj.kkeks dks çLrqr djus dh frFkh</span> / Date of submission of analytical results</td>
			<td><?php if(isset($test_report) && $test_report[0]['grade_user_flag']=="RAL" ) { echo $test_report[0]['ral_anltc_rslt_rcpt_dt']; } else { echo $test_report[0]['cal_anltc_rslt_rcpt_dt']; } ?></td>
		</tr>
	</table>