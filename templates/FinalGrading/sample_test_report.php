<!-- This file is updated on 14-09-2022 by Amol, all report pdf sections are distributed in small chunks of elements now -->

<?php echo $this->element('/final_sample_test_report/common_top_css'); ?>

<?php echo $this->element('/final_sample_test_report/top_main_header'); ?>
	
<?php echo $this->element('/final_sample_test_report/lab_details_header'); ?>
	
<?php echo $this->element('/final_sample_test_report/report_title'); ?>

<?php echo $this->element('/final_sample_test_report/report_other_details'); ?>

<?php echo $this->element('/final_sample_test_report/common_notes_section'); ?>
	

<table width="100%" border="1">
	
	<?php echo $this->element('/final_sample_test_report/report_table_headers_main'); ?>

	<?php echo $this->element('/final_sample_test_report/all_value_rows_and_nabl_bifurcation'); ?>

</table>
	
	
<?php echo $this->element('/final_sample_test_report/final_grade_row'); ?>

<?php echo $this->element('/final_sample_test_report/signature_section'); ?>

<?php echo $this->element('/final_sample_test_report/qr_code_section'); ?>

