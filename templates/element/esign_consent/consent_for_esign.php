
<!-- Created new form to post xml through Form Based esign method 
	updated on 28-05-2021 by Amol
	with predefined 3 hidden fields for xml string, transaction id and content-type-->

	<!-- The Modal -->
	<div id="grantDeclarationModal" class="modal">
		<!-- Modal content -->				  
		<div class="modal-content">
		
		<!--added this pdf preview link on 27-10-2017 by Amol -->
		<div class="row">
			<div class="col-md-3 d-inline">Report Pdf: </div> 
			<div class="col-md-3 d-inline"><a target="blank" href="../FinalGrading/sampleTestReportCode/<?php echo $stage_sample_code.'/'.$smple_commdity_code; ?>" >Preview</a></div>
			<span class="offset-5 close"><b>&times;</b></span>
		</div>
		<div class="clearfix"></div>
		
	<!--	<form action="https://esignservice.cdac.in/esign2.1/2.1/form/signdoc" method="POST">-->
		<?php echo $this->Form->create(null,array('action'=>'http://localhost/LIMS-Phase2/esign/requestEsign','method'=>'POST'));?>
			<input type="hidden" id = "eSignRequest" name="eSignRequest" value=''/>
			<input type="hidden" id = "aspTxnID" name="aspTxnID" value=""/>
			<input type="hidden" id = "Content-Type" name="Content-Type" value="application/xml"/>
			<input type="submit" name="submit" value="Esign" class="btn btn-success mt-2 float-right mr-2" id="esign_submit_btn">
		<!--</form>-->
		<?php echo $this->Form->end(); ?>
		
		<input type="checkbox" name="declaration_check_box" id="declaration_check_box" class="modal-checkbox" >
		<?php $esign_msg = "Please preview your report pdf, if all fine click 'Esign' to E-Sign the report, if you don't want to E-Sign now please click 'Cancel', The sample will final graded only after E-Signing the report."; 
						$aadhar_auth_msg = "I hereby state that I have no objection in authenticating myself with Aadhaar based authentication system and consent to providing my Aadhaar number, Biometric and/or One Time Pin (OTP) data for Aadhaar based authentication for the purposes of availing of eSign service from AQCMS.";
					?>	
		<label for="declaration_check_box"><?php echo $aadhar_auth_msg.'<br><br>'.$esign_msg; ?></label><br>

		<p id="plz_wait" class="pleaseWait">Please Wait...</p>
		
		<div class="row offset-7">
			
		</div>

		</div>				 
	</div>

	<input type="hidden" id="stage_sample_code" value="<?php echo $stage_sample_code; ?>">
	<input type="hidden" id="smple_commdity_code" value="<?php echo $smple_commdity_code; ?>">

<?php echo $this->Html->Script('esign_call_script'); ?>