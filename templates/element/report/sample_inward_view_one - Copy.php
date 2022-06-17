<style>
	.textLeft { text-align: left; }
	.textCenter { text-align: center; }
	.textRight { text-align: right; }
</style>

<?php ?>
	<div id="myModal" class="modal"  >
		<div class="modal-dialog modal-lg" >
			<!-- Modal content-->
			<div class="modal-content" >
				<div class="modal-header ">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>					
					<div class="row">
						<div class="col-md-12 ">
							<div id="pageNavPosition"  align="center"> </div>	
							<!--<button type="button" class="btn btn-default " onclick="myFunction()">Print</button>
							<button type="button" class="btn btn-primary" id="pdf" >Pdf</button>-->
							<p class="text-right"><b>Date:</b><?php echo date("d/m/Y"); ?></p>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-8 col-md-12 ">
							<h5 class="textCenter">भारत सरकार/Goverment of India</h5>
							<h5 class="textCenter">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
							<h5 class="textCenter">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
							<h5 class="textCenter">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
							
							<!--<h5 class="textCenter"><?php echo $_SESSION['user_flag'].','.$_SESSION['ro_office']; ?></h5>-->
							<?php if(isset($sample_inward['0']['0']['user_flag']) && isset($ral_lab_name) && $ral_lab_name=='RAL'){ ?>
									<h5 class="textCenter"><b>प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , <?php echo $sample_inward['0']['0']['ro_office'];?></b></h5>
							<?php }else if(isset($sample_inward['0']['0']['user_flag']) && isset($ral_lab_name) && $ral_lab_name=='CAL'){ ?>
									<h5 class="textCenter"><b>केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory</b></h5>
									<h5 class="textCenter">उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010</h5>							
							<?php }else if(isset($sample_inward['0']['0']['user_flag']) && $sample_inward['0']['0']['user_flag']=='RO'){ ?>
									<h5 class="textCenter"><b>प्रादेशिक कार्यालय / Regional Office , <?php echo $sample_inward['0']['0']['ro_office'];?></b></h5>
							<?php }	else if(isset($sample_inward['0']['0']['user_flag']) && $sample_inward['0']['0']['user_flag']=='SO'){ ?>
									<h5 class="textCenter"><b>उप-कार्यालय / Sub Office , <?php echo $sample_inward['0']['0']['ro_office'];?></b></h5>
							<?php } ?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-8 col-md-12 textCenter">
							<h4 class="textCenter" id="test_title"><b>कोडींग/डिकोडिंग अनुभाग / Coding-Decoding Section</b></h4>
							<b>
								<h4 class="textCenter">
									<?php 									
										if($_SESSION['user_flag']=="HO"){
											echo $_SESSION['user_flag'].','.$_SESSION['ro_office'];
										}else if(isset($sample_inward1['0']['0']['ro_office']) && isset($ral_lab_name)){ 
											echo $ral_lab_name.','.$sample_inward1['0']['0']['ro_office']; 
										}else { 
											echo $_SESSION['user_flag'].','.$_SESSION['ro_office']; 
										} 
									?>
								</h4>
							</b>
						</div>
					</div>						
					<div class="row">		
						<div class="col-xs-12 col-sm-8 col-md-12 ">								
							<?php if($from_date!='' && $to_date!=''){ ?>
								<div class="col-md-6">
									<p>Sample Received in the Period From<b> <?php echo $from_date;  ?> to <?php echo $to_date; //Remove/change date format on 22-05-2019 by Amol?></b></p>							
								</div>
							<?php } ?>
						</div>
					</div>		
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-12  ">
					 <div class="classWithPad">
						<p class="textLeft">प्रति,</p>
						<p class="textLeft">प्रभारी रसायन,मसाला,तेल,सूक्ष्मजीवशास्त्र/विषविद्या /खाद्यान्न अनुभाग |</p>
						<p class="textLeft">कृपया निन्म उल्लिखित कोडेड नमुने का विश्लेषण कर निर्धारित समय के भीतर अधोहस्ताक्षरी को रिपोर्ट भेज जाए |</p>
						<p class="textLeft">नमुने का ब्योरा:</p>
					 </div>		
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-12 col-sm-offset-2 col-md-offset-0 ">
						<div class="classWithPad">
							<div class="table-responsive" id="avb">
								<table class="table table-bordered" id="tablepaging">
									<thead>
										<tr>
											<th>क्र.स.</th>
											<th>पण्य का नाम</th>
											<th>कोड संख्या</th>
											<th>जारी की गई मात्रा</th>
											<th>जारी दिनांक</th>
											<th>आगे की गई दिनांक</th>
											<th>नमुने का प्रकार</th>
											<th>टिप्पणी</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											$i=1;
											foreach($sample_inward1 as $res1):
										?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo $res1['commodity_name']; ?></td>
											<td><?php echo $res1['stage_sample_code']; ?></td>
											<td><?php echo $res1['sample_total_qnt']." ".$res1['unit_weight']; ?></td>
											<td><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
											<td><?php echo $res1['tran_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
											<td><?php echo $res1['sample_type_desc']; ?></td>
											<td><?php echo $res1['remark']; ?></td>	
										</tr>										
										<?php $i++; endforeach; ?>										
									</tbody>
								</table>
							</div>	  
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-12 ">
					 <div class="classWithPad">
						<p></p>
						<p class="textRight">कोडींग अधिकारी के हस्ताक्षर</p>
						<p class="textRight">कृते निदेशक प्रयोगशालाए</p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-12 ">
						<div class="classWithPad">
							<p class="textCenter">पावती</p>
							<p>प्रमाणित किया जाता है कि:</p>
							<p>	
								1. आईएसओ/आईईसी/ 17025-2005 के तहत प्रबंधकीय आवश्यकताओं के खंड 4.4 के संबंध में सौंपे गए 							
								कार्य करने की उचित व्यकवस्था है/ नहीं है।
							</p>							
							<p>
								2. सौंपे गए कार्य को करने के लिए आवश्य क कार्मिक, सूचना और उपयुक्त  संसाधनों, जिसमें रसायन, 							
								रीएजेन्ट्सं, ग्ला सवेयर, प्रमाणित संदर्भ सामग्री, संयंत्र, उपकरण, मान्य ता प्राप्तट पद्धतियों आदि का समावेश है, 							
								केन्द्रीय एगमार्क प्रयोगशाला, नागपुर में उपलब्ध् है। 	
							</p>						
							<p>
								3. कर्मियों में वह कौशल और निपुणता है जो प्रश्नुगत परीक्षण के प्रदर्शन के लिए आवश्यक है और वे माप 							
								की अनिश्चिंतता और सीमा का संसूचन आदि करने में भी सक्षम हैं। 
							</p>							
							<p>
								4. सौंपे गए कार्य के लिए जिम्मेदार कार्मिक द्वारा प्रयोग की जाने वाली विधि को पर्याप्त रूप से परिभाषित 							
								किया जाता है, उसका दस्ताकवेजीकरण किया जाता है एवं समझा जाता है।
							</p>							
							<p>5. जिन परीक्षण/पद्धतियों का चयन किया जाता है उनसे ग्राहकों की आवश्याकता पूर्ण होती है।</p> 							
							<p>6. जो अनुरोध/अनुबंध बनाया गया उसमें कोई मतभेद नहीं है और वह प्रयोगशाला और ग्राहक दोनों को मान्य  है। </p>							
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-12 ">
					 <div class="classWithPad">
						<p class="textRight">तकनीकी प्रबंधक के हस्ताक्षर</p>
					 </div>	
					</div>
				</div>	
				<div class="modal-footer" id="mdFooter">
					<button type="button" class="btn btn-default"  onclick='printDiv();'>Print</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<div id="pageNavPosition"  align="center"></div>
				</div>
			</div>
		</div>
	</div>