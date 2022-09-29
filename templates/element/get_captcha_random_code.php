<?php 
    //created on 15-07-2017 by Amol
    $string = "";
    //$chars = "A0B1C2D3E4F5G6H7I8J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z";
    $chars = "ABC2D3E4F5G6H78JKL2M3N45P6Q7R8S9TUV2W3X4Y5Z"; //removed O,0,1,I from list on 09-10-2017 by Amol
    //updated logic on 12-08-2017 by Amol to contain atleast one number in string
    $match = null;	
    while($match != 1){
        for($i=0;$i<6;$i++){
                $string.=substr($chars,rand(0,strlen($chars)-1),1);	
                $match = preg_match('~[0-9]~', $string);
        }
        if($match == 1 ){
                $string = substr($string, 0, 6);//string should not more than 5 length
                $code=$string;
                $_SESSION["code"]=$code;

        }
    }
?>