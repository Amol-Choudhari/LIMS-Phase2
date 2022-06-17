<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */


		print  $this->Session->flash("flash", array("element" => "flash-message_new"));	
		
		$_SESSION['randSalt'] = Rand();
			
		$salt_server = $_SESSION['randSalt'];
			
		//$code=rand(1000,9999);
		//$_SESSION["code"]=$code;
			echo $this->element('get_captcha_random_code');//added on 15-07-2017 by Amol
		$captchacode = $_SESSION["code"];
		
		//echo $msg;
		
	?>
	
	
				<div class="form-style-3">
			<?php echo $this->form->create('Dmi_user'); ?>
				<h2>Authorized User Login</h2>
				<fieldset><legend>Personal</legend>
									
					<label for="field2"><span>Email Id <span class="required">*</span></span>
						<?php echo $this->form->input('email', array('label'=>'', 'id'=>'email', 'placeholder'=>'email')); ?>
						<div id="error_email"></div>
					</label>
					
					<label for="field3"><span>Password <span class="required">*</span></span>
						<?php echo $this->form->input('password', array('label'=>'', 'id'=>'passwordValidation', 'placeholder'=>'Password')); ?>
						<div id="error_password"></div>					
						<?php echo $this->form->input('salt_value', array('label'=>'', 'id'=>'hiddenSaltvalue', 'type'=>'hidden', 'value'=>$salt_server)); ?>
					</label>
					
					<label for="field3"><span>Verify <span class="required">*</span></span>
					</label><br />
					
					<label for="field3">
						<span><?php echo $this->Html->image(array('controller'=>'users','action'=>'create_captcha')); ?> <a href="<?php echo $this->element('get_captcha_random_code');?>" id="new"><img src="<?php  echo $this->webroot;?>img/captcha_reload.png" style="width: 25px;margin-left:10px;" /></a></span>
						<?php echo $this->form->input('captcha', array('label'=>'', 'id'=>'captchacode', 'type'=>'text', 'placeholder'=>'Please enter captcha code')); ?>
						<div id="error_captchacode"></div>
						
						<!--<span class="captchacode"><?php //echo $captchacode; ?></span>-->
						
					</label>
					
					<label><span>&nbsp;</span>
						<?php echo $this->form->input('Submit', array('type'=>'submit', 'name'=>'submit', 'label'=>false, 'onclick'=>'myFunction();return false')); ?>
					</label>
					<label class="button-link"><a href="<?php echo $this->webroot; ?>users/forgot_password" >Forgot Password</a></label>
				</fieldset>

			<?php echo $this->form->end(); ?>
		</div>
	
	
	

				
				
				<script>
				
				
						$("#new").click(function() {
						//	var code="<?php echo $this->element('get_captcha_random_code');?>";
							//alert(code);
							//alert("dasd");
							//$("#captchacode").attr("src", "<?php echo $base_url;?>users/create_captcha");
						});    
				
				
						function myFunction(){
						
						
						var email=$("#email").val();
						var password=$("#passwordValidation").val();
						var captchacode=$("#captchacode").val();
						
						if(email==""){
								alert("Some Fields are missing");
							$("#error_email").show().text("Please enter your email.");
							$("#error_email").css({"color":"red","font-size":"14px"});
							//setTimeout(function(){ $("#error_email").fadeOut();},8000);
							$("#email").click(function(){$("#error_email").hide().text;});
							return false;
							}
							
						else if(password==""){
								alert("Some Fields are missing");
							$("#error_password").show().text("Please enter your password.");
							$("#error_password").css({"color":"red","font-size":"14px"});
							//setTimeout(function(){ $("#error_password").fadeOut();},8000);
							$("#passwordValidation").click(function(){$("#error_password").hide().text;});
							return false;
							}
							
						else if(captchacode==""){
								alert("Some Fields are missing");
							$("#error_captchacode").show().text("Please enter your password.");
							$("#error_captchacode").css({"color":"red","font-size":"14px"});
							//setTimeout(function(){ $("#error_password").fadeOut();},8000);
							$("#captchacode").click(function(){$("#error_captchacode").hide().text;});
							return false;
							}
						
						
								var PasswordValue = document.getElementById('passwordValidation').value;
								var SaltValue = document.getElementById('hiddenSaltvalue').value;
								var EncryptPass = calcMD5(PasswordValue);
			
								var SaltedPass = SaltValue.concat(EncryptPass);
								
								var Saltedmd5pass = calcMD5(SaltedPass);
								
								document.getElementById('passwordValidation').value = Saltedmd5pass;
								
								//alert(EncryptPass);
								//alert(Saltedmd5pass);
								exit();
								
						}
				</script>
				

		
		
		
				
						