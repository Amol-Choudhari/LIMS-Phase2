<?php 
	echo $this->Html->Script('bootstrap-datepicker.min');
			echo $this->Html->script('jquery.dataTables.min');
	echo $this->Html->css('jquery.dataTables.min');
	//print  $this->Session->flash("flash", array("element" => "flash-message_new")); ?>
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
.table-responsive{
	height:auto;
	overflow-y: none;
	
}
@media screen and (min-width: 768px) {
        .modal-dialog {
			width: 900;
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
       
		#myModal1 .modal-lg {
          width: 1300px; /* New width for large modal */
        }
    }
</style>
	<script>
		 $(document).ready(function(){
		 $('#myModal').modal('show');
		 function formSuccess() {
            alert('Success!');
        }
		function formFailure() {
            alert('Failure!');
        }
		
		$("#modal_test").validationEngine({
			promptPosition: 'inline',
			onFormSuccess: formSuccess,
            onFormFailure: formFailure
			
        }); 
		
		 $("#save_a").click(function(e)
		 {
			 $("#modal_test").submit(); 
		 });
		$("#rec_from_dt").attr("disabled", false); 
		$("#rec_to_dt").attr("disabled", true); 
		$('#rec_from_dt').datepicker({
			changeMonth: true,
			changeYear: true,
			autoclose: true,				
			todayHighlight: true,	
			firstDay: 1,
			endDate: '+0d',
			format: 'dd/mm/yyyy',
		});	
			
		$('#rec_to_dt').datepicker({
			changeMonth: true,
			changeYear: true,
			autoclose: true,
			endDate: '+0d',
			todayHighlight: true,	
			firstDay: 1,			
			format: 'dd/mm/yyyy',
		});	
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!

		var yyyy = today.getFullYear();
		var today = dd+'/'+mm+'/'+yyyy; 
		$('#rec_from_dt').val(today); 
		$('#rec_to_dt').val(today); 	
		
		$("#rec_from_dt").change(function() {
			$("#rec_to_dt").attr("disabled", false); 
		});
			$("#rec_to_dt").change(function() {
				var rec_from_dt1 = $("#rec_from_dt").val();
				var rec_to_dt2=$("#rec_to_dt").val();
				var rec_from_dt = $('#rec_from_dt').datepicker('getDate');
				var rec_to_dt   = $('#rec_to_dt').datepicker('getDate');

				if (rec_from_dt<=rec_to_dt) {
					$("#lab").attr("disabled", false); 
					$("#commodity_category").attr("disabled", false); 
				}
				else{
					var msg="You cant come back before from date!";
					errormsg(msg);
					$('#rec_from_dt').val("");
					$('#rec_to_dt').val("");
					$("#rec_from_dt").focus();
				}  
			});	 

	$('#tablepaging').DataTable({});
 });	
	</script>
  	<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0 text-dark">Audit Trail Reports</h1></div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
                <li class="breadcrumb-item active">User Hitcount</li>
              </ol>
            </div>
        </div>
      </div>
      <?php echo $this->Html->link('Back', array('controller' => 'dashboard', 'action'=>'home'),array('style'=>'float:right;','class'=>'add_btn btn btn-secondary')); ?>
			  <div class="row">
				  <section class="content form-middle">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12">
                  <div class="masters_list card card-lims">
                    <div class="card-header"><h3 class="card-title-new">User Hitcount</h3></div>
                      <div class="form-horizontal">
                        <div class="card-body">
                          <div class="row pl-5">
                            <form method="post" id="modal_test" action="" name="modal_test"class="form-horizontal" >
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col-sm-3">	
                                    <div class="form-group">				
                                      <label class="col-md-6" for="sel1">From </label>
                                        <div class="col-md-10 date">
                                          <div class="input-group input-append date" id="datePicker">
                                            <input type="text" class="form-control validate[required]" name="rec_from_dt" placeholder="dd/mm/yyyy"  id="rec_from_dt"   required  />
                                           <span class="input-group-addon add-on ml-1"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                <div class="col-sm-3">
                                  <div class="form-group">
                                    <label class="col-md-6" for="sel1"> To </label>
                                      <div class="col-md-10">
                                        <div class="input-group input-append date" id="datePicker1">
                                          <input type="text"  class="form-control validate[required]"  name="rec_to_dt" id="rec_to_dt"   placeholder="dd/mm/yyyy"  onchange="enable_cate()" disabled required >	
                                          <span class="input-group-addon add-on ml-1"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                      </div>
                                  </div>
                                </div>
                              <div class="col-sm-3">		
                                <div class="form-group">
                                  <label class="control-label col-md-6" for="sel1"> Offices </label>
                                    <div class="col-md-10">
                                      <select class="form-control validate[required]" id="lab" name="lab" onchange="getlab()"  >
                                        <option hidden="hidden" value=''>-----Select-----</option>
                                        <option  value='0'>All</option>
                                        <?php foreach ($user_flag as $user_flag1):	?>
                                        <option value="<?php echo $user_flag1['user_flag']; ?>"><?php echo $user_flag1['user_flag']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                  </div>	
                                </div>
                              </div>	
							              <div class="col-sm-3">		
							                <div class="form-group">
								                <label class="control-label col-md-8" for="sel1"> RAL/CAL Lab </label>
								                  <div class="col-md-12">
									                  <select class="form-control validate[required]" id="ral_lab" name="ral_lab" onchange="enableDesignation()"  >
										                  <option hidden="hidden" value=''>-----Select-----</option>
                                    </select>
                                  </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <div class="col-md-12 p-2"><button id="save_a" type="submit" class="btn btn-primary">Generate</button></div>
            </div>
          </div>
        </div>
      </div>
			<?php  if(isset($reportData)){ ?>
	<div class="card-header"><h3 class="card-title-new">User Hitcount</h3></div>
    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-12 ">
        <h5 class="text-center">भारत सरकार/Goverment of India</h5>
          <h5 class="text-center">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
          <h5 class="text-center">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
          <h5 class="text-center">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
        </div>
      </div>
  </div>
  <div class="row">
        <div class="text-center">
        <h5>User visits from :<b><?php echo $from_date;  ?></b> To :<b><?php echo $to_date; ?></b></h5>
        </div>
        
			</div>	
				<br>			
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-12 col-sm-offset-2 col-md-offset-0 ">
					<div class="table-responsive" id="avb">
						<table class="table table-striped" id="tablepaging">
							<thead>
								<tr>
									<th>Sr.No.</th>
									<th>Name</th>
									<th>Email Id</th>
									<th>Role</th>
									<th>Office</th>
									<th>Total Logins </th>
								</tr>
							</thead>
							<tbody>
								<?php 	$i=1;
									foreach($reportData as $res1): ?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo $res1['username']; ?></td>
									<td><?php echo $res1['email']; ?></td>
									<td><?php echo $res1['role']; ?></td>
									<td><?php echo $res1['office']; ?></td>
									<td><?php echo $res1['loginCount']; ?></td>									
								</tr>
								<?php $i++; endforeach;  ?>
							</tbody>
						</table>
				</div>	  
		   </div>
		</div>
		
	</div>
	</fieldset>	 
				<?php } ?>
	
          </section>
        </div>
    </div>
    
<script>
$(document).ready(function(){
	$("#save").click(function(){
		$('form#rpt_comm_sample').submit();
	});
});	
function getlab(){
	var user_flag = $("#lab").val();
	$("#ral_lab").prop("disabled", false);
	$.ajax({
		type: "POST",
		url:"<?php use Cake\Routing\Router; echo Router::url(array('controller'=>'Audit','action'=>'get_lab'));?>",
		data: {user_flag: user_flag},
		success: function (data) {
			$('#ral_lab option').remove();
			$('#ral_lab').append(data);
		}
	});
}
</script>
