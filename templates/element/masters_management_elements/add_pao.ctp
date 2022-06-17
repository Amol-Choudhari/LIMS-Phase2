<?php 
	echo $this->Html->css('../multiselect/jquery.multiselect');
	echo $this->Html->script('../multiselect/jquery.multiselect');
?>
	

				<div class="col-md-8  col-md-offset-2">
					<h3>Enter Details</h3>
					
					
					
					<div class="col-md-4">
					<h5>PAO/DDO Email ID</h5>

								<?php echo $this->Form->control('pao_email_id', array('type'=>'select', 'id'=>'pao_email_id', 'options'=>$pao_email_id_list,'class'=>'form-control',  'label'=>false)); ?>	
								<div id="error_pao_email_id"></div>
					</div>	
					
					<div class="col-md-4">
					<h5>PAO/DDO Alias Name</h5>

								<?php echo $this->Form->control('pao_alias_name', array('type'=>'text', 'id'=>'pao_alias_name', 'class'=>'form-control',  'label'=>false, 'placeholder'=>'Enter PAO/DDO Alias Name Here')); ?>	
								<div id="error_pao_alias_name"></div>
					</div>						
					<div class="clearfix"></div>
					
					
					<div class="col-md-4">
					<h5>Allocate State List</h5>

								<?php echo $this->Form->control('state_list', array('type'=>'select', 'id'=>'state_list', 'options'=>$all_states, 'multiple'=>'multiple',  'label'=>false)); ?>	
								<div id="error_district_list"></div>
					</div>
										
					<div class="col-md-4" id="update_district_div">
					<h5>Allocate District List</h5>

								<?php echo $this->Form->control('district_list', array('type'=>'select', 'id'=>'district_list', /*'options'=>$district_name_list,*/ 'multiple'=>'multiple',  'label'=>false)); ?>	
								<?php echo $this->Form->control('district_option', array('type'=>'hidden', 'id'=>'district_option', 'label'=>false,)); ?>
								<div id="error_district_list"></div>
					</div>
			
					<div class="col-md-3">
			
								<?php echo $this->form->submit('Add PAO/DDO', array('name'=>'add_pao', 'id'=>'add_pao_btn', 'onclick'=>'set_pao_validation();return false','class'=>'form-control', 'label'=>false ,'style'=>'margin-top: 26px; margin-right: -27px;')); ?>
	
					</div>
					
					<div class="clearfix"></div>
					
					<!--Check pao user and district name availability (Done By pravin 25/10/2017)-->
					<?php if(empty($pao_email_id_list)){ ?>
					
					<h5 style="color:navy"> User with role PAO/DDO are all set </h5>
					<?php } ?>
					
					<?php if(empty($district_name_list)){ ?>
					
					<h5 style="color:navy"> No district remaining to set for PAO/DDO </h5>
					
					<?php } ?>
					
				</div>

		<script>
		
		
		//create the dynamic path for ajax url (Done by pravin 03/11/2017)
		var host = location.hostname;
		var paths = window.location.pathname;
		var split_paths = paths.split("/");
		var path = "/"+split_paths[1]+"/"+split_paths[2];
		
			
			$('#district_list').multiselect({
				includeSelectAllOption: true,
				placeholder :'Select District',
				buttonWidth: '100%',
				maxHeight: 400,
			});
			
			
			$('#state_list').multiselect({
				includeSelectAllOption: true,
				placeholder :'Select State',
				buttonWidth: '100%',
				maxHeight: 400,
			});
			
				
						
			$('#state_list').change(function(e){					
			
				var state_id = $('#state_list').val();
				var form_data = $("#set_pao").serializeArray();
				form_data.push(	{name: "state_id",value: state_id});
				
				/* add new custom "new ms-options-update" class to avoid conflict of "ms-options" classes of two 
					multiselect dropdown options. */
				$('#update_district_div div.ms-options').addClass('ms-options-update');
				
				// Clear the place holder Text
				var selOpts = [];
				var placeholder = $('#district_list').next('.ms-options-wrap').find('> button:first-child');
				placeholder.text(selOpts.join( '' ));
				
				$.ajax({
					type: "POST",
					url: path+"/pao_district_dropdown",
					data: form_data,
					async: true,
					beforeSend: function (xhr) {
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function(data){
						$(".ms-options-update").html(data);	
					},						
				}); 

				/* For extcuting two ajax function on one event action, click the second 
					function after first ajax function success */
				$('#district_option').click();
				
			});			
			
			
			$('#district_option').click(function(){
			
				
				var state_id = $('#state_list').val();
				var form_data = $("#set_pao").serializeArray();
				form_data.push(	{name: "state_id",value: state_id});
				
				$.ajax({
					type: "POST",
					url: path+"/pao_district_option",
					data: form_data,
					beforeSend: function (xhr) {
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
					},
					success: function(response){
						
					$("#district_list").html(response);
					},						
				}); 				
								
			});
			
			
			function set_pao_validation(){
				
				var pao_email_id = $('#pao_email_id').val();
				var district_list = $('#district_list').val();
				var pao_alias_name = $('#pao_alias_name').val();
				
				
				value_return = 'true';
				
				if(pao_email_id == null){
					
					$("#error_pao_email_id").show().text('Select PAO/DDO email ID');
					$("#error_pao_email_id").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					$("#pao_email_id").click(function(){$("#error_pao_email_id").hide().text;});
					
					value_return = 'false';
					
				}
				
				if(district_list == null){
					
					$("#error_district_list").show().text('Select district');
					$("#error_district_list").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					$("#district_list").click(function(){$("#error_district_list").hide().text;});
					
					value_return = 'false';
					
				}
				
				if(pao_alias_name == ''){
					
					$("#error_pao_alias_name").show().text('Enter pao alias name');
					$("#error_pao_alias_name").css({"color":"red","font-size":"14px","font-weight":"500","text-align":"right"});
					$("#pao_alias_name").click(function(){$("#error_pao_alias_name").hide().text;});
					
					value_return = 'false';
					
				}
				
				
				if(value_return == 'false')
				{
					
					return false;
				}
				else{
						exit();   
					}
				
			}




		</script>
		
		
		

