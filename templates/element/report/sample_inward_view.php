<?php ?>
<style>
	.table-bordered{border: 1px solid #ddd;}
	.textLeft { text-align: left; }
	.textCenter { text-align: center; }
	.textRight { text-align: right; }
	.lineHeightSmall { line-height: 4px; }
	.lineHeightMedium { line-height: 6px; }
	.lineHeightLarge { line-height: 8px; }
	.fontSizeFour { font-size: 4px; }
	.fontSizeFive { font-size: 5px; }
	.fontSizeSix { font-size: 6px; }
	.fontSizeSeven { font-size: 7px; }
	.fontSizeExtraExtraExtraSmall { font-size: 7px; }
	.fontSizeExtraExtraSmall { font-size: 8px; }
	.fontSizeExtraSmall { font-size: 9px; }
	.fontSizeSmall { font-size: 10px; }
	.fontSizeMedium { font-size: 12px; }
	.fontSizeLarge { font-size: 14px; }
	.fontBold { font-weight: bold; }
	.transparent { color: #fff; }
</style>
	<h5 class="textCenter lineHeightMedium">भारत सरकार/Goverment of India</h5>
	<h5 class="textCenter lineHeightMedium">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
	<h5 class="textCenter lineHeightMedium">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
	<h5 class="textCenter lineHeightMedium">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
	<b></b>
	<h5 class="textCenter"><b> 
	<?php switch($report_name){
		case '	':
			echo "Sample received from ".$ral_lab_name.','.$sample_inward['0']['ro_office'];
		break ;
		
		case 'Samples Alloted/Analyzed/Pending Report(RAL/CAL)':
			echo "Samples Alloted/Analyzed/Pending at ".$abc;
		break ;
		case 'Sample Workflow':
			echo "Sample Workflow ";
		break ;
		case 'Commodity-wise Research & Private Samples analysed':
			echo "Commodity-wise Research Samples analysed during Financial Year ".$sample_inward['0']['fin_year'];
		break;
		case 'Commodity-wise Check & Challenged Samples Analysed':
			echo "Commodity-wise Check & Challenged Samples Analysed during Financial Year 2016-2017";
		break;
		case 'Brought forward,Analysed and carried forward of samples':
			echo "Statement of Check Samples Brought forward/ Carry forward Annexure I";
		break;
		case 'Time Taken for Analysis of Samples':
			echo "Time Taken report";
		break;
		case 'Chemist –wise sample analysis':
			echo "Details of the samples Analysed by chemist, CAL, Nagpur";		
		break;
		case "Consolidated statement of Brought forward and carried forward of samples":
			echo "Monthly report of Carry forward and Brought forward";
		break;
		case "Commodity wise details of samples analyzed by RAL Annexure E":
			echo "Commodity wise details of samples analyzed by RAL, ".$ral_lab_name." for the month of ".$month;;
		break;
		case "Monthly status of analyzed of Check samples and pending samples of RAL Annexure E":
			echo "Revised ANNEXURE-E";
			echo "<br>"; ?>
			<span class="fontSizeExtraSmall"><?php echo "Monthly status of analysis of check samples and pendency (Chemist-wise) of samples of RAL, ".$ral_lab_name." for the month of ".$month;?></span><?php
		break;
		default: 
				echo $report_name; ?></b></h5>
	<?php } ?>
	<p class="textRight fontSizeSmall"><span class="fontBold">Date: </span><?php echo date("d/m/Y"); ?></p>	
	<?php if($from_date!='' || $to_date!='') { ?>
		<p class="textLeft fontSizeSmall">Sample Received in the Period From  : <span class="fontBold"><?php echo $from_date; //Remove/change date format on 22-05-2019 by Amol ?></span> To : <span class="fontBold"><?php echo $to_date; //Remove/change date format on 22-05-2019 by Amol ?></span></p>								
	<?php } ?>
	<?php if(!empty($commodity) && $commodity!='') {  ?>							
		<?php if(null !== ($sample_inward['0']['commodity_name'])) { ?>
			<h5 class="textLeft fontSizeSmall">Commodity Name : <b><?php echo $sample_inward['0']['commodity_name']; ?></b></h5>
		<?php } ?>							
	<?php }  ?>
	<?php if(!empty($sampleo) && $sampleo!='') { ?>
		<?php if(!empty($sample_inward['0']['sample_type_desc'])) { ?>
			<h5 class="textLeft fontSizeSmall">Sample Type :<b> <?php echo $sample_inward['0']['sample_type_desc']; ?></b></h5>
		<?php }  ?>
	<?php }	?>								
	<?php if(!empty($user1)) { ?>
		<?php if(!empty($sample_inward['0']['f_name']) && !empty($sample_inward['0']['role'])) { ?>
			<h5 class="textLeft fontSizeSmall">Chemist Name : <b><?php echo $sample_inward['0']['f_name'].' '.$sample_inward['0']['l_name'] .'<br>('.$sample_inward['0']['role'].')';  ?></b></h5>
		<?php } else if(!empty($f_name) && null !== ($l_name) && $f_name!='' && $l_name!='') { ?>
			<h5 class="textLeft fontSizeSmall"><b>User Name</b> : <?php echo $f_name.' '.$l_name;  ?></h5>					
		<?php } ?>
	<?php } ?>
	<?php if(!empty($abc) && $abc!='') { ?>
		<h5 class="textLeft fontSizeSmall">Lab Name : <b><?php echo $abc; ?></b></h5>
	<?php } ?>
	<?php if(!empty($sample_inward['0']['location_desc']) && null !== ($ral_lab_name) && $ral_lab_name!='') { ?>
		<?php if($ral_lab_name=="RO" || $ral_lab_name=="SO") { ?>
			<h5 class="textLeft fontSizeSmall"><b>Office Name</b> : <?php echo $ral_lab_name.','.$sample_inward['0']['location_desc']; ?></h5>
		<?php } else { 
			switch($report_name) {
				case 'Sample received from RO/SO/RAL/CAL': ?>
					<h5 class="textLeft fontSizeSmall"><b>Lab Name</b> : <?php echo $ral_lab_name.','.$sample_inward['0']['location_desc']; ?></h5>
				<?php 
					break ;
					default:   
				?>
				<h5 class="textLeft fontSizeSmall"><b>Lab Name</b> : <?php echo $abc; ?></h5>
			<?php } ?>
		<?php } ?>
	<?php }   ?>
	<?php if($sample_code!='') { ?>
			<h5 class="textLeft fontSizeSmall"><b>Sample Code</b> : <?php echo $sample_code; ?></h5>
	<?php } ?>
	<table class="table table-bordered display" id="example" cellspacing="0" cellpadding="1" border="0.5">       
		<?php  switch($report_name) {
			case 'Category-wise Received Sample': ?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="60">S.No</th>
					<th width="270">Category Name</th>
					<th width="200">No. of Received Sample</th>											   
				</tr>											
				<?php											
					$i = 1;
					foreach ($sample_inward as $res1): 
				?>		
					<tr class="textCenter fontSizeSmall">
						<td width="60"><?php echo $i; ?></td>
						<td width="270" class="textLeft"><?php echo $res1['category_name'] ?></td>
						<td width="200"><?php echo $res1['count'] ?></td>													
					</tr>
				<?php													
					$i++;
					endforeach;
			break;

			case 'Commodity-wise consolidated report of lab': ?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="30">S.No</th>
					<th width="260">Commodity Name</th>
					<th width="80">Brought Forward</th>
					<th width="80">Analyzed</th>
					<th width="80">Carried Forward</th>												
				</tr>								
				<?php
					$i = 1;
					foreach ($final_res as $res1):																							
				?>		
				<tr class="textCenter fontSizeSmall">
					<td width="30"><?php echo $i; ?></td>
					<td width="260" class="textLeft"><?php echo $res1['commodity_name'] ?></td>
					<td width="80"><?php echo $res1['brought_for'] ?></td>
					<td width="80"><?php echo $res1['analyzed_count'] ?></td>
					<td width="80"><?php echo $res1['cf_count'] ?></td>
				</tr>
				<?php
					$i++;
					endforeach;
			break;

			case 'Commodity-wise Check & Challenged Samples Analysed': 	?>
				<tr class="textCenter fontSizeExtraSmall fontBold">
					<th rowspan="2" width="30">S.No</th>
					<th rowspan="2" width="160">Commodity Name</th>
					<th rowspan="2" width="30">B/F</th>
					<th rowspan="2" width="70">Sample Received during</th>
					<th rowspan="2" width="30">Total</th>
					<th class="text-center" colspan="3">Samples Analyzed</th>
					<th rowspan="2" width="30">C/F</th>
				</tr>
				<tr class="textCenter fontSizeExtraSmall fontBold">
					<th>Standard</th>
					<th>Sub-Standard</th>
					<th>Total</th>                                   
				</tr>
				<?php
					$i = 1;
					foreach ($final_res as $res1):
				?>		
				<tr class="textCenter fontSizeExtraSmall">
					<td width="30"><?php echo $i; ?></td>
					<td width="160"><?php echo $res1['commodity_name'] ?></td>
					<td width="30"><?php echo $res1['brought_for'] ?></td>
					<td width="70"><?php echo $res1['received_count'] ?></td>
					<td width="30"><?php echo $res1['total'] ?></td>
					<td><?php echo $res1['pass_count'] ?></td>
					<td><?php echo $res1['fail_count'] ?></td>
					<td><?php echo $res1['total_analysed'] ?></td>
					<td width="30"><?php echo $res1['cf_count'] ?></td>
					
				</tr>
				<?php
					$i++;
					endforeach;
			break;		

			case 'Time Taken for Analysis of Samples - test ankur': ?>
				<tr>
					<th width="530">Name of Commodity : Ghee</th>
				</tr>
				<tr class="textCenter fontSizeExtraSmall fontBold">
					<th width="30">Sr.No</th>
					<th width="60">Code No.(RO/SO)</th>
					<th width="100">Name of Sample</th>
					<th width="60">Date of receipt of sample in RALs/CAL</th>
					<th width="60">Date of Dispatch of results to RO/SO/Others</th>
					<th width="60">Time taken for analysis/submission</th>
					<th width="80">Reason For Delay</th>
					<th width="80">Remarks</th>
				</tr>
				<?php	
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
					//if($res1["category_code"] == "Ghee") {
				?>
				<tr class="textCenter fontSizeExtraSmall">
					<td width="30"><?php echo $i ?></td>
					<td><?php echo $res1["stage_sample_code"] ?></td>
					<td><?php echo $res1["commodity_name"] ?></td>
					<td><?php echo $res1["received_date"]; //Remove/change date format on 22-05-2019 by Amol ?></td>
					<td><?php if(!empty($res1["dispatch_date"])){ echo $res1["dispatch_date"]; }//Remove/change date format on 22-05-2019 by Amol ?></td>
					<td><?php echo $res1["time_taken"] ?></td>
					<td></td>
				</tr>
				<?php 
					endforeach;
			break;

			case 'Time Taken for Analysis of Samples': ?>
				<?php 
						$g_inc=0; $gs_inc=0; $vo_inc=0; $ws_inc=0; $b_inc=0; $p_inc=0;
						foreach($sample_inward as $sample) { 
							if($sample['category_name'] == 'Ghee') {
								$ghee[$g_inc] = $sample;
								$g_inc++;
							}
							else if($sample['category_name'] == 'Ground Spices') {
								$ground_spices[$gs_inc] = $sample;
								$gs_inc++;
							}							
							else if($sample['category_name'] == 'Blended Edible Vegetable Oil') {
								$vegetable_oils[$vo_inc] = $sample;
								$vo_inc++;
							}							
							else if($sample['category_name'] == 'Whole Spices') {
								$whole_spices[$ws_inc] = $sample;
								$ws_inc++;
							}							
							else if($sample['category_name'] == 'Food Grains and Allied Products' && $sample['commodity_name'] == 'Besan') {
								$besan[$b_inc] = $sample;
								$b_inc++;
							}							
							else if($sample['category_name'] == 'Food Grains and Allied Products' && $sample['commodity_name'] != 'Besan') {
								$pulses[$p_inc] = $sample;
								$p_inc++;
							}							
						}
						
						$commodities = ['Ghee'=>$ghee, 'Ground Spices'=>$ground_spices, 'Vegetable Oils'=>$vegetable_oils, 'Whole Spices'=>$whole_spices, 'Besan'=>$besan, 'Pulses'=>$pulses];
						foreach($commodities as $commodity) { ?>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="530">Name of Commodity : 
						<?php 
							if($commodity[0]['category_name'] == 'Blended Edible Vegetable Oil') {
								echo 'Vegetable Oils';
							}
							else if($commodity[0]['category_name'] == 'Food Grains and Allied Products' && $commodity[0]['commodity_name'] == 'Besan') {
								echo 'Besan';
							}
							else if($commodity[0]['category_name'] == 'Food Grains and Allied Products' && $commodity[0]['commodity_name'] != 'Besan') {
								echo 'Pulses';
							}
							else {
								echo $commodity[0]['category_name'];
							}
						?>
					</th>
				</tr>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="30">Sl. No.</th>
					<th width="60">No. of samples</th>
					<th width="100">Date of receipt of sample at RAL</th>
					<th width="70">Date of submission of results</th>
					<th width="70">Time taken for analysis/submission</th>
					<th width="100">Reason for any delay</th>
					<th width="100">Remarks</th>
				</tr>
				<?php 
					$i=1;
					foreach($commodity as $commo) { ?>
						<tr class="textCenter fontSizeSeven">
							<td width="30"><?php echo $i; ?></td>
							<td width="60"><?php echo $commo['sample_total_qnt']; ?></td>
							<td width="100"><?php echo $commo['received_date']; ?></td>
							<td width="70"><?php echo $commo['dispatch_date']; ?></td>
							<td width="70"><?php echo $commo['time_taken']; ?></td>
							<td width="100"></td>
							<td width="100"></td>
						</tr><?php
						$i++;
					}
				?>
				<tr>
					<td></td>
				</tr>
				<?php	}
			break;

			case 'Commodity-wise Research & Private Samples analysed': ?>
				<tr>
					<th>Sr.No</th>
					<th>Name of Commodity</th>
					<th>Research Samples</th>
					<th>Private Sample</th>
				</tr>
				<?php	
					$sum=0; $i = 0;
					foreach ($sample_inward as $res1):
					$i++; 
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['count']; $sum+=$res1['count']; ?></td>
					<td></td>	
				</tr>
				<?php endforeach; ?>
				<tr >	
					<td colspan="16"><b>Total Number of Samples :- <?php echo $sum ; ?></b></td>
				</tr>
			<?php	
			break;
			
			case 'Forwarded sample': ?>
				<tr>
					<th>Sr.No</th>
					<th>Received Date</th>
					<th>Sample Code</th>
					<th>Name of Commodity</th>
					<th>Type Of Sample</th>									
				</tr>
				<?php	
					$sum="";$i = 0;
					foreach ($sample_inward as $res1):
					$i++; 
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $res1['received_date'] ?></td>
					<td><?php echo $res1['stage_smpl_cd'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>	
					<td colspan="16"><b>Total Number of Samples :-  <?php echo $i ; ?></b></td>
				</tr> <?php	
			break; ?>

			<?php	case 'Samples Accepted by Chemist For Testing':  ?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="40">S.No</th>
					<th>Accepted Date</th>
					<th>Sample Code</th>
					<th width="160">Commodity Name</th>
					<th>Sample Type</th>
				</tr>
				<?php 
					$i=1;	$sum=0;
					foreach ($sample_inward as $res1) {
				?>
				<tr class="textCenter fontSizeSmall">
					<td width="40"><?php echo $i; ?></td>
					<td><?php echo  $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td width="160" class="textLeft"><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>										
				</tr>
				<?php $i++;  } ?>
				<tr class="fontSizeSmall">
					<td colspan="16"><b>Total Number of accepted Samples by Chemist :- <?php echo $i-1 ; ?></b></td>									
				</tr>
			<?php  break;
										
			case 'Rejected Samples': ?>
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Rejected Date</th>
					<th>Received By</th>
					<th>Lab Name</th>
					<th>Sample Code</th>
					<th>Commodity Name</th>
					<th>Sample Type</th>
				</tr>
				<?php	
					$i=1; $sum=0;
					foreach ($sample_inward as $res1){ 
				?>										
				<tr>
					<td class="text-right"><?php echo $i; ?></td>
					<td><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
					<td><?php echo $res1['reject_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
					<td><?php echo $res1['f_name'].' '.$res1['l_name'] ?></td>
					<td><?php echo $res1['user_flag'].",".$res1['ro_office'] ?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>									
					<!--<td><?php echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
				</tr>										
				<?php $i++;  } ?>
				<tr>
					<td colspan="16"><b>Total Number of Rejected Samples :-<?php echo $i-1 ; ?> </b></td>									
				</tr>
				<?php
			break;
									
			case 'Rejected Samples From RAL/CAL': ?>
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Accepted Date</th>
					<th>Lab Name</th>
					<th>Sample Code</th>
					<th>Commodity Name</th>
					<th>Sample Type</th>
					<th>Rejected Remark</th>
				</tr>
				<?php	
					$i=1; $sum=0;
					foreach ($sample_inward as $res1){ 
				?>										
				<tr>
					<td class="text-right"><?php echo $i; ?></td>
					<td><?php echo  $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
					<td><?php echo  $res1['acceptstatus_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
					<td><?php echo $res1['user_flag'].",".$res1['ro_office'] ?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>
					<td><?php echo $res1['acceptstatus_remark'] ?></td>
					<!--<td><?php echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
				</tr>										
				<?php $i++;  } ?>
				<tr>								
					<td colspan="16"><b>Total Number of Rejected Samples :-<?php echo $i-1 ; ?> </b></td>									
				</tr>
				<?php
			break;
										
			case 'Samples Pending for Dispatch': ?>
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Sample Code</th>
					<th>Commodity Name</th>
					<th>Sample Type</th>
					<th>Status</th>
				</tr>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;										
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo  $res1['received_date']; ?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>
					<td><?php echo $res1['status'] ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="16"><b>Total Number Samples :-<?php echo $i ; ?> </b></td>
				</tr>
				<?php 
			break;

			case 'Sample Inward with Details': ?>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;										
				?>
				<div class="row">
					<div class="col-md-6 text-left"> <label  class="control-label" for="sel1"><b style="font-weight: 800">Sample : </b><?php echo $res1['stage_sample_code'] ?></label></div>	
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Commodity Name : </b><?php echo $res1['commodity_name'] ?></label></div>
				</div>			
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800">Sample Location From: </b></label><?php echo $res1['user_flag'].','.$res1['ro_office']; ?></div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Designation : </b><?php echo $res1['role']; ?></label> </div>
				</div>
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800">Sample Received From: </b><?php echo $res1['received_date']; ?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Letter Ref No : </b><?php echo $res1['letr_ref_no'] ?></label></div>
				</div>				
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800">Letter Date : </b><?php echo  $res1['letr_date']; //Remove/change date format on 22-05-2019 by Amol?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Received Date : </b><?php echo  $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></label></div>
				</div>
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800">	Container Type : </b><?php echo $res1['container_desc'] ?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Physical Appearance : </b><?php echo $res1['phy_appear_desc'] ?></label></div>
				</div>
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800">	Package Condition : </b><?php echo $res1['par_condition_desc'] ?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Sample Condition : </b><?php echo $res1['sam_condition_desc'] ?></label></div>
				</div>				
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800"> Type Of Sample : </b><?php echo $res1['sample_type_desc'] ?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Quantity : </b><?php echo $res1['sample_total_qnt'] ?></label></div>
				</div>				
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800"> Commodity Category  : </b><?php echo $res1['category_name'] ?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Commodity : </b><?php echo $res1['commodity_name'] ?></label></div>
				</div>			
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800"> Reference Source Code  : </b><?php echo $res1['ref_src_code'] ?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Expiry Month : </b><?php echo $res1['expiry_month'] ?></label></div>
				</div>			
				<div class="row">
					<div class="col-md-6 text-left"><label  class="control-label" for="sel1"><b style="font-weight: 800"> Year : </b><?php echo $res1['expiry_year'] ?></label>			</div>
					<div class="col-md-6 text-left"><label  class="control-label " for="sel1"><b style="font-weight: 800">Status : </b><?php if($res1['acc_rej_flg']=='A'){ echo "Accpeted"; } else if($res1['acc_rej_flg']=='P'){ echo "Pending"; } else if($res1['acc_rej_flg']=='R'){ echo "Rejected"; }  ?></label></div>
				</div>	
				<?php endforeach; ?>
				<?php
			break;

			case 'Coding Register': ?>
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Sample Code</th>
					<th>Commodity Name</th>
					<th>Sample Type</th>
					
				</tr>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo  $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>										
				</tr>
				<?php endforeach;
			break;

			case 'Samples alloted to Chemist for testing': ?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="40">S.No</th>
					<th width="80">Registered Date</th>
					<th width="80">Sample Code</th>
					<th width="80">Sample Type</th>
					<th width="160">Commodity Name</th>
					<!--<th>Chemist Name</th>-->
					<th width="80">Alloted Date</th>
				</tr>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
				?>
				<tr class="textCenter fontSizeSmall">
					<td width="40"><?php echo $i; ?></td>
					<td width="80"><?php echo  $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td width="80"><?php echo $res1['org_sample_code'] ?></td>
					<td width="80"><?php echo $res1['sample_type_desc'] ?></td>
					<td width="160"><?php echo $res1['commodity_name'] ?></td>
					<!--<td><?php //echo $res1['f_name'] .' '.$res1['l_name']?></td>-->
					<td width="80"><?php echo $res1['alloc_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>										
				</tr>
				<?php endforeach; ?>
				<tr class="fontSizeSmall">
					<td colspan="16"><b>Total Number of Samples :-<?php echo $i ; ?> </b></td>
				</tr>
				<?php 
			break;

			case 'Samples alloted to Chemist for Re-testing': ?>
				<tr class="textCenter fontSizeExtraSmall fontBold">
					<th width="30">S.No</th>
					<th width="60">Receieved Date</th>
					<th width="60">Sample Code</th>
					<th width="60">Sample Type</th>
					<th width="140">Commodity Name</th>
					<th width="120">Chemist Name</th>
					<th width="60">Allocate Date</th>					
				</tr>
				<?php								
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;										
				?>
				<tr class="textCenter fontSizeExtraSmall">
					<td width="30"><?php echo $i; ?></td>
					<td width="60"><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td width="60"><?php echo $res1['org_sample_code'] ?></td>
					<td width="60"><?php echo $res1['sample_type_desc'] ?></td>
					<td width="140"><?php echo $res1['commodity_name'] ?></td>
					<td width="120"><?php echo $res1['f_name']." ".$res1['l_name'] ?></td>
					<td width="60"><?php echo $res1['alloc_date']; //Remove/change date format on 22-05-2019 by Amol?></td>										
				</tr>
				<?php endforeach;
			break;

			case 'Test result submitted by chemist': ?>								
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="30">S.No</th>
					<th width="80">Receieved Date</th>
					<th width="100">Commodity Name</th>
					<!--<th>Sample Code</th>-->
					<th width="80">Chemist Code</th>
					<th width="180">Test Name</th>
					<th width="60">Result</th>
				</tr>
				<?php
					$i = 1;
					foreach ($all_data as $res1) {												
						if(!empty($res1['test_name'])) {
							$str1=trim($res1['test_name']);
							$str1=explode(',',$str1);
						} 													 
						if(!empty($res1['result'])) {
							$str2=trim($res1['result']);
							$str2=explode(',',$str2);
						}												
						$count=count($str1);
				?>
				<tr class="textCenter fontSizeSmall">
					<td rowspan="<?php echo $count;?>" width="30"><?php echo $i; ?></td>
					<td rowspan="<?php echo $count;?>" width="80"><?php echo  $res1['recby_ch_date']; ?></td>
					<td rowspan="<?php echo $count;?>" width="100"><?php echo $res1['commodity_name'] ?></td>
					<!--<td rowspan='<?php //echo $count;?>'><?php //echo $res1['sample_code'] ?></td>-->
					<td rowspan="<?php echo $count;?>" width="80"><?php echo $res1['chemist_code'] ?></td>																	
				</tr>
				<?php for($j=0;$j<count($str1)-1;$j++) { ?>										
				<tr class="textCenter fontSizeExtraSmall">								
					<td width="180"><?php echo $str1[$j] ?></td>
					<td width="60"><?php echo $str2[$j] ?></td>
				</tr>
				<?php } 
					$i++;
				?>
				<?php }				
			break;

			case 'Test result submitted by chemist with readings': 	?>								
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Commodity Name</th>
					<!--<th>Sample Code</th>-->
					<th>Chemist Code</th> 
				</tr>
				<?php
					$i = 1; 
					foreach ($all_data as $res1):
				?>
				<tr>
					<td ><?php echo $i; ?></td>
					<td ><?php echo  $res1['recby_ch_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td ><?php echo $res1['commodity_name'] ?></td>
					<!--<td rowspan='<?php echo $count;?>'><?php echo $res1['sample_code'] ?></td>-->
					<td ><?php echo $res1['chemist_code'] ?></td>
						<tr>
							<th>Test Name</th>														
							<th colspan='3'>Result</th>							
						</tr>											
					<?php	
						foreach ($res as $res2): 
							$str1=trim($res2['test_name']);
							$count=count($str1);
								if($res1['sample_code']==$res2['sample_code']){
								?>									
									<tr>
										<td ><?php echo $res2['test_name'] ?></td>
										<?php 	if($test[0]['test_code']==$res2['test_code']){																
													$alphas = range('a', 'z');
													?>
													<th >Field Name</th>
													<th>Readings</th>	
													<?php	foreach($test as $test1){ ?>																				
														<?php   foreach($alphas as $value){
																	if(isset($res2[0]["$value"])){ ?>
																		<tr>
																			<td>&nbsp;</td>
																			<td ><?php	echo $test1['field_name']; ?></td>
																			<td><?php	echo $res2["$value"]; ?></td>
																			<td>&nbsp;</td>
																		</tr>
																	<?php	}
																}
															}
													?>																	
										<?php   } else { ?>
												<td  rowspan='<?php echo $count;?>'><?php echo $res2['result'] ?></td>																	
										<?php   } ?>	
									</tr>	
					<?php 		}   
						endforeach;
					endforeach;
				?>
				</tr>	<?php 
			break;	

			case 'Tested Samples': ?>
				<tr class="textCenter fontSizeExtraSmall fontBold">
					<th width="30">S.No</th>
					<th width="80">Chemist Name</th>
					<th width="50">Accepted Date</th>
					<th>Sample Type</th>
					<th width="120">Commodity Name</th>
					<th width="50">Sample Code</th>
					<th width="50">Expected Date of Completion</th>
					<th width="50">Tests Completed on</th>
					<!--<th>Delayed by no. of Day's</th>-->
					<th width="40">Result</th>
				</tr>
				<?php
					$i=1; $sum=0;
					foreach ($sample_inward as $res1){
				?>
				<tr class="textCenter fontSizeExtraSmall">
					<td width="30"><?php echo $i; ?></td>
					<td width="80"><?php echo $res1['user_name'] ?></td>
					<td width="50"><?php echo $res1['recby_ch_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>
					<td width="120"><?php echo $res1['commodity_name'] ?></td>
					<td width="50"><?php echo $res1['org_sample_code'] ?></td>
					
					
					<td width="50"><?php echo $res1['expect_complt']; //Remove/change date format on 22-05-2019 by Amol ?></td>
					<td width="50"><?php if($res1['commencement_date']!=''){ echo $res1['commencement_date']; } else { echo $res1['commencement_date']; } //Remove/change date format on 22-05-2019 by Amol ?></td>
					<!--<td><?php //echo $res1['delay'] ?></td>-->
					<td width="40"><?php echo $res1['grade'] ?></td>
					<!--<td><?php //echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
				</tr>
				<?php $i++;  } ?>
				<tr class="fontSizeSmall">									
					<td colspan="16"><b>Total Number of Tested Samples :- <?php echo $i-1 ; ?></b></td>
				</tr>
				<?php 
			break;

			case 'Tests pending to be conducted on samples': ?>
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Sample Code</th>
					<th>Commodity Name</th>
					<th>Sample Type</th>
					<th>Lab Name</th>
					<th>User</th>
				</tr>
				<?php								
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>
					<td><?php echo $res1['lab_name'] ?></td>
					<!--<td><?php echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
				</tr>
				<?php endforeach;
			break;

			case 'Re-Tested Samples submitted by chemist': 	?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="30">S.No</th>
					<th width="80">Receieved Date</th>
					<th width="80">Sample Code</th>
					<th width="180">Commodity Name</th>
					<th width="100">Sample Type</th>
					<th width="60">Lab Name</th>
					<!--<th>User</th>-->
				</tr>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
				?>
				<tr class="textCenter fontSizeSmall">
					<td width="30"><?php echo $i; ?></td>
					<td width="80"><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td width="80"><?php echo $res1['org_sample_code'] ?></td>
					<td width="180"><?php echo $res1['commodity_name'] ?></td>
					<td width="100"><?php echo $res1['sample_type_desc'] ?></td>
					<td width="60"><?php echo $res1['lab_name'] ?></td>
					<!--<td><?php //echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
				</tr>
				<?php endforeach;
			break;

			case 'Commoditywise grading result': ?>
				<tr>
					<th>Sr.No.</th>
					<th>Name of Commodity</th>
					<th>Pass </th>
					<th>Fail </th>
					<th>Count</th>
				</tr>
				<?php 
					$i=1;
					foreach($sample_inward as $res1):
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $res1['commodity_name']; ?></td>
					<td><?php if($res1['grade']=="Pass"){ echo $res1['count']; } ?></td>
					<td><?php if($res1['grade']=="Fail"){echo $res1['count']; }  ?></td>									
					<td><?php echo $res1['count']; ?></td>
				</tr>								
				<?php $i++; endforeach; 
			break;

			case 'Re-Tested Samples': 	?>
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Sample Code</th>
					<th>Commodity Name</th>
					<th>Sample Type</th>
					<th>Lab Name</th>
				</tr>
				<?php											
					$i=1; $sum=0;
					foreach ($sample_inward as $res1){
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>
					<td><?php echo $res1['lab_name'] ?></td>
					<!--<td><?php echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
				</tr>
				<?php $i++;  } ?>								
				<tr>
					<td colspan="16"><b>Total Number of Re-tested Samples :- <?php echo $i-1 ; ?></b></td>
				</tr>
				<?php
			break;

			case 'Grading of Samples': 	?>
				<tr>
					<th>S.No</th>
					<th>Receieved Date</th>
					<th>Sample Code</th>
					<th>Commodity Name</th>
					<th>Sample Type</th>
					
				</tr>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<td><?php echo $res1['org_sample_code'] ?></td>
					<td><?php echo $res1['commodity_name'] ?></td>
					<td><?php echo $res1['sample_type_desc'] ?></td>
				</tr>
				<?php endforeach;
			break;

			case 'Sample Workflow': 	?>
				<?php 	
					foreach ($sample_inward as $res1):									
					$stage=$res1['stage'];									
					endforeach; 
				?>
				<tr>
					<th>S.No</th>
					<th>Source</th>
					<th>From User Name</th>
					<th>Destination</th>
					<th>To User Name</th>
					<th>Stage</th>
					<th>Transaction Date</th>
				</tr>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $res1['src_role']	.','.$res1['src_location'] ?></td>
					<td><?php echo $res1['src_user'] ?></td>
					<td><?php echo $res1['dst_role'].','.$res1['dst_location'] ?></td>
					<td><?php echo $res1['dst_user'] ?></td>
					<td><?php echo $res1['stage_desc'] ?></td>
					<td><?php echo $res1['tran_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
				</tr>
				<?php endforeach;
			break;

			case 'Sample received from RO/SO/RAL/CAL': ?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="40">Sr.No</th>
					<th width="70">Receieved Date</th>
					<!--<th>Lab/Office Name</th>-->
					<th width="70">Sample Code</th>
					<th width="140">Category Name</th>
					<th width="140">Commodity Name</th>
					<th width="70">Sample Type</th>
					<!--<th>User</th>-->
				</tr>
				<?php
					$i = 0;
					foreach ($sample_inward as $res1):
					$i++;
				?>
				<tr class="textCenter fontSizeSmall">
					<td width="40"><?php echo $i; ?></td>
					<td width="70"><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
					<!--<td><?php //echo $res1['user_flag'].",".$res1['ro_office'] ?></td>-->
					<td width="70"><?php echo $res1['org_sample_code'] ?></td>
					<td width="140"><?php echo $res1['commodity_name'] ?></td>
					<td width="140"><?php echo $res1['category_name'] ?></td>
					<td width="70"><?php echo $res1['sample_type_desc'] ?></td>
					<!--<td><?php //echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
				</tr>									
				<?php  endforeach; ?>
				<tr class="fontSizeSmall">
					<td colspan="16"><b>Total Number of Samples :- <?php echo $i ; ?></b></td>
				</tr>
				<?php 	
			break;

			case 'Samples Analyzed(Count)':  ?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="40">S.No</th>
					<th>Type of Samples</th>
					<th width="220">Commodity Name</th>
					<th>No of samples analyzed</th>
				</tr>
				<?php
					$i = 1;$sum=0; 
					foreach ($sample_inward as $res1) { 
				?>
				<tr class="textCenter fontSizeSmall">
					<td width="40"><?php echo $i;?></td>
					<td><?php echo $res1['sample_type_desc'];?></td>
					<td width="220"><?php echo $res1['commodity_name'];?> </td>
					<td><?php echo $res1['count_samples']; $sum+= $res1['count_samples'];?></td>										
				</tr>
				<?php $i++;  } ?>								
				<tr class="fontSizeSmall">
					<td colspan="16"><b>Total No of samples analyzed :- <?php echo $sum ; ?></b></td>
				</tr>
				<?php	
			break; 

			case 'Samples Alloted/Analyzed/Pending Report(RAL/CAL)':
				if($selection_type=='all') { ?>
				<tr class="textCenter fontSizeSmall fontBold">							
					<th>Sr No</th>
					<th>Lab Name </th>
					<th>Sample Alloted </th>
					<th>Sample Analyzed</th>
					<th>Pending Sample</th>
				</tr>
				<?php } else { ?>
					<tr class="textCenter fontSizeSmall fontBold">
						<th width="30">Sr No</th>
						<th width="165">Sample Alloted </th>
						<th width="165">Sample Analyzed</th>
						<th width="165">Pending Sample</th>
					</tr>
				<?php }
					if($selection_type=='all') {
						$i = 1;foreach ($all_data as $res1):?>
							<tr class="textCenter fontSizeSmall">
								<td><?php echo $i;?></td>
								<td><?php echo $res1['lab_name'];?></td>
								<td><?php echo $res1['allotment_count'];?></td>
								<td><?php echo $res1['analyzed_count'];?></td>
								<td><?php echo $res1['pending_count'];?></td>
							</tr>
					<?php
						$i++;
						endforeach;						
					} else {
				?>
				<tr class="textCenter fontSizeSmall">
					<td>1</td>
					<td><?php echo $allotment_count; ?></td>
					<td><?php echo $analyzed_count; ?></td>
					<td><?php echo $pending_count; ?></td>					
				</tr>
				<?php } ?>	
				<tr class="fontSizeSmall">														
					<td colspan="16"><b>Total Number of Samples :- <?php echo $allotment_count;?></b></td>														
				</tr>
				<?php		
			break;

			case 'No. of Pending & Rejected Samples':
				if($selection_type=='all') { ?>
					<tr>							
						<th>Sr No</th>
						<th>Office Name </th>
						<th>No.Of Sample Pending</th>
						<th>No. Of Sample Rejected</th>
					</tr>
				<?php } else { ?>
					<tr>
						<th>Sr No</th>
						<th>Office Name </th>
						<th>No.Of Sample Pending</th>
						<th>No. Of Sample Rejected</th>
					</tr>
				<?php }
					if($selection_type=='all') {
						$i = 1;foreach ($all_data as $res1):?>
						<tr>
							<td><?php echo $i;?></td>
							<td><?php echo $res1['lab_name'];?></td>
							<td><?php echo $res1['allotment_count'];?></td>
							<td><?php echo $res1['analyzed_count'];?></td>
							<td><?php echo $res1['pending_count'];?></td>
						</tr>
				<?php
						$i++;
						endforeach;
					} else {
				?>
					<tr>
						<td>1</td>
						<td>RO Officer</td>
						<td><?php echo $pending_count; ?></td>
						<td><?php echo $rejected_count; ?></td>
					</tr>
				<?php } ?>	
				<tr>
					<td colspan="16"><b>Total Number of Samples :- <?php echo $rejected_count + $pending_count ;?></b></td>									
				</tr>
				<?php		
			break;

			case 'No. of Check, Private & Research Samples analyzed by RALs' : ?>
				<tr>
					<th rowspan='2'>Sr No</th>
					<th rowspan='2'>Lab Name </th>
					<?php if(isset($month)){foreach($month as $key=>$val){ ?>
					<th colspan='4' class='text-center'><?php echo $val;?></th>
					<?php } }?>
				</tr>
				<tr>
				<?php foreach($month as $data){ ?>												
					<th>Check</th>
					<th>Research</th>
					<th>Challenged</th>
					<th>Other</th>
				</tr>
				<?php } 
					echo $all_data;
			break;?><?php
											
			case 'Sample Register':?>
				<thead>
					<tr style="font-size:6px; font-weight:bold; text-align:center;">
						<th width="15">Sr No</th>
						<th width="30">Date of Receipt of Samples</th>
						<th width="30">Sample Type</th>
						<th width="40">Name of Commodity</th>
						<th width="40">Nature of Commodity</th>
						<th width="40">Source of Sample</th>
						<th width="70">Reference / File No. </th>
						<th width="30">Sample Code </th>
						<th width="30">Quantity of Samples </th>
						<th width="20">Unit</th>
						<th width="30">Condition of Sealed </th>
						<th width="20">Code No. of CAL </th>
						
						<th width="30">Date of Issue of Samples </th>
						<th width="30">Date of Receipt of Results </th>
						<th width="30">Date of Comunication of Results </th>
						<th width="30">Remark</th>
					</tr>
				</thead>								
				<?php 
					$i=1; $sum= 0;$unit="";
					foreach($sample_inward as $res1) { 
				?>
					<tr style="font-size:6px;">
						<td class="text-right" width="15"><?php echo $i;?></td>
						<td width="30"><font size="5"><?php echo $res1['received_date'];//Remove/change date format on 22-05-2019 by Amol?></font></td>
						<td width="30"><?php echo $res1['sample_type_desc'];?></td>
						<td width="40"><?php echo $res1['commodity_name'];?></td>
						<td width="40"><?php echo $res1['category_name'];?></td>
						<td width="40"><?php echo $res1['user_flag'].",".$res1['ro_office'];?></td>
						<td width="70"><?php echo $res1['letr_ref_no'];?></td>
						<td width="30"><?php echo $res1['stage_sample_code'];?></td>
						<td class="text-right" width="30"><?php echo $res1['sample_total_qnt']; $sum+= $res1['sample_total_qnt'];?></td>
						
						<td width="20"><?php echo $res1['unit_weight'];  ?></td>
						<td width="30"><?php echo $res1['par_condition_desc'];?></td>
						<td class="text-right" width="20"><?php echo $res1['sample_qnt'];?></td>
						<td width="30"><font size="5"><?php echo $res1['letr_date'];//Remove/change date format on 22-05-2019 by Amol?></font></td>
						<td width="30"><font size="5"><?php if(null !== ($res1['dispatch_date'])){ echo $res1['dispatch_date']; } else{ echo $res1['dispatch_date']; } //Remove/change date format on 22-05-2019 by Amol?></font></td>
						<td width="30"><font size="5"><?php  if(null !== ($res1['grading_date'])){ echo $res1['grading_date']; } else{ echo $res1['grading_date']; }  //Remove/change date format on 22-05-2019 by Amol?></font></td>
						<td width="30"><?php echo $res1['remark'];?></td>									
					</tr>
				<?php $i++; } ?>								
					<tr style="font-size:7px;">											
						<td colspan="16"><b>Total Number of Samples :- <?php echo $i-1 ; ?></b></td>												
					</tr>							
					<?php 
			break;

			case 'Sample Registration Details': ?>
				<thead>							
					<tr>
						<th>Sr.No.</th>
						<th>Name of Commodity</th>
						<th>Grade</th>
						<th>Name and Address of authorised packer</th>
						<th>Lot No. </th>
						<th>Date of Packing</th>
						<th>Pack size</th>
						<th>TBL</th>
						<th>Name and address of shop/packer premises from where sample drawn</th>
						<th>Sample Size</th>
						<th>Date of Drawl Sample</th>
						<th>Name of Officer drawn the sample</th>
						<th>Code No. </th>
						<th>Date of sending sample to RAL</th>
						<th>Name of RAL</th>
						<th>Date of reciept of result of RAL</th>
						<th>Parameter wise analytical result of RALs</th>
						<th>Whether Pass/Misgraded</th>
						<th>Date of issue of misgrading report/warming</th>
						<th>Whether misgrading report challanged</th>
						<th>Date of sending challenged sample to CAL</th>
						<th>Date of receipt of result from CAL</th>
						<th>Parameter wise analytical results of CAL,Nagpur </th>
						<th>Date of C.A. suspended/cancelled</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i=1;
						foreach($sample_inward as $res1):													
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $res1['commodity_name']; ?></td>
						<td><?php echo $res1['grade'];  ?></td>
						<td><?php if($res1['pckr_nm']!=''){echo $res1['pckr_nm'].", ". $res1['pckr_addr']; } else { echo $res1['pckr_nm']; } ?></td>
						<td><?php echo $res1['lot_no']; ?></td>
						<td><?php echo $res1['pack_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
						<td><?php echo $res1['pack_size']; ?></td>
						<td><?php echo $res1['tbl']; ?></td>
						<td><?php echo $res1['shop_name'].",". $res1['shop_address'] ; ?></td>
						<td ><?php echo $res1['parcel_size']; ?></td>
						<td><?php if($res1['smpl_drwl_dt']!=''){ echo $res1['smpl_drwl_dt']; } else { echo $res1['smpl_drwl_dt']; } //Remove/change date format on 22-05-2019 by Amol ?></td>
						<td><?php echo $res1['f_name'].' '.$res1['l_name']?></td>
						<td><?php echo $res1['org_sample_code']; ?></td>
						<td><?php if($res1['dispatch_date']!=''){ echo $res1['dispatch_date'];  }else { echo $res1['dispatch_date']; } //Remove/change date format on 22-05-2019 by Amol?></td>
						<td>&nbsp;</td>
						<td><?php if($res1['ral_anltc_rslt_rcpt_dt']!=''){ echo $res1['ral_anltc_rslt_rcpt_dt']; }else { echo $res1['ral_anltc_rslt_rcpt_dt']; }  //Remove/change date format on 22-05-2019 by Amol ?></td>
						<td><?php echo $res1['anltc_rslt_chlng_flg']; ?></td>
						<td><?php echo $res1['grade']; ?></td>
						<td><?php if($res1['grading_date']!=''){ echo $res1['grading_date']; } else { echo $res1['misgrd_report_issue_dt']; } //Remove/change date format on 22-05-2019 by Amol ?></td>
						<td><?php echo $res1['misgrd_reason']; ?></td>
						<td><?php if($res1['chlng_smpl_disptch_cal_dt']!=''){ echo $res1['chlng_smpl_disptch_cal_dt']; } else { echo $res1['chlng_smpl_disptch_cal_dt']; } //Remove/change date format on 22-05-2019 by Amol ?></td>
						<td><?php if($res1['cal_anltc_rslt_rcpt_dt']!=''){ echo $res1['cal_anltc_rslt_rcpt_dt']; } else { echo $res1['cal_anltc_rslt_rcpt_dt']; } //Remove/change date format on 22-05-2019 by Amol ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>												
					<?php $i++; endforeach; ?>
					<tr>													
						<td colspan="24"><b>Total Number of Samples :- <?php echo $i-1 ; ?></b></td>													
					</tr>
				</tbody>
				<?php 
			break; 

			case 'Performance Report of RAL/CAL': ?>
				<tr class="textCenter fontSizeSmall fontBold">
					<th width="40">Sr No</th>
					<th width="120">Name Of Lab</th>
					<th width="145">Progressive Total(Sapmle analyze uptill now)</th>
					<th width="145">Total sample analyze during month</th>
					<th width="80">Remark</th>
				</tr>
				<?php	
						$sum1=0;
						$i = 1;foreach ($all_data as $res1):
				?>
				<tr class="textCenter fontSizeSmall">
					<td width="40"><?php echo $i;?></td>
					<td width="120"><?php echo $res1['lab_name'];?></td>
					<td width="145"><?php echo $res1['prog_sample']; $sum1+=$res1['prog_sample']; ?></td>
					<td width="145"><?php echo $res1['analyz_sample']; $sum1+=$res1['prog_sample']; ?></td>
					<td width="80"></td>
				</tr>
				<?php
					$i++;
					endforeach;
				?>	
				<tr class="fontSizeSmall">
					<td colspan="16"><b>Total No of samples analyzed and progress:- <?php echo $sum1 ; ?></b></td>
				</tr>
				<?php
			break;

			case 'Consolidated statement of Brought forward and carried forward of samples - test ankur': ?>
				<tr>
					<th class="text-center" colspan='10'>
						<?php  $monthNum = $month;
									$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
									echo $monthName; 
						?>
					</th>
				</tr>
				<tr>
					<th>Sr No</th>
					<th>Name Of Lab</th>
					<th>Name Of Post</th>
					<th>BF</th>
					<th>Received during month</th>
					<th>Total</th>
					<th>sample analyze during month original</th>
					<th>Duplicate</th>
					<th>Carried forward</th>
					<th>Remark</th>
				</tr>
				<?php 										 								
					$i = 1;
					$res1=$all_data;
					
					if($selection_type!='all') {
						$str1=trim($res1['post_name']);
						$str1=explode(',',$str1);					
						$count=count($str1);  ?>
						<tr>
							<td rowspan='<?php echo $count;?>'><?php echo $i;?></td>
							<td rowspan='<?php echo $count;?>'><?php echo $res1['ral_name'];?></td>
							<td rowspan=''></td>	
							<td rowspan='<?php echo $count;?>'><?php echo $res1['brought_for'];?></td>
							<td rowspan='<?php echo $count;?>'><?php echo $res1['received_count'];?></td>
							<td rowspan='<?php echo $count;?>'><?php echo $res1['total'];?></td>
							<td rowspan='<?php echo $count;?>'><?php echo $res1['analyz_in_month'];?></td>
							<td rowspan='<?php echo $count;?>'><?php echo $res1['analyz_in_month_repeat'];?></td>
							<td rowspan='<?php echo $count;?>'><?php echo $res1['carried_for'];?></td>
							<td rowspan='<?php echo $count;?>'></td>							
						</tr>
						<?php  for($j=0;$j<count($str1)-1;$j++) { ?>										
							<tr>								
								<td><?php echo $str1[$j]?></td>										
							</tr>
						<?php } 
					} else {
						foreach ($all_data as $res1) {
							$str1=trim($res1['post_name']);
							$str1=explode(',',$str1);
							//pr($str1);
							$count=count($str1);?>
							<tr>
								<td rowspan='<?php echo $count;?>'><?php echo $i;?></td>
								<td rowspan='<?php echo $count;?>'><?php echo $res1['ral_name'];?></td>
								<td rowspan=''></td>
								<td rowspan='<?php echo $count;?>'><?php echo $res1['brought_for'];?></td>
								<td rowspan='<?php echo $count;?>'><?php echo $res1['received_count'];?></td>
								<td rowspan='<?php echo $count;?>'><?php echo $res1['total'];?></td>
								<td rowspan='<?php echo $count;?>'><?php echo $res1['analyz_in_month'];?></td>
								<td rowspan='<?php echo $count;?>'><?php echo $res1['analyz_in_month_repeat'];?></td>
								<td rowspan='<?php echo $count;?>'><?php echo $res1['carried_for'];?></td>
								<td rowspan='<?php echo $count;?>'></td>															
							</tr>
							<?php for($j=0;$j<count($str1)-1;$j++) { ?>										
								<tr>													
									<td><?php echo $str1[$j]?></td>															
								</tr>
							<?php }
						}
					}  
				?>
				<?php 	
			break;	

			case 'Consolidated statement of Brought forward and carried forward of samples': ?>
				<tr class="textCenter fontSizeSix fontBold">
					<th>Monthly Report of samples Analysed & carry forward for the month of <?php $month_variable ?> as per OM NO. <?php $OM_number_variable; ?> Dated <?php $dated_variable; ?></th>
				</tr>
				<tr class="fontSizeSix">
					<th width="29" class="textLeft">S.NO</th>
					<th width="60" class="textLeft">DIVISION</th>
					<th width="90">Brought forward</th>
					<th width="90">Received</th>
					<th width="90">Total</th>
					<th width="90">Analysed</th>
					<th width="90">carry forward</th>
				</tr>
				<tr class="fontSizeSix">
					<th></th>
					<th></th>
					<th width="30">check</th>
					<th width="30">CHECK-APEX</th>
					<th width="30">challenge</th>
					<th width="30">check</th>
					<th width="30">CHECK-APEX</th>
					<th width="30">challenge</th>
					<th width="30">check</th>
					<th width="30">CHECK-APEX</th>
					<th width="30">challenge</th>
					<th width="30">check</th>
					<th width="30">CHECK-APEX</th>
					<th width="30">challenge</th>
					<th width="30">check</th>
					<th width="30">CHECK-APEX</th>
					<th width="30">challenge</th>
				</tr>
				<?php 
						$sample_variable = ['Oils&Fats', 'Spices', 'Food Grains'];
						$i = 0;
						foreach($sample_variable as $sample) { ?>
						<tr class="fontSizeSix">					
							<td><?php echo $i+1 ?></td>
							<td><?php echo $sample ?></td>
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
							<td></td>	
						</tr>
						<?php $i++;
						} 
					?>
				<tr class="fontSizeSix">
					<td>Total</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td>Total</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php 	
			break;	

			case 'Brought forward,Analysed and carried forward of samples': ?>
				<tr>
					<th class="textCenter fontSizeSmall fontBold" colspan='10'>
						<?php  
						if(null != $month) {
							$monthNum = $month;
							$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
							echo $monthName; 
						}							
						?>
					</th>
				</tr>
				<tr class="textCenter fontSizeExtraSmall fontBold">
					<th width="20">S. No.</th>
					<th width="60">Name of RAL</th>
					<th width="60">Name of Post</th>
					<th width="50">Sanctioned Strength</th>
					<th width="40">Staff Strength</th>
					<th width="20">BF</th>
					<th width="40">Received during month</th>
					<th width="25">Total</th>
					<th width="40">Analyzed during month Original</th>
					<th width="40">Duplicate</th>
					<th width="30">Repeat</th>
					<th width="40">Carried forward</th>
					<th width="74">Remarks</th>
				</tr>
				<?php 	
					$i = 1;
					$res1=$all_data;
					
					if($selection_type!='all') {
						$str1=trim($res1['post_name']);
						$str1=explode(',',$str1);
						$count=count($str1);
				?>
				<tr class="textCenter fontSizeExtraExtraSmall">
					<td rowspan="<?php echo $count;?>"  width="20"><?php echo $i;?></td>
					<td rowspan="<?php echo $count;?>" width="60"><?php echo $abc;?></td>
					<td width="60">Chief Chemist</td>
					<td width="50"></td>
					<td width="40"></td>
					<td rowspan="<?php echo $count;?>" width="20"><?php echo $res1['brought_for'];?></td>
					<td rowspan="<?php echo $count;?>" width="40"><?php echo $res1['received_count'];?></td>
					<td rowspan="<?php echo $count;?>" width="25"><?php echo $res1['total'];?></td>
					<td rowspan="<?php echo $count;?>" width="40"><?php echo $res1['analyz_in_month'];?></td>
					<td rowspan="<?php echo $count;?>" width="40"><?php echo $res1['analyz_in_month_repeat'];?></td>
					<td rowspan="<?php echo $count;?>"></td>
					<td rowspan="<?php echo $count;?>" width="40"><?php echo $res1['carried_for'];?></td>
					<td rowspan="<?php echo $count;?>" width="74"></td>
				</tr>
				<tr class="textCenter fontSizeExtraExtraSmall">
					<td>Sr. Chemist</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="textCenter fontSizeExtraExtraSmall">
					<td>Jr. Chemist</td>
					<td></td>
					<td></td>
				</tr>
				<?php
					} else {
						foreach ($all_data as $res1) {
							$str1=trim($res1['post_name']);
							$str1=explode(',',$str1);
							//pr($str1);
							$count=count($str1);?>
							<tr class="textCenter fontSizeExtraExtraSmall">
								<td rowspan="<?php echo $count;?>"><?php echo $i;?></td>
								<td rowspan="<?php echo $count;?>"><?php echo $res1['ral_name'];?></td>
								<td rowspan=""></td>
								<td rowspan="<?php echo $count;?>"><?php echo $res1['brought_for'];?></td>
								<td rowspan="<?php echo $count;?>"><?php echo $res1['received_count'];?></td>
								<td rowspan="<?php echo $count;?>"><?php echo $res1['total'];?></td>
								<td rowspan="<?php echo $count;?>"><?php echo $res1['analyz_in_month'];?></td>
								<td rowspan="<?php echo $count;?>"><?php echo $res1['analyz_in_month_repeat'];?></td>
								<td rowspan="<?php echo $count;?>"><?php echo $res1['carried_for'];?></td>
								<td rowspan="<?php echo $count;?>"></td>															
							</tr>
							<?php for($j=0;$j<count($str1)-1;$j++) { ?>										
								<tr>
									<td><?php echo $str1[$j]?></td>
								</tr>
							<?php }
						}
					}
				?>
				<?php
			break;

			case 'Sample Analyzed by Chemist': ?>
				<tr class="textCenter fontSizeExtraExtraSmall fontBold">
					<th width="20">Sr No</th>	
					<th width="60">Name of chemist whom allotted</th>
					<th width="60">Sample Received from</th>
					<th width="40">Letter no/Date</th>
					<th width="60">Name of the commodity</th>
					<th width="30">Sample Quantity</th>
					<th width="30">RO/SO Code</th>
					<th width="40">Date of receipt in lab</th>
					<!--<th>Visual Appearance</th>
					<th>Packing Method</th>
					<th>Condition of seal</th>-->
					<th width="30">Lab Code</th>										
					<th width="40">Date of allottment</th>	
					<th width="40">Date of receipt of result</th>
					<th width="40">Date of communication of report</th>
					<th width="40">Remark</th>										
				</tr>
				<?php
					$i = 1;
					foreach ($all_data as $res1):
					//	if(isset($res1[0]['f_name'])){
						if(!empty($res1['f_name'])) {
							$str1=trim($res1['f_name']);
							$str1=explode(',',$str1);
						}
						if(!empty($res1['alloc_date'])) {
							$str2=trim($res1['alloc_date']);
							$str2=explode(',',$str2);
						}
						
						$count=count($str1);
					//}	
				?>
					<tr class="textCenter fontSizeExtraExtraSmall">									
						<td rowspan="<?php echo $count;?>" width="20"><?php echo $i;?></td>
						<td></td>
						<td rowspan="<?php echo $count;?>" width="60"><?php echo $res1['lab_name'].",".$res1['lab'];?></td>
						<td rowspan="<?php echo $count;?>" width="40"><?php echo $res1['letr_ref_no']; ?><br/><font size="6"><?php echo $res1['letr_date']; //Remove/change date format on 22-05-2019 by Amol ?></font></td>
						<td rowspan="<?php echo $count;?>" width="60"><?php echo $res1['commodity_name'];?></td>
						<td rowspan="<?php echo $count;?>" width="30"><?php echo $res1['sample_total_qnt'];?></td>
						<td rowspan="<?php echo $count;?>" width="30"><?php echo $res1['stage_sample_code'];?></td>
						<td rowspan="<?php echo $count;?>" width="40"><font size="6"><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></font></td>
						<!--<td rowspan="<?php //echo $count;?>"><?php //echo $res1['sam_condition_desc'];?></td>
						<td rowspan="<?php //echo $count;?>"><?php //echo $res1['container_desc'];?></td>
						<td rowspan="<?php //echo $count;?>"><?php //echo $res1['par_condition_desc'];?></td>-->
						<td rowspan="<?php echo $count;?>" width="30"><?php echo $res1['lab_code'];?></td>										
						<td></td>										
						<td rowspan="<?php echo $count;?>" width="40"><font size="6"><?php if($res1['grading_date']!=''){ echo $res1['grading_date']; }else { echo $res1['grading_date']; } //Remove/change date format on 22-05-2019 by Amol?></font></td>
						<td rowspan="<?php echo $count;?>" width="40"><font size="6"><?php  if($res1['grading_date']!=''){ echo $res1['grading_date']; } else { echo $res1['grading_date']; } //Remove/change date format on 22-05-2019 by Amol?></font></td>
						<td rowspan="<?php echo $count;?>" width="40"><?php echo $res1['remark'];?></td>
					</tr>
				<?php for($j=0;$j<count($str1)-1;$j++) { ?>										
					<tr class="textCenter fontSizeExtraExtraSmall">										
						<td><?php echo $str1[$j]?></td>
						<td><font size="6"><?php echo $str2[$j]; //Remove/change date format on 22-05-2019 by Amol?></font></td>
					</tr>
				<?php } ?>
				<?php
					$i++;												
					endforeach;
				?>
				<tr class="fontSizeExtraSmall">									
					<td colspan="16"><b>Total Number of Samples :- <?php echo $i-1 ; ?></b></td>									
				</tr>
				<?php
			break;

			case 'Chemist –wise sample analysis - test ankur':?>
				<tr>
					<th rowspan='2'>Sr No</th>
					<th rowspan='2'>Lab Name </th>
					<th rowspan='2'>Chemist name </th>
					<th colspan='4' class='text-center'><?php  $monthNum = $month;
						$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
						echo $monthName; //if(isset($month)){  $monthName = date("F", mktime(0, 0, 0, $month, 10)); echo $monthName; } else { echo $month; } ?>
					</th>
				</tr>
				<tr>
					<th>Check</th>
					<th>Research</th>
					<th>Challenged</th>
					<th>Other</th>
				</tr>
				<?php echo $all_data;
			break;

			case 'Chemist –wise sample analysis':?>
				<tr class="textCenter fontSizeExtraExtraSmall">
					<th>Details of the samples Analysed by chemist, CAL, Nagpur for the month of <?php $month_variable; ?></th>
				</tr>
				<tr class="textCenter fontSizeExtraExtraSmall">
					<th>TYPE OF SAMPLES</th>
				</tr>
				<tr class="fontSizeExtraExtraExtraSmall">
					<th width="20">S.NO</th>
					<th width="84">NAME OF THE CHEMIST</th>
					<th width="25">no. of working days</th>
					<th width="15">CHECK</th>
					<th width="15">CHECK APEX</th>
					<th width="15">CHALLENGE</th>
					<th width="15">ILC</th>
					<th width="15">RESEARCH</th>
					<th width="15">RETESTING</th>
					<th width="15">OTHER</th>
					<th width="25">Project samples</th>
					<th width="25">ANALYSED ON INSTRUMENT</th>
					<th width="60">NAME OF THE COMMODITY</th>
					<th width="40">N.O.S</th>
					<th width="30">No. of parametres</th>
					<th width="60">OTHER WORK</th>
					<th width="20">TOTAL NOS</th>
					<th width="45">WHETHER ANALAYZED AS PER NORMS</th>
				</tr>
				<?php
					$i=1;
					$chemists = ['PK Roy', 'Sudha Murti'];
					foreach($chemists as $chemist) {
				?>
				<tr class="fontSizeSix">
					<td width="20" rowspan="10"><?php echo $i; ?></td>
					<td width="84" rowspan="10"><?php echo $chemist; ?></td>
					<td width="25" rowspan="10"><?php echo(rand(20,31)); ?></td>
					<td width="15" rowspan="10"></td>
					<td width="15" rowspan="10"></td>
					<td width="15" rowspan="10"></td>
					<td width="15" rowspan="10"></td>
					<td width="15" rowspan="10"></td>
					<td width="15" rowspan="10"></td>
					<td width="15" rowspan="10"></td>
					<td width="25" rowspan="10"></td>
					<td width="25" rowspan="10"></td>
					<td colspan="2">Check samples</td>
					<td width="30" rowspan="10"><?php echo(rand(1,31)); ?></td>
					<td width="60" rowspan="10"></td>
					<td width="20" rowspan="10"><?php echo(rand(1,31)); ?></td>
					<td width="45" rowspan="10"></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>CHALLENGE SAMPLE</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>samples on instrument</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>ILC samples</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>Re-TEST samples</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td width="20" rowspan="14"></td>
					<td width="84" rowspan="14"></td>
					<td width="25" rowspan="14"></td>
					<td width="15" rowspan="14"></td>
					<td width="15" rowspan="14"></td>
					<td width="15" rowspan="14"></td>
					<td width="15" rowspan="14"></td>
					<td width="15" rowspan="14"></td>
					<td width="15" rowspan="14"></td>
					<td width="15" rowspan="14"></td>
					<td width="25" rowspan="14"></td>
					<td width="25" rowspan="14"></td>
					<td colspan="2">CHALLENGE SAMPLE</td>
					<td width="30" rowspan="14"></td>
					<td width="60" rowspan="14"></td>
					<td width="20" rowspan="14"></td>
					<td width="45" rowspan="14"></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>check samples</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>Re Testing samples</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>Research samples</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>ILC samples</td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
				</tr>
				<br>
				<?php
				$i++; } 
			break;

			case 'Chemistwise Sample Pending & Analyze': ?>
				<tr>
					<th rowspan='2'>Sr No</th>
					<th rowspan='2'>Lab Name </th>
					<th rowspan='2'>Chemist name </th>
					
				</tr>
				<tr>
					<th>Alloted</th>
					<th>Analysed</th>
					<th>Pending</th>
				</tr>
				<?php
					echo $all_data;
			break;

			case 'Information of Annexure E along with MPR divison wise': ?>
				<tr class="fontSizeSix fontBold">
					<th width="15">S.No</th>					
					<th width="60">Name of the chemist</th>					
					<th width="90">No of check/challenge samples (BF)</th>					
					<th width="90">No of check/challenge samples alloted</th>					
					<th width="100">no of check/challenge samples analysed in (commodity wise)</th>					
					<th width="40">No. of parametres analysed</th>					
					<th width="90">No of samples pending (CF)</th>					
					<th width="50">remarks/reason for carry forward</th>					
				</tr>
				<tr class="fontSizeSix">
					<td width="15"></td>
					<td width="60"></td>
					<td width="30">check samples</td>
					<td width="30">challenge samples</td>
					<td width="30">CHECK-APEX</td>
					<td width="30">check samples</td>
					<td width="30">challenge samples</td>
					<td width="30">CHECK-APEX</td>
					<td width="50">commodity</td>
					<td width="25">no</td>
					<td width="25">NO. OF PARAMETRES IN COMMODITY</td>
					<td width="40"></td>
					<td width="30">check samples</td>
					<td width="30">challenge samples</td>
					<td width="30">CHECK-APEX</td>
					<td width="50"></td>
				</tr>
				<tr class="fontSizeSix">
					<td width="15" rowspan="5"></td>
					<td width="60" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td colspan="2">Check samples</td>
					<td width="25"></td>
					<td width="40"></td>
					<td width="30" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td width="30" rowspan="5"></td>
					<td width="50" rowspan="5"></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>CHALLENGE SAMPLE</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>TOTAL</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td width="15" rowspan="4"></td>
					<td width="60" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td colspan="2">CHALLENGE SAMPLE</td>
					<td width="25"></td>
					<td width="40"></td>
					<td width="30" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td width="30" rowspan="4"></td>
					<td width="50" rowspan="4"></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>TOTAL</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			break;

			case 'No. of Remnent/Research/other/ILC samples Analyzed (Commodity wise)': ?>
				<tr class="textCenter fontSizeSix fontBold">
					<th>No. of Remanent/Research/other/ILC samples Analyzed (Commodity wise) Month of <?php $month_variable ?></th>
				</tr>
				<tr class="fontSizeSix fontBold">
					<th width="15" rowspan="2">S.No</th>					
					<th width="60" rowspan="2">Name of the chemist</th>					
					<th width="60" rowspan="2">Remanent samples/other samples</th>					
					<th width="60" rowspan="2">Research samples</th>					
					<th width="40" rowspan="2">Re-Test samples</th>					
					<th width="40" rowspan="2">ILC samples</th>					
					<th width="160" colspan="3">no of Remanent/Other/ILC samples Analysed (commodity wise)</th>					
					<th width="45" rowspan="2">No. of parameters analysed</th>					
					<th width="59" rowspan="2">Total No of samples</th>					
				</tr>
				<tr class="fontSizeSix fontBold">
					<th width="80">commodity</th>
					<th width="30">no</th>
					<th width="50">NO. OF PARAMETERS IN COMMODITY</th>
				</tr>
				<tr class="fontSizeSix">
					<td rowspan="9"></td>
					<td rowspan="9"></td>
					<td rowspan="9"></td>
					<td rowspan="9"></td>
					<td rowspan="9"></td>
					<td rowspan="9"></td>
					<td>samples on instrument</td>
					<td></td>
					<td></td>
					<td></td>
					<td rowspan="9"></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>ILC samples</td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>Research samples</td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>Re-TEST samples</td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>TOTAL</td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td rowspan="7"></td>
					<td rowspan="7"></td>
					<td rowspan="7"></td>
					<td rowspan="7"></td>
					<td rowspan="7"></td>
					<td rowspan="7"></td>
					<td>Re-TEST samples</td>
					<td></td>
					<td></td>
					<td></td>
					<td rowspan="7"></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>Food safety parametres</td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td></td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>Other samples</td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeSix">
					<td>TOTAL</td><td></td><td></td><td></td>
				</tr>
				<?php
			break;

			case 'Details of samples analyzed by RALs Annexure B': ?>
				<tr class="fontSizeExtraSmall fontBold">
					<th width="30" class="textCenter">Sr. No.</th>					
					<th width="150">Type of Sample</th>					
					<th width="100">No. of Samples analyzed</th>					
					<th width="250">Code no. of Samples analyzed</th>				
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td class="textCenter">(A)</td>
					<td colspan="3">Check Sample :</td>
				</tr>
				<?php
					$i=1;
					$samples = ['Blended Oil', 'Mustard Oil', 'Turmeric Powder', 'Chilli Powder', 'Coriander Powder', 'Ghee', 'Fennel powder', 'Mix Masala', 'Besan', 'Masoor Whole',
											'Kala Chana', 'Safed Chana', 'Arhar', 'Rajma', 'Fennel Whole', 'Coriander Whole', 'Sattu', 'Ajwain', 'Mastard Seed'];
					foreach($samples as $sample) { ?>
					<tr class="fontSizeExtraSmall">
						<td class="textCenter"><?php echo $i; ?></td>
						<td><?php echo $sample; ?></td>
						<td></td>
						<td></td>
					</tr><?php
					$i++; }
				?>
				<tr class="fontSizeExtraSmall"><td></td><td></td><td></td><td></td></tr>
				<tr class="textCenter fontSizeExtraSmall fontBold">
					<td></td><td>TOTAL</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall"><td></td><td></td><td></td><td></td></tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td class="textCenter">(B)</td><td>Inter laboratory Comparison samples</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td></td><td>TOTAL</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td class="textCenter">(C)</td><td>Private Sample</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td></td><td>TOTAL</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td class="textCenter">(D)</td><td>Internal Check Samples</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td></td><td></td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td class="textCenter">(E)</td><td>Project Samples</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td class="textCenter">(F)</td><td>Repeat Samples</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td class="textCenter">(G)</td><td>PT Sample</td><td></td><td></td>
				</tr>
				<tr class="fontSizeExtraSmall fontBold">
					<td></td><td class="textRight">Total</td><td></td><td></td>
				</tr>
				<?php
			break;

			case 'Bifercation of samples analyzed by RAL': ?>
				<tr class="fontSizeSeven fontBold">
					<th width="20" class="textCenter" rowspan="2">Sr. No.</th>					
					<th width="70" rowspan="2">Name of the Chemist</th>					
					<th width="260" class="textCenter">No. of Samples Analyzed in the month of <?php $month_variable; ?></th>					
					<th width="40" rowspan="2">No. of working Days (20)</th>				
					<th width="100" rowspan="2">Any Other work Attended</th>				
					<th width="48" rowspan="2">Whether Samples analysed as per Norms</th>				
				</tr>
				<tr class="textCenter fontSizeSeven fontBold">
					<td width="30">Check</td>
					<td width="40">Private sample</td>
					<td width="40">Samples from CAL</td>
					<td width="40">Research</td>
					<td width="40">Proficiency / ILC</td>
					<td width="30">Internal Check</td>
					<td width="40">Total</td>
				</tr>
				<?php										
					for($i=1; $i<=5; $i++) { ?>
					<tr class="fontSizeSeven">
						<td width="20" class="textCenter"><?php echo $i . '.'; ?></td>
						<td width="70"></td>
						<td width="30"></td>
						<td width="40"></td>
						<td width="40"></td>
						<td width="40"></td>
						<td width="40"></td>
						<td width="30"></td>
						<td width="40"></td>
						<td width="40"></td>
						<td width="100"></td>
						<td width="48"></td>
					</tr><?php
					}
				?>
				<tr class="fontSizeSeven fontBold">
					<td width="20"></td>
					<td width="70">Total:</td>
					<td width="30"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="30"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="100"></td>
					<td width="48"></td>
				</tr>				
				<?php
			break;

			case 'Perticulars of samples received and analyzed by RAL Annexure D': ?>
				<tr class="fontSizeExtraSmall fontBold">
					<th>Particulars of samples received and analyzed during the month of <?php $month_variable; ?></th>
				</tr>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="20" class="textLeft" rowspan="2">Sr. No.</th>					
					<th width="80" class="textLeft" rowspan="2">Commodity</th>					
					<th width="40" rowspan="2">Brought forward (1)</th>					
					<th width="60" rowspan="2">Samples received During the month (2)</th>				
					<th width="50" rowspan="2">Total (3)</th>				
					<th width="80" rowspan="2">Sample analysed during the month (4)</th>				
					<th width="50" rowspan="2">Progressive Total of samples received During the year (5)</th>				
					<th width="60" rowspan="2">Progressive Total of samples analysed During the year (6)</th>				
					<th width="40" rowspan="2">Sample carried forwarded (7)</th>				
					<th width="59" rowspan="2">Remarks (8)</th>				
				</tr>		
				<tr><th></th></tr>
				<tr class="textCenter fontSizeSeven fontBold">
					<td width="20" class="textLeft">(A)</td>
					<td width="80" class="textLeft">Check Sample:</td>
					<td width="40"></td>
					<td width="60"></td>
					<td width="50"></td>
					<td width="40">Original</td>
					<td width="40">Duplicate</td>
					<td width="50"></td>
					<td width="60"></td>
					<td width="40"></td>
					<td width="59"></td>
				</tr>
				<?php
					$i=1;
					$samples = ['Mustard Oil', 'Blended Oil', 'Sunflower Oil', 'Ghee', 'Honey', 'Turmeric Powder', 'Chili Powder', 'Coriander Powder', 'Curry Powder', 'Fennel Powder',
											'Ginger Powder', 'Cumin Powder', 'Besan', 'Wheat Atta', 'Pulses', 'Mix Masala', 'Ajowan', 'Fennel Whole', 'Fenugreek Whole', 'Mustard Seed',
											'Coriander Whole', 'Cumin Whole', 'Chana Sattu', 'Raw Mango Powder', 'Butter', 'Fatspread'];
					foreach($samples as $sample) { ?>
						<tr class="fontSizeSeven textCenter">
							<td class="textRight"><?php echo $i; ?></td>
							<td class="textLeft"><?php echo $sample; ?></td>
							<td><?php echo(rand(0,300)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,350)); ?></td>
							<td><?php echo(rand(0,120)); ?></td>
							<td><?php echo(rand(0,180)); ?></td>
							<td><?php echo(rand(0,800)); ?></td>
							<td><?php echo(rand(0,500)); ?></td>
							<td><?php echo(rand(0,200)); ?></td>
							<td></td>
						</tr><?php
					$i++; }
				?>
				<tr class="fontSizeSeven fontBold">
					<td></td>
					<td>Total</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSeven fontBold">
						<td>(B)</td>
						<td>Any other type of samples</td>
						<td colspan="9"></td>
				</tr>
				<?php
					$sample_types = ['Inter Lab. Comparison Samples', 'Internal Quality Check samples'];
					foreach($sample_types as $sample_type) {
				?>	
				<tr class="fontSizeSeven textCenter">
						<td></td>
						<td class="textLeft"><?php echo $sample_type; ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td><?php echo(rand(0,50)); ?></td>
						<td></td>
				</tr><?php 
				} ?>
				<tr class="fontSizeSeven fontBold">
					<td></td>
					<td>Total:</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSeven fontBold">
						<td>(C)</td>
						<td>Private Sample</td>
						<td colspan="9"></td>
				</tr>	
				<?php
					$i=1;
					$private_samples = ['Turmeric Powder', 'Ghee'];
					foreach($private_samples as $private_sample) { ?>
						<tr class="fontSizeSeven textCenter">
							<td class="textRight"><?php echo $i; ?></td>
							<td class="textLeft"><?php echo $private_sample; ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td></td>
						</tr><?php
					$i++; }
				?>
				<tr class="fontSizeSeven fontBold">
					<td></td>
					<td>Total:</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSeven fontBold">
					<td>(D)</td>
					<td>Proficiency Test</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSeven fontBold">
					<td></td>
					<td>Total</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSeven fontBold">
						<td>(E)</td>
						<td>Project Sample</td>
						<td colspan="9"></td>
				</tr>
				<?php
					$i=1;
					$project_samples = ['Soya Flour', 'Poha', 'Basil Seeds', 'Dill Seeds', 'Oregano', 'Ragi flour', 'Anjeer', 'Quinoa'];
					foreach($project_samples as $project_sample) { ?>
						<tr class="fontSizeSeven textCenter">
							<td class="textRight"><?php echo $i; ?></td>
							<td class="textLeft"><?php echo $project_sample; ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td></td>
						</tr><?php
					$i++; }
				?>
				<tr class="fontSizeSeven fontBold">
					<td></td>
					<td>Total</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSeven fontBold">
						<td>(F)</td>
						<td>Check Samples under Special Drive</td>
						<td colspan="9"></td>
				</tr>
				<?php 
					$i=1;
					$special_samples = ['Mustard Oil', 'BEVO'];
					foreach($special_samples as $special_sample) { ?>
						<tr class="fontSizeSeven textCenter">
							<td class="textRight"><?php echo $i; ?></td>
							<td class="textLeft"><?php echo $special_sample; ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td><?php echo(rand(0,50)); ?></td>
							<td></td>
						</tr><?php
					$i++; }
				?>
				<tr class="fontSizeSeven fontBold">
					<td></td>
					<td>Total</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="fontSizeSeven fontBold">
					<td></td>
					<td>Total (A+B+C+D+E+F)</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
			break;

			case 'Monthly status of analyzed of Check samples and pending samples of RAL Annexure E': ?>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="20" class="textLeft" rowspan="2">Sr. No.</th>					
					<th width="80" class="textLeft" rowspan="2">Name of Chemist</th>					
					<th width="40" rowspan="2">No. of Check samples BF</th>					
					<th width="40" rowspan="2">No. of Check samples alloted</th>				
					<th width="100">No. of Check samples analyzed (Commodity wise)</th>				
					<th width="40" rowspan="2">No. of parameters analyzed</th>				
					<th width="40" rowspan="2">Total</th>				
					<th width="40" rowspan="2">No. of samples pending (CF)</th>				
					<th width="40" rowspan="2">Remark</th>				
					<th width="59" rowspan="2">(Reason for CF)</th>				
				</tr>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="60">Commodity</th>
					<th width="40">No.</th>
				</tr>
				<tr class="fontSizeSeven textCenter">
					<td width="20" rowspan="2"></td>
					<td width="80" rowspan="2"></td>
					<td width="40" rowspan="2"></td>
					<td width="40" rowspan="2"></td>
					<td width="60"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="40" rowspan="2"></td>
					<td width="40" rowspan="2"></td>
					<td width="40" rowspan="2"></td>
					<td width="59" rowspan="2"></td>
				</tr>
				<tr class="fontSizeSeven textCenter">
						<td></td>
						<td></td>
						<td></td>
				</tr>
				<tr class="fontSizeSeven textCenter">
					<td width="20"></td>
					<td width="80">Total</td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="60"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="40"></td>
					<td width="59"></td>
				</tr>
				<?php
			break;

			case 'Commodity wise details of samples analyzed by RAL Annexure E': ?>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="30" class="textLeft">S. No.</th>					
					<th width="100" class="textLeft">Commodity</th>					
					<th width="50">BF from the month January</th>					
					<th width="50">Sample Received for the month February</th>				
					<th width="50">Total samples received (3+4)</th>				
					<th width="60">Sample Analyzed</th>				
					<th width="140">Grade</th>				
					<th width="50">CF for the month <?php $month; ?></th>		
				</tr>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="30"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="60"></th>
					<th width="70">Conformed to Standard</th>
					<th width="70">Mis-graded</th>
					<th width="50"></th>
				</tr>
				<tr class="textCenter fontSizeSeven fontBold">
					<th width="30">1</th>
					<th width="100">2</th>
					<th width="50">3</th>
					<th width="50">4</th>
					<th width="50">5</th>
					<th width="60">6</th>
					<th width="70">7</th>
					<th width="70">8</th>
					<th width="50">9</th>
				</tr>
				<?php
					for($i=1; $i<=85; $i++) { ?>
						<tr class="textCenter fontSizeSeven">
							<td class="textRight"><?php echo $i; ?></td>
							<td class="textLeft"><?php echo 'Commodity Code'.(rand(0,50)); ?></td>
							<td><?php echo (rand(0,50)); ?></td>
							<td><?php echo (rand(0,50)); ?></td>
							<td><?php echo (rand(0,50)); ?></td>
							<td><?php echo (rand(0,50)); ?></td>
							<td><?php echo (rand(0,50)); ?></td>
							<td><?php echo (rand(0,50)); ?></td>
							<td><?php echo (rand(0,50)); ?></td>
						</tr><?php
					}
				?>
				<tr class="fontSizeSeven fontBold">
					<th width="30"></th>
					<th width="100" class="textLeft">Total</th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="50"></th>
					<th width="60"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="50"></th>
				</tr>
				<?php
			break;
			?>
		<?php } ?>
	</table>



	
	