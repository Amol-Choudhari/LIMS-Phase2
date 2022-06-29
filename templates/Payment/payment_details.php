<?php ?>

<div class="col-md-12 mt-5"><?php echo $this->element('/progress_bars/sample_registration_progress'); ?></div>

	<div class="col-md-10">
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="card card-success">
					<div class="card-header"><h3 class="card-title-new">Payment</h3></div>
					<div class="form-horizontal">
						<div class="card-body  p-0 m-2 rounded">
							<table class="table table-striped table-hover table-bordered table-primary">
								<thead class="tablehead">
									<tr>
										<th>Category</th>
										<th>Commodities</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?php echo $category; ?></td>
										<td><?php echo $commodity; ?></td>
									</tr>
									<tr>
										<td class="boldtext">Processing Fee</td>
										<td class="boldtext">Rs.<?php echo $commercial_charges; ?></td>
									</tr>
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
											
										<?php echo $this->element('payment_elements/payment_information_details'); ?>
									<?php echo $this->Form->end(); ?>
									<!-- Call element of declaration message box out of Form tag on 31-05-2021 by Amol for Form base esign method -->
									<?php  //echo $this->element('declaration-message_boxes'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>

	<input type="hidden" id="payment_confirmation_status_id" value="<?php echo $payment_confirmation_status; ?>">

	<?php echo $this->Html->script('payment/payment_details'); ?>