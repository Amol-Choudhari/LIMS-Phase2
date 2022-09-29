<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width,initial-scale=1">
    <?php 
	echo $this->fetch('css');
	echo $this->Html->css('my_custom_error'); ?>
</head>
<body>
    <div class="error-container">
        <div class="myerrorclass">
		
			<h3>Oops!</h3>
			<h4>Something Wrong Happened.. Please Try Again</h4>
			<p><a href="/"> Way to Home Page</a></p>
		
		</div>
    </div>
</body>
</html>
