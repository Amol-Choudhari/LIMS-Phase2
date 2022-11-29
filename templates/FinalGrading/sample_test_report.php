<!-- This file is updated on 14-09-2022 by Amol, all report pdf sections are distributed in small chunks of elements now -->

<?php echo $this->element('/final_sample_test_report/common_top_css'); ?>

<?php echo $this->element('/final_sample_test_report/top_main_header'); ?>
	
<?php echo $this->element('/final_sample_test_report/lab_details_header'); ?>
	
<?php echo $this->element('/final_sample_test_report/report_title'); ?>

<?php echo $this->element('/final_sample_test_report/report_other_details'); ?>

<?php echo $this->element('/final_sample_test_report/common_notes_section'); ?>
	

<table width="100%" border="1">

	<!--added for if else condition for ilc flow when main sample is present work on if contition
	 other wise else conditon is working for other samples 28-11-2022 by shreeya-->
	 <?php if($sampleTypeCode ==9 && !empty($checkifmainilc)){ ?>
		<?php echo $this->element('/final_sample_test_report/ilc_final_zscore_report'); ?>
	<?php }else { ?>

		<?php echo $this->element('/final_sample_test_report/report_table_headers_main'); ?>

		<?php echo $this->element('/final_sample_test_report/all_value_rows_and_nabl_bifurcation'); ?>

	<?php } ?>

</table>
<!-- if condition added for ilc flow could not show grade -->
<?php if($sampleTypeCode !=9){ ?> 
	<?php echo $this->element('/final_sample_test_report/final_grade_row'); ?>
<?php } ?>	

<?php echo $this->element('/final_sample_test_report/signature_section'); ?>

<?php echo $this->element('/final_sample_test_report/qr_code_section'); ?>

