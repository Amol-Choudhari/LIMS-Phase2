
<?php
echo $this->Html->script('jquery.dataTables.min');
echo $this->Html->css('jquery.dataTables.min');
//print  $this->Session->flash("flash", array("element" => "flash-message_new")); ?>
<head>
<style>
    #test_parameter li {
        cursor: pointer;
    }     
    
      
        .Absolute-Center {
            margin: auto;
            border: 1px solid #ccc;
            background-color: #f3fafe;
            padding:20px;
            border-radius:3px;	
            top: 0; left: 0; bottom: 0; right: 0;
        }
        
        body{
            font-family: Arial, Helvetica, sans-serif;
        }
       @media screen and (min-width: 768px) {
        .modal-dialog {
			width: 900;
			height:900;
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
		#exampleModal .modal-lg {
          width: 1300px; /* New width for large modal */
		  height:900px;
        }
    }
    </style>
</head>
<script>
$(function(){
	$("li a").addClass('disabled');  // to disable menubar
})
</script>

<!-- <?php/* // Ajax Call
//this ajax code added on 21-12-2019 by Amol, to get method code list on selection on test
//then update the methos code select drop down with the response list.
		$data = $this->Js->get('#frm_commodity_grade')->serializeForm(array('isForm' => true, 'inline' => true));			
			
			$this->Js->get('#test_code')->event(
				'change',
				$this->Js->request(
				  array('controller'=>'commodity_grade','action' => 'get_test_methods'),
				  array(
				   'update' => '#method_code',
				   'complete' => 'enable_standrd();',//added this call to function on 19-01-2021 by Amol, and removed from inline element call.
					'data' => $data,
					'async' => true,    
					'dataExpression'=>true,
					'method' => 'POST'
						
				 )
				)
			  );		  

	echo $this->Js->writeBuffer();
  */                                              
