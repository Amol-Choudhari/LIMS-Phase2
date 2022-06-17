<?php
echo $this->Html->Script('bootstrap-datepicker.min');
echo $this->Html->script('reportCountSample/report-functionality');
//print  $this->Session->flash("flash", array("element" => "flash-message_new")); 
?>
<?php echo $this->Form->control('user_role_id', array('type' => 'hidden', 'id' => 'user_role_id', 'value'=>$_SESSION['role'], 'label' => false,)); ?>
<?php // echo $this->Form->input('user_role_id', array('type' => 'hidden', 'id' => 'user_role_id', 'value'=>$_SESSION['role'], 'label' => false,)); ?>


<?php //pr($user);  
?>
<div class="content-wrapper">
	<div class="content-header page-header" id="page-load">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h6 class="m-0 ml-3"><?php echo 'Statistics Reports'; ?></h6>
				</div>
				<div class="col-sm-6 my-auto">
				<ol class="breadcrumb float-sm-right">
					<span class="badge bg-light my-auto"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home'));?></a></span>
					<span class=""><i class="fas fa-chevron-right px-2" style="font-size:80%"></i><span class="badge page-header"><?php echo 'Reports'; ?></span></span>
					<span class=""><i class="fas fa-chevron-right px-2" style="font-size:80%"></i><span class="badge page-header"><?php echo 'Statistics Reports'; ?></span></span>
				</ol>
				</div>				  
			</div>
    	</div>
  	</div>

	<div class="row mx-1">
		<div class="col-md-12">
			<fieldset class="fsStyle">
				<legend class="legendStyle">Title</legend>
				<div class="panel-group row justify-content-center">
					<?php foreach($label as $label1) { ?>
						<?php if ($label1['label_code'] != 14) { ?>
							<span class="col-md-2 text-center shadow border rounded mx-1 sample-card my-auto">
								<span class="">
									<!-- <h4 class=""> -->
										<a id="<?php echo $label1['label_desc']; ?>"href="#panel<?php echo $label1['label_code']; ?>" onclick="hideparameter();"> <?php echo $label1['label_desc']; ?></a>
									<!-- </h4> -->
								</span>
								<div id="panel<?php echo $label1['label_code']; ?>" class="display-none">
									<div class="panel-body">
										<?php foreach ($report as $report1) :
											if ($label1['label_code'] == $report1['label_code']) {

										?>
												<p><label for="<?php echo $report1['report_desc']; ?>" id="<?php echo $report1['report_desc']; ?>" class="<?php echo $report1['report_desc']; ?>  control-label"><?php echo $report1['report_desc']; ?></label></p>
										<?php  }
										endforeach; ?>
									</div>
								</div>
							</span>
						<?php } ?>
					<?php } ?>
				</div>
			</fieldset>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 report-menu" style="margin-left: 25%;">
		<fieldset class="fsStyle">
			<legend class="legendStyle">Title</legend>
			<div class="panel-group" id="accordion">
				<?php foreach($label as $label1) { ?>
					<?php if ($label1['label_code'] != 14) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a id="<?php echo $label1['label_desc']; ?>" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#panel<?php echo $label1['label_code']; ?>" onclick="hideparameter();"><i class="glyphicon glyphicon-plus"></i> <?php echo $label1['label_desc']; ?></a>
								</h4>
							</div>
							<div id="panel<?php echo $label1['label_code']; ?>" class="panel-collapse collapse">
								<div class="panel-body">
									<?php foreach ($report as $report1) :
										if ($label1['label_code'] == $report1['label_code']) {

									?>
											<p><label for="<?php echo $report1['report_desc']; ?>" id="<?php echo $report1['report_desc']; ?>" class="<?php echo $report1['report_desc']; ?>  control-label"><?php echo $report1['report_desc']; ?></label></p>
									<?php  }
									endforeach; ?>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</fieldset>
	</div>
	<div class="col-md-6 parameters">
		<form id="rpt_comm_sample" method="post" action="" autocomplete="off" target="_blank">
			<input type="hidden" class="form-control" name="label_name" id="label_name">
			<fieldset class="fsStyle">
				<legend class="legendStyle">Parameters</legend>
				<?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin') { ?>
					<div class="row" id="paralast">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="last" class="validate[minCheckbox[2]] checkbox" value="" disabled>During Last</label>
							</div>
						</div>
						<div class="col-md-6">
							<label class="control-label" for="sel1">days</label>
							<div class="col-md-4">
								<input type="text" class="form-control" id="days" name="days">
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC') { ?>
					<div class="row" id="paraperiod">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="period" class="validate[minCheckbox[2]] checkbox" name="period" value="period" value="" disabled>Period</label>
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<!--<label class="control-label col-md-4" for="sel1">From Date</label>-->
								<div class="col-md-8">
									<div class="input-group input-append date" id="datePicker">
										<input type="text" class="form-control" name="from_date" id="from_date" title="From Date" placeholder="Form (dd/mm/yyyy)" id="letr_date" />
										<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
									</div>
								</div>
								<!-- Hint for the search from date, Added by Pravin Bhakare on 07-06-2019-->
								<label class="control-label" for="sel1">(Select Range of 'Sample Registration' Date)</label>
							</div>
						</div>
					</div>
					<div class="row" id="parato_date">
						<div class="col-md-6">
							<div class="checkbox">

							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<!--	<label class="control-label col-md-4" for="sel1">To date</label>-->
								<div class="col-md-8">
									<div class="input-group input-append date" id="datePicker1">
										<input type="text" class="form-control" name="to_date" id="to_date" title="To Date" placeholder="TO (dd/mm/yyyy)" id="letr_date" />
										<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
									</div>
								</div>
							</div>
						</div>
					</div><br>
				<?php }	?>
				<?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL') {  ?>
					<div class="row" id="paramonth">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="month1" class="validate[minCheckbox[2]] checkbox" name="montho" value="montho" disabled>Month</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="month" name="month">
								<option value=''>----Select----</option>
								<option value='1'>Janaury</option>
								<option value='2'>February</option>
								<option value='3'>March</option>
								<option value='4'>April</option>
								<option value='5'>May</option>
								<option value='6'>June</option>
								<option value='7'>July</option>
								<option value='8'>August</option>
								<option value='9'>September</option>
								<option value='10'>October</option>
								<option value='11'>November</option>
								<option value='12'>December</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC') {  ?>

					<!-- show loading image until ajax respond with result, added on 18th JAN 2021 by Aniket Ganvir -->
					<div class="row" id="loading_con">
						<div class="col-md-6">
						</div>
						<div class="col-md-4">
							<?php echo $this->Html->image('other/loader.gif', array('class' => 'loader_img')); ?>
						</div>
					</div>

					<div class="row" id="paralab1">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="lab1" class="validate[minCheckbox[2]] checkbox" name="labo" value="labo" disabled>Offices</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="lab" name="lab" onchange="getlab();">
								<option hidden="hidden" value=''>-----Select-----</option>
								<?php foreach ($user_flag as $user_flag1) : 	?>
									<?php /*if($_SESSION['user_flag']=="RAL"){  ?>
									<option value="<?php echo $_SESSION['user_flag']; ?>" selected><?php echo $_SESSION['user_flag']; ?></option>
									<?php } */ ?>
									<option value="<?php echo $user_flag1[0]['user_flag']; ?>"><?php echo $user_flag1[0]['user_flag']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'Lab Incharge' || $_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' ||  $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'DOL') { ?>
					<div class="row" id="pararal_lab">
						<div class="col-md-6">
							<div class="checkbox">
								<label id="ral_lab2"><input type="checkbox" id="ral_lab1" class="validate[minCheckbox[2]] checkbox" name="ral_lab1" value="labo" disabled></label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="ral_lab" name="ral_lab" onchange="getuser();">
								<option hidden="hidden" value=''>-----Select-----</option>
								<option value='all'>All</option>
								<?php //foreach ($ral_list as $ral_list): 	
								?>
								<!--<option value="<?php echo $ral_list['Offices_RO_SRO']['office_code'] . "~" . $ral_list['Offices_RO_SRO']['office_type'] . " ," . $ral_list['Offices_RO_SRO']['office_addr']; ?>">
									<?php echo $ral_list['Offices_RO_SRO']['office_type'] . "," . $ral_list['Offices_RO_SRO']['office_addr']; ?></option>-->
								<?php //endforeach; 
								?>

								<?php foreach ($office as $office1) :	?>
									<option value="<?php echo $office1['posted_ro_office'] . "~" . $office1['user_flag'] . " ," . $office1['ro_office']; ?>">
										<?php echo $office1['user_flag'] . "," . $office1['ro_office']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
					<div class="row" id="parasample_type">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="sample" class="validate[minCheckbox[2]] checkbox" name="sampleo" value="sampleo" disabled>Sample Type</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="sample_type" name="sample_type">
								<option hidden="hidden" value=''>-----Select-----</option>
								<?php foreach ($Sample_Type as $Sample_Type1) :	?>
									<option value="<?php echo $Sample_Type1['sample_type_code']; ?>"><?php echo $Sample_Type1['sample_type_desc']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
					<div class="row" id="paraCategory">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="cat" class="validate[minCheckbox[2]] checkbox" name="cat" value="cotto" disabled>Category</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="Category" name="Category">
								<option hidden="hidden" value=''>-----Select-----</option>
								<?php /* <?php foreach ($category_commodity as $Category1):	?>
									<option value="<?php echo $Category1['category_commodity']['category_code']; ?>"><?php echo $Category1['category_commodity']['category_name']; ?></option>
									<?php endforeach; ?> */ ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'RO Officer' || $_SESSION['role'] == 'SO Officer' || $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'DOL') { ?>
					<div class="row" id="paraCommodity">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="commo" class="validate[minCheckbox[2]] checkbox" name="commo" value="commo" disabled>Commodity</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="Commodity" name="Commodity">
								<option hidden="hidden" value=''>-----Select-----</option>
								<?php foreach ($Commodity as $Commodity1) :	?>
									<option value="<?php echo $Commodity1['commodity_code']; ?>"><?php echo $Commodity1['commodity_name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'Admin') { ?>
					<div class="row" id="paratest">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="test1" class="validate[minCheckbox[2]] checkbox" name="testo" value="testo" disabled>Test</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="test" name="test">
								<option hidden="hidden" value=''>-----Select-----</option>
								<?php foreach ($Test as $Test1) :	?>
									<option value="<?php echo $Test1['Test']['test_code']; ?>"><?php echo $Test1['Test']['test_name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'DOL' || $_SESSION['role'] == 'DOL') { ?>
					<div class="row" id="parauser_a">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="user1" class="validate[minCheckbox[2]] checkbox" name="user1" value="usero" disabled>User</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="user_a" name="user" required>
								<option hidden="hidden" value=''>-----Select-----</option>
								<?php foreach ($user as $user1) : 	?>
									<option value="<?php echo $user1['Dmi_user']['id']; ?>"><?php echo $user1['Dmi_user']['f_name']; ?> <?php echo $user1['Dmi_user']['l_name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist' ||  $_SESSION['role'] == 'DOL') { ?>
					<div class="row" id="parachemist_code">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="code2" class="validate[minCheckbox[2]] checkbox" name="code2" value="codeoo" disabled>Sample Code Available at Chemist</label>
							</div>
						</div>

						<?php //pr($user_str); 
						?>
						<div class="col-md-4">
							<select class="form-control" id="chemist_code" name="chemist_code" required>
								<option value="">-----Select----- </option>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'DOL' ||  $_SESSION['role'] == 'Head Office' || $_SESSION['role'] == 'RAL/CAL OIC' || $_SESSION['role'] == 'Inward Officer' || $_SESSION['role'] == 'RO/SO OIC' || $_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'RO Officer') { ?>
					<div class="row" id="parasample_code">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="code1" class="validate[minCheckbox[2]] checkbox" name="code1" value="codeo" disabled>Sample Code</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control" id="Sample_code" name="sample_code" required>
								<option value="">-----Select----- </option>
							</select>
						</div>
					</div>
				<?php } ?>
			</fieldset>
		</form>
		<div class="row parameters">
			<div class="col-md-6 col-md-offset-4 text-center">
				<span>
					<button class="btn btn-primary" type="submit" name="save" id="save">Generate Report</button>
				</span>
				<span>
					<button class="btn btn-primary" name="cancel" id="cancel">Cancel</button>
				</span>
				<span>
					<button class="btn btn-primary" name="close" id="close">Close</button>
				</span>
				<button type="reset" id="btn_reset" style="display: none;"></button>
			</div>
		</div>
	</div>
</div>


<?php
if (isset($sample_inward) && $sample_inward != '') {
	echo $this->element('report/sample_inward_view');
} elseif (isset($sample_inward1)) {
	echo $this->element('report/sample_inward_view_one');
} elseif (isset($test_report)) {
	echo $this->element('report/test_report_view');
}
?>

<!--						
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css" />  

<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>	
<script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>	

<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>	
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>	
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>	  
-->


<!-- below is JS of Pagination and export Div functions -->
<?php echo $this->Html->script('reportCountSample/pagerAndPrintDiv'); ?>
<!--  -->
<!-- below is CSS of count_sample -->
<?php echo $this->Html->css('reportCountSample/count_sample');  ?>
<!-- below is CSS of count_sample -->
<?php echo $this->Html->css('reportCountSample/style_count_sample');  ?>
<!--  -->
<!-- below is JS of jQuery get_chemist_sample ajax function -->
<?php echo $this->Html->script('reportCountSample/getChemistSample'); ?>
<!--  -->
<!-- below is JS of jQuery hideparameters, ajax-get_lab_name && datepicker functions -->
<?php echo $this->Html->script('reportCountSample/documentOnReady'); ?>
<!--  -->
<!-- below is JS of jQuery getuser, getuserdelayed, getlab, ajax-search_value && save-cancel-close functions -->
<?php echo $this->Html->script('reportCountSample/remainingAjax'); ?>
<!--  -->
<!-- below is JS of Intialisation of Pager -->
<?php echo $this->Html->script('reportCountSample/pagerInit'); ?>
<!--  -->