<?php ?>
	<?php echo $this->Form->control('actual_payment', array('type'=>'hidden', 'id'=>'actual_payment', 'value'=>$sample_charge, 'label'=>false,)); ?>

		<div class="card-body" style="margin-top: 22px; padding: 1px;">
			<div class="callout callout-danger p-4">
				<legend>How To Do Online Payment </legend>
	
				<label class="row"style="font-size:16px"><i class="glyphicon glyphicon-arrow-right paymentlabels"></i>Link To Payment Online :<a class="boldtext" href="https://bharatkosh.gov.in/" target="blank">bharatkosh.gov.in</a></label>

				<label class="row"style="font-size:16px"><i class="glyphicon glyphicon-arrow-right paymentlabels"></i><a href="#" target="blank"> FAQ on payments</a></label>

				<label class="row" style="font-size:16px"><i class="glyphicon glyphicon-arrow-right paymentlabels"></i>PAO/DDO to whom payment is to be made : <span class="badge badge-info" style="margin:5px;"><?php echo $pao_to_whom_payment; ?></span></label>

				<label class="row" style="font-size:16px"><i class="glyphicon glyphicon-arrow-right paymentlabels"></i> Is payment done on Bharatkosh?
				<?php 					
					$options=array('yes'=>'Yes','no'=>'No');
					$attributes=array('legend'=>false, 'value'=>$bharatkosh_payment_done, 'id'=>'bharatkosh_payment_done', 'label'=>true);					
					echo $this->Form->radio('bharatkosh_payment_done',$options,$attributes); ?>
				</label>
			</div>
			</div>
			<div id="payment_details">
				<div class="card card-cyan col-md-12 p-0">
					<div class="card-header"><h3 class="card-title-new">Payment Details</h3></div>
						<div class="card-footer p-0">
							<ul class="nav flex-column">
								<li class="nav-item row pt-2">
									<label for="field3" class="col-md-5 "><span class="col-form-label">Payment Amount<span class="required-star">*</span></span></label>
										<div class="col-sm-6">
											<?php echo $this->Form->control('payment_amount', array('type'=>'text', 'escape'=>false, 'value'=>$payment_amount, 'id'=>'payment_amount', 'label'=>false, 'placeholder'=>'Please Enter Payment Amount','class'=>'form-control')); ?>
										</div>
									<div id="error_payment_amount"></div>
								</li>
                				<li class="nav-item row pt-2">
									<label for="field3" class="col-md-5"><span class="col-form-label">Transaction ID/Receipt NO. <span class="required-star">*</span></span></label>
										<div class="col-sm-6">
											<?php echo $this->Form->control('payment_transaction_id', array('type'=>'text', 'escape'=>false, 'value'=>$payment_transaction_id, 'id'=>'payment_transaction_id', 'label'=>false, 'placeholder'=>'Please Enter Transaction ID/Receipt NO','class'=>'form-control')); ?>
										</div>
									<div id="error_payment_transaction_id"></div>
                				</li>
                				<li class="nav-item row pt-2">
									<label for="field3" class="col-md-5"><span class="col-form-label">PAO/DDO Name <span class="required-star">*</span></span></label>
										<div class="col-sm-6">
											<?php echo $this->Form->control('pao_name', array('type'=>'text', 'escape'=>false, 'value'=>$pao_to_whom_payment, 'id'=>'pao_name', 'label'=>false, 'readonly'=>true,'class'=>'form-control')); ?>
										</div>
									<div id="error_payment_amount"></div>
               					</li>
                				<li class="nav-item row pt-2">
									<label for="field3" class="col-md-5"><span class="col-form-label">Date of Transaction<span class="required-star">*</span></span></label>
										<div class="col-sm-6">
											<?php echo $this->Form->control('payment_trasaction_date', array('type'=>'text', 'escape'=>false, 'value'=>$payment_trasaction_date[0], 'id'=>'payment_trasaction_date', 'label'=>false, 'readonly'=>true, 'placeholder'=>'Please Enter Date of Transaction','class'=>'form-control')); ?>
										</div>
									<div id="error_payment_trasaction_date"></div>
                				</li>
								<li class="nav-item row pt-2">
									<label for="field3" class="col-md-5"><span class="col-form-label">Upload Payment Receipt<span class="required-star">*</span></span></label>									
										<div class="col-sm-6">
											<div class="form-group row">
												<label for="inputEmail3" class="col-sm-4 col-form-label">Attach File :
													<?php if(!empty($payment_receipt_docs)){ ?>
														<a target="blank" id="payment_receipt_document_value" href="<?php echo str_replace("D:/xampp/htdocs","",$payment_receipt_docs); ?>">Preview</a>
													<?php } ?>	
												</label>
												<div class="custom-file col-sm-8">
												  <?php echo $this->Form->control('payment_receipt_document',array('type'=>'file', 'id'=>'payment_receipt_document', 'onchange'=>'file_browse_onclick(id);return false', 'multiple'=>'multiple', 'label'=>false,'class'=>'custom-file-input')); ?>
												  <label class="custom-file-label" for="customFile">Choose file</label>
												  <span id="error_payment_receipt_document" class="error invalid-feedback"></span> <!-- create div field for showing error message ( by pravin 06/05/2017)-->
												  <span id="error_size_payment_receipt_document" class="error invalid-feedback"></span> <!--create div field for showing error message (by pravin 09/05/2017)-->
												  <span id="error_type_payment_receipt_document" class="error invalid-feedback"></span> <!--create div field for showing error message (by pravin 09/05/2017)-->
												  <p class="lab_form_note_pay"><i class="fa fa-info-circle"></i> File type: PDF, jpg & max size upto 2 MB</p>
												</div>
												
											  </div>
										</div>
								</li>


							</ul>
						</div>
            		
						</div>
				<p><i class="fas fa-info-circle middle"></i> <b><u>Note: Fees once paid, shall not be refunded </b></u></p>


			</div>

			<div id="not_confirmed_reason" class="shadowforpage"><legend>Referred Back History</legend>	
				<div class="remark-history">
					<table class="table table-bordered table-dark">
						<tr class="boxformenus">
						<th class="tablehead">Date</th>
						<th class="tablehead">Reason</th>
						<th class="tablehead">Comment</th>
						</tr>
						<!-- change variable fetch_comment_reply to fetch_applicant_communication(by pravin 03/05/2017)-->
						<?php 	
								$options = array('0'=>'Payment amount does not match','1'=>'Transaction ID Invalid',
								'2'=>'PAO/DDO Name Invalid', '3'=>'Transaction Date Invalid', '4'=>'Payment Receipt Invalid');
						
						foreach($fetch_pao_referred_back as $comment_reply){ ?>
							
							<tr>
							<td><?php echo $comment_reply['modified']; ?></td>
							<td><?php echo $options[$comment_reply['reason_option_comment']]; ?></td>
							<td><?php echo $comment_reply['reason_comment']; ?></td>
							</tr>
							
						<?php }?>
					</table>
					</div>
		</div>


			



