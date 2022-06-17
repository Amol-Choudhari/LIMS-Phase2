<style>
	.textLeft { text-align: left; }
	.textCenter { text-align: center; }
	.textRight { text-align: right; }
	.lineHeightSmall { line-height: 4px; }
	.lineHeightMedium { line-height: 6px; }
	.lineHeightLarge { line-height: 8px; }
	.fontSizeSix { font-size: 6px; }
	.fontSizeSeven { font-size: 7px; }
	.fontSizeEight { font-size: 8px; }
	.fontSizeExtraSmall { font-size: 9px; }
	.fontSizeSmall { font-size: 10px; }
	.fontSizeMedium { font-size: 12px; }
	.fontSizeLarge { font-size: 14px; }
	.fontBold { font-weight: bold; }
</style>

<?php ?>
  <h5 class="textCenter fontSizeEight lineHeightMedium">भारत सरकार/Goverment of India</h5>
	<h5 class="textCenter fontSizeEight lineHeightMedium">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
	<h5 class="textCenter fontSizeEight lineHeightMedium">कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture, Cooperation & Farmers Welfare</h5>
	<h5 class="textCenter fontSizeEight lineHeightMedium">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
	<h5 class="textCenter fontSizeEight lineHeightMedium">केन्द्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory</h5>
	<h5 class="textCenter fontSizeEight lineHeightMedium">उत्तर अम्बाझरी मार्ग / North Ambazari Road</h5>
	<h5 class="textCenter fontSizeEight lineHeightMedium">नागपुर / Nagpur-440010</h5>
	<h5 class="textCenter fontSizeSeven lineHeightMedium">Email-cal@nic.in --- [(0712)2565647, 2561748, Fax-2982066]</h5>
	<!-- <b></b> -->
  <p class="textRight fontSizeExtraSmall"><span class="fontBold">Date: </span><?php echo date("d/m/Y"); ?></p>
  <p class="fontSizeExtraSmall lineHeightMedium">प्रति, चचे</p>
  <p class="fontSizeExtraSmall lineHeightMedium">भारी - तेल एव वसा / रसायन / मसाला / सुशामजीवशास्त्र / विषविद्या / खाद्यान अनुभागचच</p>
  <p class="fontSizeExtraSmall lineHeightMedium">कृपया निम्नलिखित कोडेड नमूनों का विश्लेषण कर निर्धारित समय सीमा के भीतर अधोहस्ताक्षरी को रिपोर्ट भेज दी जाए ।</p>
  <p class="fontSizeEight lineHeightMedium">नमूनों का ब्यौरा: </p>
  <table id="tablepaging" cellspacing="0" cellpadding="1" border="1">
    <tr class="textCenter fontSizeExtraSmall fontBold">
      <th width="30">S.No.</th>					
      <th width="100" class="textLeft">Commodity</th>					
      <th width="70">CODE NUMBER</th>					
      <th width="70">Quantity</th>				
      <th width="70">Type of sample</th>				
      <th width="90">Parameters</th>				
      <th width="90">L I M S.Number.</th>
    </tr>
    <?php
      for($i=1; $i<=14; $i++) { ?>
        <tr class="textCenter fontSizeExtraSmall">
          <td><?php echo $i; ?></td>
          <td class="textLeft"><?php $random = (rand(0,1));
            if($random == 0) {
              echo 'Blended oil';
            }
            else if($random == 1) {
              echo 'Mustard oil';
            }
          ?></td>
          <td><?php echo 'CAL-'.(rand(70,85)); ?></td>
          <td><?php $random = (rand(0,1));
            if($random == 0) {
              echo '100.gms';
            }
            else if($random == 1) {
              echo '90.gms';
            }
          ?></td>
          <td><?php $random = (rand(0,1));
            if($random == 0) {
              echo 'CHECK';
            }
            else if($random == 1) {
              echo 'challenged';
            }
          ?></td>							
          <td>All</td>         							
          <td><?php echo (rand(6000000,9000000)); ?></td>         							
        </tr><?php	
      }
    ?>
  </table>
  <p class="fontSizeExtraSmall">एसओ/आईईसी/17025-2005 के तहत प्रबंधकीय आवश्यकताओ के संबंध में सौपे गए कार्य करने की करने की उचित व्यवस्था है / नहीं है । </p>
  <p class="fontSizeExtraSmall">1. सौपे गए कार्य  करने के लिए आवश्यक कार्मिक, सूचना और उपयुक्त संसाधनों, जिसमें रसायन, रीएजेण्ट्स, ग्लासवेयर, प्रमाण्ति संदर्भ सामग्री, संयंत्र , 
उपकरण, मान्यता प्राप्त पद्धतियों आदि का समावेश है, केंद्रीय एगमार्क प्रयोशाला नागपुर में उपलब्ध है ।</p>
  <p class="fontSizeExtraSmall">2. कर्मियों में वह कौशल और निपुणता है जो प्रशंगत परिक्षण् के प्रदर्शन के लिए आवश्यक है और वे माप की अनिशिचकता और सीमा का संसूचन आदि करने में भी सक्षमहै ।</p>
  <p class="fontSizeExtraSmall">3. सौपे गए कार्य के लिए जिम्मेदार कार्मिक द्वारा प्रयोग की जाने वाली विधि को पर्याप्त रूप से परिभाषित किया जाता है, उसका दस्तावेजीकरण किया जाता है एव समझा जाता है ।</p>
  <p class="fontSizeExtraSmall">4. जिन परिक्षण् / पद्धतियों का चयन किया जाता है उनसे ग्राहको की आवश्यता पूर्ण होती है ।</p>
  <p class="fontSizeExtraSmall">5. जो अनुरोध / अनुंबध बनाया गया उसमें कोई मतभेद नहीं है और वह प्रयोशाला और ग्राहक दोनों को मान्य है ।</p>
  <br><br><br>
  <p class="fontSizeExtraSmall textRight">कोडिंग अधिकारी</p>
  