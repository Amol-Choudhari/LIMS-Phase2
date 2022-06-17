<?php
	echo $this->Html->script('jquery_validationui');
	echo $this->Html->script('languages/jquery.validationEngine-en');
	echo $this->Html->script('jquery.validationEngine');
	echo $this->Html->css('validationEngine.jquery');
	echo $this->Html->Script('bootstrap-datepicker.min');
	echo $this->Html->script('jquery.dataTables.min');
	echo $this->Html->css('jquery.dataTables.min');
	print  $this->Session->flash("flash", array("element" => "flash-message_new"));
//pr($_SESSION);
?>
<head>
<style>
    .container-fluid {
	overflow-x: hidden;
}	
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

.col-xs-15,
.col-sm-15,
.col-md-15,
.col-lg-15 {
    position: relative;
    min-height: 1px;
    padding-right: 10px;
    padding-left: 10px;
}
.col-xs-15 {
    width: 20%;
    float: left;
}
@media (min-width: 768px) {
    .col-sm-15 {
        width: 20%;
        float: left;
    }
}
@media (min-width: 992px) {
    .col-md-15 {
        width: 20%;
        float: left;
    }
}
@media (min-width: 1200px) {
    .col-lg-15 {
        width: 20%;
        float: left;
    }
}
    </style>
	<style>
.no-margin {
    margin: 0;
}
#page-content-wrapper{
	padding:0px;
}

@media screen and (min-width: 768px) {
        .modal-dialog {
			width: 900;
			height:600;
			margin: 30px auto;
		  }
		  .modal-content {
			-webkit-box-shadow: 0 5px 15px rgba(0, 0, 0, .5);
			box-shadow: 0 5px 15px rgba(0, 0, 0, .5);
		  }
		  .modal-sm {
			width: 300px;
		  }
    }
    @media screen and (min-width: 992px) {
		#myModal .modal-lg {
          width: 1000px; /* New width for large modal */
		  height:400px;
        }
    }
	#myModal.modal-content{
		height:50% !important;
	}
	.table-responsive{
		height:70% !important;
	}
	
	.modal-header {
		padding: 15px;
		border-bottom: 0px solid 
		#e5e5e5;
	}
	
	.table-responsive {
		border: 0px solid #eee;
	}
	.table.dataTable {
		width: 99%;
	}
	
