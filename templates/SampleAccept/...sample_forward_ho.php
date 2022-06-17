<?php
	echo $this->Html->script('jquery_validationui');
	echo $this->Html->script('languages/jquery.validationEngine-en');
	echo $this->Html->script('jquery.validationEngine');
	echo $this->Html->css('validationEngine.jquery');
	echo $this->Html->Script('bootstrap-datepicker.min');
	print  $this->Session->flash("flash", array("element" => "flash-message_new"));	
?>
<style>
.Absolute-Center {
    margin: auto;
    border: 1px solid #ccc;
    background-color: #f3fafe;
    padding:20px;
    border-radius:3px;	
    top: 0; left: 0; bottom: 0; right: 0;
}
.no-margin {
    margin: 0;
}
</style>
<script>
	function get_user_name()
		{
			$("#dst_usr_cd").find('option').remove();
			var dst_loc_id=$("#dst_loc_id").val();
				//alert(dst_loc_id);
			 $.ajax({
					type:"POST",
					url:"<?php echo Router::url(array('controller'=>'SampleForward','action'=>'get_user'));?>",
					data:{dst_loc_id:dst_loc_id},
					cache:false,
					success : function(data){
					if(data=='    [error]'){
						var msg="incorrect values";
							errormsg(msg);
						}
						else
						{
							if(data==0){
								var msg="Inward Officer not present at this location";
								errormsg(msg);
								$("#ral").prop("disabled", true);
							}
							else{
								$("#ral").prop("disabled", false);
								var loc=$("#dst_loc_id option:selected").text();
								$("#dst_usr_lbl").text("");
								$("#dst_usr_lbl").append("Inward officer from "+loc);
							//$("#dst_usr_cd").append("<option value='-1'>----Select----</option>");	
							$.each($.parseJSON(data), function (key, value) {
								//alert(value[0]['f_name']);
							$("#dst_usr_cd").append("<option value="+value[0]['id']+">"+value[0]['f_name']+' '+value[0]['l_name']+"</option>")	;
							});
							}
						}
						
						
					}
					 
				});	 
			
		}	
		
    function update_commodity()
    {
        var stage_sample_code = $("#stage_sample_code").val();
        //alert(stage_sample_code);
		 $("#dst_loc_id").val("");
		  $("#dst_loc_id").find('option').remove();
		 $("#dst_loc_id").append("<option value=''>----Select----</option>");
		  $("#dst_usr_cd").val("");
		  $("#dst_usr_cd").find('option').remove();
		 $("#dst_usr_cd").append("<option value=''>----Select----</option>");
		 $("#sample_type").val("");
		$('input[name="ral_cal"]').prop('checked', false);
		var dist_code='stage_sample_code=' + stage_sample_code;
		if(stage_sample_code!='')
		{
			$('input[name="ral_cal"]').prop('disabled', false);
			$.ajax({
            type: "POST",
          url:"<?php echo Router::url(array('controller'=>'SampleForward','action'=>'check_details'));?>",
            data: {stage_sample_code: stage_sample_code},
            success: function (data) {
				if(data>0){
					$.ajax({
						type: "POST",
						url:"<?php echo Router::url(array('controller'=>'SampleForward','action'=>'get_commodity'));?>",
						data: {stage_sample_code: stage_sample_code},
						success: function (data) {
							if(data=="    [error]"){
								var msg="Error in stage sample code values!";
								errormsg(msg);
								$("#stage_sample_code").val($("#stage_sample_code option:first").val());
								return;
							}else{ 
								var op;
								$.each($.parseJSON(data), function (key, value) {
									$('#commodity_code').val(value['a']['commodity_name']);   
									$('#sample_type').val(value['b']['sample_type_desc']);
									if(value['b']['sample_type_code']==4 || value['b']['sample_type_code']==5){
										$('#acc_rej_flg1').prop('checked',true);
										$('#acc_rej_flg1').trigger("change");
										$('#acc_rej_flg').prop('disabled', true);
									}
									if(value['b']['sample_type_code']==1){
										$('#acc_rej_flg1').prop('disabled', false);
									}
								});
								$('#commodity_code option').remove();
							}
						}
					});	
				}
				else{
					alert("Please fill sample details first!");	
				}
            }
        });
     
		}
		else
		{
			$('input[name="ral_cal"]').prop('disabled', true);
			$('#commodity_code').val(''); 
		}
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
		function formSuccess() {
            alert('Success!');
        }

        function formFailure() {
            alert('Failure!');
        }

        $("#frm_sample_forward").validationEngine({
			promptPosition: 'inline',
            onFormSuccess: formSuccess,
            onFormFailure: formFailure
        });
	});
