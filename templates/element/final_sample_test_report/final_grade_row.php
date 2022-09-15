<table border="1" width="100%">
	  <tr>
		<td><b>Grade</b></td>
		<!--<td><b><?php //if(isset($test_report)) { echo $test_report[0]['grade_desc']; } ?></b></td>-->
		<td><b><?php if(isset($test_report)) { echo $_SESSION['gradeDescFinalReport']; } ?></b></td><!-- Added on 27-05-2022 by Amol, to show grade selected by OIC while final grading -->
	  </tr>	
	</table> 