<script>
	
		$("#not_confirmed_reason").hide();
		<?php if($payment_confirmation_status == 'payment_not_submit'){ ?>

		$("#payment_details").hide();
		$("#submit_payment_detail").hide();	
		$('#bharatkosh_payment_done-yes').click(function(){	
						
			$("#payment_details").show();	
			$("#submit_payment_detail").show();	
		});
		$('#bharatkosh_payment_done-no').click(function(){	
						
			$("#payment_details").hide();
			$("#submit_payment_detail").hide();		
		});
		
		<?php } ?>	

	$(document).ready(function () {

		$('#payment_trasaction_date').datepicker({
			format: "dd/mm/yyyy",
			autoclose: true,      
			endDate: new Date() 
		});
		
		
		$('#payment_amount').focusout(function(){
			
			var input_payment_amount = $('#payment_amount').val();
			var actual_payment_amount = $('#actual_payment').val();
			
			if(input_payment_amount != actual_payment_amount)
			{
				$("#error_payment_amount").show().text("Please enter valid payment amount");
				$("#error_payment_amount").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				setTimeout(function(){ $("#error_payment_amount").fadeOut();},2000);
			}	
			
		});
		
	//added new script with ajax below to check unique trsnsaction id.
	//on 14-10-2019 by Amol
		$('#payment_transaction_id').focusout(function(){
			
			var trans_id = $("#payment_transaction_id").val();

			$.ajax({
				type : 'POST',
				url : '../AjaxFunctions/check_unique_trans_id_for_appl',
				async : true,
				data : {trans_id:trans_id},
				beforeSend: function (xhr) { 
								xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
						},
				success : function(response){
					
					response = response.match(/~([^']+)~/)[1];
					
					if($.trim(response)=='no'){
						var msg = 'The Transaction/Receipt Id "'+ trans_id  +'" is already used. Please verify and enter again.';
						alert(msg);
						$("#payment_transaction_id").val('');
					}
					
				}
				
			});
			
		});
		
					
	});
	
	
	if($('#bharatkosh_payment_done-yes').is(":checked")){		
				
		$("#submit_payment_detail").show();
	}
	
	<?php  if($payment_confirmation_status == 'replied'){ ?>
		
		$("#not_confirmed_reason").show();
		$("#submit_payment_detail").hide();
				
	<?php  } ?>
	<?php  if($payment_confirmation_status == 'pending' || $payment_confirmation_status == 'confirmed'){ ?>
		
		$("#submit_payment_detail").hide(); 
		$("#final_submit_btn").hide();		
		
	<?php  } ?>
	
	
	
	
	
	
	function payment_fields_validation(){
		
		var input_payment_amount = $('#payment_amount').val();
		var input_payment_transaction_id = $('#payment_transaction_id').val();
		var input_payment_trasaction_date = $('#payment_trasaction_date').val();
		var input_payment_receipt_document = $('#payment_receipt_document').val();
		var actual_payment_amount = $('#actual_payment').val();
		var value_return = 'true';
		
		
		if($('input[name="bharatkosh_payment_done"]:checked').val() == 'yes'){
			
			if(input_payment_amount == '')
			{
				$("#error_payment_amount").show().text("Please enter valid payment amount");
				$("#error_payment_amount").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				$("#payment_amount").click(function(){$("#error_payment_amount").hide().text;});
				value_return = 'false';
				
			}else{
				
				if(input_payment_amount != actual_payment_amount)
				{
					$("#error_payment_amount").show().text("Please enter valid payment amount");
					$("#error_payment_amount").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					$("#payment_amount").click(function(){$("#error_payment_amount").hide().text;});
					value_return = 'false';
				}
			}
			
			if(input_payment_transaction_id == ''){
				
				$("#error_payment_transaction_id").show().text("Please enter payment transction id");
				$("#error_payment_transaction_id").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				$("#payment_transaction_id").click(function(){$("#error_payment_transaction_id").hide().text;});
				value_return = 'false';
				
			}
			
			if(input_payment_trasaction_date == ''){
				
				$("#error_payment_trasaction_date").show().text("Please select payment transction date");
				$("#error_payment_trasaction_date").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
				$("#payment_trasaction_date").click(function(){$("#error_payment_trasaction_date").hide().text;});
				value_return = 'false';
				
			}
			if($('#payment_receipt_document_value').text() == ""){
				
				if(input_payment_receipt_document == ''){
					
					$("#error_payment_receipt_document").show().text("Please upload payment receipt");
					$("#error_payment_receipt_document").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					$("#payment_receipt_document").click(function(){$("#error_payment_receipt_document").hide().text;});
					value_return = 'false';
					
				}
			}
			if(value_return == 'false')
			{	
				alert("Please check some fields are missing or not proper.");
				return false;
			}
			else{
				exit();
				
			}
		}
	}

</script>		
				


	