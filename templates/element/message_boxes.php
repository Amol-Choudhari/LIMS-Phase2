<?php echo $this->Html->css("message_box/message_box"); ?>

<?php

// SET MESSAGE HEADER THEME AS PER RESPONSE LIKE success, failed
// By Aniket Ganvir dated 8th DEC 2020
if (!isset($message_theme) || $message_theme=='') {

	$message_theme = 'info';
}

if ($message_theme == 'success') {

	$message_header = "";
	$message_header .= "<div class='mod-header mod-header-success'>";
	$message_header .= "<i class='fa fa-check-circle'></i> ";
	$message_header .= "Success";
	$message_header .= "</div>";

} elseif ($message_theme == 'failed') {

	$message_header = "";
	$message_header .= "<div class='mod-header mod-header-failed'>";
	$message_header .= "<i class='fas fa-times-circle'></i> ";
	$message_header .= "Failed";
	$message_header .= "</div>";

} elseif ($message_theme == 'warning') {

	$message_header = "";
	$message_header .= "<div class='mod-header mod-header-warning'>";
	$message_header .= "<i class='fa fa-info-circle'></i> ";
	$message_header .= "Warning";
	$message_header .= "</div>";

} elseif ($message_theme == 'alertinfo') {

	$message_header = "";
	$message_header .= "<div class='mod-header mod-header-alertinfo'>";
	$message_header .= "<i class='fas fa-exclamation-triangle'></i> ";
	$message_header .= "Alert";
	$message_header .= "</div>";

} else {

	$message_header = "";
	$message_header .= "<div class='mod-header'>";
	$message_header .= "<i class='fas fa-bell'></i> ";
	$message_header .= "Notification";
	$message_header .= "</div>";
}

?>

<div class="container-fluid mod-bg"></div>
<div class="mod container-fluid">
	<div class="mod-con">

		<?php echo $message_header; ?>

		<div class="mod-body">
			<p><?php echo $message; ?></p>
		</div>
		<div class="mod-footer">
			<button id="redirectto">Continue</button>
		</div>
	</div>
</div>
<input type="hidden" id="redirect_to" value="<?php echo $redirect_to; ?>">
<?php echo $this->Html->script("element/message_box"); ?>
