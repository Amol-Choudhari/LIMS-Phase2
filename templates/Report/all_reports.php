<?php
echo $this->Html->script('jquery.dataTables.min');
echo $this->Html->css('jquery.dataTables.min');
?>

<script type="text/javascript">
$(function(){
	$("li a").addClass('disabled');  // to disable menubar
})
    $(document).ready(function () {
		function formSuccess() {
            alert('Success!');
        }

        function formFailure() {
            alert('Failure!');
        }

        $("#add12").validationEngine({
            onFormSuccess: formSuccess,
            onFormFailure: formFailure
        });
		
	});


</script>
 <?php ?>
<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6"><h1 class="m-0 text-dark">Reports Master</h1></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Reports</li>
					</ol>
				</div>
			</div>
		</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 mb-2">

					<?php echo $this->Html->link('Add New', array('controller' => 'report', 'action'=>'add_report'),array('style'=>'float:left;','class'=>'add_btn btn btn-primary')); ?>

						
						<?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('style'=>'float:right;','class'=>'add_btn btn btn-secondary')); ?>

						</div>
						<div class="col-md-12">
							<div class="card card-lims">
								<?php echo $this->Form->create(null, array('id'=>'add12', 'name'=>'sampleForm','class'=>'form-group')); ?>
									<div class="card-header"><h3 class="card-title-new">List of All Reports</h3></div>
										<div class="form-horizontal">
											<div class="card-body">
													<div class="panel panel-primary filterable">
														<table id="pages_list_table" class="table table-striped table-hover table-bordered">
														<thead class="tablehead">
															<tr>
															<th>SR.No</th>
															<th>Reports</th>
															<th>Reports (hindi)</th>
															<th>Action</th>
														</tr>
													</thead>	
													<tbody>
													<?php
														
														if (!empty($Label)) {
														
															$sr_no = 1;		
														
															foreach ($Label as $each) { ?>
														
																<tr>
																	<td><?php echo $sr_no; ?></td>
																	<td><?php echo $each['report_desc']; ?></td>
																	<td><?php echo $each['l_report_desc']; ?></td>
																	<td> 
																		<?php echo $this->Html->link('A', array('class'=>'glyphicon glyphicon-edit')); ?> |
																		<?php echo $this->Html->link('B', array('class'=>'glyphicon glyphicon-trash')); ?>
																	</td>
																</tr>

                            						<?php $sr_no++; } } ?>
														</tbody>
													</table>
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


