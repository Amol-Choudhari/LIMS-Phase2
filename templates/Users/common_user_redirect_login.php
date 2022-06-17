<?php $_SESSION['randSalt'] = Rand(); $salt_server = $_SESSION['randSalt'];?>


	<div class="container-fluid">
  	<div class="row mb-2">
    	<div class="col-sm-12 text-center">
				<h4> LIMS Login</h4>
				</div>
    	</div>
  	</div>
		<section class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-6 align-center mx-auto">
						<?php echo $this->Form->create(null,array('id'=>'commuserform')); ?>
							<div class="card img-thumbnail box2shadow">
								<div class="card-body register-card-body">
									<h4 class="login-box-msg">Common User Login Redirect</h4>
										<div class="input-group mb-3">
											<label for="field3" class="col-md-3"><span> Password <span class="required-star">*</span></span></label>
												<?php echo $this->Form->control('password', array('label'=>'', 'id'=>'passwordValidation', 'placeholder'=>'Please enter your Password','class'=>'form-control mtminus8')); ?>
													<div class="input-group-append">
														<div class="input-group-text">
															<span class="fas fa-lock"></span>
		            									</div>
		         									</div>
												<span id="error_password" class="error invalid-feedback"></span>
											<?php echo $this->Form->control('salt_value', array('label'=>'', 'id'=>'hiddenSaltvalue', 'type'=>'hidden', 'value'=>$salt_server)); ?>
										</div>
									</div>
									<div class="card-footer">
										<div class="col-6 p-0">
											<?php echo $this->Form->control('Submit', array('type'=>'submit', 'name'=>'submit', 'id'=>'comm_user', 'label'=>false,'class'=>'btn btn-success')); ?>
										</div>
						 			</div>
								</div>
							<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
		</section>
		<input type="hidden" value="<?php echo $return_error_msg; ?>" id="return_error_msg"/>
		<?php echo $this->Html->script("users/common_user_redirect_login"); ?>
