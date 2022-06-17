<?php
	$_SESSION['randSalt'] = Rand();
	$salt_server = $_SESSION['randSalt'];
	echo $this->element('get_captcha_random_code');//added on 15-07-2017 by Amol
	$captchacode = $_SESSION["code"];
?>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-12 text-center">
				<h4> LIMS Login</h4>
			</div>
		</div>
	</div>
</section>
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-10 align-center mx-auto">
				<div class="offset-2 card img-thumbnail box2 shadow1">
					<div class="card-body register-card-body">
						<p class="login-box-msg"><b>Authorized User Login</b></p>
							<div class="row">
								<div class="col-7">
									<?php echo $this->Form->create(null, array('autocomplete'=>'off', 'id'=>'login_user_form')); ?>
									<div id="error_email" class="text-red text-sm"></div>
									<div class="input-group mb-3">
										<?php $this->Form->setTemplates(['inputContainer' => '{{content}}']); ?>
										<?php echo $this->Form->control('email', array('label'=>'', 'id'=>'email', 'placeholder'=>'Enter Your Email ID','class'=>'form-control')); ?>
										<div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
									</div>

									<div class="dnone">
										<?php echo $this->Form->control('', array('label'=>'','type'=>'text', 'name'=>'username', 'value'=>'Hello')); ?>
									</div>

									<?php if (!empty($captcha_error_msg)) { ?>
										<div class="text-red">Enter Password Again</div>
										<?php echo $this->Html->script('users/password_validation_call') ;?>
									<?php } ?>

									<div id="error_password" class="text-red text-sm"></div>
										<div class="input-group mb-3">
										<?php echo $this->Form->control('password', array('label'=>'', 'id'=>'passwordValidation', 'class'=>'form-control input-field', 'placeholder'=>'Password','autocomplete'=>'new-password')); ?>
										<div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
										<?php echo $this->Form->control('salt_value', array('label'=>'', 'id'=>'hiddenSaltvalue', 'type'=>'hidden', 'value'=>$salt_server)); ?>
									</div>

									<div class="dnone">
										<?php echo $this->Form->control('', array('label'=>'','type'=>'password', 'name'=>'temp_pass', 'value'=>'mypassword')); ?>
									</div>

									<div id="error_captchacode" class="text-red float-right text-sm"></div>

									<?php if(!empty($captcha_error_msg)){ ?>
										<div class="text-red float-right text-sm"><?php echo $captcha_error_msg; ?></div>
										<?php echo $this->Html->script('users/password_validation_call') ;?>
									<?php } ?>

									<div class="input-group mb-3">
										<span id="captcha_img" class="col-4 mr-2 rounded p-0 d-flex">
											<?php echo $this->Html->image(array('controller'=>'users','action'=>'create_captcha'), array('class'=>'rounded')); ?>
										</span>
										<div class="col-2 btn m-0 p-0">
											<img class="img-responsive img-thumbnail border-0 shadow-none" id="new_captcha" src="<?php echo $this->request->getAttribute('webroot');?>img/refresh.png" />
										</div>

										<?php echo $this->Form->control('captcha', array('label'=>false, 'id'=>'captchacode', 'type'=>'text', 'placeholder'=>'Enter captcha', 'class'=>'form-control col-5 p-21px')); ?>
											<div class="input-group-append">
												<div class="input-group-text">
													<span class="fas fa-lock"></span>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="social-auth-links text-center d-flex col-12 m-0 p-0">
												<div class="col-6 p-0">
													<?php echo $this->Form->control('Submit', array('type'=>'submit', 'name'=>'submit', 'label'=>false, 'id'=>'login_btn', 'class'=>'btn btn-success btn-block p-4px')); ?>
												</div>
												<div class="col-6 p-0">
													<a href="../../DMI/users/forgot_password" class="btn btn-block btn-outline-secondary btn-sm d-block ml-1 p-4px">
														<i class="fas fa-key mr-2"></i>Forgot Password
													</a>
												</div>
											</div>
										</div>
									<?php echo $this->Form->end(); ?>
								</div>
								<div class="col-5 login-tips">
									<h6><b>Trouble Logging In?</b></h6>
										<ul>
											<li>User Id is case sensitive</li>
											<li>Password is case sensitive</li>
											<li>Captcha is case sensitive</li>
											<li>Enter the details properly</li>
											<li>Refresh captcha if not visible</li>
											<li>Password related queries refer the <a target="_blank" href="/writereaddata/DMI/manuals/DMI User Manual to Reset Password.pdf">Manual</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

<?php if($already_loggedin_msg == 'yes'){ echo $this->element('already_loggedin_msg'); } ?>
<?php echo $this->Html->script('users/login_user'); ?>
