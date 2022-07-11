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
		padding: 3px 5px;
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
				Department of Agriculture & Farmers Welfare<br>
				Directorate of Marketing & Inspection<br>
				</h4>				
			</td>
			<td width="12%" align="center">
				<img src="img/logos/agmarklogo.png">
			</td>				
		</tr>
	</table>
	
	<table width="100%" border="1">
		<tr>				
		<?php if($showNablLogo=='yes'){ ?>
			<td width="79%" align="center">
		<?php }else{ ?>	
			<td align="center">
		<?php } ?>
			
				<?php 
					if($test_report[0]['grade_user_flag']=="CAL" ){ ?>
						
						<h5><span style="font-family: krutidev010;">dsanzh; ,xekdZ ç;ksx“kkyk</span> / Central Agmark Laboratory<br />
						<span style="font-family: krutidev010;">mŸkj vEck>jh ekxZ</span> / North Ambazari Road Nagpur 440010<br />											
						Phone:0712-2561748,Fax: 0712-2540952 T-2315 mail:cal@nic.in</h5>
				
				<?php } ?>
				
				<?php 
					if($test_report[0]['grade_user_flag']=="RAL" ){ ?>
						
						<h5><span style="font-family: krutidev010; font-weight:bold; font-size:13px;">{¨™kh; ,xekdZ ç;ksx“kkyk</span> / Regional Agmark Laboratory , <?php echo $_SESSION['ro_office'];?></h5>
				
				<?php }elseif(isset($test_report[0]['ro_office']) && isset($ral_lab_name) && $ral_lab_name=='RAL'){ ?>
				
						<h5><span style="font-family: krutidev010; font-weight:bold; font-size:13px;">{¨™kh; ,xekdZ ç;ksx“kkyk</span> / Regional Agmark Laboratory , <?php echo $test_report[0]['ro_office'];?></h5>
				<?php } ?>	
			
			</td>
			<?php if($showNablLogo=='yes'){ ?>
				<td width="21%" align="center">
					<img width="45" src="img/logos/nabl-logo.png">
					<p><?php echo $urlNo; ?></p>
				</td>
			<?php } ?>
		</tr>
	</table>
	
	<table width="100%" border="1">				
		<tr><td align="right">Date: <?php echo $sample_final_date; ?></td></tr>
		
		<tr><td align="center"><h5>Test Report For <?php if(isset($test_report)) {echo $test_report[0]['commodity_name']."(".$test_report[0]['category_name'].")"; } ?></h5></td></tr>
			
	</table>
	
	
	<table width="100%" border="1">
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">fji¨VZ la[;k</span> /  Report No</td>
			<td><?php if(isset($test_report)) { echo $test_report[0]['report_no']; } ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">xzkgd dk uke v©j irk</span> / Name and Address of customer</td>
			<td><b><?php if(!empty($sample_forwarded_office)) { echo $sample_forwarded_office[0]['user_flag'].",".$sample_forwarded_office[0]['ro_office']; } ?></b></td>
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
										
		<?php 	
			if(isset($method_homo)){  ?>
				<tr>
					<td class="td1" ><span style="font-family: krutidev010; font-size:10px;">uewuk buls eqä Fkk vFkok ugha</span> / Whether the sample was free from</td>
				
					<td class="td1" colspan="3">
						<?php foreach($method_homo as $method){
								if($method['m_sample_obs_code']!=1 && $method['m_sample_obs_code']!=2){ ?>															
								
									<?php echo $method['m_sample_obs_desc']; ?> : <?php echo  $method['m_sample_obs_type_value']; ?><br />
									
								<?php }
							} 
						?>
					</td>
				</tr>
		<?php } ?>

		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">fo'ys"k.k ds çkjaHk gksus fd frFkh</span> / Date of commencement of analysis</td>
			<td><?php if(isset($comm_date)) {echo $comm_date;} ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">fo'ys"k.kkRed ifj.kkeks dks çLrqr djus dh frFkh</span> / Date of submission of analytical results</td>
			<td><?php if(isset($test_report) && $test_report[0]['grade_user_flag']=="RAL" ) { echo $test_report[0]['ral_anltc_rslt_rcpt_dt']; } else { echo $test_report[0]['cal_anltc_rslt_rcpt_dt']; } ?></td>
		</tr>
	</table>
	
	
	<h5><span style="font-family: krutidev010; font-weight:bold; font-size:13px;">uksV</span> / Note : </h5>
	<table width="100%" border="1">
		<tr>
			<td>1. <span style="font-family: krutidev010; font-size:10px;">mi;qZä ifj.kke dsoy bl ç;ksX'kkyk esa ;Fkk çkIr ,oa ijhf{kr uequs ls lacaf/kr gS</span> |</td>
			<td>1. The above results pertain only to the sample tested and as received by the laboratory</td>
		</tr>
		<tr>
			<td>2. <span style="font-family: krutidev010; font-size:10px;">uequks dk fo'ys"k.k flQZ fuosfnr ijkfefr;ks ds fy;s fd;k x;k</span> |</td>
			<td>2. The sample has been analysed only for the requested parameters.</td>
		</tr>
		<tr>
			<td>3. <span style="font-family: krutidev010; font-size:10px;">çkIr ifj.kkeks dks ç;ksx'kkyk fd vuqefr ds fcuk vkaf'kd ;k iw.kZ] :i ls çdkf'kr foKkfir ;k fdlh dkuquh dkjokbZ ds fy;s tkjh djus gsrw bLrseky ugha fd;k x;k</span> |</td>
			<td>3. The result either in part or full shall not be published advertised or used for any legal action without the permisssion of the issuing laboratory.</td>
		</tr>
		<tr>
			<td>4. <span style="font-family: krutidev010; font-size:10px;">;nh bl laca/k esa fo'ks"k funsZ'k tkjh ukgh gksrs gS rks uequk çkIrh dh rkjh[k ls flQZ rhu ekg fd vof/k rd fg bl ç;ksx'kkyk }kjk 'ks"k cps gq, uequks dks laHkky ds j[kk tk,xk</span> |</td>
			<td>4. Remnant samples will be retained by this laboratory for a time period of only three months from the date of receipt unless specific instructions to the contrary are received.</td>
		</tr>
		<tr>
			<td>5. <span style="font-family: krutidev010; font-size:10px;">ijhf{kr uequk bl ç;ksx'kkyk }kjk vkgjhr ugha gS</span> |</td>
			<td>5. The sample is not drawn by this laboratory.</td>
		</tr>
	</table>

	<br pagebreak="true" />	
	<?php
	//this if part is for "Food Safety Samples" report
	//02-06-2022 by Shreeya
		$sampleTypeCode =  $getSampleType['sample_type_code'];
		if($sampleTypeCode==8){  ?>
                
            <table width="100%" border="1">
				<tr>
					<td width="10%"><b>S.No. <span style="font-family: krutidev010; font-size:10px;">Ø-la</span></b></td>											
					<td width="30%"><b><span style="font-family: krutidev010; font-size:10px;">iSjkehVj  dk  uke </span>/ Name Of Parameter</b></td>
					
					<td><b><span style="font-family: krutidev010; font-size:10px;">çkIr eku</span>/ Value Obtained (Mg/Kg)</b></td>
					<td><b><span style="font-family: krutidev010; font-size:10px;">vf/kdre  lhek </span>/ Maximum value (Mg/Kg)</b></td>
					<td><b><span style="font-family: krutidev010; font-size:10px;">viukbZ x;h i)fr</span>/ Method Followed</b></td>
				</tr>
				<?php if(isset($table_str)){ echo $table_str; }?>
			</table>
            
    <?php }

	//this else part is for regular report as before
	//02-06-2022 by Shreeya
	else { ?>

	<table width="100%" border="1">
		<tr>
			<td rowspan="2" width="5%"><b>S.No. <span style="font-family: krutidev010; font-size:10px;">Ø-la</span></b></td>											
			<td rowspan="2" width="20%"><b><span style="font-family: krutidev010; font-size:10px;">fof'k"V fo'ks"krk,</span>/Special Characteristics</b></td>
			<td  colspan="<?php if(isset($commo_grade)){echo count($commo_grade); }?>" ><b><span style="font-family: krutidev010; font-size:10px;">fofunsZ'kks dh jsat</span>/Range of Specification</b></td>											
			
			<?php if($count_test_result>0){
						
						for($i=1;$i<=$count_test_result;$i++){ ?>
						
							<td  colspan="1"rowspan="2"><br><br><b>Chemist <?php echo $i; ?></b></td>												
					<?php } ?>
					
					<td  colspan="1"rowspan="2"><br><br><b>Approved Result</b></td>
					
			<?php }else { ?>
				
						<td  rowspan="2"><br><br><b><span style="font-family: krutidev010; font-size:10px;">çkIr eku</span>/ Value Obtained</b></td>
						
			<?php 	} ?>
			
			<td  rowspan="2"><b><span style="font-family: krutidev010; font-size:10px;">viukbZ x;h i)fr</span>/ Method Followed</b></td>
		</tr>
		
		<tr>
			<?php 	if(isset($commo_grade)){ 
			
						foreach($commo_grade as $row){ ?>
						
							<td align="center"><?php echo $row['grade_desc']; ?></td>
							
						<?php }
					}
			?>
		</tr>
		
		
		<?php if(isset($table_str)){ echo $table_str; }?>
		
		
	</table>
	
	<?php 	} ?>
	
	<?php
	$sampleTypeCode =  $getSampleType['sample_type_code'];
		if($sampleTypeCode!=9){ ?> 
			
		<table border="1" width="100%">
		<tr>
			<td><b>Grade</b></td>
			<td><b><?php if(isset($test_report)) { echo $_SESSION['gradeDescFinalReport']; } ?></b></td><!-- Added on 27-05-2022 by Amol, to show grade selected by OIC while final grading -->
		</tr>	
		</table> 
		<?php 	} ?> 
	<br><br><br><br>
	<table width="100%">
	  <tr>
		<td></td>
		<td align="right"><b>(Authorized Signatory/Incharge)</b> <br><br> <?php if(isset($test_report)) { echo $test_report[0]['grade_user_flag'].','.$test_report[0]['ro_office']; } ?></td>
	  </tr>	
	</table> 