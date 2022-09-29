<?php ?>

<!-- on 23-10-2017, Below noscript tag added to check if browser Scripting is working or not, if not provided steps -->
<noscript>
		<?php echo $this->element('javascript_disable_msg_box'); ?>
</noscript>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Directorate of Marketing & Inspection</title>
<?php
		echo $this->Html->meta('icon');
		echo $this->Html->charset();
		//echo $this->Html->css('forms-style');
		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('../dashboard/css/datepicker3');
		echo $this->Html->css('font-awesome.min');
		echo $this->Html->css('cwdialog');
		echo $this->Html->css('tempusdominus-bootstrap.min');
		echo $this->Html->css('icheck-bootstrap.min');
		echo $this->Html->css('jqvmap.min');
		echo $this->Html->css('adminlte.min');
		echo $this->Html->css('OverlayScrollbars.min');
		echo $this->Html->css('daterangepicker');
		echo $this->Html->css('summernote-bs4');
		echo $this->Html->css('all.min');
		echo $this->Html->css('dataTables.bootstrap.min');
		echo $this->Html->css('responsive.bootstrap.min');
		echo $this->Html->css('toastr.min');
		echo $this->Html->css('custom-style');
		echo $this->Html->css('jquery-confirm.min');

		//echo $this->Html->script('jquery.min');
		echo $this->Html->script('jquery_main.min'); //newly added on 24-08-2020 updated js
		echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('sha512.min');
		echo $this->Html->script('validation');
		echo $this->Html->script('primary_forms_validations');
		echo $this->Html->script('jssor.slider-21.1.6.min');
		echo $this->Html->script('no_back');
		echo $this->Html->script('cwdialog');
		echo $this->Html->script('../dashboard/js/bootstrap-datepicker');
		echo $this->Html->script('jquery-confirm.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

	?>
</head>

<?php echo $this->element('common_loader'); ?>
<body class="sidebar-mini layout-boxed">
<!-- Main Site Header-->
	<?php echo $this->element('main_site_header'); ?>

	<!-- Login User Content-->
		<div class="wrapper boxformenus">
				<div id="content-wrapper form_layout_wrapper">
					<?php echo $this->fetch('content'); ?>
				</div>

		<!-- Toaast Messages-->
				<div id="toast-container" class="toast-top-right">
					<div id="toast-msg-box-error" class="toast toast-error" aria-live="assertive">
						<div class="toast-message" id="toast-msg-error"></div>
					</div>
					<div id="toast-msg-box-success" class="toast toast-success" aria-live="assertive">
						<div class="toast-message" id="toast-msg-success"></div>
					</div>
			</div>

			<!--SideBAR-->
				<aside class="main-sidebar sidebar-dark-primary elevation-4">
					<nav class="mt-2">
        		<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    					<li class="nav-item">
								<a href="/DMI" class="nav-link">
								<b>Home</b></a>
			 				</li>
						</ul>
      		</nav>
 			 	</aside>
			</div>

				<!-- FOOTER SECTION-->
		<div class="wrapper">
			<?php echo $this->element('footer_section'); ?>
		</div>

			<?php
				//added this code to fetch message boxes view commonly BY akash //11-02-2021
				if(!empty($message)){

					echo $this->element('message_boxes');
				}
			?>
	</body>
</html>

<input type="hidden" id="csrftoken" value="<?= json_encode($this->request->getParam('_csrfToken')) ?>">
<?php echo $this->Html->script("layout/form_layout"); ?>
