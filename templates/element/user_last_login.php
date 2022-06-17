
	
	<?php if($user_last_login == 'First login'){
			
			echo "No last log";
		}else{ ?>

		<?php	echo $user_last_login['date'];?> <?php echo $user_last_login['time_in']; ?>
	
	<?php } ?>