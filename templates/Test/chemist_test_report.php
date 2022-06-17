<?php ?>
<style>
	h4 {
		padding: 5px;
		font-family: times;
		font-size: 12pt;
	}	

	h5 {
		padding: 5px;
		font-family: times;
		font-size: 11pt;
	}

	table{
		padding: 5px;
		font-size: 9pt;
		font-family: times;
	}

				
</style>
	
	
	<table width="100%" border="1">
		<tr>				
			<td width="12%" align="center">
				<img width="35" src="img/logos/emblem.png">
			</td>
			<td width="76%" align="center">
				<h4>Government of India <br> Ministry of Agriculture and Farmers Welfare<br>
				Department of Agriculture & Farmers Welfare<br>
				Directorate of Marketing & Inspection</h4>				
			</td>
			<td width="12%" align="center">
				<img src="img/logos/agmarklogo.png">
			</td>				
		</tr>
	</table>
	
	
	<table width="100%" border="0">
		<h4 align="center">Chemist Test Report</h4>
	</table>
	
	<table width="100%" border="1">
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">i.;   dk  uke</span> /  Name of the Commodity</td>
			<td><?php echo $sample_details['category_name']; ?> <?php echo $sample_details['commodity_name']; ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">jlk;uK  uequk  dksM  la[;k  ,o  ek=k</span> / <br> Code no of the sample of chemist & Qty.</td>
			<td> <?php echo $sample_details['stage_smpl_cd']; ?>   /   <?php echo $smpl_qty.' '.$smpl_unit; ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">vuqHkkx  dksM  la[;k </span> / Section Code No</td>
			<td></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">{ks- dk -  dksM  la[;k </span> / R.O Code No</td>
			<td></td>
		</tr>
		<tr> 
			<td><span style="font-family: krutidev010; font-size:10px;">uequk  çkIrh  fd  rkfjd </span> / Date of receipt of the sample</td>
			<td><?php echo $sample_details['recby_ch_date']; ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">uequk fo'ys"k.k  vkjaHk fd rkfjd </span> / Date of commencement of analysis</td>
			<?php 
			if(isset($sample_allocated_test))
				{ 
					$j=0;
					foreach($sample_allocated_test as $each_test) ?>
					<td><?php echo $each_test['test_perfm_date']; ?></td>
			<?php $j++;  }  ?>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">fjiksVZ çLrqr  dj.ks fd rkfjd  </span> / Date of submission of report</td>
			<td><?php echo $test_finalized_date; ?></td>
			
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">daVsuj  dk çdkj    </span> / Type of container</td>
			<td><?php echo $sample_details['container_desc']; ?></td>
		</tr>
		<tr>
			<td><span style="font-family: krutidev010; font-size:10px;">lhy  fd fLrFkh   </span> / Condition of seal</td>
			<td><?php echo $sample_details['par_condition_desc']; ?></td>
		</tr>
		<?php 	
			if(isset($method_homo)){  ?>
				<tr>
					<td class="td1" ><span style="font-family: krutidev010; font-size:10px;">fo'ys"k.k   ls  iwoZ  uequs dks gksekstukbt  dj.ks fd  fof'k"V  çfØ;k  / i)rh</span> / Specific procedure followed for homogenizing the sample before analysis
			</td>
				
					<td class="td1" colspan="3">
						<?php foreach($method_homo as $method ){

								if($method['m_sample_obs_code']==25){ ?>															
								
								<?php echo $method['m_sample_obs_desc']; ?> : <?php echo  $method['m_sample_obs_type_value']; ?><br />
									
								<?php }
							} 
						?>
					</td>
				</tr>
		<?php } ?>
		<?php if(isset($method_homo)){  ?>
				<tr>
					<td>	
					<span style="font-family: krutidev010; font-size:10px;">uewus  fd  HkkSfrd   ljprk ,oa   lkekU;  oh'ks"krk;s </span> / physical appearance of the sample  General characteristics
					</td>
				
					<td class="td1" colspan="3">
						<?php foreach($method_homo as $method){
								if($method['m_sample_obs_code']==24){ ?>															
								
									<?php echo $method['m_sample_obs_desc']; ?> : <?php echo  $method['m_sample_obs_type_value']; ?><br />
									
								<?php }
							} 
						?>
					</td>
				</tr>
		<?php } ?>
		<?php if(isset($method_homo)){  ?>
				<tr>
					<td>	
					<span style="font-family: krutidev010; font-size:10px;"> ls jfgr</span> // Free From:<br><br>
						1)<span style="font-family: krutidev010; font-size:10px;"> feyk;s  x;s  jax   </span> / Added colour:<br>
						2)<span style="font-family: krutidev010; font-size:10px;"> lsMhesaV  vkSj  llisaMsaM  eSVj   </span> / Sediment and Suspended Matter <br>
						3)<span style="font-family: krutidev010; font-size:10px;"> [kVokl   </span> / Rancidity
					</td>
				
					<td class="td1" colspan="3">
						<?php foreach($method_homo as $method){
								if($method['m_sample_obs_code']==16){ ?>															
								
									<?php echo $method['m_sample_obs_desc']; ?> : <?php echo  $method['m_sample_obs_type_value']; ?>
									
								<?php } ?><br>
								<?php

								if($method['m_sample_obs_code']==4){ ?>															
																
									<?php echo $method['m_sample_obs_desc']; ?> : <?php echo  $method['m_sample_obs_type_value']; ?>
									
								<?php } ?>

								<?php

								if($method['m_sample_obs_code']==5){ ?>															
																
									<?php echo $method['m_sample_obs_desc']; ?> : <?php echo  $method['m_sample_obs_type_value']; ?>
									
								<?php } 
							} 
						?>
					</td>
				</tr>
		<?php } ?>

		
	</table>
		
	<br pagebreak="true" />	
	<table width="100%" border="1">
	<tr>
		<td rowspan="2" width="5%"><b>S.No. <span style="font-family: krutidev010; font-size:10px;">Ø-la</span></b></td>											
		<td rowspan="2" width="23%"><b><span style="font-family: krutidev010; font-size:10px;">fof'k"V fo'ks"krk,</span>/Special Characteristics</b></td>
		<td  colspan="<?php if(isset($commo_grade)){echo count($commo_grade); }?>" ><b><span style="font-family: krutidev010; font-size:10px;">fofunsZ'kks dh jsat</span>/Range of Specification</b></td>											

    <?php if($count_test_result>0){
                
                for($i=1;$i<=$count_test_result;$i++){ ?>
                
                    <td  colspan="1"rowspan="2"><br><br><b>Chemist <?php echo $i; ?></b></td>												
            <?php } ?>
            
            <td  colspan="1"rowspan="2"><br><br><b>Approved Result</b></td>
            
    <?php }else { ?>
        
    <?php 	} ?>
	<td rowspan="2"><span style="font-family: krutidev010; font-size:10px;">jlk;uK  }kjk  çkIr   oSY;w   </span> </td>
	<td  rowspan="2"><br><br><b><span style="font-family: krutidev010; font-size:10px;">vkSlr </span>/ Average Value rounded off by I/C</b></td>
    <td  rowspan="2"><b><span style="font-family: krutidev010; font-size:10px;">viukbZ x;h i)fr</span>/ Method Followed</b></td>
   
	</tr>
	
	<tr>
	
        <?php 	if(isset($commo_grade)){ 
    
                foreach($commo_grade as $row){ ?>
                
                    <td align="center"><?php echo $row['grade_desc']; ?></td>
                    
                <?php }
            }
    	?>
	</tr>

	
	
	<?php if(isset($table_str)){ echo $table_str; }?>
	
	
