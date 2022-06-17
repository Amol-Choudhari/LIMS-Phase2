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
  <h5 class="textCenter lineHeightMedium">भारत सरकार/Goverment of India</h5>
	<h5 class="textCenter lineHeightMedium">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
	<h5 class="textCenter lineHeightMedium">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
	<h5 class="textCenter lineHeightMedium">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
	<b></b>
	<h5 class="textCenter" id="test_title">Monthly Report of Regional Agmark Laboratory Mahim, Mumbai for the Month of MARCH, 2021</h5>
  <br><br><br><br>
	<p class="textLeft fontSizeExtraSmall fontBold">(1) Staff Strength:</p>
	<table id="tablepaging" cellspacing="0" cellpadding="1" border="1">
		<thead>
			<tr class="textCenter fontBold fontSizeExtraSmall">
				<th width="25">Sr. No.</th>
				<th width="140">Name of the Post</th>
				<th width="100">Sanctioned</th>
				<th width="100">Filled up</th>
				<th width="140">Vacant Since When</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$i=1;
        $post_name = ['Chief Chemist', 'Senior Chemist', 'Junior Chemist'];
				foreach($post_name as $post) {
			?>
			<tr class="fontSizeExtraSmall">
				<td width="25" class="textCenter"><?php echo $i; ?></td>
				<td width="140"><?php echo $post; ?></td>
				<td width="100" class="textCenter"></td>
				<td width="100" class="textCenter"></td>
				<td width="140" class="textCenter"></td>	
			</tr>										
			<?php $i++; 
      } ?>										
		</tbody>
	</table>
	
  <p class="textLeft fontSizeExtraSmall fontBold">(2) Status of Instruments / Equipments etc. :</p>
	<table id="tablepaging" cellspacing="0" cellpadding="1" border="1">
		<thead>
			<tr class="textCenter fontBold fontSizeExtraSmall">
				<th width="25">Sr. No.</th>
				<th width="160">Name of the Instrument / Equipment</th>
				<th width="80">Whether Functional / Non functional</th>
				<th width="80">If Non Functional since When</th>
				<th width="80">Reason for Non Functional</th>
				<th width="80">Action taken / Remark</th>
			</tr>
		</thead>
		<tbody>
      <tr class="textLeft fontSizeExtraSmall">
        <td width="25">1</td>
        <td width="160">2</td>
        <td width="80">3</td>
        <td width="80">4</td>
        <td width="80">5</td>
        <td width="80">6</td>
      </tr>
			<?php 
				$i=1;
        $instruments_name = ['U.V.Spectrophotometer with Double Beam', 'Refractometer'];
				foreach($instruments_name as $instrument) {
			?>
			<tr class="fontSizeExtraSmall">
				<td width="25" class="textCenter"><?php echo $i; ?></td>
				<td width="160"><?php echo $instrument; ?></td>
				<td width="80" class="textCenter"><?php echo 'Functional'; ?></td>
				<td width="80" class="textCenter"></td>
				<td width="80" class="textCenter"></td>	
				<td width="80" class="textCenter"></td>	
			</tr>										
			<?php $i++; 
      } ?>										
		</tbody>
	</table>
  
  <p class="textLeft fontSizeExtraSmall fontBold">(3) AMC of Instrument / Equipments</p>
	<table id="tablepaging" cellspacing="0" cellpadding="1" border="1">
		<thead>
			<tr class="textCenter fontBold fontSizeExtraSmall">
				<th width="25">S. No.</th>
				<th width="160">Name of the Instrument / Equipment</th>
				<th width="80">Whether under AMC</th>
				<th width="80">If not, reason there on</th>
				<th width="80">Period of AMC</th>
				<th width="80">Action taken / Remark</th>
			</tr>
		</thead>
		<tbody>
      <tr class="textLeft fontSizeExtraSmall">
        <td width="25">1</td>
        <td width="160">2</td>
        <td width="80">3</td>
        <td width="80">4</td>
        <td width="80">5</td>
        <td width="80">6</td>
      </tr>
			<?php 
				$i=1;
        $instruments_name = ['U.V.Spectrophotometer with Double Beam', 'Refractometer'];
				foreach($instruments_name as $instrument) {
			?>
			<tr class="fontSizeExtraSmall">
				<td width="25" class="textCenter"><?php echo $i; ?></td>
				<td width="160"><?php echo $instrument; ?></td>
				<td width="80" class="textCenter"></td>
				<td width="80" class="textCenter"></td>
				<td width="80" class="textCenter"></td>	
				<td width="80" class="textCenter"></td>	
			</tr>										
			<?php $i++; 
      } ?>										
		</tbody>
	</table>
  
  <p class="textLeft fontSizeExtraSmall fontBold">(4) Whether required Chemicals / Glass wares are available, if not, reason there of ---</p>
  
  <p class="textLeft fontSizeExtraSmall fontBold lineHeightSmall">(5) Details of the Sample Analyzed during the month:</p>
  <p class="textLeft fontSizeExtraSmall lineHeightSmall">(i) No. of Check Samples analysed:</p>
  <p class="textLeft fontSizeExtraSmall lineHeightSmall">(ii) No. of Research / Project Samples analysed:</p>
  <p class="textLeft fontSizeExtraSmall lineHeightSmall">(iii) No. of Private Samples analysed ---</p>
  <p class="textLeft fontSizeExtraSmall lineHeightSmall">(iv) No. of Check Samples analysed (Internal check) :</p>
  <p class="textLeft fontSizeExtraSmall lineHeightSmall">(v) No. of Proficiency Testing samples -----</p>
  <p class="textLeft fontSizeExtraSmall lineHeightSmall">(vi) No. of Inter Lab. Comparison Samples</p>
  <p class="textLeft fontSizeExtraSmall lineHeightSmall">(vii) No. of Repetition Samples</p>
  <p class="textLeft fontSizeExtraSmall fontBold lineHeightSmall">Total</p>
  
  <p class="textLeft fontSizeExtraSmall fontBold">(6) Whether the number of samples analysed is as per norms:</p>
	


	