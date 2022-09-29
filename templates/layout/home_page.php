<?php ?>
<!-- on 23-10-2017, Below noscript tag added to check if browser Scripting is working or not, if not provided steps -->	
<noscript>
		<?php echo $this->element('javascript_disable_msg_box'); ?>
</noscript>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta name="viewport" content="width=device-width,initial-scale=1">
<?php
			
		echo $this->Html->meta('icon');
		echo $this->Html->charset();
		echo $this->Html->css('forms-style');
		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('font-awesome.min');
		
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('md5');
		echo $this->Html->script('validation');
		//echo $this->Html->script('jquery.min');
		
		echo $this->Html->script('jquery_main.min'); //newly added on 24-08-2020 updated js
		echo $this->Html->script('sha512.min');
		echo $this->Html->script('jssor.slider-21.1.6.min');
		echo $this->Html->script('no_back');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>

<script type="text/javascript">
        jssor_1_slider_init = function() {

            var jssor_1_SlideoTransitions = [
              [{b:0,d:600,y:-290,e:{y:27}}],
              [{b:0,d:1000,y:185},{b:1000,d:500,o:-1},{b:1500,d:500,o:1},{b:2000,d:1500,r:360},{b:3500,d:1000,rX:30},{b:4500,d:500,rX:-30},{b:5000,d:1000,rY:30},{b:6000,d:500,rY:-30},{b:6500,d:500,sX:1},{b:7000,d:500,sX:-1},{b:7500,d:500,sY:1},{b:8000,d:500,sY:-1},{b:8500,d:500,kX:30},{b:9000,d:500,kX:-30},{b:9500,d:500,kY:30},{b:10000,d:500,kY:-30},{b:10500,d:500,c:{x:87.50,t:-87.50}},{b:11000,d:500,c:{x:-87.50,t:87.50}}],
              [{b:0,d:600,x:410,e:{x:27}}],
              [{b:-1,d:1,o:-1},{b:0,d:600,o:1,e:{o:5}}],
              [{b:-1,d:1,c:{x:175.0,t:-175.0}},{b:0,d:800,c:{x:-175.0,t:175.0},e:{c:{x:7,t:7}}}],
              [{b:-1,d:1,o:-1},{b:0,d:600,x:-570,o:1,e:{x:6}}],
              [{b:-1,d:1,o:-1,r:-180},{b:0,d:800,o:1,r:180,e:{r:7}}],
              [{b:0,d:1000,y:80,e:{y:24}},{b:1000,d:1100,x:570,y:170,o:-1,r:30,sX:9,sY:9,e:{x:2,y:6,r:1,sX:5,sY:5}}],
              [{b:2000,d:600,rY:30}],
              [{b:0,d:500,x:-105},{b:500,d:500,x:230},{b:1000,d:500,y:-120},{b:1500,d:500,x:-70,y:120},{b:2600,d:500,y:-80},{b:3100,d:900,y:160,e:{y:24}}],
              [{b:0,d:1000,o:-0.4,rX:2,rY:1},{b:1000,d:1000,rY:1},{b:2000,d:1000,rX:-1},{b:3000,d:1000,rY:-1},{b:4000,d:1000,o:0.4,rX:-1,rY:-1}]
            ];

            var jssor_1_options = {
              $AutoPlay: true,
              $Idle: 2000,
              $CaptionSliderOptions: {
                $Class: $JssorCaptionSlideo$,
                $Transitions: jssor_1_SlideoTransitions,
                $Breaks: [
                  [{d:2000,b:1000}]
                ]
              },
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };

            var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

            /*responsive code begin*/
            /*you can remove responsive code if you don't want the slider scales while window resizing*/
            function ScaleSlider() {
                var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
                if (refSize) {
                    refSize = Math.min(refSize, 1140);
                    jssor_1_slider.$ScaleWidth(refSize);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }
            ScaleSlider();
            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            /*responsive code end*/
        };
    </script>
<title>Directorate of Marketing & Inspection</title>
</head>

<body>

<div class="container">
		<div id="header">
			<?php echo $this->element('main_site_header'); ?> 		
			
        </div>   
		<!-- Change banner image path // Done  by pravin 28/04/2018 -->
		<div class="banner-slider">
				<div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: 1140px; height: 240px; overflow: hidden; visibility: hidden;">
					<!-- Loading Screen -->
					<div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
						<div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
						<div style="position:absolute;display:block;background:url('img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
					</div>
					<div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 1140px; height: 240px; overflow: hidden;">
						<div data-p="112.50">
							<img data-u="image" src="/writereaddata/home-slider/banner1.jpg" />
							
						</div>
						<div data-p="112.50" style="display: none;">
							<img data-u="image" src="/writereaddata/home-slider/banner2.jpg" />
							
						</div>
						<div data-p="112.50" style="display: none;">
							<img data-u="image" src="/writereaddata/home-slider/banner3.jpg" />
							
						</div>
						<div data-p="112.50" style="display: none;">
							<img data-u="image" src="/writereaddata/home-slider/banner4.jpg" />
							
						</div>
						<div data-p="112.50" style="display: none;">
							<img data-u="image" src="/writereaddata/home-slider/banner5.jpg" />
							
						</div>
						<div data-p="112.50" style="display: none;">
							<img data-u="image" src="/writereaddata/home-slider/banner6.jpg" />
							
						</div>
						
					</div>
					<!-- Bullet Navigator -->
					
					<!-- Arrow Navigator -->
					<span data-u="arrowleft" class="jssora02l" style="top:0px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
					<span data-u="arrowright" class="jssora02r" style="top:0px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
				</div>
	</div>
        
       <!-- <div id="menu">
        	<ul>
            	<li class="menuitem"><a href="index.html">Home</a></li>
                <li class="menuitem"><a href="acts_rules.html">Acts & Rules</a></li>
                <li class="menuitem"><a href="#">e-NAM</a></li>
                <li class="menuitem"><a href="#">Agmark</a></li>
                <li class="menuitem"><a href="#">Schemes</a></li>
				<li class="menuitem"><a href="#">Agmark Portal</a></li>
				<li class="menuitem"><a href="#">Contact</a></li>
             
            </ul>
        </div> -->
     <div class="main-content-outer">   
        <div id="leftmenu">

        <div id="leftmenu_top"></div>

			<div id="leftmenu_main">    
             
                <?php echo $this->element('side_menu', array('menus' => $menus)); ?>        
                
			</div>
                
                
              <div id="leftmenu_bottom"></div>
        </div>
        
        
        
        
		<div id="content">
        
        <!-- Create login window Title dynamic // Done by pravin 28/04/2018 -->
        <!--<div id="content_top"></div>-->
        <div id="content_main">
        	<h2><?php echo $home_page_content[3]['title']; ?></h2>
<h3><?php echo $home_page_content[4]['title']; ?></h3>
        	
		<div class="service">	
		<a href="<?php echo $this->request->getAttribute('webroot'); ?>customers/login-customer-redirect/1">
			<img src="img/other/ca.png">
       	  <h3><?php echo $home_page_content[5]['title']; ?></h3>  
		 </a>
		</div>
		
		<div class="service">	
		<a href="<?php echo $this->request->getAttribute('webroot'); ?>customers/login-customer-redirect/2">
			<img src="img/other/cpp.png">
       	  <h3><?php echo $home_page_content[6]['title']; ?></h3>  
		 </a>
		</div>
		
		<div class="service">	
		<a href="<?php echo $this->request->getAttribute('webroot'); ?>customers/login-customer-redirect/3">
			<img src="img/other/cl.png">
       	  <h3><?php echo $home_page_content[7]['title']; ?></h3>
		</a>
		</div>
		
		<div class="service">	
		<a href="../LIMS/users/login_user">
			<img src="img/other/lmis.png">
       	  <h3><?php echo $home_page_content[8]['title']; ?></h3>
		</a>
		</div>
		<div class="clear"></div>
            

        </div>
        <!--<div id="content_bottom"></div>-->
            
    
      </div><div class="clear"></div>
	  
	</div>  
	  
<div id="footerPanel">
	<div class="footerStrip">
		<div class="mid">
			<?php echo $this->element('bottom_menu', array('bottommenus' => $bottommenus)); ?>
		</div>
	</div>
	
	
	<?php echo $this->element('footer_section'); ?>
	
	
</div>
	  
	  
	  
	  
	  
   </div>
   
   
  
   
 <script type="text/javascript">jssor_1_slider_init();</script>  
 
 <?php 
 //added this code to fetch message boxes view commonly
 //11-02-2021
	if(!empty($message)){	 
		echo $this->element('message_boxes');
	}
?>
   
</body>
</html>