</table>

<table width="100%">
  <tr>
	<td width="5%">A</td>
	<td width="20%"><b>Conventional Titration</b></td>

  </tr>	
  <tr>
  <td width="5%">B</td>
	<td width="20%"><b>Auto Titration</b></td>
  </tr>
</table> 

<br><br>
<table width="100%">
  	<tr>
		<td>
			<span style="font-family: krutidev010; font-size:10px;">fnukad</span><br>
			<span style="font-family: krutidev010; font-size:10px;">fo'ys"k.k  jlk;uK  gLrk{kj </span><br>
			Sigature of analyzing Chemist
		</td>
	  
		 
		
					
	  
		  
		<td align="right">
			<span style="font-family: krutidev010; font-size:10px;">vuqHkkx  vf/kdkjh  </span><br>
			<span style="font-family: krutidev010; font-size:10px;">dsaæh;   ,xekdZ  ç;ksx'kkyk ] ukxiwj</span>
		</td>
	</tr>	
</table> 
<br><br>
<table width="100%">
  	<tr>
		<td>
			<span style="font-family: krutidev010; font-size:10px;">tkp  ,oa  lR;kfir  fd xbZ </span><br>
			Checked and verified by
		</td>
	  
		 
	
					
	  
		  
		<td align="right">Approved by:<br>
			<span style="font-family: krutidev010; font-size:10px;">funs'kd ç;ksx'kkys; </span> / Director Laboratory 
		</td>
  	</tr>	
</table> 

			
