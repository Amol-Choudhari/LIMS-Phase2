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

<p class="fontSizeSmall textCenter lineHeightMedium">नोट</p>
<p class="fontSizeSmall">निम्नलिखित चेक/एपेक्स और शोध नमूने, श्री/श्रीमती ..........................................., कनिष्ट  रसायन/वरिषठ रसायन को प्राथमिको के आधार पर आंशिक/पूर्ण विश्लेषण  के लिये आवंटित किये जा रहे है ।  नमूनों की जांच ______ कार्यालयीन दिवस में किया जाए ।</p>
<p class="fontSizeSmall">अगर परिणाम प्रस्तुत करने में विलंभ होता है, तो उसका उचित स्पष्टिकरण के साथ जवाब दिया जाए ।</p>
<p class="fontSizeSmall">The following Check/Apex/Research Samples are alloted to MR/Mrs _______________________________, Junior Chemist/Senior Chemist for complete/partial analysis on priority basis. The analysis may be completed in ______ working day/s.</p>
<p class="fontSizeSmall">Delay in submission of results, if any, may kindly be explained with proper justification.</p>
<table id="tablepaging" cellspacing="0" cellpadding="1" border="1">
  <tr class="textCenter fontSizeExtraSmall fontBold">
    <th width="30">क्र.सं./S.No.</th>					
    <th width="200">पाण्य का नाम/ Name of Commodity</th>					
    <th width="80">कोड सांख्या/ Code No.</th>				
    <th width="100">परामीतियाँ/ Parameters</th>				
    <th width="100">अभ्युक्ति/ Remarks</th>
  </tr>
  <?php
    for($i=0; $i<=14; $i++) { ?>
      <tr class="textCenter fontSizeExtraSmall">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr><?php
    }
  ?>
</table>
<br><br><br><br><br>
<table id="tablepaging" cellspacing="0" cellpadding="0" border="0">
  <tr class="fontSizeSmall">
    <th>दिनांक:</th>
    <th>रसायन के हस्ताक्षर</th>
    <th>अनुभाग प्रभारी के हस्ताक्षर</th>
  </tr>
  <tr class="fontSizeSmall">
    <td>Date:</td>
    <td>Signature of Chemist</td>
    <td>Signature Incharge of Section</td>
  </tr>
</table>