</style>
</head>
<body>
<div id="myModal" class="modal" role="dialog" data-target=".bs-example-modal-lg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
                <h4 class="modal-title" id="test_title" align="center">Sample's received back without accepting by chemist</h4>
            </div>
			<form method="post" id="modal_test" action="" name="modal_test"class="form-horizontal" autocomplete="off">
				<div class="modal-body"  >
			      <div class="row">
                        <div style='margin: 0px 10px'>
                            <div class="table-responsive">
                                <table  class="table table-bordered" id="input_parameter_text" >
                                    <thead>
										<tr>
											<th>Sr No.</th>
											<th>Sample </th>
											<th>Commodity</th>
											<th>Chemist Code</th>
											<th>Remark</th>
											<th>Send Back Date</th>
											<th>Reallocate</th>
										</tr>
									</thead>
									<tbody>
										<?php if(isset($sendback))
										{
											$i = 0;
											foreach($sendback as $data){ 
											$i++;
											?>
											<tr id="">
											<td><?php echo $i; ?></td>
											<td><label  class="control-label " for="sel1"><?php echo $data[0]['sample_code'] ?></td>
											<td><label  class="control-label " for="sel1"><?php echo $data[0]['commodity_name'] ?></td>
											<td><label  class="control-label " for="sel1"><?php echo $data[0]['chemist_code'] ?></td>
											<td><label  class="control-label " for="sel1"><?php echo $data[0]['sendback_remark'] ?></td>
											<td><label  class="control-label " for="sel1"><?php echo $data[0]['recby_ch_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
											<td><i class="fa fa-share reallocate" aria-hidden="true" id='<?php echo $data[0]['sample_code'] ?>' style="cursor: pointer;"></i></td>
										<?php
											}
										}
										?>
									</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
				</div>
				<div class="modal-footer" style="border-top:none;">
                    <button type="button" class="btn btn-default" id="close1" data-dismiss="modal">Close</button>
                </div>  
			</form>				
		</div>
	</div>
</div>

	<div id="add_div" >
		<form id="frm_sample_allocate"  class="form-horizontal"   method="post" action="" autocomplete="off">
		<?php
					 if (isset($errors)){ 
					?>
					<div class="row">
					<div class="col-xs-12 col-sm-8 col-md-12 text-center">
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
		 <?php } ?>
		<fieldset class="fsStyle" style="margin: 21px 15px !important; ">
			<legend  class="legendStyle">Sample Allocation for Retest<span class='blink_me' style='color:red'><span id='pendingCount'></span></span> </legend>
			<div class="row">	
				<div class="col-xs-6 col-sm-6 col-md-3 col-md-offset-5">		
					<div class="form-group">
						<label class="radio-inline "><input class="validate[required] radio" type="radio" id="type" name="type" onchange="change_type()" value="F" required> Forward</label>
						<label class="radio-inline "><input class="validate[required] radio" type="radio" id="type"  name="type" onchange="change_type()" value="A" checked="checked" required>Allocate Tests</label>
					</div>
				</div>
				<?php //if(isset($sendback)>0){ ?>
					<div class="col-md-3 col-md-offset-1"  id='returnssample' style="text-align: right;font-size: 13px;font-weight: bold;margin-top: -15px;">		
						<a href="#" style='color:#8b00ff' onclick='returnssample();'>View samples returned by chemist</a>
					</div>
				<?php //} ?>
				<br>	
				<br>
			</div>	
			<div class="row">
				<?php 	$current_date = date('Y-m-d');
						$from_date = strtotime($current_date);
						$to_date = strtotime($current_date.' -1 year');
					?>
				<input type="hidden" name="fin_year" id="fin_year"  class="form-control" value="2016-2017">
				<input type="hidden" name="test_n_r_no" id="test_n_r_no"  class="form-control" value="1">
				<input type="hidden" name="tests" id="tests"  class="form-control" value="">
				<input type="hidden" name="alloc_by_user_code" id="alloc_by_user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
				<input type="hidden" name="chemist_code" id="chemist_code"  class="form-control" value="">
				<input type="hidden" name="li_code" id="li_code"  class="form-control" value="">
				<input type="hidden" name="alloc_cncl_flag" id="alloc_cncl_flag"  class="form-control" value="N">
				<input type="hidden" name="tran_date" id="tran_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
				<input type="hidden" name="alloc_date" id="alloc_date"  class="form-control" value="<?php echo date('Y-m-d');?>">
				<input type="hidden" name="button" id="button"  class="form-control" value="view">
				<input type="hidden" name="login_timestamp" id="login_timestamp"  class="form-control" value="<?php echo $timezone;?>"> 
				<input type="hidden" name="user_code" id="user_code"  class="form-control" value="<?php echo $_SESSION["user_code"];?>">
				<input type="hidden" name="rec_from_dt" id="rec_from_dt" value="<?php echo date('d/m/Y',$from_date); ?>"/>
				<input type="hidden" name="rec_to_dt"  id="rec_to_dt" value="<?php echo date('d/m/Y', $to_date); ?>">
					
					
				<div class="col-xs-15 col-sm-15 col-md-15">		
					<div class="form-group" id="sample_code_div">
						<label class="control-label col-md-4" for="sel1"> Sample Code </label>
						<div class="col-md-8">
						<select class="form-control" id="sample_code"  name="sample_code" onchange="getsampledetails();getqty();getsampleinfo();" required>
						</select>
						</div>
					</div>
				</div>
			
				<div class="col-xs-15 col-sm-15 col-md-15">		
					<div class="form-group">
						<label class="control-label col-md-4" for="sel1"> Category </label>
						<div class="col-md-8">									
						<select class="form-control"  id="commodity_category"  name="category_code" required>										
						</select>
						</div>
					</div>
				</div>
			
				<div class="col-xs-15 col-sm-15 col-md-15">		
					<div class="form-group">
						<label class="control-label col-md-4" for="sel1"> Commodity </label>
						<div class="col-md-8">
						<select class="form-control" id="commodity_name"  name="commodity_code" required>
						</select>
						</div>
					</div>
				</div>	
			
				<div class="col-xs-3 col-sm-3 col-md-3">		
					<div class="form-group">
						<label class="control-label col-md-4" for="sel1"> Sample Type </label>
						<div class="col-md-8">
							<select class="form-control" id="sample_type"  name="sample_type" required>                                    
							</select>									
						</div>
					</div>
				</div>	
			
			</div>
		</fieldset>		
		<div style="float: right;width:49%;height:auto" class="col-xs-6 col-sm-5 col-md-6" id="select_test_div">
			<fieldset class="fsStyle" style="margin: 21px 15px !important;height:50% ">						
				<legend  class="legendStyle" > <label id="labelalloc">Allocate  tests </label></legend>  						
					<div class="form-group" id="test_select_div">
						<div class="form-inline ">
							<!--label class="control-label col-md-6" for="sel1"> Test </label-->
							<div class="" style="height:90%;width:49%;float:left">
								<select autocomplete="off"  style="height:100%;width:70%" class="form-control" multiple="multiple" id="test_select" name="test_select[]" onchange="gettest()" align="center">
									
								 <option value="-1" disabled="">------Select----- </option></select>
								
								<!--<img src="../app/webroot/img/switch.png" class="img-rounded" style="margin-left:10px"alt="Cinque Terre" width="30" height="30" id="moveleft" onclick="move_left()">-->
								<button autocomplete="off" class="btn btn-primary" name="name" id="moveleft" onclick="move_left()">&gt;&gt;</button>
							</div>
							<div class="" style="height:90%;width:49%;float:right">
									<!--<img src="../app/webroot/img/switch.png" class="img-rounded" style="margin-left:10px"alt="Cinque Terre" width="30" height="30" id="moveright" onclick="move_right()">-->
									<button disabled="disabled" autocomplete="off" class="btn btn-primary" name="name" id="moveright" onclick="move_right()">&lt;&lt;</button>
										<select autocomplete="off"  style="height:100%;width:70%" class="form-control" multiple="multiple" id="test_select1" name="test_select1[]" onchange="removetest()" align="center">
										
								<option value="-1" disabled="">------Select----- </option></select>
							</div>
						</div>
					</div> 
			</fieldset>
		</div>		
		<fieldset class="fsStyle" style="margin: 21px 15px !important; ">
			<legend class="legendStyle"></legend>
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6">		
					<div class="form-group" id="test_select_div">
						<label  class="control-label col-md-4" for="sel1"> User type </label>
						<div class="col-md-8">
							<select class="form-control" id="user_type" name="user_type"  required onchange="get_users()"  disabled >
								<option value="-1">-----Select----- </option>
								<?php
									foreach ($user_type as $row1):
								?>
									<option value="<?php echo $row1['Dmi_user']['role']; ?>"><?php echo $row1['Dmi_user']['role']; ?></option> 
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6">		
					<div class="form-group" id="test_select_div">
						<label class="control-label col-md-4" for="sel1"> User Name </label>
						<div class="col-md-8">
						<select class="form-control" id="alloc_to_user_code" name="alloc_to_user_code"  required onchange='getalloctest1();getchem_code();getuserdetail();' disabled >
						   <option>-----Select----- </option>
						</select>
						</div>
					</div>
				</div>
			</div>
			<div class="row">	
				<div class="col-xs-5 col-sm-5 col-md-5" style="padding-right: 0px;">		
					<div class="form-group" id="smpl_qnt_div">
						<label  class="control-label col-md-4" for="sel1">Sample Quantity</label>
						<div class="col-md-8">
							<input type="number" name="sample_qnt" id="sample_qnt"  onchange="chk_qnt()"  value="1" min="1" maxlength='5' placeholder="Enter quantity" disabled   required class="form-control">	 <!--onkeyup="valid_qnt()"-->	
						</div>
					</div>
					<p id="qty"></p>
					<p id="unit"></p>
				</div>
				<div class="col-xs-2 col-sm-2 col-md-2" style="padding: 0px;">		
					<div class="form-group">											
						<div class="col-md-12">
							<?php echo $this->Form->input('sample_unit',array('options'=>$unit_desc,'id'=>'sample_unit', 'empty'=>'--Select--','label'=>false,'class'=>'form-control validate[required]') );?>
						</div>
					</div>										
				</div>
				<div class="col-xs-5 col-sm-5 col-md-5" id="exp_dt_div">
					<div class="form-group">
					<label class="control-label col-md-4" for="sel1"> Expected Completion  </label>
						<div class="col-md-8">
						<div class="input-group input-append date" id="datePicker2">
						<input type="text"  class="form-control"  name="expect_complt" id="expect_complt"    placeholder="dd/mm/yyyy"  disabled   required >	
						<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
						</div>
						</div>
					</div>	
				</div>
			</div>							 
		</fieldset>						  
		<div class="row">
			<div  class="col-xs-6 col-sm-5 col-md-6" id="test_type_div">	
				<fieldset class="fsStyle" style="margin: 21px 15px !important; ">
					<legend  class="legendStyle">Test</legend>
					<div class="form-group">								
						<label class="control-label col-md-3"><input type="radio" id="test_n_r"  name="test_n_r" value="R"  disabled required class='test_n_r_n_r' checked="checked">Retest</label>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<br>
			<div class="row" style='margin-bottom: 40px;'>
				<div class="col-lg-12 text-center" >                    
					<span>
						<button  class="btn btn-primary"  id="save" >Save</button>
					</span>
					<span>
						<button class="btn btn-primary" id="update" disabled>Edit</button>
					</span>
					<span>
						<button class="btn btn-primary" id="view" >View</button>
					</span>
					<span>
						<button class="btn btn-primary" id="close">Close</button>
					</span>
				</div>
			</div>
			
			<div class="row" id="avb" style='margin: 21px 15px !important;'>
				<div class="col-xs-18 col-sm-12 col-md-12 ">
					<fieldset class="fsStyle1">
						<legend  class="legendStyle">Allocated Samples</legend>
						<div class="table-responsive"   style="overflow-y:scroll">
							<table class="table table-striped " id="check_div1"  >
								<thead>
									<tr>
										<th>Sr No</th>
										<th>Sample Code</th>
										<th>Chemist/Division Code</th>
										<th>Sample Allocation Date</th>
										<th>Chemist Name</th>
										<th>Test Name</th>
									</tr>
								</thead>
								<tbody>
									<?php
									
										foreach($allRes1 as $data2){												
											$count=count($data2[0]['test_name']);
										}
										
										if(isset($allRes))
										{
											$i = 0;
											foreach ($allRes as $res1):
													$i++;  ?>
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $res1[0]['sample_code']; ?></td>
													<td><?php echo $res1[0]['chemist_code']; ?></td>
													<td><?php echo $res1[0]['alloc_date']; //Remove/change date format on 22-05-2019 by Amol?></td>
													<td><?php echo $res1[0]['cun_f_name'] ?></td>
													<td >
														<?php foreach($allRes1 as $data1){															
															if( $res1[0]['chemist_code']==$data1[0]['chemist_code']){ ?>
																<label  class="control-label" for="sel1"><?php echo $data1[0]['test_name'] ?></label></br>	
															
															<?php }
														} ?>
													</td>
												</tr>
									<?php endforeach; }?>
								</tbody>
							</table>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="row" id="avbfwd" style='margin: 21px 15px !important;'>
				<div class="col-xs-18 col-sm-12 col-md-12 ">
					<fieldset class="fsStyle">
						<legend  class="legendStyle">Forwarded Samples</legend>
						<div class="table-responsive"   style="overflow-y:scroll">
							<table class="table table-striped " id="check_div"  >
								<thead>
									<tr>
										<th>Sr No</th>
										<th>Sample Code</th>										
										<th>User Name</th>
										<th>Designation</th>
									</tr>
								</thead>
								<tbody>
									<?php						
									if(isset($res))
									{
										$i = 0;
										foreach ($res as $res1):
												$i++;
											?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $res1[0]['stage_smpl_cd']; ?></td>
												<td><?php echo $res1[0]['f_name'] ?></td>
												<td><?php echo $res1[0]['role'] ?></td>
											</tr>
									<?php endforeach; }?>							
								</tbody>
							</table>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
		</form>  
	</div>
	
	<script>
	
		$('#sample_code').select2();
		$("#save").attr("disabled",true);
		$('#input_parameter_text').DataTable({
			lengthMenu: [5, 10, 20, 50]
		});	
		$('#check_div1').DataTable({
			lengthMenu: [5, 10, 20, 50]
		});	
		$('#check_div').DataTable({
			lengthMenu: [5, 10, 20, 50]
		});		
		
		$('i.reallocate').click(function() { 
			var id = $(this).attr('id');
			$("#sample_code").select2().val(id).trigger("change");						
			$('#myModal').modal('toggle');	
		});
	
		function returnssample(){		
			$('#myModal').modal('show');
		}		
	</script>
	
	
	
<script>
	
	 var arr=new Array();
	  var allarr=new Array();
	  var arrtestalloc=new Array();
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        $("#menu-toggle-2").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled-2");
            $('#menu ul').hide();
        });
		
	//  pravin bhakare 29-10-2019
	function getsampledetails(){
		
		$("#commodity_category").find('option').remove();
		$("#commodity_name").find('option').remove();
		$("#sample_type").find('option').remove();
		$("#test_select").find('option').remove();
		$("#test_select1").find('option').remove();
		$("#save").attr("disabled",true);
		$("#update").attr("disabled",true);
		
		var sample_code=$("#sample_code").val();
		$.ajax({
				type: "POST",
				url: 'get_sample_cat_comm_type_details',
				data: {sample_code:sample_code},
				success: function (data) {
					
						$.each($.parseJSON(data), function (key, value) {
							
							if(key == 'mst'){
								
								$("#sample_type").append("<option value='" + $.trim(value['sample_type_code']) + "'>" + $.trim(value['sample_type_desc']) + "</option>");
								
							}else if(key == 'mc'){
								
								$("#commodity_name").append("<option value='" + $.trim(value['commodity_code']) + "'>" + $.trim(value['commodity_name']) + "</option>");
								
							}else if(key == 'mcc'){
								
								$("#commodity_category").append("<option value='" + $.trim(value['category_code']) + "'>" + $.trim(value['category_name']) + "</option>");
							}
						});
						
						/* call getqty function to get sample quantity, done by pravin bhakare,28-11-2019 */
						getqty();
				}
			});
	}	
	
	change_type();
	function change_type()
	{			
		var type=$("#type:checked").val();
		if(type=='F')
		{		
			$("#user_type option").remove();			
			$("#allocate_to").text("Forward To");
			$("#returnssample").prop("hidden", true);
			$("#smpl_qnt_div").hide();
			$("#exp_dt_div").hide();
			$("#test_type_div").hide();
			$("#select_test_div").hide();
			$("#sample_unit").hide();	
			$("#avb").prop("hidden", true);
			$("#avbfwd").prop("hidden", false);
			
			$("#sample_code option").remove();
			$("#commodity_name option").remove();
			$("#commodity_category option").remove();				
			$("#user_type").append("<option value=''>----Select----</option>");
			
			$.ajax({
				type: "POST",
				url: 'get_users_type',
				data: {type: type},
				success: function (data) {						
					$("#user_type").append(data);
				}
			});
		}
		else{
			
			$("#allocate_to").text("Allocate To");
			$("#returnssample").prop("hidden", false);
			$("#sample_code option").remove();
			$("#commodity_name option").remove();
			$("#commodity_category option").remove();				
			$("#avb").prop("hidden", false);
			$("#avbfwd").prop("hidden", true);
			$("#sample_unit").show();	
			$("#smpl_qnt_div").show()
			$("#exp_dt_div").show()
			$("#test_type_div").show()
			$("#select_test_div").show()				
			$("#user_type option").remove();
			$("#user_type").append("<option value='-1'>-----Select----- </option>");
			<?php foreach ($user_type as $row1): ?>				
				$("#user_type").append("<option value='<?php echo $row1['Dmi_user']['role']; ?>'><?php echo $row1['Dmi_user']['role']; ?></option>");
			<?php endforeach; ?>
		}
		
		$.ajax({
			type: "POST",
			url: 'get_sample_code_retest',
			data: {type:type},
			success: function (data) {
				
				var i = 0;
				if(data!="[]")
				{	$("#sample_code").append("<option value=''>---Select---</option>");
					$.each($.parseJSON(data), function (key, value) {
						$("#sample_code").append("<option value='" + $.trim(value[0]['stage_smpl_cd']) + "'>" + $.trim(value[0]['stage_smpl_cd']) + "</option>");
						i++;
					});
				}
				$('#pendingCount').text(' ( Pending : '+i+' )');
					
			}
		});
		
	  
	}
		function getuserdetail()
        {
			var type=$("#type:checked").val();
			$(".test_n_r").attr("disabled", true); 
			if(type=='F')
			{
				var sample_code=$("#sample_code").val();
				var alloc_to_user_code=$("#alloc_to_user_code").val();
				var test_n_r= $('input[name=test_n_r]:checked', '#frm_sample_allocate').val();
				var re_test=$('input[name=re_test]:checked', '#frm_sample_allocate').val();
				
				$.ajax({
                    type: "POST",
                    url: 'getuserdetail',
                    data: {sample_code:sample_code,alloc_to_user_code:alloc_to_user_code},
                    success: function (data) {
                       if(data>0)
					   {
							alert("Sorry! This sample once forwarded to same user.");
					   }				   
					}
                });
				
				if(test_n_r=="R"){
				$.ajax({
                    type: "POST",
                    url: 'gettest_n_r_no',
                    data: {test_n_r: test_n_r, re_test: re_test,sample_code:sample_code,alloc_to_user_code:alloc_to_user_code},
                    success: function (data) {
						$("#test_n_r_no").val(data);
                    }
                });
				}
			}
		}
		
        function initMenu() {
            $('#menu ul').hide();
            $('#menu ul').children('.current').parent().show();
            $('#menu li a').click(
                    function () {
                        var checkElement = $(this).next();
                        if ((checkElement.is('ul')) && (checkElement.is(':visible'))) {
                            return false;
                        }
                        if ((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
                            $('#menu ul:visible').slideUp('normal');
                            checkElement.slideDown('normal');
                            return false;
                        }
                    }
            );
        }
		 function gettest_n_r_no()
		{
				var sample_code=$("#sample_code").val();
				var alloc_to_user_code=$("#alloc_to_user_code").val();
				var test_n_r= $('input[name=test_n_r]:checked', '#frm_sample_allocate').val();
				var re_test=$('input[name=re_test]:checked', '#frm_sample_allocate').val();
			 $.ajax({
                    type: "POST",
                    url: 'gettest_n_r_no',
                    data: {test_n_r: test_n_r, re_test: re_test,sample_code:sample_code,alloc_to_user_code:alloc_to_user_code},
                    success: function (data) {
						$("#test_n_r_no").val(data);
                    }
                });
		}
		/* function gettest(){
			var test_select= $("#test_select").val();
			var test_text= $("#test_select :selected").text();
			$("#test_select :selected").remove();
			$("#test_select1").append("<option value='" + test_select + "'>" + test_text + "</option>");
				arr.push(test_select);
				 arrtestalloc.push(test_select[0]);
		} */
		function gettest(){
			var test_select= $("#test_select").val();
			$("#save").attr("disabled",false);
			var test_text= $("#test_select :selected").text();
			var selText=new Array();
			//alert(test_select.length);
			$("#test_select option:selected").each(function () {
				var $this = $(this);
				if ($this.length) {
					//selText = $this.text();
					selText.push($this.text());
				}
			});
			
			for(var i=0;i<test_select.length;i++){
				$("#test_select :selected").remove();
				$("#test_select1").append("<option value='" + test_select[i] + "'>" + selText[i] + "</option>");
				arr.push(test_select[i]);
				arrtestalloc.push(test_select[i]);
				if(arrtestOrigin.length<=0)
					arrtestOrigin.push(test_select[i]);
				if(!arrtestOrigin.includes(test_select[i]))
					arrtestOrigin.push(test_select[i]);
			}
		
			var test_select= $('#test_select option').length;
			if(test_select==1)
				$('#moveleft').attr('disabled',true);
			else
				$('#moveleft').attr('disabled',false);
			
			var test_select= $('#test_select1 option').length;
			if(test_select==1)
				$('#moveright').attr('disabled',true);
			else
				$('#moveright').attr('disabled',false);
			
		
		}
			function move_left(){
			//alert('hi');
			$('#moveleft').attr('disabled',true);
			$('#moveright').attr('disabled',false);
			//$("#save").attr("disabled",false);
			var test_text= $("#test_select :selected").text();
			var selText=new Array();
			var test_select=new Array();
			$("#test_select option").each(function () {
				var $this = $(this);
				if ($this.length) {
					//selText = $this.text();
					selText.push($this.text());
					test_select.push($this.val());
				}
			});
			var arrtestalloc	= new Array();
			for(var i=1;i<test_select.length;i++){
				//$("#save").attr("disabled",false);
				$("#test_select option").remove();
				$("#test_select").append("<option value='" + test_select[0] + "' disabled>" + selText[0] + "</option>");
				$("#test_select1").append("<option value='" + test_select[i] + "'>" + selText[i] + "</option>");
				arr.push(test_select[i]);
				arrtestalloc.push(test_select[i]);
				if(!arrtestOrigin.includes(test_select[i]))
					arrtestOrigin.push(test_select[i]);
				
			}
		/* 	alert('left'+arrtestalloc);
			alert('left arrtestOrigin'+arrtestOrigin); */
		}
		function move_right(){
			//alert('right');
			$('#moveright').attr('disabled',true);
			$('#moveleft').attr('disabled',false);
			//$("#save").attr("disabled",false);
			var test_text= $("#test_select1 :selected").text();
			var selText=new Array();
			var test_select=new Array();
			$("#test_select1 option").each(function () {
				var $this = $(this);
				if ($this.length) {
					//selText = $this.text();
					selText.push($this.text());
					test_select.push($this.val());
				}
			});
			var arrtestalloc	= new Array();
			for(var i=1;i<test_select.length;i++){
				$("#test_select1 option").remove();
				$("#test_select1").append("<option value='" + test_select[0] + "' disabled>" + selText[0] + "</option>");
				$("#test_select").append("<option value='" + test_select[i] + "'>" + selText[i] + "</option>");
				arr.push(test_select[i]);
				arrtestalloc.push(test_select[i]);
				
			}
			var length= $('#test_select1 option').length;
			if(length==1){
				//$('#save').attr('disabled',true);
			}else{
				//$('#save').attr('disabled',false);
			}
			arrtestOrigin.length=0;
			/* alert('right'+arrtestalloc);
			alert('right arrtestOrigin'+arrtestOrigin); */
		}
			function removetest(){
			
			var test_select= $("#test_select1").val();
			var test_text= $("#test_select1 :selected").text();
			var selText=new Array();
			//alert(test_select.length);
			$("#test_select1 option:selected").each(function () {
			   var $this = $(this);
			   if ($this.length) {
				//selText = $this.text();
				selText.push($this.text());
				
			   }
			});
			for(var i=0;i<test_select.length;i++){
				$("#test_select1 :selected").remove();
				$("#test_select").append("<option value='" + test_select[i] + "'>" + selText[i] + "</option>");
				arr.push(test_select[i]);
				var index = arrtestalloc.indexOf(test_select[i]);
				var index1 = arrtestOrigin.indexOf(test_select[i]);
				
				if (index > -1) {
					arrtestalloc.splice(index, 1);
					
				}
				if(arrtestOrigin.includes(test_select[i])){
					//alert("exists and removed "+test_select[i]);
					var index = arrtestOrigin.indexOf(test_select[i]);
					arrtestOrigin.splice(index, 1);
				}
				
				//arrtestalloc.push(test_select[i]);
			}
			var test_select= $('#test_select1 option').length;
			if(test_select==1){
				$('#moveright').attr('disabled',true);
				$('#save').attr('disabled',true);
			}else{
				$('#moveright').attr('disabled',false);
				//$('#save').attr('disabled',false);
			}
			
			var test_select= $('#test_select option').length;
			if(test_select==1)
				$('#moveleft').attr('disabled',true);
			else
				$('#moveleft').attr('disabled',false);
		
	
		}
		
		$("#all").click(function (e) {
			 $('#test_select option').remove();
					for(var i = 0; i < allarr.length; i++){
					label = allarr[i].code;
					value = allarr[i].text;
					$("#test_select1").append("<option value='" + label + "'>" + value + "</option>");
					arr.push(label);
					arrtestalloc.push(label);
				} 
			 $("#all").attr("disabled", true); 
		});
			/* function removetest(){
				var test_select= $("#test_select1").val();
				 var test_text= $("#test_select1 :selected").text();
				$("#test_select1 :selected").remove();
					$("#test_select").append("<option value='" + test_select + "'>" + test_text + "</option>");
					for(j=0;j<arr.length;j++){
				   if(parseInt(arr[j])==parseInt(test_select))
				   {
					   arr.splice(j,1);
					}
				  }
				  for(k=0;k<arrtestalloc.length;k++){
				   if(parseInt(arrtestalloc[k])==parseInt(test_select))
					{
					   arrtestalloc.splice(k,1);
					}
				}
			} */
		 function getalloctest1()
		 {
			 $("#test_select").attr("disabled", false); 
				$("#test_select1").attr("disabled", false); 
				$("#test_alloc").attr("disabled", false); 
				$("#sample_qnt").attr("disabled", false); 
				$("#parcel_size").attr("disabled", false); 
				 arrtestalloc.length = 0;
				var sample_code=$("#sample_code").val();
				var alloc_to_user_code=$("#alloc_to_user_code").val();
			    var nameuser= $("#alloc_to_user_code :selected").text();
				
				var category_code = $("#commodity_category").val();
				var rec_from_dt=$("#rec_from_dt").val();
				var rec_to_dt=$("#rec_to_dt").val();
				var test_n_r='R';
				$("#labelalloc").text("Allocate tests to "+nameuser);
				$.ajax({
					type:"POST",
					url: 'get_details',
					data: {sample_code:sample_code,alloc_to_user_code:alloc_to_user_code},
					cache:false,
					success : function(data){
						//alert(data);
						if(data!='NO_DATA'){
						 $.each($.parseJSON(data), function (key, value) {
							 
							 var $radios = $('input:radio[id=test_n_r]');
							$radios.filter('[value=R]').prop('checked', true);
							
						/* if((key=='test_n_r')&&(value=='R'))
						{
							 var $radios = $('input:radio[id=test_n_r]');
            				   $radios.filter('[value=R]').prop('checked', true);
						} */
						
						$("#update").attr("disabled",false);
						$("#delete").attr("disabled",false);
						$("#save").attr("disabled",true);
						$("#add").attr("disabled",true);
						 });
					} 
					else{
							
						    $("#sample_qnt").val('');
							$("#update").attr("disabled",true);
							$("#delete").attr("disabled",true);
							$("#save").attr("disabled",false);
							$("#add").attr("disabled",true);
						}
				}	
			});		
					
			
            if (category_code != "")
            {
                var commodity_code = $("#commodity_name").val();
			}
			$("#test_select1").find('option').remove();
            $("#test_select1").append("<option value='-1'>------Select----- </option>");
			$("#test_select").find('option').remove();
            $("#test_select").append("<option value='-1'>------Select----- </option>");
			$.ajax({
                    type: "POST",
                    url: 'get_alloc_test',
                    data: {sample_code: sample_code, alloc_to_user_code: alloc_to_user_code},
                    success: function (data) {
                      // alert(data); 
                        $.each($.parseJSON(data), function (key, value) {
                            $("#test_select1").append("<option value='" + key + "'>" + value + "</option>");
							arrtestalloc.push(key);
                        });
                    }
                });
				
				$.ajax({
                    type: "POST",
                    url: 'get_test_by_commodity_id',
                    data: {commodity_code: commodity_code, category_code: category_code,sample_code: sample_code, alloc_to_user_code: alloc_to_user_code},
                    success: function (data) {
						if(data.indexOf('[error]') !== -1){
							var msg =data.split('~');
							errormsg(msg[1]); 
							$("#stage_sample_code").val('');
							return;
						}else{
							var j=0;
							$.each($.parseJSON(data), function (key, value) {
							   $("#test_select").append("<option value='" + key + "'>" + value + "</option>");
								allarr.push({ code: key,text:value });
							});
							 $("#all").attr("disabled", false); 
							}
						}
                });
		 }
		 function getalloctest()
		 {
				$("#test_select").attr("disabled", false); 
				$("#test_select1").attr("disabled", false); 
				$("#test_alloc").attr("disabled", false); 
				$("#sample_qnt").attr("disabled", false); 
				$("#parcel_size").attr("disabled", false); 
				 arrtestalloc.length = 0;
				var sample_code=$("#sample_code").val();
				var alloc_to_user_code=$("#alloc_to_user_code").val();
			    var nameuser= $("#alloc_to_user_code :selected").text();
				
				var category_code = $("#commodity_category").val();
				var rec_from_dt=$("#rec_from_dt").val();
				var rec_to_dt=$("#rec_to_dt").val();
				$("#labelalloc").text("Allocate tests to "+nameuser);
					$.ajax({
					type:"POST",
					url: 'get_details',
					data: {sample_code:sample_code,alloc_to_user_code:alloc_to_user_code},
					cache:false,
					success : function(data){
						 if(data.indexOf('[error]') !== -1){
							var msg =data.split('~');
							errormsg(msg[1]); 
							return;
						}else{
						if(data!='NO_DATA'){
						 $.each($.parseJSON(data), function (key, value) {
							if((key=='test_n_r')&&(value=='N'))
							{
							 var $radios = $('input:radio[id=test_n_r]');
							$radios.filter('[value=N]').prop('checked', true);
							}
						if((key=='test_n_r')&&(value=='R'))
						{
							 var $radios = $('input:radio[id=test_n_r]');
            				   $radios.filter('[value=R]').prop('checked', false);
						}
						if(key=='sample_qnt')
						{
							$("#sample_qnt").val(value);
						}
						if(key=='expect_complt'){
							var start_date=formatDate(value);
							
							$("#expect_complt").val(start_date);
							
						}
						$("#update").attr("disabled",false);
						$("#delete").attr("disabled",false);
						$("#save").attr("disabled",true);
						$("#add").attr("disabled",true);
						 });
					} 
					else{
						 var $radios = $('input:radio[id=test_n_r]');
            			   $radios.filter('[value=N]').prop('checked', false);
						   var $radios = $('input:radio[id=test_n_r]');
            			   $radios.filter('[value=R]').prop('checked', false);
						    $("#sample_qnt").val('');
							$("#update").attr("disabled",true);
							$("#delete").attr("disabled",true);
							$("#save").attr("disabled",false);
							$("#add").attr("disabled",true);
						}
					}
				}	
			});			
			
            if (category_code != "")
            {
                var commodity_code = $("#commodity_name").val();
			}
			$("#test_select1").find('option').remove();
            $("#test_select1").append("<option value='-1'>------Select----- </option>");
			$("#test_select").find('option').remove();
            $("#test_select").append("<option value='-1'>------Select----- </option>");
			$.ajax({
                    type: "POST",
                    url: 'get_alloc_test',
                    data: {sample_code: sample_code, alloc_to_user_code: alloc_to_user_code},
                    success: function (data) {
                      // alert(data); 
                        $.each($.parseJSON(data), function (key, value) {
                            $("#test_select1").append("<option value='" + key + "'>" + value + "</option>");
							arrtestalloc.push(key);
                        });
                    }
                });
				
				$.ajax({
                    type: "POST",
                    url: 'get_test_by_commodity_id',
                    data: {commodity_code: commodity_code, category_code: category_code,sample_code: sample_code, alloc_to_user_code: alloc_to_user_code},
                    success: function (data) {
						if(data.indexOf('[error]') !== -1){
							var msg =data.split('~');
							errormsg(msg[1]); 
							$("#stage_sample_code").val('');
							return;
						}else{
							var j=0;
							$.each($.parseJSON(data), function (key, value) {
							   $("#test_select").append("<option value='" + key + "'>" + value + "</option>");
								allarr.push({ code: key,text:value });
							});
							 $("#all").attr("disabled", false); 
							}
						}
                });
		}
		 function commodity_test()
        {
			$("#sample_code").attr("disabled", false); 
			$("#user_type").attr("disabled", false); 
			$("#expect_complt").attr("disabled", false); 

			$("#test_parameter").find('li').remove();

            $("#test_select").find('option').remove();
            $("#test_select").append("<option value='-1'>------Select----- </option>");
			$("#alloc_to_user_code").find('option').remove();
			$("#alloc_to_user_code").append("<option value='-1'>------Select----- </option>");
			//$("#qty").remove();
			var type=$("#type:checked").val();
			$("#sample_code").find('option').remove();
            var category_code = $("#commodity_category").val();
			$("#sample_code").append("<option value=''>----Select----</option>");
            if(type!=''){
				if(type=="F"){
					$("#qty").val("");
					
					$("#user_type").find('option').remove();
					$("#user_type").append("<option value=''>----Select----</option>");
					//$("#sample_code option").remove();
					//$("#commodity_category option").remove();
					//$("#alloc_to_user_code option").remove();
					//$("#commodity_name option").remove();
					$.ajax({
						type: "POST",
						url: 'get_users_type',
						data: {type: type},
						success: function (data) {
							
							$("#user_type").append(data);
						}
					});
				}
				if (category_code != "")
				{
					var commodity_code = $("#commodity_name").val();					
				}
				else {
					var msg="Select commodity category first!";
					errormsg(msg);
				}
			}
			else{
				var msg="Select commodity category first!";
				errormsg(msg);
			}
		}
	function getflag()
		{
			var sample_code=$("#sample_code").val();
			var type=$("#type:checked").val();
			$("#flg").empty();
			$.ajax({
				type:"POST",
				url:'get_flag_retest',
				data:{sample_code:sample_code},
				success:function(data){
					
					 if(data.indexOf('[error]') !== -1){
							var msg =data.split('~');
							errormsg(msg[1]); 
							$("#sample_code").val("");
							return;
							
						}else{
				 $.each($.parseJSON(data), function (key, value) {
					//alert(value[0]['flg1']);
					if(type!='F')
									{
										
											$("#flg").append("  "+value[0]['flg1']+"");
									}else{
										document.getElementById("flg").innerHTML = "";
									}
										
				 });	
				}				 
				}
			});
			
		}
		function getqty()
        {
			$("#sample_code").attr("disabled", false); 
			$("#user_type").attr("disabled", false); 
			$("#expect_complt").attr("disabled", false); 
			$("#parel_size").attr("disabled", true); 
			var type=$("#type:checked").val();
			$("#qty").empty();
			$("#unit").empty();
            var category_code = $("#commodity_category").val();
			var sample_code=$("#sample_code").val();
			$("#user_type").val($("#user_type option:first").val());
			$("#alloc_to_user_code").val($("#user_type option:first").val());
			//$("#sample_code").val($("#sample_code option:first").val());
			$("#sample_qnt").val("");
			
			/* remove old option value from sample unit drop down and append the sample unit of selected sample, done by pravin bhakare,28-11-2019 */
			$("#sample_unit").find('option').remove();
			
			 if (sample_code != "")
            {
                var commodity_code = $("#commodity_name").val();
                $.ajax({
                    type: "POST",
                    url: 'get_qty',
                    data: {sample_code:sample_code,type:type,commodity_code: commodity_code, category_code: category_code},
                    success: function (data) {
						//	alert(data);
							 if(data.indexOf('[error]') !== -1){
							var msg =data.split('~');
							//errormsg(msg[1]); 
							return;
						}else{
									 $.each($.parseJSON(data), function (key, value) {
									if(type!='F')
									{
										
										if(value[0]['sample_total_qnt']){
											$("#qty").append("Available Quantity - "+value[0]['total']+' ' +value[0]['unit_weight']+"");
											/* append the sample unit like gram, done by pravin bhakare,28-11-2019 */
											$("#sample_unit").append("<option value='"+value[0]['parcel_size']+"'>"+value[0]['unit_weight']+"</option>");
										}
										else if(value[0]['sample_total_qnt']==0){
											$("#qty").append("Available Quantity - "+value[0]['total']+"");
											/* append the sample unit like gram, done by pravin bhakare,28-11-2019 */
											$("#sample_unit").append("<option value='"+value[0]['parcel_size']+"'>"+value[0]['unit_weight']+"</option>");
										}
										else{
											$("#qty").append("Available Quantity - "+value[0]['total']+ ' ' +value[0]['unit_weight']+"");
											/* append the sample unit like gram, done by pravin bhakare,28-11-2019 */
											$("#sample_unit").append("<option value='"+value[0]['parcel_size']+"'>"+value[0]['unit_weight']+"</option>");
										}
									}else{
									
										document.getElementById("qty").innerHTML = "";

									}
									
									
								});
							}

                    }
                });
			}
            else {
				var msg="Select Sample Code first!";
				$("#user_type").val($("#user_type option:first").val());
				$("#alloc_to_user_code").val($("#user_type option:first").val());
				$("#sample_qnt").val("");
				
				errormsg(msg);
            }
		}
	
	
	function view1()
	{
		$("#avbfwd").prop("hidden", true);
		$("#avb").prop("hidden", false);
			var i=1;
			$("#check_div tbody").empty();
			var button=$("#button").val();
		   $.ajax({
                    type: "POST",
                    url: 'view_data',
                    data: {button:button},
                    success: function (data) 
					{
						$.each($.parseJSON(data), function (key, value)
						{
								$.each( value,function (key1, value1)
									{
										var rowcontent="<tr><td>"+i+"</td>";
										$.each( value1,function (key2, value2)
										{	//alert(value2);
											if(value2 == 'N'){
											value2 ="Normal";
											}
											else if(value2 == 'R')
											{
												value2="Retest";
											}
											rowcontent=rowcontent+"<td>"+value2+"</td>";
										});
                                    	$("#check_div tbody").append(rowcontent);
									});
									i++;
						});
					}
                });
	 }
	 
	 function view_fwd()
	{
		$("#avbfwd").prop("hidden", false);
		$("#avb").prop("hidden", true);
			var i=1;
			$("#check_div tbody").empty();
			var button=$("#button").val();
		   $.ajax({
                    type: "POST",
                    url: 'view_data_fwd',
                    data: {button:button},
                    success: function (data) 
					{
						$.each($.parseJSON(data), function (key, value)
						{
								$.each( value,function (key1, value1)
									{
										var rowcontent="<tr><td>"+i+"</td>";
										$.each( value1,function (key2, value2)
										{	//alert(value2);
											if(value2 == 'N'){
											value2 ="Normal";
											}
											else if(value2 == 'R')
											{
												value2="Retest";
											}
											rowcontent=rowcontent+"<td>"+value2+"</td>";
										});
                                    	$("#check_div tbody").append(rowcontent);
									});
									i++;
						});
					}
                });
	 }
	

	$(document).ready(function () {		
	
		$("#avb").prop("hidden", true);
				$("#avbfwd").prop("hidden", true);
		$("#parel_size").attr("disabled", true); 
 
		$('#rec_from_dt').datepicker({
				changeMonth: true,
				changeYear: true,
				autoclose: true,
				
				todayHighlight: true,	
				firstDay: 1,
				endDate: '+0d',
				format: 'dd/mm/yyyy',
        }).on('changeDate', function (selected) {
			var minDate = new Date(selected.date.valueOf());
			$('#rec_to_dt').datepicker('setStartDate', minDate);
			
			/* Below line added on 23-05-2019 by Pravin Bhakare
				Why : To hide datepicker window after picking a date. */
			$(this).datepicker('hide'); 
			
		});		
		var lastDate = new Date();
		 $('#rec_to_dt').datepicker({
			changeMonth: true,
			changeYear: true,
			autoclose: true,
			 endDate: '+0d',
			todayHighlight: true,	
			firstDay: 1,			
			format: 'dd/mm/yyyy',
        }).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('#rec_from_dt').datepicker('setEndDate', maxDate);
        });
		// var dateToday = new Date(); 
		 var date = new Date();
        var currentMonth = date.getMonth(); // current month
        var currentDate = date.getDate(); // current date
        var currentYear = date.getFullYear(); //this year
		$('#expect_complt').datepicker({
			    startDate: new Date(),
				todayHighlight: true,
				autoclose: true,
				format: 'dd/mm/yyyy',
        });	
		
		/* Below two lines commented on 23-05-2019 by Pravin Bhakare
		   Why : To resolved "from and To" selecting date issue.	
		   Before : Defualt From and To date fields value selected with current date.
		   Now : Defualt From and To date fields value not selected. */	
		//$('#rec_to_dt').datepicker('setDate', 'today');
		//$('#rec_from_dt').datepicker('setDate', 'today');	
	
	
		$( "#expect_complt" ).datepicker('setDate', 'today');
		$( "#expect_complt" ).datepicker({ format: 'dd/mm/yyyy' });
		$( "#rec_from_dt" ).datepicker({ format: 'dd/mm/yyyy' });
		$( "#rec_to_dt" ).datepicker({ format: 'dd/mm/yyyy' });


		//$('#rec_from_dt').change(function() {
		/* Below datepicker id changed for "changeDate Event on" on 23-05-2019 by Pravin Bhakare
		   Why : To resolved "from and To" selecting date issue.	
		   Before : "changeDate Event" call on "from date changed".
		   Now : "changeDate Event" call on "To date changed". */		
		var datePicker = $('#rec_to_dt').datepicker().on('changeDate', function(ev) {
			
			var rec_from_dt1 = $("#rec_from_dt").val();
			var rec_to_dt2=$("#rec_to_dt").val();
			
			var rec_from_dt = $('#rec_from_dt').datepicker('getDate');
			var rec_to_dt   = $('#rec_to_dt').datepicker('getDate');
			var type=$("#type:checked").val();
			if (rec_from_dt<=rec_to_dt) {
				$("#commodity_category").attr("disabled", false); 
					$.ajax({
						type: "POST",
						url: 'get_category_retest',
						   data: {type:type,rec_from_dt: rec_from_dt1,rec_to_dt:rec_to_dt2},
						   success: function (data) {
									if(data.indexOf('[error]') !== -1){
									var msg =data.split('~');
									errormsg(msg[1]); 
									return;
								}else{
						$("#commodity_category").find('option').remove();
						$("#commodity_category").append("<option value='-1'>-----Select----- </option>");
						 if(data=='NO_DATA')
						{
							/* Below message updated on 23-05-2019 by Pravin Bhakare  
								Why : old message was not properly written */
							 var msg="No samples available !!!";
							 errormsg(msg);
						}
						 else{
							$("#commodity_category").append(data);
						 }
							
						}
						   }
					});

			}
			else {
				
				var msg="Please select from date before To Date";
				errormsg(msg);
				
				/* Below line added on 23-05-2019 by Pravin Bhakare
				   Why : To resolved "from and To" selecting date issue.	
				   Before : Nothing.
				   Now : If user selected first "To date" before "From Date" then show the error message and blank the selected "To date" automaticaly */	
				$('#rec_to_dt').datepicker('setDate', null);
				
			}
		});	
	
	
		/* Below condition added on 23-05-2019 by Pravin Bhakare
		   Why : To resolved "from and To" selecting date issue for generating forwarded sample letter.	
		   Before : Nothing.
		   Now : If you selected "from date" then "To date" blank automaticaly */	
		var datePicker = $('#rec_from_dt').datepicker().on('changeDate', function(ev) {
			$('#rec_to_dt').datepicker('setDate', null);
		});


		$('#rec_to_dt').datepicker().on('changeDate', function(ev) {
			var StartDate = $('#rec_from_dt').datepicker('getDate');
			var EndDate   = $('#rec_to_dt').datepicker('getDate');
			  if(StartDate > EndDate)
				{
				//alert("Please ensure that the End Date is greater than or equal to the Start Date.");
				var msg="Please ensure that the To Date must be greater than From Date.";
								errormsg(msg);
								
				$('#rec_to_dt').datepicker('setDate', 'today');				
			
				}
				else{
					$("#stage_sample_code1").prop("disabled", false);
				}
			
			
		});
		
 
		$("#add").click(function (e) {
			$("#rec_from_dt").attr("disabled", false); 
			$("#rec_to_dt").attr("disabled", false); 
		
			$(".test_n_r_n_r").attr("disabled", false); 
			$("#save").attr("disabled", false); 
			$("#cancel").attr("disabled", false); 
			$("#add").attr("disabled", true); 
		});   
	
		$("#view").click(function(e)
		{ e.preventDefault(); 
			var type=$("#type:checked").val();
			if(type=='F')
			{
				$("#avb").prop("hidden", true);
				$("#avbfwd").prop("hidden", false);
			}
			else
			{
				$("#avb").prop("hidden", false);
				$("#avbfwd").prop("hidden", true);
			}
		});
		
		$("#cancel").click(function() {
			  $(".formError").remove();
			 $("#users").empty();
			 $('form#frm_sample_allocate')[0].reset();
			 $("#commodity_category").attr("disabled", true); 
			 $("#commodity_name").attr("disabled", true); 
			  $("#sample_code").attr("disabled", true);
			  //$("#sample_code").attr("disabled", true);
		});
		
		$("#close").click(function(e) {
			 e.preventDefault(); 
			  location.href="<?php echo $home_url; ?>";
		});
		
        $("#delete").click(function (e) {
		  $("#button").val('delete');
			BootstrapDialog.confirm("Delete sample allocated to test!!!", function(result){
				if(result){
					$("frm_sample_allocate").submit();
					location.reload();
				}
			});
		});
		
		$("#update").click(function (e) {
			
			e.preventDefault();
			var sample_code = $("#sample_code").val();
			var alloc_to_user_code = $("#alloc_to_user_code").val();
	
			var user_type=$("#user_type").val();
			var commodity_category=$("#commodity_category").val();
			var fin_year=$("#fin_year").val();
			//var test_n_r=$("#test_n_r").val();
			var test_n_r= $('input[name=test_n_r]:checked', '#frm_sample_allocate').val();
			var commodity_name=$("#commodity_name").val();
			var rec_from_dt=$("#rec_from_dt").val();
			var rec_to_dt=$("#rec_to_dt").val();
			var sample_qnt=$("#sample_qnt").val();
			 var expect_complt=$("#expect_complt").val();
			 var re_test=$("#re_test").val();
				 //alert(alloc_to_user_code);
			$.ajax({
				type: "POST",
				url: "update_details",
				cache:false,
			  data: {sample_code:sample_code,alloc_to_user_code:alloc_to_user_code,arrtestalloc:arrtestalloc,user_type:user_type,commodity_name:commodity_name,commodity_category:commodity_category,fin_year:fin_year,test_n_r:test_n_r,rec_from_dt:rec_from_dt,rec_to_dt:rec_to_dt,sample_qnt:sample_qnt,expect_complt:expect_complt,re_test:re_test},
				success: function (data) {
					//alert(data); 
					if(data=='1'){
						var msg="Record Updated Successfuly!!!";
						errormsg(msg);
						view1();
						return;
					}
				}
			});
		});
	 
		$("#save").click(function (e) {
			  
			e.preventDefault();		  
			var type=$("#type:checked").val();				
			$("#button").val('add');
			 
			if(type=='A'){
				
				//Hide save button after click on save button, done by pravin bhakare, 05-12-2019
				$("#save").attr("disabled", true);
				$("#tests").val(arrtestalloc);				
				$("#frm_sample_allocate").submit();
				
			}else if(type=='F'){	
			
				//Hide save button after click on save button, done by pravin bhakare, 05-12-2019
				$("#save").attr("disabled", true);
				$("#frm_sample_allocate").submit();					
			}
		});
	 
	});
	
	
	function valid_qnt()
	{
		 var Qty = $("#sample_qnt").val();
		// alert(Qty);
		 if (!/^[0-9]\d?$/.test(Qty)){
			var msg="Please Enter Valid Quntity!!!";
			errormsg(msg);
		
			$("#sample_qnt").val('');
			$("#sample_qnt").focus();
			return false;
		}
	}
	
	
	function chk_qnt()
	{		
		$(".test_n_r_n_r").attr("disabled", false); 
		$(".test_n_r").attr("disabled", false); 
		var val=$(".test_n_r").val();
		
		var $radios = $('input:radio[id=test_n_r]');
		$radios.filter('[value=N]').prop('checked', true);
		if(val=="undefined")
		{
			var $radios = $('input:radio[id=test_n_r]');
			$radios.filter('[value=N]').prop('checked', true);	
		}
		var sample_code = $("#sample_code").val();
		var category_code = $("#commodity_category").val();
		var type=$("#type:checked").val();
		var sample_qnt = $("#sample_qnt").val();
		var commodity_code = $("#commodity_name").val();
		$(".test_n_r").attr("disabled", false); 
		if(sample_qnt=="")
		{
			 var msg="please enter sample quantity!!";
							errormsg(msg);
			return;
		}
		
		$.ajax({
			type: "POST",
			url: 'get_ttl_qnt',
			data: {sample_code:sample_code,type:type,commodity_code: commodity_code,category_code:category_code},
			success: function (data) {
				if(data.indexOf('[error]') !== -1){
						var msg =data.split('~');
						errormsg(msg[1]); 
						return;
						}else{
					 //alert(data);return;
					var tot_qnt=parseInt(data);
					var tot_alloc_qnt=parseInt(sample_qnt);
					if(tot_qnt!=0){
					if(tot_qnt<tot_alloc_qnt)
						{
							var msg="  Sample quantity for allocation exceeds total quantity,Please enter valid quantity..!!!";
								errormsg(msg);
							$("#sample_qnt").val('');
							$("#sample_qnt").focus();
							return;
						}
					}else{
						errormsg("Quantity not available!");
					}
				}
			}
		});
	}
	
	
	
	function get_commodity()
	{
		$("#commodity_name").attr("disabled", false); 
		$("#sample_code").find('option').remove();
		$("#sample_code").append("<option value='-1'>-----Select----- </option>");
		$("#commodity_name").find('option').remove();
		$("#commodity_name").append("<option value='-1'>-----Select---- </option>");
		$("#test_select").find('option').remove();
		$("#test_select").append("<option value='-1'>----Select----- </option>");
		var category_code = $("#commodity_category").val();
		var rec_from_dt = $("#rec_from_dt").val();
		var rec_to_dt=$("#rec_to_dt").val();
		$.ajax({
			type: "POST",
			url: 'get_commodity',
			data: {category_code: category_code,rec_from_dt:rec_from_dt,rec_to_dt:rec_to_dt},
			success: function (data) {
			//alert(data); 
				if(data.indexOf('[error]') !== -1){
						var msg =data.split('~');
						errormsg(msg[1]); 
						return;
					}else{
				$("#commodity_name").append(data);
				
			}
			}
		});
	}
	
	
	
	function get_users()
	{
		$("#alloc_to_user_code").attr("disabled", false); 
		$("#alloc_to_user_code").find('option').remove();
		$("#alloc_to_user_code").append("<option value='-1'>-----Select----- </option>");
		var user_type = $("#user_type").val();
		$.ajax({
			type: "POST",
			url: 'get_users',
			data: {user_type: user_type},
			success: function (data) {
				$("#alloc_to_user_code").append(data);
			}
		});
	}
	
	
	
	function getchem_code()
	{
		var user_type = $("#user_type").val();
		$.ajax({
			type: "POST",
			url: 'get_chem_li_code',
			data: {user_type: user_type},
			success: function (data) {
			 var chem_li_code =data.split('~');
				$("#chemist_code").val(chem_li_code[0]);
				$("#li_code").val(chem_li_code[1]);
			}
		});
	}
	
	
	
	$('.test_n_r_n_r').click(function(){
		
		var rBtnVal = $(this).val();
		
		if(rBtnVal == "N"){
			
			  $("#reset_id").prop("hidden", true); 
			  $(".pre_new_test").attr('checked', false);
			 $(".pre_new_test").attr("disabled", true); 
			 
		}else{ 
			  $("#reset_id").prop("hidden", false); 
			 $(".pre_new_test").attr("disabled", false); 
			 $("#update").attr("disabled",true);
			$("#delete").attr("disabled",true);
			$("#save").attr("disabled",false);
		 }
    });
	
	
	function enable_cate()
	{	
		$("#commodity_category").attr("disabled", false); 
	}

	function getsampleinfo(){
		$("#avb").prop("hidden", false);
		var i=1;
		$("#check_div tbody").empty();
		var sample_code=$("#sample_code").val();
		//alert(sample_code);
		$.ajax({
			type: "POST",
			url: 'view_data1',
			data: {sample_code:sample_code},
			success: function (data) {
				$.each($.parseJSON(data), function (key, value)
				{
					$.each( value,function (key1, value1)
					{
						var rowcontent="<tr><td>"+i+"</td>";
						$.each( value1,function (key2, value2)
						{	//alert(value2);
							if(value2 == 'N'){
							value2 ="Normal";
							}
							else if(value2 == 'R')
							{
								value2="Retest";
						}
							rowcontent=rowcontent+"<td>"+value2+"</td>";
						});
						$("#check_div tbody").append(rowcontent);
					});
					i++;
				});
			}
		});
	}


    </script>
</body>
</html>