<script>
$(document).ready( function () {
    $('#pages_list_table').DataTable();
} );
</script>
    <script type="text/javascript">
    function blockSpecialChar(e){
        var k;
		
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 );
        }
    </script>

    <script>
        
        $(document).ready(function () {
            document.cookie = "type=add";
            initMenu();
			view1();
		//	$("#avb").hide();
			$("#add").prop("disabled", true);
			$("#report_desc").prop("disabled", true);
			$("#l_report_desc").prop("disabled", true);
         
			$("#add").click(function () {
            document.cookie = "type=add";
			$("#type").val("add");
            $("#add_div").show();
           
			$("#label_code").prop("disabled", false);
			$("#label_code").focus();
			$("#report_desc").prop("disabled", false);
			$("#l_report_desc").prop("disabled", false);
			$("#save").prop("disabled", false);
			$("#add").prop("disabled", true);
			$("#cancel").prop("disabled", false);
			//$("#delete").prop("disabled", false);
			//$('#delete_commodity_select').val('na');
			//$('#commodity_name_select_delete').val('na');
			//$("#add").prop("disabled", true);
        });
         $("#update").click(function () {
           
			
            document.cookie = "type=update";
			$("#type").val("update");
			
			$("#report_desc").prop("disabled", false);
			$("#l_report_desc").prop("disabled", false);
		    $("#delete").prop("disabled", true);
			$("#save").prop("disabled", false);
			$("#update").prop("disabled", true);
			$("#report_desc").focus();
            
        });
       $("#delete").click(function () {
			document.cookie = "type=delete";
			//var type=$("#type").val();
			  var label_code = $("#label_code").val();
			  var report_desc  = $("#report_desc").val();
			  if(label_code=='-1')
			{
				var msg="Please select category name!!!";
				errormsg(msg);
				$("#report_desc").val("");
				return;
			}
			   var report_code = $("#rep_code").val();
		 
         BootstrapDialog.confirm("Do u really want to delete "+report_desc+"?", function(result){
			  if(result) {
            
                $.ajax({
                    type: "POST",
                    url: 'add_report',
                    data: {label_code: label_code,report_code:report_code},
                    success: function (data) {
						
                        if (data == 1)
                        {
							var msg="Record deleted successfully!";
							errormsg(msg);
                           	$("#report_desc").val("");
							$("#l_report_desc").val("");
                           view1();
						   return;
                        }
						else{
							var msg="Record not deleted successfully!";
							errormsg(msg);
							
							$("#report_desc").val("");
							$("#l_report_desc").val("");
                           view1();
						   return;
						}
                    }
                });
            }
			else{
				$("#report_desc").val("");
				$("#l_report_desc").val("");
				$("#report_desc").prop("disabled", true);
				$("#l_report_desc").prop("disabled", true);
			}
		});
		    //$("#category_code").prop("disabled", true);
			$("#label_code").val("-1");
			$("#add").prop("disabled", false);
			$("#delete").prop("disabled", true);
			$("#update").prop("disabled", true);
        });
		$("#view").one(function(e) {
			
			 
				e.preventDefault(); 
	
	
				view1();
		
	  
    
		});
		$("#cancel").click(function() {
			$('form#add12')[0].reset();
				$("#check_div tbody").empty();
			$("#report_desc").prop("disabled", true);
			$("#save").prop("disabled", true);
			$("#update").prop("disabled", true);
			$("#delete").prop("disabled", true);
			$("#add").prop("disabled", true);
			$("#l_label_desc").prop("disabled", true);
		//	$('form#update1')[0].reset();
			//$('#category_code').val("-1");
			//$('#view').trigger("click");
			view1();
			//$("#cancel").prop("disabled", true);
			
		});
		$("#close").click(function() {
		 location.href="<?php echo $home_url; ?>";
		});
			
			
		
    
			
			
        });
		
