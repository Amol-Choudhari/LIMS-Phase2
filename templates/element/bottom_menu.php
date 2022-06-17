<?php 
?>
<ul>
<?php
foreach($bottommenus as $bottommenu){ ?>
<li>
<?php
 if(!empty($bottommenu['external_link'])){

		$url = 'home?'.'$type='.$bottommenu['link_type'].'&'.'$page='.$bottommenu['link_id'].'&'.'$menu='.$bottommenu['id'];
		echo $this->Html->link(__($bottommenu['title'], $url), array('controller' => 'pages', 'action'=>$url)); 
		
		
		
	}else{
		
		$url = 'home?'.'$type='.$bottommenu['link_type'].'&'.'$page='.$bottommenu['link_id'];
		echo $this->Html->link(__($bottommenu['title'], $url), array('controller' => 'pages', 'action'=>$url)); 
		
	} 
?>

 </li>
<?php } ?>	
</ul>