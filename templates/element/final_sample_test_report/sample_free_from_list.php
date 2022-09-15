<?php 	
			if(isset($method_homo)){  ?>
				<tr>
					<td class="td1" ><span style="font-family: krutidev010; font-size:10px;">uewuk buls eqä Fkk vFkok ugha</span> / Whether the sample was free from</td>
				
					<td class="td1" colspan="3">
						<?php foreach($method_homo as $method){
								if($method['m_sample_obs_code']!=1 && $method['m_sample_obs_code']!=2){ ?>															
								
									<?php echo $method['m_sample_obs_desc']; ?> : <?php echo  $method['m_sample_obs_type_value']; ?><br />
									
								<?php }
							} 
						?>
					</td>
				</tr>
		<?php } ?>