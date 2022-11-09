
<!-- Create homepage title dynamic and change logo image url path // Done by pravin 28/04/2018 -->
<div class="wrapper logo-title row">
	<div class="col-md-2 col-xs-2 header-img1">
		<img class="img-responsive" src="/writereaddata/logos/emblem.png">
	</div>
	<div class="col-md-8 col-xs-8 header-text">
	<!-- Updated on 27-08-2018, up downs-->
		<h2><?php echo $home_page_content[2]['title']; ?><br><?php echo $home_page_content[1]['title']; ?></h2>
		<h1><?php echo $home_page_content[0]['title']; ?></h1>
		
	</div>
	<div class="col-md-2 col-xs-2 header-img2">
		<img class="img-responsive" src="/writereaddata/logos/agmarklogo.png">
	</div>
	<div class="clear"></div>
</div>


<?php 
//for email encoding
if (filter_var(base64_decode((string) $this->getRequest()->getSession()->read('username'), FILTER_VALIDATE_EMAIL))) {

	if(isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']=='yes'){
		echo $this->element('user_header_login_strip');
	}
  
}
 

 ?>