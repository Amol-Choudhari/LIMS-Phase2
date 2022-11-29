<?php //this if part is for "Food Safety Samples" report //02-06-2022 by Shreeya
		if($sampleTypeCode ==8){  ?>
			<?php echo $this->element('/final_sample_test_report/food_safety_report_table_headers'); ?>

		<?php } elseif($sampleTypeCode ==9){ ?>  <!-- this elseif part is for ILC sub sample report Done By Shreeya on 17-11-2022 -->

			<?php //echo $this->element('/final_sample_test_report/ilc_sub_labs_report'); ?>

			<!-- temparorily the above line commented and code taken out here below, as it was not working with element call on 29-11-2022-->
			<tr>
				<td width="10%"><b>S.No. <span style="font-family: krutidev010; font-size:10px;">Ø-la</span></b></td>											
				<td width="30%"><b><span style="font-family: krutidev010; font-size:10px;">iSjkehVj  dk  uke </span>/ Name Of Parameter</b></td>
				
				<td><b><span style="font-family: krutidev010; font-size:10px;">çkIr eku</span>/ Value Obtained (Mg/Kg)</b></td>
				<td><b><span style="font-family: krutidev010; font-size:10px;">viukbZ x;h i)fr</span>/ Method Followed</b></td>
			</tr>


		<?php }else { //this else part is for regular report as before //02-06-2022 by Shreeya ?>
			<?php echo $this->element('/final_sample_test_report/regular_report_table_headers'); ?>
	
	<?php } ?>
	