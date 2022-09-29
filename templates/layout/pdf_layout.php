<?php ?>
<!-- on 23-10-2017, Below noscript tag added to check if browser Scripting is working or not, if not provided steps -->	
<noscript> <?php //echo $this->element('javascript_disable_msg_box'); ?></noscript>
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
			//echo $this->Html->script('jquery.min');
			
			echo $this->Html->script('jquery_main.min'); //newly added on 24-08-2020 updated js
		
			echo $this->fetch('meta');
			echo $this->fetch('css');
			echo $this->fetch('script');
		?>	
	</head>
	<body>
		<div id="content_main">
			<?php echo $this->Flash->render(); ?>
			<?php echo $this->fetch('content'); ?>
			<div class="clear"></div>
		</div>		
	</body>
</html>