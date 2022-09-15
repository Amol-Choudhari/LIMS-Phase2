<?php //this if part is for "Food Safety Samples" report //02-06-2022 by Shreeya
		if($getSampleType['sample_type_code']==8){  ?>
			<?php echo $this->element('/final_sample_test_report/food_safety_report_table_headers'); ?>

	<?php } else { //this else part is for regular report as before //02-06-2022 by Shreeya ?>
			<?php echo $this->element('/final_sample_test_report/regular_report_table_headers'); ?>
	
	<?php } ?>