
<?php echo $this->Form->input('user_role_id', array('type' => 'hidden', 'id' => 'user_role_id', 'value'=>$_SESSION['role'], 'label' => false,)); ?>
<?php	// echo $this->Form->create(null, array('type'=>'hidden', 'id'=>'user_role_id','label'=>false,)); 
?>


<?php //pr($user);  
?>
<div class="row">
	<div class="col-md-6 report-menu" style="margin-left: 25%;">
		<fieldset class="fsStyle">
			<!-- <legend class="legendStyle">Title</legend> -->
			<div class="panel-group" id="accordion">
				<?php foreach ($label as $label1) : ?>
					<?php if ($label1['label_code'] != 14) { ?>
						<div class="panel panel-default">
							<div class="panel-heading label-box">
								<h4 class="panel-title">
									<a id="<?php echo $label1['label_desc']; ?>" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#panel<?php echo $label1['label_code']; ?>" onclick="hideparameter();"><span class="colmd10"> <?php echo $label1['label_desc']; ?></span> <span class="colmd2"><i class="glyphicon glyphicon-plus"></i></span></a>
								</h4>
							</div>
							<div id="panel<?php echo $label1['label_code']; ?>" class="panel-collapse collapse">
								<div class="panel-body label-desc text-center">
									<?php foreach ($report as $report1) :
										if ($label1['label_code'] == $report1['label_code']) {
									?>
											<p class="badge border bg-dark"><label for="<?php echo $report1['report_desc']; ?>" id="<?php echo $report1['report_desc']; ?>" class="<?php echo $report1['report_desc']; ?>  control-label open-model" data-toggle="modal" data-target="#exampleModal"><span class="label-text"><?php echo $report1['report_desc']; ?></span></label></p>
									<?php  }
									endforeach; ?>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>	

		<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="label-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- <div class="col-md-6 parameters"> -->
				<!-- <form id="rpt_comm_sample" method="post" action="" autocomplete="off" target="_blank"> -->
				
					<?php echo $this->element('report/report_parameters'); ?>
					
				
					<!-- </form> -->
					
				<!-- </div> -->
			</div>
			</div>
		</div>
	</div>


	
</div>


<?php //pr($sample_inward1); exit;
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

<?php
echo $this->Html->Script('bootstrap-datepicker.min');
echo $this->Html->script('reportCountSample/report-functionality');
//print  $this->Session->flash("flash", array("element" => "flash-message_new")); 
?>