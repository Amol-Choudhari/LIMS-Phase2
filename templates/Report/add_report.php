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
               <div class="col-md-12">
                  <div class="card card-lims">
                     <div class="card-header"><h3 class="card-title-new">Report</h3></div>
                        <div class="form-horizontal">
                           <div class="card-body">
                              <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group">
                                       <label class="control-label col-md-4">Label</label>
                                          <select class="form-control " id="label_code" name="label_code" onchange="view1();" required>
                                             <option value='-1'>-----Select----- </option>
                                                <?php
                                                $i = 1;
                                                foreach ($Label as $row):?>
                                                <option value="<?php echo $row['label_code']; ?>"><?php echo $row['label_desc']; ?></option> 
                                                <?php $i++;
                                                endforeach; ?>
                                             </select>
                                           </div>
                                        </div>
                                        <div class="col-md-4">
                                           <div class="form-group"  id="commodity_add_input" >
							                        <label class="control-label col-md-4" for="report_desc">Report Name</label>
							                           <div class="col-md-6">
                                                   <input type="hidden" class="form-control" id="rep_code"  name="rep_code" >
                                                   <input type="hidden" name="login_timestamp" id="login_timestamp"  class="form-control" value="<?php echo $timezone;?>"> 
                                                   <input type="text" class="form-control validate[maxSize[50],minSize[4]]" placeholder="Report Name" id="report_desc" onkeypress="return blockSpecialChar(event)" name="report_desc"  >
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="col-md-4">
                                                <div class="form-group">
                                                   <label class="control-label col-md-4">Report Name<?php echo $local_lang;?></label>
                                                      <div class="col-md-6">
                                                         <input type="text" class="form-control  validate[maxSize[50]]" placeholder="Report Name<?php echo $local_lang;?>" id="l_report_desc"  onkeypress="return blockSpecialChar(event)"  name="l_report_desc"  >
                                                      </div>
                                                </div>
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


    </script>