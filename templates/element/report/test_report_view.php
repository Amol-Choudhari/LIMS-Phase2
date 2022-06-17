<?php ?>
	<div id="myModal" class="modal fade"  role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="row">
						<div class="col-md-12 ">
							<div id="pageNavPosition"  align="center"></div>	
							<p class="text-right"><b>Date:</b><?php echo date("d/m/Y"); ?></p>
						</div>
					</div>
				</div>
					<div class="row" id="report" >
						<div class="col-xs-12 col-sm-8 col-md-12 ">
							<h5 class="text-center">भारत सरकार/Goverment of India</h5>
							<h5 class="text-center">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
							<h5 class="text-center">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
							<h5 class="text-center">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
							<?php 
								if($test_report[0]['Sample_Inward']['grade_user_flag']=="CAL" ){ ?>
									<h5 class="text-center"><b>केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory</b></h5>
									<h5 class="text-center">उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010</h5>												
									<h5 class="text-center">Phone:0712-2561748,Fax: 0712-2540952 T-2315 mail:cal@nic.in</h5>
							<?php } ?>
							<?php 
								if($test_report[0]['Sample_Inward']['grade_user_flag']=="RAL" ){ ?>
									<h5 class="text-center"><b>प्रादेशिक एगमार्क प्रयोगशाला / Ragional Agmark Laboratory , <?php echo $_SESSION['ro_office'];?></b></h5>
									<!--<h5 class="text-center"><?php echo $_SESSION['user_flag'].','.$_SESSION['ro_office']; ?></h5>-->
							<?php }else if(isset($test_report[0]['ml']['ro_office']) && isset($ral_lab_name) && $ral_lab_name=='RAL'){ ?>
									<h5 class="text-center"><b>प्रादेशिक एगमार्क प्रयोगशाला / Ragional Agmark Laboratory , <?php echo $test_report[0]['ml']['ro_office'];?></b></h5>
							<?php } ?>											
							<h5 class="text-center text-uppercase"><b>TEST REPORT FOR <?php if(isset($test_report)) {echo $test_report[0]['commodity']['commodity_name']."(".$test_report[0]['category']['category_name'].")"; } ?></b></h5>
							<div class="table-responsive" id="avb">
								<div class="classWithPad">
									<table class="table table-bordered" id="check_div">
										<tr>
											<td >रिपोर्ट संख्या /  Report No</td>
											<td colspan="3"><?php if(isset($test_report)) { echo $test_report[0]['Sample_Inward']['report_no']; } ?></td>
										</tr>
										<tr>
											<td >ग्राहक का नाम और पता / Name and Address of customer</td>
											<td colspan="3"><b><?php if(isset($test_report)) { echo $test_report[0]['dmi_user_roles']['user_flag'].",".$test_report[0]['ml']['ro_office']; } ?></b></td>
										</tr>
										<tr>
											<td >पण्य का नाम और नमूने की प्रकृति / Name of Commodity and Nature of Sample</td>
											<td  colspan="3"> <?php if(isset($test_report)) { echo $test_report[0]['commodity']['commodity_name']."(".$test_report[0]['category']['category_name'].")"; ?> <b><?php echo $test_report[0]['sample_type']['sample_type_desc']; } ?></b></td>
										</tr>
										<tr>
											<td >नमूना कोड संख्या / Sample Code No</td>
											<td  colspan="3"><?php if(isset($test_report)) { echo $Sample_code_as;} ?></td>
										</tr>
										<tr> 
											<td >ग्राहक की संदर्भ संख्या / Reference No of customer</td>
											<td  colspan="3"><?php if(isset($test_report)) { echo $test_report[0]['Sample_Inward']['letr_ref_no']."(".$test_report[0]['Sample_Inward']['letr_date'].")";} //Remove/change date format on 22-05-2019 by Amol?></td>
										</tr>
										<tr>
											<td  >कंटेनर का प्रकार / Type of Container</td>
											<td colspan="3"><?php if(isset($test_report)) { echo $test_report[0]['container_type']['container_desc'];} ?></td>
										</tr>
										<tr>
											<td  >पैकेज की  अवस्था / State of Package</td>
											<td colspan="3"><?php if(isset($test_report)) { echo $test_report[0]['par_condition']['par_condition_desc'];} ?></td>
											
										</tr>
										<tr>
											<td > नमूने की प्राप्त मात्रा / Quantum of Sample Received</td>
											<td colspan="3"><?php if(isset($test_report)) {echo $test_report[0]['Sample_Inward']['sample_total_qnt']." ".$test_report[0]['unit_weight']['unit_weight'];} ?></td>
										</tr>
										<tr>
											<td >प्रयोगशाला में नमूना की प्राप्ति की तिथि / Date of receipt of sample in the laboratory</td>
											<td colspan="3"><?php if(isset($test_report)) {echo $test_report[0]['Sample_Inward']['phy_accept_sample_date'];} //Remove/change date format on 22-05-2019 by Amol ?></td>
										</tr>
										<?php 
											if(isset($method_homo)){ 
												foreach($method_homo as $method){
													//pr($method);
													if($method[0]['m_sample_obs_code']==1){ ?>
														<tr>
															<td>एकरुपण की विधि /  <?php echo $method[0]['m_sample_obs_desc']; ?></td>
															<td colspan="3"><?php echo  $method[0]['m_sample_obs_type_value']; ?></td>
											
														</tr>		
													<?php	}
													if($method[0]['m_sample_obs_code']==2){ ?>
														<tr>
															<td >नमूने के संबंध में सामान्य राय / <?php echo $method[0]['m_sample_obs_desc']; ?></td>
															<td colspan="3"><?php echo  $method[0]['m_sample_obs_type_value']; ?></td>
														</tr>														
													<?php
													} 
												}
											} 
										?>											
										<?php 	
											if(isset($method_homo)){  ?>
												<tr>
													<td rowspan="4">नमूना इनसे मुक्त था अथवा नहीं / Whether the sample was free from</td>
												</tr>
												<?php
													foreach($method_homo as $method){
														if($method[0]['m_sample_obs_code']!=1 && $method[0]['m_sample_obs_code']!=2){ ?>															
														<tr>
															<td  colspan="3"><?php echo $method[0]['m_sample_obs_desc']; ?> : <?php echo  $method[0]['m_sample_obs_type_value']; ?></td>
														</tr>	
														<?php }
													} 
											} 
										?>
										<!--<tr>
											
											<td  colspan="3">2) सस्पेंडेड पदार्थ / Suspended Matter  :<?php if(isset($test_report)) {if($test_report[0]['Sample_Inward']['suspended_matter_flg']=='Y'){ echo "Yes";}else{echo "No";}}   ?></td>
										</tr>
										<tr>
											
											<td  colspan="3">3) खटवास / Rancidity   :<?php if(isset($test_report)) {if($test_report[0]['Sample_Inward']['rancidity_flg']=='Y'){ echo "Yes";}else{echo "No";}}   ?></td>
										</tr>
										<tr>
											
											<td  colspan="3">4)अप्रिय गंध / Obnoxious odour  :<?php if(isset($test_report)) {if($test_report[0]['Sample_Inward']['obnoxious_odour_flg']=='Y'){ echo "Yes";}else{echo "No";}}   ?></td>
										</tr> -->
										<tr>
											<td >विश्लेषण के प्रारंभ होने कि तिथी / Date of commencement of analysis</td>
											<td  colspan="3"><?php if(isset($comm_date)) {echo $comm_date;} //Remove/change date format on 22-05-2019 by Amol ?></td>
										</tr>
										<tr>
											<td colspan="3">विश्लेषणात्मक परिणामो को प्रस्तुत करने की तिथी / Date of submission of analytical results</td>
											<td  colspan="3"><?php if(isset($test_report) && $test_report[0]['Sample_Inward']['grade_user_flag']=="RAL" ) { echo $test_report[0]['Sample_Inward']['ral_anltc_rslt_rcpt_dt']; } else { echo $test_report[0]['Sample_Inward']['cal_anltc_rslt_rcpt_dt']; } //Remove/change date format on 22-05-2019 by Amol ?></td>
										</tr>
									</table>
									<h5><b>नोट / Note : </b></h5>
									<table class="table table-bordered" id="check_div">
										<tr>
											<td>उपर्युक्त परिणाम केवल इस प्रयोग्शाला में यथा प्राप्त एवं परीक्षित नमुने से संबंधित है|</td>
											<td>1)The above results pertain only to the sample tested and as received by the laboratory</td>
										</tr>
										<tr>
											<td>नमुनो का विश्लेषण सिर्फ निवेदित परामितियो के लिये किया गया |</td>
											<td>The sample has been analysed only for the requested parameters.</td>
										</tr>
										<tr>
											<td>प्राप्त परिणामो को प्रयोगशाला कि अनुमति के बिना आंशिक या पूर्ण, रूप से प्रकाशित विज्ञापित या किसी कानुनी कारवाई के लिये जारी करने हेतू इस्तेमाल नहीं किया गया |</td>
											<td>The result either in part or full shall not be published advertised or used for any legal action withour the permisssion of the issuing laboratory.</td>
										</tr>
										<tr>
											<td> यदी इस संबंध में विशेष निर्देश जारी नाही होते है तो नमुना प्राप्ती की तारीख से सिर्फ तीन माह कि अवधि तक हि इस प्रयोगशाला द्वारा शेष बचे हुए नमुनो को संभाल के रखा जाएगा</td>
											<td>Remnant samples will not be retained by this laboratory for a time period of only three months from the date of receipt unless specific instructions to the contrary are received.</td>
										</tr>
										<tr>
											<td>परीक्षित नमुना इस प्रयोगशाला द्वारा आहरीत नहीं है |</td>
											<td>The sample is not drawn by this laboratory.</td>
										</tr>
									</table>									  
									<table class="table table-bordered" id="check_div">
										<tr>
											<th class="text-center" rowspan="2">S.No. क्र.सं</th>											
											<th class="text-center" rowspan="2">विशिष्ट विशेषताए Specific Characters</th>
											<th class="text-center" colspan="<?php if(isset($commo_grade)){echo count($commo_grade); }?>">विनिर्देशो की रेंज Range of Characteristics</th>											
											<?php if($count_test_result>0){
														for($i=1;$i<=$count_test_result;$i++){ ?>
															<th class="text-center" colspan="1"rowspan="2"><br/><br/>Chemist <?php echo $i; ?></th>												
													<?php }  
												}else { ?>
														<th class="text-center" colspan="1"rowspan="2"><br/><br/>प्राप्त मान / Value Obtained</th>
											<?php 	} ?>											
											<th class="text-center" rowspan="2">अपनाई गयी पद्धति Method Followed</th>
										</tr>
										<tr>
											<?php 	if(isset($commo_grade)){ 
														foreach($commo_grade as $row){ ?>
															<td class="text-center"><?php echo($row[0]['grade_desc']); ?></td>
														<?php }
													}?>
										</tr>
										<tr>
											<?php if(isset($table_str)){echo $table_str;}?>
										</tr>
									</table>
									<table class="table table-bordered" id="check_div">
									  <tr>
										<th>Grade</th>
										<th><?php if(isset($test_report)) { echo $test_report[0]['m_grade_desc']['grade_desc']; } ?></th>
									  </tr>	
									</table> 
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 ">
									<div class="classWithPad">
										<br><br>
										<?php //pr($_SESSION); ?>
										<p class="text-right">(<?php if(isset($test_report)) { echo $test_report[0]['dmi_users1']['f_name'].' '.$test_report[0]['dmi_users1']['l_name']; }?>)</p>
										<p class="text-right"><?php if(isset($test_report)) { echo $test_report[0]['Sample_Inward']['grade_user_flag'].','.$test_report[0]['ml1']['ro_office']; } ?></p>
										<!--<p class="text-right">कृते निदेशक प्रयोगशालाए, केएप्र</p>
										<p class="text-right">प्राधिकृत अधिकारी के हस्ताक्षर / Signature of authorized officer</p>-->
										<br><br>
									</div>
								</div>
							</div>	
						</div>
					</div>
					<div class="modal-footer" style="border-top:none;" id="mdFooter">
						<button type="button" class="btn btn-default"  onclick='printDiv();'>Print</button>
						<button type="button" class="btn btn-default" id="close1" data-dismiss="modal">Close</button>
					</div>  <?php exit; ?>
			</div>
		</div>
	</div>