$("#save").click(function () 
{
	var type=$("#type").val();
		
		
		
			
		
		if(type=="update")
		{
			var report_desc=$("#report_desc").val();
			var l_report_desc=$("#l_report_desc").val();
			var label_code=$("#label_code").val();
			if(l_report_desc!=''){
				
			}
			else{
				var msg="Please enter the report name(हिंदी)";
				errormsg(msg);
							
				return;
			}
			if(label_code=='-1')
			{
				var msg="Please select category name!!!";
				errormsg(msg);
				$("#report_desc").val("");
				return;
			}
			var report_code=$("#rep_code").val();
			 $.ajax({
                    type: "POST",
                    url: 'add_report',
                    data: {l_report_desc:l_report_desc,report_desc:report_desc,label_code:label_code,report_code:report_code},
                    success: function (data) {
						
						if(data==1)
						{
							var msg=report_desc+" has been Updated!!";
							errormsg(msg);
							$("#l_report_desc").val("");
							view1();
							return;
						}
						else{
							$.each($.parseJSON(data), function (key, value){
									errormsg(value);
									
								});	
							$("#l_report_desc").val("");
							view1();
							return;
						}
						
						}
							
							
		    });	
							
							$("#update").attr("disabled", true); 
							$("#save").attr("disabled", true); 
							$("#add").attr("disabled", false); 
							$("#report_desc").val('');
							$("#report_desc").prop("disabled", true);
							$("#l_report_desc").prop("disabled", true);
							//$("#category_code").prop("disabled", true);
							$("#label_code").val('-1');
		}
		else if(type=="add")
		{
			var report_desc=$("#report_desc").val();
			
			var l_report_desc=$("#l_report_desc").val();
			
			var label_code=$("#label_code").val();
		
			if(label_code=='-1'){
				var msg="Please select Label!!";
				errormsg(msg);
				$("#report_desc").val('');
				return;
			}
			if(l_report_desc!=''){
				
			}
			else{
				var msg="Please enter the report name(हिंदी)";
				errormsg(msg);
				return;
			}
			
			if(report_desc!=''){
				
		 $.ajax({
                    type: "POST",
                    url: 'add_report',
                    data: {type:type,l_report_desc:l_report_desc,report_desc:report_desc,label_code:label_code},
                    success: function (data) {
						
						if(data==1)
						{
							var msg=report_desc+" has been Saved!!";
							errormsg(msg);
							$("#l_report_desc").val("");
							view1();
							
						}
						else if(data==3){
							var msg=report_desc+" Record for this label already exists! Please Contact to Administrator";
							errormsg(msg);
							
							view1();
						}
						else
						{
							$.each($.parseJSON(data), function (key, value){
									errormsg(value);
									
								});	
							view1();
							//return;
							
						}
					} 
					    
                });
			
							
							$("#save").attr("disabled", true); 
							$("#add").attr("disabled", false); 
							$("#cancel").attr("disabled", true); 
							$("#report_desc").val('');
							$("#l_report_desc").val("");
							$("#report_desc").prop("disabled", true);
							$("#l_report_desc").prop("disabled", true);
							//$("#category_code").prop("disabled", true);
							$("#report_code").val('-1');
					return;	
			}
		else{
			var msg="Please enter the report name";
			errormsg(msg);
			return;	
		}			
			}
		

		
  });	
    </script>
    <script>
	
	function view1()
	 {
		$("#report_desc").val('');
		$("#l_report_desc").val('');
		//$("#avb").show();
		$("#check_div tbody").empty();
		$("#add").prop("disabled", false);
		var  label_code =$("#label_code").val();
		  $('#check_div').DataTable().clear().destroy();

			var i=1;
				$.ajax({
                    type: "POST",
                    url: 'view_data',
                    data: {label_code:label_code},
                    success: function (data) {
					
					$.each($.parseJSON(data), function (key, value)
								 {
									
									$("#check_div tbody").append('<tr    id='+i+' onclick="get_parameter(\''+value['Report']['label_code']+'\', \''+value['Report']['report_code']+'\',\''+value['Report']['report_desc']+'\',\''+value['Report']['l_report_desc']+'\' )"><td>'+i+'</td><td>'+value['Report']['report_desc']+'</td><td>'+value['Report']['l_report_desc']+'</td></tr>');
									$("#check_div tbody tr").css("cursor","pointer");
									
									i++;
							
								});
						$('#check_div').DataTable();
                        $("#view").attr("disabled", false); 	
					    }
					
                });
		$("#report_desc").prop("disabled", true);
		$("#l_report_desc").prop("disabled", true);		
	 }
	 function get_parameter(lab_code,rep_code,rep_name,l_rep_name)
		 {
			 
			$("#report_desc").val(rep_name);
			$("#label_code").val(lab_code);
			$("#l_report_desc").val(l_rep_name);
			
			$("#rep_code").val(rep_code);
			
			$("#report_desc").prop("disabled", true);
			$("#l_report_desc").prop("disabled", true);
			$("#delete").prop("disabled", false);
			$("#add").prop("disabled", true);
			$("#update").prop("disabled", false);
			$("#cancel").prop("disabled", false);
			 $("#save").prop("disabled", true);
			 $("#report_desc").focus();
		 } 
        
        
    
        
        
    </script>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script>

          // Load the Google Transliteration API
          google.load("elements", "1", {
                packages: "transliteration"
              });
          function onLoad() {
            var options = {
              sourceLanguage: 'en',
              destinationLanguage: ['hi'],
			  //destinationLanguage: ['mr'],
              shortcutKey: 'ctrl+g',
              transliterationEnabled: true
            };
            // Create an instance on TransliterationControl with the required
            // options.
            var control = new google.elements.transliteration.TransliterationControl(options);
            // Enable transliteration in the textfields with the given ids.
            var ids = ["l_report_desc"];
            control.makeTransliteratable(ids);
			var keyVal = 32; // Space key
			$("#report_desc").on('keydown', function(event) {
				if(event.keyCode === 32) {
					var engText = $("#report_desc").val() + " ";
					var engTextArray = engText.split(" ");
					$("#l_report_desc").val($("#l_report_desc").val() + engTextArray[engTextArray.length-2]);

					document.getElementById("l_report_desc").focus();
					$("#l_report_desc").trigger ( {
						type: 'keypress', keyCode: keyVal, which: keyVal, charCode: keyVal
					} );
				}
			});

			$("#l_report_desc").bind ("keyup",  function (event) {
				setTimeout(function(){ $("#report_desc").val($("#report_desc").val() + " "); document.getElementById("report_desc").focus()},0);
			});
          }
          google.setOnLoadCallback(onLoad);
		  </script>

