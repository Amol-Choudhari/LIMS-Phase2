<?php 
	echo $this->Html->Script('bootstrap-datepicker.min');
		echo $this->Html->script('jquery.dataTables.min');
	echo $this->Html->css('jquery.dataTables.min');
//	print  $this->Session->flash("flash", array("element" => "flash-message_new")); ?>
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

td.details-control {
    background: url('<?php echo $this->request->getAttribute('webroot');?>/img/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('<?php echo $this->request->getAttribute('webroot');?>/img/details_close.png') no-repeat center center;
}
</style>
	<script>
	function parseDateTime(dt) {
        var date = false;
        if (dt) {
            var c_date = new Date(dt);
            var hrs = c_date.getHours();
            var min = c_date.getMinutes();
            if (isNaN(hrs) || isNaN(min) || c_date === "Invalid Date") {
                return null;
            }
            var type = (hrs <= 12) ? " AM" : " PM";
            date = ((+hrs % 12) || hrs) + ":" + min + type;
        }
        return date;
    }
	function format ( d ) {
		var sData	= d.subdata;
		
		 var str = '<table id="subtable'+d.id+'" style="padding-left:50px;">';
		 str+=	'<thead><th>Sr.No</th><th>Form Name</th><th>Time</th></thead>';
		 str+=	'<tbody>';
		 
		for(var i=0;i<sData.length; i++){
			str+=	'<tr>';
			str+=	'<td>'+(i+1)+'</td>'+
					'<td>'+sData[i]['form_name']+'</td>';
			str+=	'<td>'+parseDateTime(sData[i]['trans_in_time'])+'</td>';
			//str+=	'<td>'+convertTo24Hour(sData[i][0]['trans_in_time'])+'</td>';
			str+=	'</tr>';
		}
		str+=	'</tbody>';
		str+='</table>';
		
		return str;
	} 

		$(document).ready(function(){
		  var table = $('#example').DataTable( {
			"columns": [
				{
					"className":      'details-control',
					"orderable":      false,
					"data":           null,
					"defaultContent": ''
				},
				{ "data": "f_name" },
				{ "data": "email_id" },
				{ "data": "ro_office" },
				{ "data": "role" },
				{ "data": "date" },
				{ "data": "time_in" },
				{ "data": "time_out" }	
			],
			//"order": [[1, 'asc']],
			"data": <?php echo json_encode($audit_trail)?>
		} );  
		$('#example tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	 
			if ( row.child.isShown() ) {
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				row.child( format(row.data()) ).show();
				$('#subtable'+row.data().id).DataTable({});
				tr.addClass('shown');
			}
		} );
	//	alert(<?php echo json_encode($audit_trail)?>);
			});	
	</script>
			<div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 text-dark">Audit Trail Reports</h1></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
                    <li class="breadcrumb-item active">Visited Users History</li>
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
                <div class="card-header"><h3 class="card-title-new">Visited Users History</h3></div>
                  <div class="form-horizontal">
                    <div class="card-body">
                      <div class="row">
                        <table class="table table-striped table-hover m-0 table-bordered" id="example">
                          <thead class="tablehead">
														<tr>
															<th></th>
															<th>Name</th>
															<th>Email Id</th>
															<th>Office</th>
															<th>Role</th>
															<th>Login Date</th>
															<th>Login Time </th>
															<th>Logout Time </th>
														</tr>
													</thead>
													<tbody>
													<?php 	$i=1;
												foreach($finalArr as $res1):	?>
													<tr>
														<td><?php echo $i; ?></td>
														<td><?php echo $res1['f_name']." ".$res1['l_name']; ?></td>
														<td><?php echo $res1['email_id']; ?></td>
														<td><?php echo $res1['ro_office']; ?></td>
														<td><?php echo $res1['role']; ?></td>
														<td><?php echo $res1['date']; //Remove/change date format on 22-05-2019 by Amol ?></td>
														<td><?php echo $res1['time_in'];  ?></td>									
														<td><?php echo $res1['time_out'];  ?></td>									
													</tr>
													<?php $i++;
												endforeach;  ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
			
		
			
<script>
$(document).ready(function(){
	$('#example').DataTable();


});	
	
</script>
