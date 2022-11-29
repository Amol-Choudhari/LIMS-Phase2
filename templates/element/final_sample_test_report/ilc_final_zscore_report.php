
<tr>
    <td width="5%"><b>S.No.</b></td>											
    <td witdh="20%"><b>Name Of Parameter</b></td>
    
    <?php
    foreach($result as $eachoff){ ?>
        <td witdh="10%">value</td>
        <td><?php echo $eachoff['ro_office']; ?> (<?php echo $eachoff['office_type']; ?>)</td>
    <?php
    }
    
    ?>

</tr>

<?php		

    if (isset($testarr)) {	

        $j=1;		
        $i=0;	
        foreach ($testarr as $eachtest) { ?>
        
        <tr>
            <td><?php echo $j; ?></td>   
            <td><?php echo $testnames[$i]; ?> </td>
            <?php

                $l=0;
                foreach($smplList as $eachoff){
                ?>
                <?php
                    $num = (int) $zscorearr[$i][$l];
                    $format = round($num, 2);
                ?>
                <td>20.1</td>
                <td><?php echo $format; ?> </td>
                
            <?php $l++;	} ?>

        </tr>
                    
                        
<?php $i++; $j++; } } ?>
            
                    
                        


                    
                    