<?php 
	$_SESSION['randSalt'] = Rand();						
	$salt_server = $_SESSION['randSalt'];					
	echo $this->element('get_captcha_random_code');//added on 15-07-2017 by Amol			
	$captchacode = $_SESSION["code"];
?>
	
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0 text-dark"></h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            </ol>
          </div>
        </div>
      </div>
    </div>
	 	<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-8">
						<?php echo $this->Form->create(null,array('id'=>'reset_password_form')); ?>
							<div class="card card-Lightblue">
								<div class="card-header"><h4 class="card-title-new">Reset Password</h4></div>
									<div class="form-horizontal">
										<div class="card-body">
											<div class="form-group row">
												<div class="form-group row">
													<label for="inputEmail3" class="col-sm-3 col-form-label">New Password <span class="required-star">*</span></label>
														<div class="col-sm-6">
															<?php echo $this->Form->control('new_password', array('label'=>'', 'type'=>'password', 'id'=>'Newpassword', 'placeholder'=>'Enter New Password')); ?>
															<span id="error_newpass" class="error invalid-feedback"></span>
														</div>
													</div>

													<p id="password_msg" class="badge badge-info">Note:- Password length should be min. 8 character, min. 1 number, min. 1 Special char. and min. 1 Capital Letter</p>
													
													<div class="form-group row">
														<label for="inputEmail3" class="col-sm-3 col-form-label">Confirm Password  <span class="required-star">*</span></label>
															<div class="col-sm-6">
																<?php echo $this->Form->control('confirm_password', array('label'=>'', 'type'=>'password', 'id'=>'confpass','placeholder'=>'Confirm New Password')); ?>
																	<span id="error_confpass" class="error invalid-feedback"></span>
																<?php echo $this->Form->control('salt_value', array('label'=>'', 'id'=>'hiddenSaltvalue', 'type'=>'hidden', 'value'=>$salt_server)); ?>
															</div>
													</div>
													<div class="form-group row">
														<label for="inputEmail3" class="col-sm-3 col-form-label">Confirm Password <span class="required-star">*</span></label>
															<div class="col-sm-6">
																<?php echo $this->Form->control('confirm_password', array('label'=>'', 'type'=>'password', 'id'=>'confpass', 'class'=>'input-field form-control', 'placeholder'=>'Confirm New Password')); ?>
															<span id="error_confpass"class="error invalid-feedback"></span>
														</div>
													</div>			
													<div class="input-group mb-3">
														<span id="captcha_img" class="col-4 mr-2 rounded p-0 d-flex">
															<?php echo $this->Html->image(array('controller'=>'users','action'=>'create_captcha'), array('class'=>'rounded')); ?>
														</span>
														<div class="col-2 btn m-0 p-0">
															<img class="img-responsive img-thumbnail border-0 shadow-none" id="new_captcha" src="<?php echo $this->request->getAttribute('webroot');?>img/refresh.png"/>
														</div>

														<?php echo $this->Form->control('captcha', array('label'=>false, 'id'=>'captchacode', 'type'=>'text', 'placeholder'=>'Enter captcha', 'class'=>'form-control col-5')); ?>
															<div class="input-group-append">
																<div class="input-group-text">
																	<span class="fas fa-lock"></span>
																</div>
															</div>
													</div>
													<div class="card-footer cardFooterBackground">
														<?php echo $this->Form->control('Submit', array('type'=>'submit', 'name'=>'submit', 'label'=>false,'class'=>'btn btn-success')); ?>
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
	<?php echo $this->Html->script('users/reset_password'); ?>
				
				