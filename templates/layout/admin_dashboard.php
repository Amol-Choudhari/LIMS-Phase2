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
	//css
	echo $this->Html->meta('icon');
	echo $this->Html->charset();

	//CSS FILES
	echo $this->Html->css('adminlte.min.css');
	echo $this->Html->css('cwdialog');
	echo $this->Html->css('bootstrap.min');
	echo $this->Html->css('OverlayScrollbars.min');
	echo $this->Html->css('icheck-bootstrap.min');
	echo $this->Html->css('../dashboard/css/datepicker3');
	echo $this->Html->css('../dashboard/css/bootstrap-glyphicons.min');
	echo $this->Html->css('../dashboard/plugins/fontawesome-free/css/all.min');
	echo $this->Html->css('jquery-ui');
	echo $this->Html->css('jquery-ui.structure');
	echo $this->Html->css('jquery-ui.theme');
	echo $this->Html->css('../chosen-select/chosen');
	echo $this->Html->css('jquery.multiselect');
	echo $this->Html->css('custom-style');
	echo $this->Html->css('dataTables.bootstrap.min');
	echo $this->Html->css('jquery.dataTables.min');
	echo $this->Html->css('select2.min');
	echo $this->Html->css('jquery-confirm.min');
	echo $this->Html->css('toastr.min');
	//Added by Shweta Apale 20-10-2021
	echo $this->Html->css('report');
	


	echo $this->Html->css('../dashboard/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min');
	echo $this->Html->css('../dashboard/plugins/icheck-bootstrap/icheck-bootstrap.min');
	echo $this->Html->css('../dashboard/plugins/jqvmap/jqvmap.min');
	echo $this->Html->css('../dashboard/plugins/overlayScrollbars/css/OverlayScrollbars.min');
	echo $this->Html->css('../dashboard/plugins/daterangepicker/daterangepicker');
	echo $this->Html->css('../dashboard/plugins/summernote/summernote-bs4');

	//JS FILES

	echo $this->Html->script('jquery_main.min');
	echo $this->Html->script('jquery-ui');
	echo $this->Html->script('../dashboard/js/lumino.glyphs');
	echo $this->Html->script('sha512.min');
	echo $this->Html->script('cwdialog');
	echo $this->Html->script('validation');
	echo $this->Html->script('admin_forms_validation');
	echo $this->Html->script('table_filter');
	echo $this->Html->script('ckeditor/ckeditor', array('inline' => false));
	echo $this->Html->script('../chosen-select/chosen.jquery');
	echo $this->Html->script('../multiselect/jquery.multiselect');
	echo $this->Html->Script('select2.min'); // call select dropdown searching js file,
	echo $this->Html->script('jquery-confirm.min');
	echo $this->Html->script('toastr.min');
	echo $this->Html->script('master_validations');
	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
	?>

	<title>Directorate of Marketing & Inspection</title>
</head>

<?php echo $this->element('common_loader'); ?>
<body class="hold-transition sidebar-mini layout-fixed">


	<?php echo $this->element('dashboard_side_menus'); ?>

	<div class="wrapper main-header">
		<!-- Main Sidebar Container -->

		<?php echo $this->element('main_site_header'); ?>


		<?php echo $this->fetch('content'); ?>

		<div id="toast-container" class="toast-top-right">
			<div id="toast-msg-box-error" class="toast toast-error" aria-live="assertive">
				<div class="toast-message" id="toast-msg-error"></div>
			</div>
			<div id="toast-msg-box-success" class="toast toast-success" aria-live="assertive">
				<div class="toast-message" id="toast-msg-success"></div>
			</div>
		</div>

		<?php echo $this->element('footer_section'); ?>

	</div>


	<?php
	echo $this->Html->script('adminlte');
	echo $this->Html->script('bootstrap.min');
	echo $this->Html->script('../dashboard/js/jquerysession');

	echo $this->Html->script('../dashboard/js/jquery.dataTables.min');
	echo $this->Html->script('../dashboard/js/dataTables.bootstrap.min');
	echo $this->Html->script('../dashboard/js/bootstrap-datepicker');
	echo $this->Html->script('bs-custom-file-input.min'); //used for file input tags

	echo $this->Html->script('no_back');
	echo $this->Html->script('../dashboard/plugins/bootstrap/js/bootstrap.bundle.min');
	echo $this->Html->script('../dashboard/plugins/chart.js/Chart.min');
	echo $this->Html->script('../dashboard/plugins/sparklines/sparkline');
	echo $this->Html->script('../dashboard/plugins/jqvmap/jquery.vmap.min');
	echo $this->Html->script('../dashboard/plugins/jqvmap/maps/jquery.vmap.india');
	echo $this->Html->script('../dashboard/plugins/jquery-knob/jquery.knob.min');
	echo $this->Html->script('../dashboard/plugins/moment/moment.min');
	echo $this->Html->script('../dashboard/plugins/daterangepicker/daterangepicker');
	echo $this->Html->script('../dashboard/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min');
	echo $this->Html->script('../dashboard/plugins/summernote/summernote-bs4.min');
	echo $this->Html->script('../dashboard/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min');
	echo $this->Html->script('../dashboard/dist/js/pages/dashboard');

	?>

	<?php
	//added this code to fetch message boxes view commonly
	//11-02-2021
	if (!empty($message)) {
		echo $this->element('message_boxes');
	}
	?>

	<?php echo $this->Html->script('layout/admin_dashboard'); ?>

</body>

</html>
