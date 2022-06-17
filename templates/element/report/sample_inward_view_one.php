<style>
	.textLeft { text-align: left; }
	.textCenter { text-align: center; }
	.textRight { text-align: right; }
	.lineHeightSmall { line-height: 4px; }
	.lineHeightMedium { line-height: 6px; }
	.lineHeightLarge { line-height: 8px; }
	.fontSizeExtraSmall { font-size: 9px; }
	.fontSizeSmall { font-size: 10px; }
	.fontSizeMedium { font-size: 12px; }
	.fontSizeLarge { font-size: 14px; }
	.fontBold { font-weight: bold; }
</style>

<?php ?>
	<h5 class="textCenter lineHeightMedium">भारत सरकार / Goverment of India</h5>
	<h5 class="textCenter lineHeightMedium">कृषि एवं किसान कल्याण मंत्रालय / Ministry of Agriculture & Farmers Welfare</h5>
	<h5 class="textCenter lineHeightMedium">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
	<h5 class="textCenter lineHeightMedium">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
	<b></b>
	<h5 class="textCenter" id="test_title">कोडींग/डिकोडिंग अनुभाग / Coding-Decoding Section</h5>
	<h5 class="textCenter">
		<?php 									
			if($_SESSION['user_flag']=="HO"){
				echo $_SESSION['user_flag'].', '.$_SESSION['ro_office'];
			}else if(isset($sample_inward1['0']['0']['ro_office']) && isset($ral_lab_name)){ 
				echo $ral_lab_name.', '.$sample_inward1['0']['0']['ro_office']; 
			}else { 
				echo $_SESSION['user_flag'].', '.$_SESSION['ro_office']; 
			} 
		?>
	</h5>
	<p class="textRight fontSizeSmall"><span class="fontBold">Date: </span><?php echo date("d/m/Y"); ?></p>
	<?php if($from_date!='' && $to_date!=''){ ?>
		<!-- <div class="col-md-6"> -->
			<p class="fontSizeSmall fontBold">Sample Received in the Period From<b> <?php echo $from_date;  ?> to <?php echo $to_date; //Remove/change date format on 22-05-2019 by Amol?></b></p>							
		<!-- </div> -->
	<?php } ?>
	<p class="textLeft fontSizeSmall lineHeightSmall">प्रति,</p>
	<p class="textLeft fontSizeSmall lineHeightSmall">प्रभारी रसायन,मसाला,तेल,सूक्ष्मजीवशास्त्र/विषविद्या /खाद्यान्न अनुभाग |</p>
	<p class="textLeft fontSizeSmall lineHeightSmall">कृपया निन्म उल्लिखित कोडेड नमुने का विश्लेषण कर निर्धारित समय के भीतर अधोहस्ताक्षरी को रिपोर्ट भेज जाए |</p>
	<p class="textLeft fontSizeSmall lineHeightSmall fontBold">नमुने का ब्योरा:</p>
	<table id="tablepaging" cellspacing="0" cellpadding="1" border="1">
		<thead>
			<tr class="textCenter fontBold fontSizeExtraSmall">
				<th width="25">क्र.स.</th>
				<th width="110">पण्य का नाम</th>
				<th width="50">कोड संख्या</th>
				<th width="65">जारी की गई मात्रा</th>
				<th width="50">जारी दिनांक</th>
				<th width="80">आगे की गई दिनांक</th>
				<th width="60">नमुने का प्रकार</th>
				<th width="100">टिप्पणी</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$i=1;
				foreach($sample_inward1 as $res1):
			?>
			<tr class="fontSizeExtraSmall">
				<td width="25" class="textCenter"><?php echo $i; ?></td>
				<td width="110"><?php echo $res1['commodity_name']; ?></td>
				<td width="50" class="textCenter"><?php echo $res1['stage_sample_code']; ?></td>
				<td width="65" class="textCenter"><?php echo $res1['sample_total_qnt']." ".$res1['unit_weight']; ?></td>
				<td width="50" class="textCenter"><?php echo $res1['received_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
				<td width="80" class="textCenter"><?php echo $res1['tran_date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
				<td width="60" class="textCenter"><?php echo $res1['sample_type_desc']; ?></td>
				<td width="100"><?php echo $res1['remark']; ?></td>	
			</tr>										
			<?php $i++; endforeach; ?>										
		</tbody>
	</table>
	<br/><b></b><br/><br/><br/>
	<p class="textRight fontSizeSmall lineHeightSmall fontBold">कोडींग अधिकारी के हस्ताक्षर</p>
	<p class="textRight fontSizeSmall lineHeightSmall fontBold">कृते निदेशक प्रयोगशालाए</p>
	<p class="textCenter fontSizeSmall fontBold">पावती</p>
	<p class="fontSizeSmall fontBold">प्रमाणित किया जाता है कि:</p>
	<p class="fontSizeSmall">	
		1. आईएसओ/आईईसी/ 17025-2005 के तहत प्रबंधकीय आवश्यकताओं के खंड 4.4 के संबंध में सौंपे गए 							
		कार्य करने की उचित व्यकवस्था है/ नहीं है।
	</p>							
	<p class="fontSizeSmall">
		2. सौंपे गए कार्य को करने के लिए आवश्य क कार्मिक, सूचना और उपयुक्त  संसाधनों, जिसमें रसायन, 							
		रीएजेन्ट्सं, ग्ला सवेयर, प्रमाणित संदर्भ सामग्री, संयंत्र, उपकरण, मान्य ता प्राप्तट पद्धतियों आदि का समावेश है, 							
		केन्द्रीय एगमार्क प्रयोगशाला, नागपुर में उपलब्ध् है। 	
	</p>						
	<p class="fontSizeSmall">
		3. कर्मियों में वह कौशल और निपुणता है जो प्रश्नुगत परीक्षण के प्रदर्शन के लिए आवश्यक है और वे माप 							
		की अनिश्चिंतता और सीमा का संसूचन आदि करने में भी सक्षम हैं। 
	</p>							
	<p class="fontSizeSmall">
		4. सौंपे गए कार्य के लिए जिम्मेदार कार्मिक द्वारा प्रयोग की जाने वाली विधि को पर्याप्त रूप से परिभाषित 							
		किया जाता है, उसका दस्ताकवेजीकरण किया जाता है एवं समझा जाता है।
	</p>							
	<p class="fontSizeSmall">5. जिन परीक्षण/पद्धतियों का चयन किया जाता है उनसे ग्राहकों की आवश्याकता पूर्ण होती है।</p> 							
	<p class="fontSizeSmall">6. जो अनुरोध/अनुबंध बनाया गया उसमें कोई मतभेद नहीं है और वह प्रयोगशाला और ग्राहक दोनों को मान्य  है। </p>
	<b></b>
	<p class="textRight fontSizeSmall fontBold">तकनीकी प्रबंधक के हस्ताक्षर</p>	


	