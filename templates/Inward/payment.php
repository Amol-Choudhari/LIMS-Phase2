<?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><h1 class="m-0 text-dark"></h1></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Payment</li>
					</ol>
				</div>
			</div>
		</div>
		<section class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-8 mt-2">
						<?php echo $this->element('/progress_bars/sample_registration_progress'); ?>
							<div class="card card-success">
								<div class="card-header"><h3 class="card-title-new">Payment</h3></div>
									<div class="form-horizontal">
										<div class="card-body  p-0 m-2 rounded">
											<table class="table table-striped table-hover table-bordered table-primary">
												<thead class="tablehead">
													<tr>
														<th>Category</th>
														<th>Commodities</th>
														<th>Charges</th>
													</tr>
												</thead>
												<tbody>
													<td><?php echo $Category[0]['category_name'] ?></td>
													<td><?php echo $Commodity['commodity_name'] ?></td>
													<td><?php echo $sample_charge ?></td>
												</tbody>
											</table>
		
										<!-- this row will not appear when printing -->
										<div class="row no-print">
											<div class="col-12">
												<?php echo $this->Form->create(null,array('type'=>'file', 'enctype'=>'multipart/form-data', 'id'=>'payment_modes')); ?>
												<h5 class="mt-1 mb-2">Payment</h5>
												<div class="table-format">
													<div class="total_charges_table">
														<table class="table"></table>
													</div>
												</div>
											<?php echo $this->element('payment_details_elements/payment_information_details'); ?>
											<!-- Call element of declaration message box before E-Sign of any application by pravin 10-08-2017 -->
											<?php  echo $this->element('declaration-message_boxes'); ?>
											<?php echo $this->Form->end(); ?>
											</div>
										</div>
										</div>
									</div>
							</div>
					</div>
				</div>
			</div>
		</section>
</div>


<?php if($all_section_status == 1 && ($final_submit_status == 'no_final_submit' || $final_submit_status == 'referred_back')){ ?>
<script> $("#final_submit_btn").css('display','block'); </script>
<?php } ?>

<?php  if($final_submit_status != 'no_final_submit'){ ?>
<script>

$("#form_outer_main :input").prop("disabled", true);
$("#form_outer_main :input[type='radio']").prop("disabled", true);
$("#form_outer_main :input[type='select']").prop("disabled", true);

</script>

<?php  } ?>

<?php  if($payment_confirmation_status == 'not_confirmed'){ ?>
<script>
$("#form_outer_main :input").prop("disabled", false);
$("#form_outer_main :input[type='radio']").prop("disabled", false);
$("#form_outer_main :input[type='select']").prop("disabled", false);
$("#not_confirmed_reason").show();
</script>
<?php  } ?>