?> -->
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-3"><h5 class="m-0 text-dark">Assign Test to Commodity</h5></div>
				<div class="col-sm-9">
					<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
					<li class="breadcrumb-item"><?php echo $this->Html->link('Code Files', array('controller' => 'master', 'action'=>'code_master_home')); ?></li>
					<li class="breadcrumb-item active">>Assign Test to Commodity</li>
				</ol>
			</div>
		</div>
	</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-info">
							<div class="card-header"><h3 class="card-title-new">Granding Standards</h3></div>
								<div class="col-xs-12 col-sm-6 col-md-12 col-sm-offset-2 col-md-offset-0">
                    
                        <form id="frm_commodity_grade"  class="form-horizontal"   method="post" action="">
								
								<?php if (isset($errors)) { ?>
									
									<div class="row">
										<div class="col-xs-12 col-sm-8 col-md-12 text-center">
										 
									<?php
										
										$i=0;
										
										foreach ($errors as $errors1): 
										
											$len=sizeof($errors1);
										
										for($i=0;$i<$len;$i++) {?>

										<p style="font-weight:bold;color:red"><?php echo $errors1[$i]; ?></p>
										
								<?php	}
									endforeach;
										 
										?>
										</div>
										</div>
									 <?php } ?>
				
				<fieldset class="fsStyle">
						<legend  class="legendStyle">Grading</legend>
					<input type="hidden" name="user_code" id="user_code" value="<?php echo $_SESSION['user_code']; ?>">		
					<input type="hidden" name="button" id="button"  class="form-control" value="view">					
					    <input type="hidden" id="field_arr" name="field_arr"value="" class="hidden" />
                      
					<div class="col-xs-4 col-sm-4 col-md-4">		
						<div class="form-group">
							<label class="control-label col-md-6" for="sel1"> Category </label>
							<div class="col-md-6">
							<select class="form-control " id="category_code"  onchange="get_commodity();" name="category_code"  disabled  required>
								<option value="-1">-----Select----- </option>
								
								<?php
									foreach ($commodity_category as $row1):
								?>
									<option value="<?php echo $row1['Commodity_Category']['category_code']; ?>"><?php echo $row1['Commodity_Category']['category_name']; ?></option> 
								<?php endforeach; ?>

							</select>
							</div>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4">		
						<div class="form-group">
							<label class="control-label col-md-6" for="sel1"> Commodity </label>
							<div class="col-md-6">
							<select class="form-control " id="commodity_code"  name="commodity_code" onchange="commodity_test();" disabled   required>
								<option  value="-1">-----Select-----  </option>

							</select>
							</div>
						</div>
					</div>	
							<div  class="col-xs-4 col-sm-4 col-md-4">
								<div class="form-group ">
									<label class="control-label col-md-6" for="sel1"> Test </label>
									<div class="col-md-6">
									
									<select class="form-control"   id="test_code" name= "test_code"  onchange="" disabled  >
										<option  value="-1">-----Select-----  </option>
										  </select>
									
									
									</div>
									
									
								</div>
							</div> 
							
							<div  class="col-xs-4 col-sm-4 col-md-4">
								<div class="form-group ">
									<label class="control-label col-md-6" for="sel1">Test methods </label>
									<div class="col-md-6">
									
									<select class="form-control"   id="method_code"  name= "method_code"   disabled >
										<!-- removed options from here on 21-12-2019 by Amol, now appended on test selection from ajax call -->
									  </select>
										</div>
											
								</div>
								 </div>
							<div  class="col-xs-4 col-sm-4 col-md-4">
								<div class="form-group ">
									<label class="control-label col-md-6" for="sel1">Standard</label>
									<div class="col-md-6">
									
									<select class="form-control"   id="grd_standrd"  name= "grd_standrd"   disabled >
										<option  value="-1">-----Select-----  </option>
										
										<?php
									foreach ($grades_strd as $row1):
								?>
									<option value="<?php echo $row1['Grade_Standrd']['grd_standrd']; ?>"><?php echo $row1['Grade_Standrd']['grade_strd_desc']; ?></option> 
								<?php endforeach; ?>
									  </select>
										</div>
											
								</div>
							</div>
							<div class="col-xs-4 col-sm-4 col-md-4" id="rangeDiv">
								<div class="form-group">
									<label class="control-label col-md-6" for="sel1">Min/Max</label>
									<div class="col-md-6">
									
									<select class="form-control" id="min_max" name="min_max"  onchange="enable_min_max()">
										<option value="-1">-----Select-----  </option>
										 <option value="Range">Range</option>
										 <option value="Min">Min </option>
										 <option value="Max">Max  </option>
										  
									</select>
									
									
									</div>
									
									
								</div>
							</div> 
							<div  class="col-xs-4 col-sm-4 col-md-4" id="grid_val_min_div">
								<div class="form-group ">
									<label class="control-label col-md-6" for="sel1"> Grade Value </label>
									<div class="col-md-6" id="grade_value1">

									</div>
									
									
								</div>
							</div> 
							<div  class="col-xs-4 col-sm-4 col-md-4" id="grid_val_max_div" style="display:none">
								<div class="form-group ">
									<label class="control-label col-md-6" for="sel1">Max Grade Value </label>
									<div class="col-md-6" id="grid_val_max_subdiv">

									</div>
									
									
								</div>
							</div> 		
					</fieldset>	
						
							<div class="row">
								<div  class="col-xs-4 col-sm-4 col-md-6">
									<fieldset class="fsStyle">
										<legend  class="legendStyle">Grading Order</legend>
										<div class="row">
											<div class="col-md-3">
											<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
												Select a Grading Order
											</button>		
											</div>										
											<div class="col-md-6">
												<ul id="field_list" style="font-weight:bold">
												

												</ul>	
											</div>
											
										</div>
									</fieldset>
								</div> 
							
				
						
				
					
							</div>
							</form>
							
							
	
		<div class="row">
                <div class="col-lg-12 text-center" >
                    <span>
                        <button class="btn btn-primary" id="add">Add</button>
                    </span>
					<span>
                        <button  class="btn btn-primary"   disabled  id="save" onclick="save_click()"   >Save</button>
						
                    </span>
                    <span>
                        <button class="btn btn-primary" id="update"   disabled   >Edit</button>
                    </span>
                    <span>
                        <button class="btn btn-primary" id="delete"  disabled  >Delete </button>
                    </span>
					<span>
                        <button class="btn btn-primary" id="cancel"  disabled  >Cancel</button>
                    </span>
					<span>
                        <button class="btn btn-primary" id="view"    >View</button>
                    </span>
					<span>
                        <button class="btn btn-primary" id="close"   >Close</button>
                    </span>
                </div>
            </div>
			
                           <div class="row">
						<div class="col-xs-12 col-sm-12 col-md-12 col-sm-offset-0 col-md-offset-0">
						
                    <div class="table-responsive" id="avb"   >
                        <table class="table table-striped " id="check_div">
                            <thead>
                                <tr><!--th>Select</th-->
                                      <th>Sr No</th>
									  <th>Category Name</th>
									  <th>Commodity Name</th>
									  <th>Tests</th>
									  <th>Grade </th>
                                      <th>Grade Min Value</th>
                                      <th>Grade Max Value</th>
									  <th>Grade Value</th>
                                </tr>
                            </thead>
                            <tbody>
						


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

                 <?php echo $this->Form->end(); ?>           


                    </div>
                 
			</div>


          								<div id="myModal" class="modal" role="dialog" data-target=".bs-example-modal-lg">
     <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="test_title"></h4>
            </div>
								  <div class="modal-body">
								   <form method="post" id="modal_test" name="modal_test" class="form-horizontal" action="">
									<div class="table-responsive">
										<table class="table table-striped">
											<tr>
												<th>Select</th>
												<th>Grade</th>
												<th>Order</th>
											</tr>
												<?php $i=1;
													foreach ($grades as $row1):
												?>
											<tr>
												<td><input type='checkbox'  id="<?php echo $row1['Grade_Desc']['grade_desc']; ?>" name='checkboxArray[]' value="<?php echo $row1['Grade_Desc']['grade_code']; ?>" /></td>
												<td><?php echo $row1['Grade_Desc']['grade_desc']; ?></td>
												<td>
													<div class="form-group">
														<label class="radio-inline "><input class=" validate[required] radio" type="radio" id="Higher1_<?php echo $row1['Grade_Desc']['grade_code']; ?>" name="Higher<?php echo $row1['Grade_Desc']['grade_code']; ?>" value="1">Higher</label>
														<label class="radio-inline "><input class="validate[required] radio" type="radio"  id="Higher2_<?php echo $row1['Grade_Desc']['grade_code']; ?>" name="Higher<?php echo $row1['Grade_Desc']['grade_code']; ?>" value="2" >Middle</label>
														<label class="radio-inline "><input class="validate[required] radio" type="radio"  id="Higher3_<?php echo $row1['Grade_Desc']['grade_code']; ?>" name="Higher<?php echo $row1['Grade_Desc']['grade_code']; ?>" value="3" >Lower</label>
														
													</div>
												</td>
											</tr>
												<?php $i++;
													endforeach; ?>	
										</table>
									</div>		
									
								  </div>
								  <div class="modal-footer">
									
									  <button type="submit"   class="btn btn-primary" >Save</button>
									   <button type="button" class="btn btn-default" id="close_clear" data-dismiss="modal" style="display:none">Close</button>
								  </div>
								  </form>	
								  
								</div>
							  </div>
						</div>
						<input type="text" id="type" value="" class="hidden" />
	
    <script>
	
	
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        $("#menu-toggle-2").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled-2");
            $('#menu ul').hide();
        });


        function initMenu() {
            $('#menu ul').hide();
            $('#menu ul').children('.current').parent().show();
            //$('#menu ul:first').show();
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
		 function enable_standrd()
		 {
			$("#grade_value1").find('select').remove();
			$("#grade_value1").find('input').remove();
			var test_code = $("#test_code").val();
			  $.ajax({
						type: "POST",
						url: 'get_test_type',
						data: {test_code: test_code},
						success: function (data) {
							
							$.each($.parseJSON(data), function (key, value) {
								
								if(value[0]['test_type_name']=="PN"){
									$("#rangeDiv").hide();
										$("#grid_val_min_div").show();
									$("#grade_value1").append('<select name= "grade_value" class="form-control"><option vlaue="">----Select----</option><option value="Positive">Positive</option><option value="Negative">Negative</option></select>');
									$("#grid_val_max_div").hide(); 
									$( "#grid_val_max_subdiv" ).hide();
									//alert("PN");
								}
								else if(value[0]['test_type_name']=="YN"){
									$("#rangeDiv").hide();
									$("#grid_val_min_div").show();
									$("#grade_value1").append('<select name= "grade_value" class="form-control"><option vlaue="">----Select----</option><option value="Yes">Yes</option><option value="No">No</option></select>');	
									$("#grid_val_max_div").hide(); 	
									$( "#grid_val_max_subdiv" ).hide();			
									//alert("YN");									
								}
								else if(value[0]['test_type_name']=="SV"){
									$("#rangeDiv").show();
									$("#grid_val_min_div").show();
									$("#grade_value1").append('<input type="text" placeholder="Grade Value" class="form-control"  id="grade_value" name= "grade_value" >');		
									$("#grid_val_max_div").hide(); 
									$( "#grid_val_max_subdiv" ).hide();
									//alert("SV");

								}
								else{
									$("#rangeDiv").show();
									$("#grid_val_min_div").hide();
									
									//alert("Fromula");
								}
								
								getData();//added on 19-01-2021 by Amol, and removed from inline element call
							});
						}
			  });	
			
			$("#grd_standrd").attr("disabled", false); 
			$("#method_code").attr("disabled", false); 
			$("#grade_code").attr("disabled", false); 
			
		 }
		 function enable_min_max(){
			 
			var min_max = $("#min_max").val();
			
			if(min_max=="Range"){
				$("#grid_val_min_div").show();
				$("#grid_val_max_div").show();
				$("#grid_val_max_subdiv").show();
				$("#grid_val_max_subdiv" ).empty();
				$("#grade_value1" ).empty();
				$("#grade_value1").append('<input type="text" placeholder="Min Grade Value" class="form-control"  id="grade_value"  name= "grade_value" >');
				$("#grid_val_max_subdiv").append('<input type="text" placeholder="Max Grade Value" class="form-control"  id="max_grade_value"  name= "max_grade_value" >');
				$("#min_max").attr("disabled", false);  
				/*  $("#grade_value1").show(); 
				 $("#grid_val_max_div").show(); 
				 $("#grid_val_max_subdiv").show();  */
			 }
			 else if(min_max=="Min"){
				 $("#grid_val_max_div").hide(); 
				 $("#grid_val_min_div").show(); 
				 $("#grade_value1").show();
				$("#grade_value1" ).empty();
				$("#grade_value1").append('<input type="text" placeholder="Min Grade Value" class="form-control"  id="grade_value"  name= "grade_value" >');				 
				 $("#grid_val_max_div").hide();
				 $( "#grid_val_max_subdiv" ).empty();
			 }
			else if(min_max=="Max"){
				 $("#grid_val_min_div").hide(); 
				 $("#grid_val_max_div").show();
				$("#grade_value1").empty();
				$("#grid_val_max_subdiv").empty();
				$("#grid_val_max_subdiv").show(); 
				$("#grid_val_max_subdiv").append('<input type="text" placeholder="Max Grade Value" class="form-control"  id="max_grade_value"  name= "max_grade_value" >');
				
			}
			// alert(min_max);
		 }
		 function getdetails()
		 {	
				$("#save").attr("disabled", false); 
				$("#delete").attr("disabled", true); 
				$("#update").attr("disabled", true); 
			 var category_code = $("#category_code").val();
			 
				//$("#grade_code").attr("disabled", false); 
				$("#grade_value").attr("disabled", false); 
				
            if (category_code != "")
            {
                var commodity_code = $("#commodity_code").val();
				var grade;
				var tests = $("#test_code").val();
				var grd_standrd = $("#grd_standrd").val();
				 var method_code  = $("#method_code").val();
				  var grade_code  = $("#grade_code").val();
                $.ajax({
						type: "POST",
						url: 'get_details',
						data: {commodity_code: commodity_code,category_code:category_code,tests:tests,grd_standrd:grd_standrd,method_code:method_code,grade_code:grade_code},
						success: function (data) {
							//alert(data);
							
							if(data==1)
							{
								$("#grade_value").val("");
								$("#min_max").val("");
								
								$("#min_max").val("-1");
							}
							else{
							    $.each($.parseJSON(data), function (key, value) {
							
										if(key=='grade_value')
									{	
											$("#delete").attr("disabled", false); 
											$("#update").attr("disabled", false); 
											$("#save").attr("disabled", true);
											$("#grade_value").val(value);
									}
								
									if(key=='min_max')
									{
										$("#min_max").val(value);
									}
								});
							}
							
						   /* $("#delete").attr("disabled", false); 
							$("#update").attr("disabled", false); 
							$("#save").attr("disabled", true);  */
						
					    }
                });
				
			}
		 }
		 
		
		 
		 
		 function commodity_test()
        {
			$("#method_code").find('option').remove();//added on 23-12-2019 by Amol															  
			$("#test_parameter").find('li').remove();
			$("#test_code").attr("disabled", false); 
            $("#test_code").find('option').remove();
            $("#test_code").append("<option value='-1'>------Select----- </option>");
			
            var category_code = $("#category_code").val();

            if (category_code != "")
            {
                var commodity_code = $("#commodity_code").val();

                $.ajax({
                    type: "POST",
                    url: 'get_test_by_commodity_id',
                    data: {commodity_code: commodity_code},
                    success: function (data) {
							//alert(data);
							if(data==0)
							{
								var msg="Tests are not available!!";
								errormsg(msg);
    							return;
							}
							else{
								$.each($.parseJSON(data), function (key, value) {

									$("#test_code").append("<option value='" + value['a']['test_code'] + "'>" + value['a']['test_name'] + "</option>");
			
								});
								
								getData();//added on 19-01-2021 by Amol, and removed from inline element call
							}
					    }
                });
				
			}
           else {
			   var msg="Select commodity category first!";
							errormsg(msg);
               
           }
		


                 
          
					
   		
     }
	
	function view1()
	 {
		
		
		$("#avb").prop("hidden", false);
			var i=1;
			
			  $("#check_div tbody").empty();
			
			var button=$("#button").val();
	 $('#check_div').DataTable().clear().destroy();
		   $.ajax({
                    type: "POST",
                    url: 'view_data',
                    data: {button:button},
                    success: function (data) {
						//alert(data);
								
								
								 $.each($.parseJSON(data), function (key, value)
								 {
									
										$.each( value,function (key1, value1)
										{
											var rowcontent="<tr><td>"+i+"</td>";
											$.each( value1,function (key2, value2)
											{	//alert(value2);
												
											
												rowcontent=rowcontent+"<td>"+value2+"</td>";
												
												
											});

											$("#check_div tbody").append(rowcontent);
										});
									    
										i++;
										
								});
								$('#check_div').DataTable();
                        
					    }
                });
	 }
		
  $(document).ready(function () {	
  
  $("#grid_val_min_subdiv" ).hide();
  $("#grid_val_max_subdiv" ).hide();
  $("#grid_val_min_div").hide();
  $("#rangeDiv").hide();
  $("#avb").prop("hidden", true);
  
       $("#add").click(function (e) 
	   { e.preventDefault();
			//alert("fdef");
			$("#category_code").attr("disabled", false); 
			$("#save").attr("disabled", false); 
			$("#cancel").attr("disabled", false); 
			$("#add").attr("disabled", true); 
			$("#view").attr("disabled", false); 
			
		  });   
	
	
	
	$("#view").click(function(e){
		e.preventDefault(); 
		view1();
		
	  
     });
	 
	 $("#cancel").click(function() {
		 $('form#frm_commodity_grade')[0].reset();
	 });
	 
	$("#close").click(function(e) {
		
		 location.href="<?php echo $home_url; ?>";
		 e.preventDefault();
	 });
	 
      $("#delete").click(function (e) {
		   e.preventDefault();
		  $("#button").val('delete');
		 var r= confirm("Are sure for delete the record!!!");
		
		 if(r==true)
		{
		  $("#frm_commodity_grade").submit();
		  location.reload();
		}
		 
	 });
	  
	

	  $("#update").click(function (e) {
		   //alert('dad');
		 
			$("#button").val('update');
			$("#frm_commodity_grade").submit();
			location.reload();
		  
		  
			

	 });
	 
		  
	  $("#save").click(function (e) {
		  
		 var category_code = $("#category_code").val();
		  var commodity_code = $("#commodity_code").val();
		   var test_code = $("#test_code").val();
		    var grade_code = $("#grade_code").val();
			 var grd_standrd = $("#grd_standrd").val();
			 var grade_value = $("#grade_value").val();
			  var min_max = $("#min_max").val();
			  
			/*   if(category_code=='-1'){
				   var msg="Please select  Category!!";
							errormsg(msg);
				 
				  $("#category_code").focus();
			  }
			 else if(commodity_code=='-1'){
				  var msg="Please select  Commodity!!";
							errormsg(msg);
				 
				  $("#commodity_code").focus();
			  }
			 else if(test_code=='-1'){
				  var msg="Please select  Test!!";
							errormsg(msg);
				 
				  $("#test_code").focus();
			  }
			  else  if(grd_standrd=='-1'){
				   var msg="Please select  Grade standard!!";
							errormsg(msg);
				 
				  $("#grd_standrd").focus();
			  } */
			/* else  if(grade_code=='-1'){
				var msg="Please select  Grade!!";
							errormsg(msg);
				
				  $("#grade_code").focus();
			  } */
			 /* else if(grade_value==''){
				 var msg="Please enter grade value!!";
							errormsg(msg);
				 
				  $("#grade_value").focus();
			  } */
			 /* else if(min_max=='-1'){
				  alert("Please select min max value!!");
				  $("#min_max").focus();
			  } */
			//  else{
			//alert("dfdsfs");
		  
			  //}
		 
	 });
	 
	 
	 
	  $("#modal_test").submit(function(e)
    {
		e.preventDefault();
		$("#field_list").find('li').remove();
        var str="";
        var str1="";
        var key;
        var flag=false;
        var id;
        var final_str="";
        var total=$(this).find('input[name="checkboxArray[]"]:checked').length;
		$("#save").prop("disabled", false);
		
		$.each($("input[name='checkboxArray[]']:checked"), function() {
				var number="";
				id=$(this).attr("id");
				//alert("id"+);
				
				var abc=$(this).val();
								
				var selectedradio = "";
				var selected = $("input[type='radio'][name='Higher"+abc+"']:checked");
				selectedradio = selected.val();
				selectedradioname = selected.parent('label').text();  // get grade order lable name, , Done by Pravin Bhakare, 11-10-2019			  
				var val=$("#label_"+abc).val();
				
				str+="<li>"+$(this).attr("id")+" - "+selectedradioname+"</li>";  // correct display selected grade order name, Done by Pravin Bhakare, 11-10-2019
				//str+="<li>"+selectedradio+"</li>";
				//str1+=;
				final_str=abc+"~"+selectedradio;
				str1+=final_str+"-";
				//alert(val);
			});
			//alert(str);
			$("#close_clear").click();
			$("#field_arr").val(str1);
			$("#field_list").append(str);
		
    });
	
	 
	 
	 
     });
 
 
function valid_qnt() {
	/*  var Qty = $("#grade_value").val();
	// alert(Qty);
	 if (!/^[1-9]\d?$/.test(Qty)){
    alert('Please Enter Valid grade value!!!');
	$("#grade_value").val('');
	$("#grade_value").focus();
    return false; 
}*/

var grade_value  = $("#grade_value").val();
				 if(isNaN(grade_value)){
				$("#min_max").attr("disabled", true); 
				 }
				 else{
					 $("#min_max").attr("disabled", true); 
				 }
 }
       
        function get_commodity()        {
			$("#method_code").find('option').remove();//added on 23-12-2019 by Amol															  
			$("#commodity_code").attr("disabled", false); 
            $("#commodity_code").find('option').remove();
            $("#commodity_code").append("<option value='-1'>-----Select---- </option>");
            $("#test_code").find('option').remove();
            $("#test_code").append("<option value='-1'>----Select----- </option>");
            var category_code = $("#category_code").val();
            $.ajax({
                type: "POST",
                url: 'get_commodity',
                data: {category_code: category_code},
                success: function (data) {
			//	alert(data);
                    if(data==0)
					{
						 var msg="Commodities are not available!!!";
							errormsg(msg);
						return;
					}
					else{
						$("#commodity_code").append(data);
						
						getData();//added on 19-01-2021 by Amol, and removed from inline element call
                    }
                }
            });



        }
		
		function getData(){
			var category_code	= $("#category_code").val();
			var commodity_code	= $("#commodity_code").val();
			var test_code		= $("#test_code").val();
			if(category_code=='-1')
				category_code=0;
			if(commodity_code=='-1')
				commodity_code=0;
			if(test_code=='-1')
				test_code=0;
			$("#avb").prop("hidden", false);
			var i=1;
			
			  $("#check_div tbody").empty();
			  $('#check_div').DataTable().clear().destroy();
			 $.ajax({
                    type: "POST",
                    url: 'view_data_sorting',
                    data: {category_code:category_code,commodity_code:commodity_code,test_code:test_code},
                    success: function (data) {
						$.each($.parseJSON(data), function (key, value){
							$.each( value,function (key1, value1){
								var rowcontent="<tr><td>"+i+"</td>";
								rowcontent=rowcontent+"<td>"+value1['category_name']+"</td>";
								rowcontent=rowcontent+"<td>"+value1['commodity_name']+"</td>";
								rowcontent=rowcontent+"<td>"+value1['test_name']+"</td>";
								rowcontent=rowcontent+"<td>"+ value1['grade_desc']+"</td>";
								if(value1['grade_value']){
								rowcontent=rowcontent+"<td>"+  value1['grade_value']    +"</td>";
								}
								else{
									rowcontent=rowcontent+"<td>-</td>";
								}
								if(value1['max_grade_value']){
								rowcontent=rowcontent+"<td>"+  value1['max_grade_value']    +"</td>";
								}
								else{
									rowcontent=rowcontent+"<td>-</td>";
								}
								//rowcontent=rowcontent+"<td>"+  value1['max_grade_value'] +"</td>";
								if(value1['singleval']){
								rowcontent=rowcontent+"<td>"+  value1['singleval']    +"</td>";
								}
								else{
									rowcontent=rowcontent+"<td>-</td>";
								}
								//rowcontent=rowcontent+"<td>"+  value1['singleval'] +"</td></tr>";
								/* $.each( value1,function (key2, value2){	//alert(value2);
									if(value2==""){
										value2="-";
										//alert(value2);
										rowcontent=rowcontent+"<td>"+value2+"</td>";
									}
									else{
									rowcontent=rowcontent+"<td>"+value2+"</td>";
									}
								}); */
								$("#check_div tbody").append(rowcontent);
							});
							i++;
						});
						$('#check_div').DataTable();

					}
                });
		}
		
       function save_click() 
	{
        //var type = $("#type").val();
		
		//$("#update1").submit();
		 $("#button").val('add');
		 $("#type").val('add');
		 $("#frm_commodity_grade").submit();
		// location.reload();
              
    }
	

   

  	
	

    </script>
</body>
</html>
