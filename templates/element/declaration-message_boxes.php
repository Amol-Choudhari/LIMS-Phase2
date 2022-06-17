<?php  
			
		$customer_id = $_SESSION['username'];
		
		$split_customer_id = explode('/',$customer_id);
		//added this message on 27-10-2017 by Amol
		//commented on 31-05-2018 because, getting message from DB now
		//	$esign_msg = "Please preview your application pdf, if all fine click 'Ok' to E-Sign the document, if you don't want to E-Sign now please click 'Cancel', Your Application will final submitted only after E-Signing.";
		//	$aadhar_auth_msg = 'I hereby state that I have no objection in authenticating myself with Aadhaar based authentication system and consent to providing my Aadhaar number, Biometric and/or One Time Pin (OTP) data for Aadhaar based authentication for the purposes of availing of eSign service/ e-KYC services / both in PAN application from DMI.';
		if($_SESSION['sample']==3){
		
			
		}
?>

			<!-- created new modal on 26-03-2018 by Amol, to show option with/without esign -->
				<div id="esign_or_not_modal" class="modal">
				  <!-- Modal content -->				  
				  <div class="modal-content">
					<span class="close"><b>&times;</b></span>
					<p><?php echo $without_esign; ?> </p>
					<br>
					<?php $options=array('yes'=>'Submit with Esign','no'=>'Submit without Esign');
						$attributes=array('legend'=>false,'value'=>'yes','id'=>'esign_or_not_option', 'label'=>true );					
						echo $this->Form->radio('esign_or_not_option',$options,$attributes); ?>

					<button id="proceedbtn" class="modal-button" >Proceed</button>
	
				  </div>				 
				</div>
				
				
		<!-- The Modal -->
				<div id="declarationModal" class="modal">
				  <!-- Modal content -->				  
				  <div class="modal-content">
					<span class="close"><b>&times;</b></span>
					<!--added this pdf preview link on 27-10-2017 by Amol -->
					<div class="col-md-3">Application Pdf: </div>
					<div class="col-md-4"><a target="blank" href="../<?php echo $controller_name; ?>/<?php echo $forms_pdf; ?>" >Preview</a></div><br>
					<div class="clearfix"></div>
					
					<!-- added this new text box to take aadhar no. and store in session through ajax call below  -->
				<!-- commented on 27-08-2018 , currently aadhar no. input not req. on esign -->
				<!--	<div class="col-md-3">Aadhar No: </div> 
					<div class="col-md-9"><?php //echo $this->form->input('once_no', array('type'=>'text', 'id'=>'once_no', 'label'=>false, 'escape'=>false, 'placeholder'=>'Please Enter your Aadhar No.')); ?></div>
					<div class="clearfix"></div>
					<div id="error_aadhar_no"></div>
				-->
					
					<?php echo $this->Form->control('declaration_check_box', array('type'=>'checkbox', 'id'=>'declaration_check_box', 'class'=>'modal-checkbox','label'=>$message, 'escape'=>false)); ?>

					<button id="cancelBtn" class="modal-button">Cancel</button>
					<button id="okBtn" class="modal-button">Esign</button>	
				  </div>				 
				</div>
			

		<!-- The Modal for final submit withOut Esign-->
		<!-- Added on 04-05-2018 by Amol -->
				<div id="declarationModal_wo_esign" class="modal">
				  <!-- Modal content -->				  
				  <div class="modal-content">
					<span class="close"><b>&times;</b></span>
					<!--added this pdf preview link on 27-10-2017 by Amol -->
					<div class="col-md-3">Application Pdf: </div>
					<div class="col-md-4"><a target="blank" href="../<?php echo $controller_name; ?>/<?php echo $forms_pdf; ?>" >Preview</a></div><br>
					<div class="clearfix"></div>
					
					
					<?php echo $this->Form->control('declaration_check_box_wo_esign', array('type'=>'checkbox', 'id'=>'declaration_check_box_wo_esign', 'class'=>'modal-checkbox','label'=>$message_wo_esign, 'escape'=>false)); ?>

					<button id="cancelBtn" class="modal-button">Cancel</button>
					<button id="okBtn_wo_esign" class="modal-button" name="final_submit">Submit</button>	
				  </div>				 
				</div>
				
				
				
				
				<div id="otp_popup_box" class="modal">
				  <!-- Modal content -->				  
				  <div class="modal-content">
					<span class="close"><b>&times;</b></span>
					<div id="error_esign_otp"></div>
					<?php echo $this->Form->control('esign_otp', array('type'=>'text', 'label'=>'Esign OTP', 'id'=>'esign_otp', 'escape'=>false, 'placeholder'=>'Enter OTP here')); ?>

					<!--<button id="cancelotp" class="modal-button">Cancel</button>-->
					<button id="submitotp" class="modal-button" >Submit</button>
					<a style="float:right" id="resend_otp" href="#">Resend OTP</a>
				
				
				  </div>				 
				</div>
				
				
				
				<!-- This Model added on 03-10-2018 -->
				<div id="pleasewait" class="modal">				  
				  <div class="modal-content">
					<?php echo $esign_please_wait; ?> 
				  </div>
				</div>
				

	<?php //if($final_submit_status == 'no_final_submit'){ //commented is condition on 04-11-2017 by Amol ?>	


				<script>
				
							$("#okBtn").prop("disabled", true);
							$("#okBtn_wo_esign").prop("disabled", true);//added 04-05-2018 by Amol
							
						//	$("#declaration_check_box").prop("disabled", true);//added on 07-11-2017 by Amol
							


							// Get the button that opens the modal
							var final_submit_btn = document.getElementById("final_submit_btn");

							// Get the <span> element that closes the modal
							var span = document.getElementsByClassName("close")[0];
							
							//added on 26-03-2018 by Amol
							// Get the modal
							var esign_or_not_modal = document.getElementById('esign_or_not_modal');
							// When the user clicks on the button, open the modal
							final_submit_btn.onclick = function() {
								esign_or_not_modal.style.display = "block";
								return false;
							}

							//added on 26-03-2018 by Amol
							var proceedbtn = document.getElementById('proceedbtn');
							proceedbtn.onclick = function() {
								if($('#esign_or_not_option-yes').is(":checked")){		
			
									esign_or_not_modal.style.display = "none";
									modal.style.display = "block";
									return false;
														
								}else if($('#esign_or_not_option-no').is(":checked")){
									
									$("#once_no").val(null);//set aadhar value to null
									esign_or_not_modal.style.display = "none";
									//updated on 04-05-2018 by Amol for modal without esign
									var declarationModal_wo_esign = document.getElementById('declarationModal_wo_esign');									
									declarationModal_wo_esign.style.display = "block";
									
									return false;									
								}
							}
							
								// Get the modal
							var modal = document.getElementById('declarationModal');
							
							//commented on 24-03-2018 by Amol
							// When the user clicks on the button, open the modal
						/*	btn.onclick = function() {
								modal.style.display = "block";
								return false;
							}*/
							
							$("#declaration_check_box").change(function() {
								
								if($('#esign_or_not_option-yes').is(":checked")){		
									var with_esign = 'yes';
														
								}else if($('#esign_or_not_option-no').is(":checked")){
									var with_esign = 'no';								
								}
									
								
								if($(this).prop('checked') == true) {
									
									var controller_name = "<?php echo $controller_name; ?>";
									var forms_pdf = "<?php echo $forms_pdf; ?>";
								
								//added this new ajax block to set with/without esign value on concent checkbox clicked
								//on 29-03-2018
									
									
									$.ajax({
											type:'POST',
											async: true,
											cache: false,
											beforeSend: function (xhr) { // Add this line
												xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
											},
											data:{with_esign:with_esign},											
											url: "../esign/set_esign_or_not",											
											success: function(){
												
												$.ajax({
													type:'POST',
													async: true,
													cache: false,													
													beforeSend: function (xhr) { // Add this line
														xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
													},
													url: "../" + controller_name +"/"+ forms_pdf													
												});
										
											}
											
										});
										
										
								//till here on 29-03-2018

									$("#okBtn").prop("disabled", false);

								}
								
								if($(this).prop('checked') == false){
									
									$("#okBtn").prop("disabled", true);
								}
							});
							
							$("#cancelBtn").onclick = function() {
								modal.style.display = "none";
								return false;
							}


							// When the user clicks on <span> (x), close the modal
							span.onclick = function() {
								modal.style.display = "none";
								esign_or_not_modal.style.display = "none";
							}

							// When the user clicks anywhere outside of the modal, close it
							window.onclick = function(event) {
								if (event.target == modal) {
									modal.style.display = "none";
									esign_or_not_modal.style.display = "none";
								}
							} 
							
							
							
							//enable checkbox if preview link is clicked/ added on 07-11-2017
						//	$('a').click(function() {
						//		$("#declaration_check_box").prop("disabled", false);
						//	});
						
						
						
						
						
				//below script is to show Enter OTP window popup
				
							// Get the <span> element that closes the modal
							var otp_span = document.getElementsByClassName("close")[1];
						
							var otp_modal = document.getElementById('otp_popup_box');

							// Get the button that opens the modal
							var okBtn = document.getElementById("okBtn");
							

							// When the user clicks on the button, open the modal

							//updated on 26-03-2018 by Amol
							okBtn.onclick = function() {
								
								// below 3 lines added on 03-10-2018 to show pleasewait popup on click of Esign btn-->								
								modal.style.display = "none";
								var pleasewait = document.getElementById("pleasewait");
								pleasewait.style.display = "block";
								
								//this function contains all ajax code to call CDAC esign process.on 05-07-2018
								//defined in cwdialog.js file in webroot, which is included in almost all layouts.so easy to call this function
								if(CDAC_esign_ajax_calls()=='false'){
									return false;
								}
								
								
								
							//commented on 02-05-2018 by Amol, now OTP process will be done by Esign ESP itself. 
							//also moved name attribute from OTP submit button to Concent box Ok button clicked to final submit.
							/*	$.ajax({
										type:'POST',
										async: true,
										cache: false,
										data:{once_no:once_no},
										url: "../esign/request_esign_otp",
										success: function(response){
											var token_session_id = response;
											$('#Token_key_id').val(token_session_id);
										}
										
									});
									
								document.getElementById('esign_otp').value = '';			
								modal.style.display = "none";
								otp_modal.style.display = "block";
									
								return false;	
							*/
							}
							
							
							$("#cancelotp").onclick = function() {
								otp_modal.style.display = "none";
								return false;
							}
							
							
							// When the user clicks on <span> (x), close the modal
							otp_span.onclick = function() {
								otp_modal.style.display = "none";
							}

							// When the user clicks anywhere outside of the modal, close it
						/*	window.onclick = function(event) {
								if (event.target == otp_modal) {
									otp_modal.style.display = "none";
								}
							} 
							*/
							
							$("#submitotp").focus(function(){
								
								var esign_otp = $("#esign_otp").val();
								
								if(esign_otp == ''){
			
									$("#error_esign_otp").show().text("Please enter OTP");
									$("#error_esign_otp").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"left"});
									setTimeout(function(){ $("#error_esign_otp").fadeOut();},5000);
									$("#esign_otp").click(function(){$("#error_esign_otp").hide().text;});
									
									return false;
								}
		
									
									$.ajax({
										type:'POST',
										async: true,
										cache: false,
										data: { esign_otp: esign_otp },
										url: "../esign/set_esign_otp_session",
										beforeSend: function (xhr){ // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
										},
										success: function(response){
											var token_session_id = response;
											$('#Token_key_id').val(token_session_id);
										}										
										
									});
								
							});
							
							
							
					//to resend OTP request_esign_otp
							// When the user clicks on the button, open the modal
							$("#resend_otp").click(function() {
								
								$.ajax({
										type:'POST',
										async: true,
										cache: false,
										url: "../esign/request_esign_otp",
										beforeSend: function (xhr){ // Add this line
											xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
										},
										success: function(response){
											var token_session_id = response;
											$('#Token_key_id').val(token_session_id);
										}
									});
								
								document.getElementById('esign_otp').value = '';								
								otp_modal.style.display = "block";
								return false;
							});
							
							
							
							
							
					//for final submit without esign, added on 04-05-2018 by Amol
					$("#declaration_check_box_wo_esign").change(function() {
						
						$("#okBtn_wo_esign").prop("disabled", false);
								
						if($(this).prop('checked') == false){
							
							$("#okBtn_wo_esign").prop("disabled", true);
						}
						
					});
							
				</script>
				
			
	
	
	
	
				