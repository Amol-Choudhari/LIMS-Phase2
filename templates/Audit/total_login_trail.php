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
	</style>
		<script>
			$(document).ready(function(){
				$('#tablepaging').DataTable();
			});	
		</script>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><label class="badge badge-success">Audit Trail Reports</label></div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
						<li class="breadcrumb-item active">Online Users</li>
					</ol>
				</div>
			</div>
		</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="masters_list card card-lims">
							<div class="card-header"><h3 class="card-title-new">Online Users</h3></div>
								<div class="form-horizontal">
									<div class="card-body">
										<div class="row">
											<table class="table table-striped table-hover m-0 table-bordered" id="tablepaging">
												<thead class="tablehead">
													<tr>
														<th>Sr.No.</th>
														<th>Name</th>
														<th>Email Id</th>
														<th>Office</th>
														<th>Role</th>
														<th>Login Date</th>
														<th>Login Time </th>
													</tr>
												</thead>
												<tbody>
													<?php 	$i=1;
													foreach($records as $res1):	?>
														<tr>
															<td><?php echo $i; ?></td>
															<td><?php echo $res1['f_name']." ".$res1['l_name']; ?></td>
															<td><?php echo $res1['email_id']; ?></td>
															<td><?php echo $res1['ro_office']; ?></td>
															<td><?php echo $res1['role']; ?></td>
															<td><?php echo $res1['date']; //Remove/change date format on 22-05-2019 by Amol  ?></td>
															<td><?php echo $res1['time_in'];  ?></td>									
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
	
<script>
	$(document).ready(function(){
		
		$('#tablepaging').DataTable();

		$("#save").click(function(){
			
			//var sample_code=$("#edit_flag").val();
			//$("#edit_flag1").val(sample_code);
			$('form#rpt_comm_sample').submit();
			
		});

	});	
		 function getlab()
		{
			var user_flag = $("#lab").val();
			$("#ral_lab").prop("disabled", false);
			//var dist_code='user_flag=' + user_flag;
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
