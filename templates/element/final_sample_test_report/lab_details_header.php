<table width="100%" border="1">
	<tr>				
	<?php if($showNablLogo=='yes'){ ?>
		<td width="79%" align="center">
	<?php }else{ ?>	
		<td align="center">
	<?php } ?>
		
			<?php 
				if($test_report[0]['grade_user_flag']=="CAL" ){ ?>
					
					<h5><span style="font-family: krutidev010;">dsanzh; ,xekdZ ç;ksx“kkyk</span> / Central Agmark Laboratory<br />
					<span style="font-family: krutidev010;">mŸkj vEck>jh ekxZ</span> / North Ambazari Road Nagpur 440010<br />
					Phone:0712-2561748,Fax: 0712-2540952 T-2315 mail:cal@nic.in</h5>
			
			<?php } ?>
			
			<?php 
				if($test_report[0]['grade_user_flag']=="RAL" ){ ?>
					
					<h5><span style="font-family: krutidev010; font-weight:bold; font-size:13px;">{¨™kh; ,xekdZ ç;ksx“kkyk</span> / Regional Agmark Laboratory , <?php echo $_SESSION['ro_office'];?></h5>
			
			<?php }elseif(isset($test_report[0]['ro_office']) && isset($ral_lab_name) && $ral_lab_name=='RAL'){ ?>
			
					<h5><span style="font-family: krutidev010; font-weight:bold; font-size:13px;">{¨™kh; ,xekdZ ç;ksx“kkyk</span> / Regional Agmark Laboratory , <?php echo $test_report[0]['ro_office'];?></h5>
			<?php } ?>	
		
		</td>
		<?php if($showNablLogo=='yes'){ ?>
			<td width="21%" align="center">
				<img width="45" src="img/logos/nabl-logo.png">
				<p><?php echo  $certNo ; ?></p>
			</td>
		<?php } ?>
	</tr>
</table>