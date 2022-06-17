<?php ?>
<style>
.table-bordered{border: 1px solid #ddd;}

</style>
	<div id="myModal" class="modal" >
		<div class="modal-dialog modal-lg">
					<!-- Modal content-->
			<div class="modal-content" id="modalContent">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<div class="row">
							<div class="col-md-12">
								<div id="pageNavPosition"  align="center"> </div>	
								<!--<button type="button" class="btn btn-default" onclick="myFunction()">Print</button>-->
								<!--<a type="button" href="<?php echo $this->request->getAttribute('webroot'); ?>Reports/pdf/<?php echo $report_name; ?>/D" class="btn btn-warning btn-xs pull-left"><?php echo __('lblfulldownload'); ?></a>-->
								<!--<button type="button" class="btn btn-primary" id="pdf" >Pdf</button>-->
								<p class="text-right"><b>Date:</b><?php echo date("d/m/Y"); ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-md-12 ">
								<h5 style="text-align:center">भारत सरकार/Goverment of India</h5>
								<h5 style="text-align:center">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
								<h5 style="text-align:center">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
								<h5 style="text-align:center">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
								
								<!--<h5 class="text-center"><?php echo $_SESSION['user_flag'].','.$_SESSION['ro_office']; ?></h5>-->
								<?php if(!empty($sample_inward['0']['user_flag']) && null !== ($ral_lab_name) && $ral_lab_name=='RAL') {?>
										<h5 align="center"><b>प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , <?php echo $sample_inward['0']['ro_office'];?></b></h5>
								<?php }
									else if(!empty($sample_inward['0']['user_flag']) && null !== ($ral_lab_name) && $ral_lab_name=='CAL') { ?>
										<h5 align="center"><b>केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory</b></h5>
										<h5 align="center">उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010</h5>								
								<?php }
									else if(!empty($sample_inward['0']['user_flag']) && $sample_inward['0']['user_flag']=='RO') { ?>
										<h5 align="center"><b>प्रादेशिक कार्यालय / Regional Office , <?php echo $sample_inward['0']['ro_office'];?></b></h5>
								<?php }	
									else if(!empty($sample_inward['0']['user_flag']) && $sample_inward['0']['user_flag']=='SO'){ ?>
										<h5 align="center"><b>उप-कार्यालय / Sub Office , <?php echo $sample_inward['0']['ro_office'];?></b></h5>
								<?php }  ?>								
							</div>
						</div>							
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-md-12">
								<h4 align="center"><b> 
								<?php switch($report_name){
									case '	':
										echo "Sample received from ".$ral_lab_name.','.$sample_inward['0']['ro_office'];
									break ;
									
									case 'Samples Alloted/Analyzed/Pending Report(RAL/CAL)':
										echo "Samples Alloted/Analyzed/Pending at ".$abc;;
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
									default: 
										echo $report_name ?></b></h4>
								<?php } ?>	
							</div>
						</div>
						<div class="row">
							<div class="col-md-5">
								<?php if($from_date!='' || $to_date!=''){ ?>
									<h5>Sample Received in the Period From  :<b><?php echo $from_date; //Remove/change date format on 22-05-2019 by Amol ?></b> To :<b><?php echo $to_date; //Remove/change date format on 22-05-2019 by Amol ?></b></h5>								
								<?php } ?>
							</div>
								
							<?php if(null !== ($commodity) && $commodity!=''){  ?>							
								<?php if(null !== ($sample_inward['0']['commodity_name'])){ ?>
									<div class="col-md-3">
										<h5>Commodity Name : <b><?php echo $sample_inward['0']['commodity_name']; ?></b></h5>
									</div>
								<?php } ?>							
							<?php }  ?>
														
							<?php if(!empty($sampleo) && $sampleo!=''){  ?>
								<div class="col-md-3">
									<?php if(null !== ($sample_inward['0']['sample_type_desc'])){ ?>
										<h5>Sample Type :<b> <?php echo $sample_inward['0']['sample_type_desc']; ?></b></h5>
									<?php }  ?>
								</div>	
							<?php }	?>
							<?php $f_name = ''; $l_name = ''; ?> 								
							<?php if(null !== ($user1)){ ?>
								<?php if(!empty($sample_inward['0']['f_name']) && !empty($sample_inward['0']['role'])){ ?>
								<div class="col-md-3">
									<h5>Chemist Name : <b><?php echo $sample_inward['0']['f_name'].' '.$sample_inward['0']['l_name'] .'<br>('.$sample_inward['0']['role'].')';  ?></b></h5>
								</div>
							<?php } else if(null !== ($f_name) && null !== ($l_name) && $f_name!='' && $l_name!=''){ ?>
								<div class="col-md-3">
									<h5><b>User Name</b> : <?php echo $f_name.' '.$l_name;  ?></h5>
								</div>					
							<?php } } ?>
							
							<?php if(null !== ($abc) && $abc!=''){ ?>
								<div class="col-md-3">
									<h5>Lab Name : <b><?php echo $abc; ?></b></h5>
								</div>
							<?php } ?>
							
							<?php if(!empty($sample_inward['0']['location_desc']) && null !== ($ral_lab_name) && $ral_lab_name!=''){ ?>
								<div class="col-md-3">
									<?php if($ral_lab_name=="RO" || $ral_lab_name=="SO"){ ?>
										<h5><b>Office Name</b> : <?php echo $ral_lab_name.','.$sample_inward['0']['location_desc']; ?></h5>
									<?php } else { 
													switch($report_name){
														case 'Sample received from RO/SO/RAL/CAL': ?>
															<h5><b>Lab Name</b> : <?php echo $ral_lab_name.','.$sample_inward['0']['location_desc']; ?></h5>
														<?php 
															break ;
															default:   
														?>
															<h5><b>Lab Name</b> : <?php echo $abc; ?></h5>
													<?php } ?>
									<?php } ?>
								</div>
							<?php }   ?>
									
							<?php if($sample_code!=''){ ?>
								<div class="col-md-3">
									<h5><b>Sample Code</b> : <?php echo $sample_code; ?></h5>
								</div>
							<?php } ?>								
						</div>							
				</div>				
			    <div class="col-xs-12 col-sm-8 col-md-12 col-sm-offset-2 col-md-offset-0 " id="reportDiv">
                    <div class="table-responsive" id="avb">
                        <table class="table table-bordered display" id="example" cellspacing="0" cellpadding="1" border="0.5">       
							<?php  switch($report_name){
										case 'Category-wise Received Sample': 	?>
											<tr>
												<th>S.No</th>
												<th>Category Name</th>
												<th >No. of Received Sample</th>											   
											</tr>											
											<?php											
												$i = 1;
												foreach ($sample_inward as $res1): 
											?>		
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $res1['category_name'] ?></td>
													<td><?php echo $res1['count'] ?></td>													
												</tr>
											<?php													
												$i++;
												endforeach;
										break;
										case 'Commodity-wise consolidated report of lab': 	
											?>
											<tr>
												<th>S.No</th>
												<th>Commodity Name</th>
												<th >Brought Forward</th>
												<th>Analyzed</th>
												<th >Carried Forward</th>												
											</tr>								
											<?php
												$i = 1;
												foreach ($final_res as $res1):																							
											?>		
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<td><?php echo $res1['brought_for'] ?></td>
												<td><?php echo $res1['analyzed_count'] ?></td>
												<td><?php echo $res1['cf_count'] ?></td>
											</tr>
											<?php
												$i++;
												endforeach;
										break;	
										case 'Commodity-wise Check & Challenged Samples Analysed': 	?>
											<tr>
												<th rowspan="2">S.No</th>
												<th rowspan="2">Commodity Name</th>
												<th rowspan="2">B/F</th>
												<th rowspan="2" >Sample Received during</th>
												<th rowspan="2">Total</th>
												<th class="text-center" colspan="3">Samples Analyzed</th>
												<th rowspan="2">C/F</th>
											</tr>
											<tr>
												<th>Standard</th>
												<th>Sub-Standard</th>
												<th>Total</th>                                   
											</tr>
											<?php
												$i = 1;
												foreach ($final_res as $res1):
											?>		
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<td><?php echo $res1['brought_for'] ?></td>
												<td><?php echo $res1['received_count'] ?></td>
												<td><?php echo $res1['total'] ?></td>
												<td><?php echo $res1['pass_count'] ?></td>
												<td><?php echo $res1['fail_count'] ?></td>
												<td><?php echo $res1['total_analysed'] ?></td>
												<td><?php echo $res1['cf_count'] ?></td>
												
											</tr>
											<?php
												$i++;
												endforeach;
										break;										
										case 'Time Taken for Analysis of Samples': ?>
											<tr>
												<th>Sr.No</th>
												<th>Code No.(RO/SO)</th>
												<th>Name of Sample</th>
												<th>Date of receipt of sample in RALs/CAL</th>
												<th>Date of Dispatch of results to RO/SO/Others</th>
												<th>Reason For Delay</th>
											</tr>
											<?php	
												$i = 0;
												foreach ($sample_inward as $res1):
												$i++; 
											?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $res1['stage_sample_code'] ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<td><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
												<td><?php  if(isset($res1['dispatch_date'])){ echo $res1['dispatch_date']; }//Remove/change date format on 22-05-2019 by Amol ?></td>
												<!--<td><?php echo $res1['time_taken'] ?></td>-->
												<td></td>
											</tr>
											<?php 
												endforeach;
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
										case 'Forwarded sample':
								?>
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
											</tr>
								<?php	break; ?>
								<?php	case 'Samples Accepted by Chemist For Testing':  ?>
											<tr>
												<th>S.No</th>
												<th>Accepted Date</th>
												<th>Sample Code</th>
												<th>Commodity Name</th>
												<th>Sample Type</th>
											</tr>
											<?php 
												$i=1;	$sum=0;
												foreach ($sample_inward as $res1) {
											?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo  $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
												<td><?php echo $res1['org_sample_code'] ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<td><?php echo $res1['sample_type_desc'] ?></td>										
											</tr>
											<?php $i++;  } ?>
											<tr>
												<td colspan="16"><b>Total Number of accepted Samples by Chemist :- <?php echo $i-1 ; ?></b></td>									
											</tr>
								<?php   break;
										case 'Rejected Samples': 	
								?>
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
										case 'Rejected Samples From RAL/CAL': 	
								?>
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
										case 'Samples Pending for Dispatch': 	
								?>
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
										case 'Sample Inward with Details': 
								?>
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
										case 'Coding Register': 	
								?>
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
										case 'Samples alloted to Chemist for testing': 	
								?>
											<tr>
												<th>S.No</th>
												<th>Registered Date</th>
												<th>Sample Code</th>
												<th>Sample Type</th>
												<th>Commodity Name</th>
												<!--<th>Chemist Name</th>-->
												<th>Alloted Date</th>
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
												<td><?php echo $res1['sample_type_desc'] ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<!--<td><?php echo $res1['f_name'] .' '.$res1['l_name']?></td>-->
												<td><?php echo $res1['alloc_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>										
											</tr>
											<?php endforeach; ?>
											<tr>
												<td colspan="16"><b>Total Number of Samples :-<?php echo $i ; ?> </b></td>
											</tr>
									<?php 
										break;
										case 'Samples alloted to Chemist for Re-testing': 	
									?>
											<tr>
												<th>S.No</th>
												<th>Receieved Date</th>
												<th>Sample Code</th>
												<th>Sample Type</th>
												<th>Commodity Name</th>
												<th>Chemist Name</th>
												<th>Allocate Date</th>
												
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
												<td><?php echo $res1['sample_type_desc'] ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<td><?php echo $res1['f_name']." ".$res1['l_name'] ?></td>
												<td><?php echo $res1['alloc_date']; //Remove/change date format on 22-05-2019 by Amol?></td>										
											</tr>
											<?php endforeach;
										break;
										case 'Test result submitted by chemist': 	?>								
											<tr>
												<th>S.No</th>
												<th>Receieved Date</th>
												<th>Commodity Name</th>
												<!--<th>Sample Code</th>-->
												<th>Chemist Code</th>
												<th>Test Name</th>
												<th>Result</th>
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
											<tr>
												<td rowspan='<?php echo $count;?>'><?php echo $i; ?></td>
												<td rowspan='<?php echo $count;?>'><?php echo  $res1['recby_ch_date']; ?></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['commodity_name'] ?></td>
												<!--<td rowspan='<?php echo $count;?>'><?php echo $res1['sample_code'] ?></td>-->
												<td rowspan='<?php echo $count;?>'><?php echo $res1['chemist_code'] ?></td>																	
											</tr>
											<?php for($j=0;$j<count($str1)-1;$j++) { ?>										
											<tr>								
												<td><?php echo $str1[$j] ?></td>
												<td><?php echo $str2[$j] ?></td>
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
											</tr>	
								<?php	break;	
										case 'Tested Samples': 	
								?>
											<tr>
												<th>S.No</th>
												<th>Chemist Name</th>
												<th>Accepted Date</th>
												<th>Sample Type</th>
												<th>Commodity Name</th>
												<th>Sample Code</th>
												<th>Expected Date of Completion</th>
												<th>Tests Completed on</th>
												<!--<th>Delayed by no. of Day's</th>-->
												<th>Result</th>
											</tr>
											<?php
												$i=1; $sum=0;
												foreach ($sample_inward as $res1){
											?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $res1['user_name'] ?></td>
												<td><?php echo $res1['recby_ch_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
												<td><?php echo $res1['sample_type_desc'] ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<td><?php echo $res1['org_sample_code'] ?></td>
												
												
												<td><?php echo $res1['expect_complt']; //Remove/change date format on 22-05-2019 by Amol ?></td>
												<td><?php if($res1['commencement_date']!=''){ echo $res1['commencement_date']; } else { echo $res1['commencement_date']; } //Remove/change date format on 22-05-2019 by Amol ?></td>
												<!--<td><?php echo $res1['delay'] ?></td>-->
												<td><?php echo $res1['grade'] ?></td>
												<!--<td><?php echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
											</tr>
											<?php $i++;  } ?>
											<tr>									
												<td colspan="16"><b>Total Number of Tested Samples :- <?php echo $i-1 ; ?></b></td>
											</tr>
							<?php 
									break;
									case 'Tests pending to be conducted on samples': 	
							?>
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
											<tr>
												<th>S.No</th>
												<th>Receieved Date</th>
												<th>Sample Code</th>
												<th>Commodity Name</th>
												<th>Sample Type</th>
												<th>Lab Name</th>
												<!--<th>User</th>-->
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
									case 'Sample received from RO/SO/RAL/CAL': 	?>
											<tr>
												<th>Sr.No</th>
												<th>Receieved Date</th>
												<!--<th>Lab/Office Name</th>-->
												<th>Sample Code</th>
												<th>Category Name</th>
												<th>Commodity Name</th>
												<th>Sample Type</th>
												<!--<th>User</th>-->
											</tr>
											<?php
												$i = 0;
												foreach ($sample_inward as $res1):
												$i++;
											?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
												<!--<td><?php echo $res1['user_flag'].",".$res1['ro_office'] ?></td>-->
												<td><?php echo $res1['org_sample_code'] ?></td>
												<td><?php echo $res1['commodity_name'] ?></td>
												<td><?php echo $res1['category_name'] ?></td>
												<td><?php echo $res1['sample_type_desc'] ?></td>
												<!--<td><?php echo $res1['f_name'].' '.$res1['l_name'] ?></td>-->
											</tr>									
											<?php  endforeach; ?>
											<tr>
												<td colspan="16"><b>Total Number of Samples :- <?php echo $i ; ?></b></td>
											</tr>
							<?php 	break;
									case 'Samples Analyzed(Count)':  ?>
											<tr>
												<th>S.No</th>
												<th>Type of Samples</th>
												<th>Commodity Name</th>
												<th>No of samples analyzed</th>
											</tr>
											<?php
												$i = 1;$sum=0; 
												foreach ($sample_inward as $res1) { 
											?>
											<tr>
												<td ><?php echo $i;?></td>
												<td ><?php echo $res1['sample_type_desc'];?></td>
												<td ><?php echo $res1['commodity_name'];?> </td>
												<td><?php echo $res1['count_samples']; $sum+= $res1['count_samples'];?></td>										
											</tr>
											<?php $i++;  } ?>								
											<tr>
												<td colspan="16"><b>Total No of samples analyzed :- <?php echo $sum ; ?></b></td>
											</tr>
							<?php	break; 
									case 'Samples Alloted/Analyzed/Pending Report(RAL/CAL)':
											if($selection_type=='all')	{ ?>
											<tr>							
												<th>Sr No</th>
												<th>Lab Name </th>
												<th>Sample Alloted </th>
												<th>Sample Analyzed</th>
												<th>Pending Sample</th>
											</tr>
											<?php } else { ?>
												<tr>
													<th>Sr No</th>
													<th>Sample Alloted </th>
													<th>Sample Analyzed</th>
													<th>Pending Sample</th>
												</tr>
											<?php }
												if($selection_type=='all')						
												{
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
													
												}else{
											?>
											<tr>
												<td>1</td>
												<td><?php echo $allotment_count; ?></td>
												<td><?php echo $analyzed_count; ?></td>
												<td><?php echo $pending_count; ?></td>
												
											</tr>
											<?php 	} ?>	
											<tr >														
												<td colspan="16"><b>Total Number of Samples :- <?php echo $allotment_count;?></b></td>														
											</tr>
							<?php		
									break;
									case 'No. of Pending & Rejected Samples':
											if($selection_type=='all')	{ ?>
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
												if($selection_type=='all')						
												{
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
												}else{
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
											<?php } 
												echo $all_data;
									break;?>
											</tr>
							<?php 	case 'Sample Register':?>
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
												foreach($sample_inward as $res1){ 
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
											<?php $i++;  } ?>								
											<tr style="font-size:7px;">											
												<td colspan="16"><b>Total Number of Samples :- <?php echo $i-1 ; ?></b></td>												
											</tr>							
											<?php 
												break;
												case 'Sample Registration Details':
											?>
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
									case 'Performance Report of RAL/CAL': 
							?>
											<tr>
												<th>Sr No</th>
												<th>Name Of Lab</th>
												<th>Progressive Total(Sapmle analyze uptill now)</th>
												<th>Total sample analyze during month</th>
												<th>Remark</th>
											</tr>
											<?php	
													$sum1=0;
													$i = 1;foreach ($all_data as $res1):
											?>
											<tr>
												<td><?php echo $i;?></td>
												<td><?php echo $res1['lab_name'];?></td>
												<td><?php echo $res1['prog_sample']; $sum1+=$res1['prog_sample']; ?></td>
												<td><?php echo $res1['analyz_sample']; $sum1+=$res1['prog_sample']; ?></td>
												<td></td>
											</tr>
											<?php
												$i++;
												endforeach;
											?>	
											<tr>
												<td colspan="16"><b>Total No of samples analyzed and pregress:- <?php echo $sum1 ; ?></b></td>
											</tr>
											<?php
												break;
												case 'Consolidated statement of Brought forward and carried forward of samples': 
											?>
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
												
												if($selection_type!='all')
												{
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
											<?php 	}
												
												}else{
													foreach ($all_data as $res1){
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
									case 'Brought forward,Analysed and carried forward of samples': 
							?>
											<tr>
												<th class="text-center" colspan='10'>
													<?php  
														$monthNum = $month;
														$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
														echo $monthName; 
													?>
												</th>
											</tr>
											<tr>
												<th>Sr No</th>
												<th>Name Of Lab</th>
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
												
												if($selection_type!='all')
												{
													$str1=trim($res1['post_name']);
													$str1=explode(',',$str1);
													$count=count($str1);
											?>
													<tr>
														<td rowspan='<?php echo $count;?>'><?php echo $i;?></td>
														 <td rowspan='<?php echo $count;?>'><?php echo $abc;?></td>
														<td rowspan='<?php echo $count;?>'><?php echo $res1['brought_for'];?></td>
														<td rowspan='<?php echo $count;?>'><?php echo $res1['received_count'];?></td>
														<td rowspan='<?php echo $count;?>'><?php echo $res1['total'];?></td>
														<td rowspan='<?php echo $count;?>'><?php echo $res1['analyz_in_month'];?></td>
														<td rowspan='<?php echo $count;?>'><?php echo $res1['analyz_in_month_repeat'];?></td>
														<td rowspan='<?php echo $count;?>'><?php echo $res1['carried_for'];?></td>
														<td rowspan='<?php echo $count;?>'></td>
														
													</tr>
											<?php
													
												}else{
													
													foreach ($all_data as $res1){
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
									case 'Sample Analyzed by Chemist': 
							?>
											<tr>
												<th>Sr No</th>	
												<th>Name of chemist whom allotted</th>
												<th>Sample Received from</th>
												<th >Letter no/Date</th>
												<th>Name of the commodity</th>
												<th>Sample Quantity</th>
												<th>RO/SO Code</th>
												<th>Date of receipt in lab</th>
												<!--<th>Visual Appearance</th>
												<th>Packing Method</th>
												<th>Condition of seal</th>-->
												<th>Lab Code</th>										
												<th>Date of allottment</th>	
												<th>Date of receipt of result</th>
												<th>Date of communication of report</th>
												<th>Remark</th>										
											</tr>
											<?php
												$i = 1;
												foreach ($all_data as $res1):
												//	if(isset($res1[0]['f_name'])){
													$str1=trim($res1['f_name']);
													$str1=explode(',',$str1);
													$str2=trim($res1['alloc_date']);
													$str2=explode(',',$str2);
													$count=count($str1);
												//}
												
											?>
											<tr>									
												<td rowspan='<?php echo $count;?>'><?php echo $i;?></td>
												<td></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['lab_name'].",".$res1['lab'];?></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['letr_ref_no']; ?><br/><font size="2"><?php echo $res1['letr_date']; //Remove/change date format on 22-05-2019 by Amol ?></font></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['commodity_name'];?></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['sample_total_qnt'];?></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['stage_sample_code'];?></td>
												<td rowspan='<?php echo $count;?>'><font size="2"><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></font></td>
												<!--<td rowspan='<?php echo $count;?>'><?php echo $res1['sam_condition_desc'];?></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['container_desc'];?></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['par_condition_desc'];?></td>-->
												<td rowspan='<?php echo $count;?>'><?php echo $res1['lab_code'];?></td>										
												<td></td>										
												<td rowspan='<?php echo $count;?>'><font size="2"><?php if($res1['grading_date']!=''){ echo $res1['grading_date']; }else { echo $res1['grading_date']; } //Remove/change date format on 22-05-2019 by Amol?></font></td>
												<td rowspan='<?php echo $count;?>'><?php  if($res1['grading_date']!=''){ echo $res1['grading_date']; } else { echo $res1['grading_date']; } //Remove/change date format on 22-05-2019 by Amol?></td>
												<td rowspan='<?php echo $count;?>'><?php echo $res1['remark'];?></td>
											</tr>
											<?php for($j=0;$j<count($str1)-1;$j++) { ?>										
											<tr>										
												<td><?php echo $str1[$j]?></td>
												<td><font size="2"><?php echo $str2[$j]; //Remove/change date format on 22-05-2019 by Amol?></font></td>
											</tr>
											<?php } ?>
											<?php
												$i++;												
												endforeach;
											?>
											<tr>									
												<td colspan="16"><b>Total Number of Samples :- <?php echo $i-1 ; ?></b></td>									
											</tr>
							<?php
									break;
									case 'Chemist –wise sample analysis':?>
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
							<?php			echo $all_data;
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
											?>
											<?php } ?>
                        </table>
                    </div>
                </div>				
				<div class="modal-footer" id="mdFooter"> 
					<button type="button" class="btn btn-default"  onclick='printDiv();'>Print</button>					
					<button type="button" class="btn btn-default"  id="pdf">Download</button>						
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<div id="pageNavPosition"  align="center">
					</div>
				</div> 
			</div>			
		</div>
	</div>