</script>	
<html>

<body>
<?php  ?>
		<div class="row">
		<h4 class="text-center">Agmark Quality Control Management System</h4>
		
        <?php 
		  if (isset($errors)){ 
		?>
		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-12 ">
			  <?php
				$i=0;
				foreach ($errors as $errors1): 
				$len=sizeof($errors1);
				for($i=0;$i<$len;$i++) {?>
				
					<p style="font-weight:bold;color:red"><?php echo $errors1[$i]; ?></p>
				<?php	
				}
				endforeach;
			  
				?>
			</div>
		</div>
		  <?php }
		  	?>
   		<div class="col-xs-12 col-sm-8 col-md-8 col-md-offset-2 ">
			<form id="frm_sample_forward" class="form-horizontal" method="post" action="" autocomplete="off">				
                    <?php 
                   // echo $this->Form->create('Sample_Inward', array('url' => array('controller' => 'Inward', 'action' => 'sample_inward')));
                    ?>				
                    <!--form method="post" action="" style-->
					<fieldset class="fsStyle">
                            <legend  class="legendStyle">Sample Forward</legend>
                            <input type="hidden" id="tran_date" name="tran_date" value="<?php echo date('Y-m-d');?> "/>
							<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
							<input type="hidden" id="src_loc_id" name="src_loc_id" value=""/>
							
						<div class="row">
							<div class="col-xs-3 col-sm-3 col-md-6">		
								<div class="form-group">				
									<label  class="control-label col-md-4" for="sel1">Sample Code </label>
									<div class="col-md-8">
										<select class="form-control validate[required]" id="stage_sample_code" name="stage_sample_code"  onchange="update_commodity()">
											<option value=''>-----Select-----</option>
											<?php
											$i=1;
												foreach ($res as $res1):
											?>
                                    <option value="<?php echo $res1['Sample_Inward']['stage_sample_code']; ?>"><?php echo $res1['Sample_Inward']['stage_sample_code']; ?></option>
                                <?php endforeach;  ?>
										</select>
										</div>
								</div>
							</div>						
						
							<div class="col-xs-3 col-sm-3 col-md-6">	
								<div class="form-group">				
									<label  class="control-label col-md-4" for="sel1">Commodity Name</label>
									<div class="col-md-6">
										<input type="text" class="form-control" id="commodity_code" name="commodity_code"  disabled>
										
										</div>
									<input type="text" class="form-control" id="type" name="type"  hidden>		
								</div>
							</div>
						</div>
						<div class="row">	
							<div class="col-xs-3 col-sm-3 col-md-6">	
								<div class="form-group">
								<label  class="control-label col-md-4" for="sel1"><b>Select Office  </b></label>
									<div class="col-md-6">
									<label class="radio-inline "><input class="validate[required] radio" type="radio" id="acc_rej_flg" name="ral_cal" value="R" required>RAL</label>
									<label class="radio-inline "><input class="validate[required] radio" type="radio" id="acc_rej_flg1"  name="ral_cal" value="C" required >CAL</label>
									<label class="radio-inline "><input class="validate[required] radio" type="radio" id="acc_rej_flg2"  name="ral_cal" value="H" required>HO</label>
									<input type="hidden" id="inward_id"  name="inward_id">
								</div>
								</div>
							</div>
							<div class="col-xs-3 col-sm-3 col-md-6">	
								<div class="form-group">				
									<label  class="control-label col-md-4" for="sel1">Sample Type</label>
									<div class="col-md-6">
										<input type="text" class="form-control" id="sample_type" name="sample_type"  readonly>
										
										</div>
											
								</div>
							</div>
						</div>
						
						<div class="row">	
							<div class="col-xs-3 col-sm-3 col-md-6">		
							<div class="form-group">	
					
								<label  class="control-label col-md-4" for="sel1">Forward To </label>
								<div class="col-md-6">
									<select class="form-control validate[required]" id="dst_loc_id"  name="dst_loc_id" onchange="get_user_name()"  >
									<option value=''>-----Select-----</option>
							<!----	<?php  
									$i=1;
									foreach ($office as $office1):
								?>
									<option value="<?php echo $office1[0]['id']; ?>"><?php echo $office1[0]['user_flag'].','.$office1[0]['ro_office']; ?></option>
                                <?php endforeach;  ?>--->
									</select>
										</div>
							</div>
						</div>
						<div class="col-xs-3 col-sm-3 col-md-6">		
							<div class="form-group">	
					
								<label  class="control-label col-md-4" for="sel1" id="dst_usr_lbl">User Name </label>
								<div class="col-md-6">
									<select class="form-control validate[required]"  id="dst_usr_cd" name="dst_usr_cd"  >
									<option value="-1">----Select----</option>
								
									</select>
								</div>
							</div>
						</div>
						
						</div>
							<div class="row">
								<div class="form-group ">	
									<div class="col-lg-12 text-center">
										<span>
											<button class="btn btn-primary" id="ral" >Sample Forward</button>
										</span>
										
									</div>
								</div>
							</div>
						</fieldset>
						
						
						
							
				</form>
					
			<br>
				</div>
			
		</div>
	
				
  
	
              
