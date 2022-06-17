<?php 
?>
<ul>
<?php 
foreach($sidemenus as $sidemenu){ ?>
<li>
<?php

	if(!empty($sidemenu['external_link'])){

		$url = 'home?'.'$type='.$sidemenu['link_type'].'&'.'$page='.$sidemenu['link_id'].'&'.'$menu='.$sidemenu['id'];
		echo $this->Html->link(__($sidemenu['title'], $url), array('controller' => 'pages', 'action'=>$url)); 
		
		
		
	}else{
		
		$url = 'home?'.'$type='.$sidemenu['link_type'].'&'.'$page='.$sidemenu['link_id'];
		echo $this->Html->link(__($sidemenu['title'], $url), array('controller' => 'pages', 'action'=>$url)); 
		
	}
?>

 </li>
<?php } ?>	
</ul>

