

		<div class="clear"></div>
		<div  id="footer" class="mt-4 pb-4 elevation-3 rounded">


			<div class="textAlignCenter">

				<?php  echo $footer_content; ?>

				<?php if($this->request->getAttribute('here') == $this->request->getAttribute('webroot')){ ?>
					<img class="elevation-3 rounded elevationst" src="img/NIC_logo.jpg" />
				<?php }elseif(empty($this->request->getParam('pass'))){ ?>
					<img class="elevation-3 rounded elevationst" src="<?php echo $this->request->getAttribute('webroot'); ?>img/NIC_logo.jpg" />
				<?php }else{ ?>
					<img class="elevation-3 rounded elevationst" src="<?php echo $this->request->getAttribute('webroot');?>img/NIC_logo.jpg" />
				<?php } ?>

			</div>
		</div>