</div>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script>

$(document).ready(function(){
		$('input[name="ral_cal"]').prop('disabled', true);
			<?php 
				
				unset($_SESSION['sample']);
			unset($_SESSION['stage_sample_code']);
			//unset($_SESSION['loc_id']);
			//unset($_SESSION['inward_id']);
				?>
		$("#type").hide();
			
	$("#ral").click(function(e){
		
	var type=$("#dst_loc_id :selected").text();
	if(type!='')
		{
	$("#type").val(type);
		$('form#frm_sample_forward').submit();
		}
		else
			
			{
				alert("Please select User Name first!");
			}
	});
				
		
		
		
		$("#acc_rej_flg").change(function(){
			 
				var ral=$("#acc_rej_flg").val();
				//alert(ral);
				$.ajax({
					type:"POST",
					url:"<?php echo Router::url(array('controller'=>'SampleForward','action'=>'get_office'));?>",
					data:{ral:ral},
					cache:false,
					success : function(data){
						
						  $("#dst_loc_id").empty();
						    $("#dst_loc_id").append("<option>-----Select-----</option>");
						   $("#dst_loc_id").append(data);
						   $("#dst_usr_cd").empty();
						    $("#dst_usr_cd").append("<option>-----Select-----</option>");
						 
					
					}
				});	
				
				
		});
		$("#acc_rej_flg1").change(function(){
			 
				var ral=$("#acc_rej_flg1").val();
				//alert(ral);
				$.ajax({
					type:"POST",
					url:"<?php echo Router::url(array('controller'=>'SampleForward','action'=>'get_office'));?>",
					data:{ral:ral},
					cache:false,
					success : function(data){
						  $("#dst_loc_id").empty();
						  
						   $("#dst_loc_id").append(data);
						   $("#dst_usr_cd").empty();
						    $("#dst_usr_cd").append("<option>-----Select-----</option>");
						 
						    $("#dst_loc_id").change();
					
					}
				});	
					
		});
		$("#acc_rej_flg2").change(function(){
			 
				var ral=$("#acc_rej_flg2").val();
				//alert(ral);
				$.ajax({
					type:"POST",
					url:"<?php echo Router::url(array('controller'=>'SampleForward','action'=>'get_office'));?>",
					data:{ral:ral},
					cache:false,
					success : function(data){
						//alert(data);
						  $("#dst_loc_id").empty();			
//$("#dst_loc_id").append("<option>-----Select-----</option>");						  
						   $("#dst_loc_id").append(data);
						   $("#dst_usr_cd").empty();
						    $("#dst_usr_cd").append("<option >-----Select-----</option>");
						  
						   $("#dst_loc_id").change();
						   
					
					}
				});	
		});
	
});


</script>


</body>
</html>
