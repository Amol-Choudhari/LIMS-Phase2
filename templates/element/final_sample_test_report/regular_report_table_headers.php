
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
				
						<td  rowspan="2"><br><br><b><span style="font-family: krutidev010; font-size:10px;">çkIr eku</span>/ Value Obtained</b></td>
						
			<?php 	} ?>
			
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
		
		
        
    


				
	
	