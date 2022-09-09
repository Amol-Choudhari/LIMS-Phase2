<?php

    namespace app\Controller\Component;
    use Cake\Controller\Controller;
    use Cake\Controller\Component;

    class CreatecaptchaComponent extends Component {

        public $components= array('Session');
        public $controller = null;
        public $session = null;

        public function initialize(array $config): void{
                parent::initialize($config);
                $this->Controller = $this->_registry->getController();
				        $this->Session = $this->getController()->getRequest()->getSession();
        }


/***************************************************************************************************************************************************************************************************/
       

        public function createCaptcha(){

            ob_clean();
            header('Content-type: image/png');

            //Create the Image
            $im = imagecreatetruecolor(130, 35);

            //Create Colors
            $white = imagecolorallocate($im, 255, 255, 255);
            $grey = imagecolorallocate($im, 128, 128, 128);
            $black = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, 399, 35, $grey);

            // Text to Draw
            $text = $this->Session->read('code');

            //Replace Path By Our Own Font Path
            $font = WWW_ROOT.'font/Slabo27px-Regular.ttf';

            //Shadow To The Text
            //imagettftext($im, 13, 0, 11, 21, $grey, $font, $text);

            //Add Text
            imagettftext($im, 17, 5, 40, 27, $white, $font, $text);
			
			//Adding Random Lines in image 05-05-2021 by Amol
			$linecolor = imagecolorallocate($im, 255,255,255);
			for($i=0;$i<5;$i++){
				
				imageline($im,0,rand()%50,200,rand()%50,$linecolor);
			}
			
			//adding random dots in image 05-05-2021 by Amol
			$pixelcolor = imagecolorallocate($im, 255,255,255);
			for($i=0;$i<500;$i++){
				
				imagesetpixel($im,rand()%200,rand()%50,$pixelcolor);
			}

            // Using imagepng() results in clearer text compared with imagejpeg()
            imagepng($im);
            imagedestroy($im);
        }


/***************************************************************************************************************************************************************************************************/


		//this function is created to get captcha image on refresh captcha via ajax.
		//on 25-10-2017 by Amol
		public function refreshCaptchaCode(){

			$string = "";
			//$chars = "A0B1C2D3E4F5G6H7I8J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z";
			$chars = "ABC2D3E4F5G6H78JKL2M3N45P6Q7R8S9TUV2W3X4Y5Z"; //removed O,0,1,I from list on 09-10-2017 by Amol
			//updated logic on 12-08-2017 by Amol to contain atleast one number in string
			$match = null;
			//while(count($string)<=5){
				while($match != 1){
					for($i=0;$i<6;$i++){
						$string.=substr($chars,rand(0,strlen($chars)-1),1);
						$match = preg_match('~[0-9]~', $string);
					}
					if($match == 1 ){
						$string = substr($string, 0, 6);
						$code=$string;
						$_SESSION["code"]=$code;
							  
						echo "<label class='refresh_captcha'>".$_SESSION['code']."</label>";
					}
				}
		}
    }
?>
