<?php 
	echo  $this->Html->Script('bootstrap-datepicker.min');
	//print  $this->Session->flash("flash", array("element" => "flash-message_new")); ?>
	<script type="text/javascript">
 function Pager(tableName, itemsPerPage) {

this.tableName = tableName;

this.itemsPerPage = itemsPerPage;

this.currentPage = 1;

this.pages = 0;

this.inited = false;

this.showRecords = function(from, to) {

var rows = document.getElementById(tableName).rows;

// i starts from 1 to skip table header row

for (var i = 1; i < rows.length; i++) {

if (i < from || i > to)

rows[i].style.display = 'none';

else

rows[i].style.display = '';

}

}

this.showPage = function(pageNumber) {

if (! this.inited) {

alert("not inited");

return;

}

var oldPageAnchor = document.getElementById('pg'+this.currentPage);

oldPageAnchor.className = 'pg-normal';

this.currentPage = pageNumber;

var newPageAnchor = document.getElementById('pg'+this.currentPage);

newPageAnchor.className = 'pg-selected';

var from = (pageNumber - 1) * itemsPerPage + 1;

var to = from + itemsPerPage - 1;

this.showRecords(from, to);

}

this.prev = function() {

if (this.currentPage > 1)

this.showPage(this.currentPage - 1);

}

this.next = function() {

if (this.currentPage < this.pages) {

this.showPage(this.currentPage + 1);

}

}

this.init = function() {

var rows = document.getElementById(tableName).rows;

var records = (rows.length - 1);

this.pages = Math.ceil(records / itemsPerPage);

this.inited = true;

}

this.showPageNav = function(pagerName, positionId) {

if (! this.inited) {

alert("not inited");

return;

}

var element = document.getElementById(positionId);

var pagerHtml = '<span onclick="' + pagerName + '.prev();" class="pg-normal"> < < Prev </span> ';

for (var page = 1; page <= this.pages; page++)

pagerHtml += '<span id="pg' + page + '" class="pg-normal" onclick="' + pagerName + '.showPage(' + page + ');">' + page + '</span> ';

pagerHtml += '<span onclick="'+pagerName+'.next();" class="pg-normal"> Next > > </span>';

element.innerHTML = pagerHtml;

}

} 

</script>

<style>
.classWithPad { margin:20px; padding:20px; }
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
#sample_accepted {
	cursor:pointer;	
}
#sample_accepted_cal{
	cursor:pointer;	
}
@media print {
  body * {
    visibility:hidden;
  }
  #myModal, #myModal * {
    visibility:visible;
  }
  #myModal {
    position:absolute;
    left:0;
    top:0;
	width:100%;
  }
}
.pg-normal {
    color: #ffffff;
    font-size: 15px;
    cursor: pointer;
    background: #005000;
    padding: 2px 4px 2px 4px;
}
.pg-selected {
    color: #fff;
    font-size: 15px;
    background: #EE8801;
    padding: 2px 4px 2px 4px;
}
 .table-responsive {
  height:500px;	
 
}
 #avb{
	height:auto;	
}
/* .table-bordered{
    table-layout: fixed;
   word-wrap: break-word;

} */

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
		#myModal .modal-lg {
          width: 1300px; /* New width for large modal */
        }
    }


</style>

<script>
	$(document).ready(function(){
		function changeColor(id1) {
				$('label[for="'+id1+'"]').css({backgroundColor:'#84e184'});
				$('label[for="'+id1+'"]').css("cursor", "pointer");
			}  
			function RemoveColor(id1) {
				$('label[for="'+id1+'"]').css({backgroundColor:'transparent'});
			} 
			function removeall()
			{
				$( "#accordion p>label" ).each(function( index ) {
					RemoveColor($(this).attr('id'));
				});
			}
			function uncheckAll(){
				$('input[type="checkbox"]:checked').removeAttr('checked');
			}
			function untextAll(){
				$('select').attr('disabled',true);
			}
			$('#panel8 label').on('click', function (event) {
					$('input:checkbox').removeAttr('checked');
					var id1=$(this).attr('id');
					removeall(id1);
					changeColor(id1);
					$("#label_name").val(id1);
					$("#tablepaging").empty();
					//$("#avb tbody").find('tr').remove();
					$.ajax({
						type: "POST",
						url: 'ref_reports',
						data: {id1:id1},
						success: function (data) {
						//alert(data);
						$("#tablepaging").html(data)
						  
						}
					});
					
			});
	});
	</script>
	<?php //pr($user); ?>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6"><h1 class="m-0 text-dark">Master Reports</h1></div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><?php echo $this->Html->link('Dashboard', array('controller' => 'dashboard', 'action'=>'home')); ?></li>
							<li class="breadcrumb-item">Master Reports</li>
						</ol>
					</div>
			</div>
		</div>
		<section class="content form-middle">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<?php echo $this->Html->link('Back', array('controller' => 'Inward', 'action'=>'home'),array('style'=>'float:right;','class'=>'add_btn btn btn-secondary')); ?>
					</div>
					<div class="col-md-12">
						<div class="card card-lims">
							<?php echo $this->Form->create(null, array('id'=>'rpt_ref', 'name'=>'Reports','class'=>'form-group')); ?>
								<div class="card-header"><h3 class="card-title-new">List of Master Reports</h3></div>
									<div class="form-horizontal">
										<div class="card-body">
										<input type="hidden" class="form-control" name="label_name" id="label_name">
										<div class="col-md-3">
										<fieldset class="fsStyle">
										<legend  class="legendStyle">Reports Name</legend>
										<div class="panel-group" id="accordion">
											<div class="panel panel-default">
												<div class="panel-heading">
						 							<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#panel8"><i class="glyphicon glyphicon-plus"></i> Reference Reports</a></h4>
												</div>
													<div id="panel8" class="panel-collapse collapse In">
														<div class="panel-body">
														
														<p><label for="category" id="category" class="category  control-label">Commodity Category</label></p>
														<p><label for="test_type" id="test_type" class="test_type  control-label">Test Type</label></p>	
														<p><label for="test_fields" id="test_fields" class="test_fields  control-label">Test Fields</label></p>
														<p><label for="test_method" id="test_method" class="test_method  control-label">Test Method</label></p>
														<p><label for="phy_appear" id="phy_appear" class="phy_appear  control-label">Physical Appearance</label></p>
														<p><label for="sam_cond" id="sam_cond" class="sam_cond  control-label">Sample Condition</label></p>
														<p><label for="pakg_cond" id="pakg_cond" class="pakg_cond  control-label">Package Condition</label></p>
														<p><label for="method_homogen" id="method_homogen" class="method_homogen  control-label">Method of Homogenisation</label></p>
														<p><label for="general_obs" id="general_obs" class="general_obs  control-label">General Observation</label></p>	
														<p><label for="unit_desc" id="unit_desc" class="unit_desc  control-label">Unit Description</label></p>	
														<p><label for="sample_type" id="sample_type" class="sample_type  control-label">Type of Sample</label></p>	
														<p><label for="grade" id="grade" class="grade  control-label">Grade</label></p>	
														<p><label for="location" id="location" class="location  control-label">Location</label></p>
														</div>
													</div>
											</div>
										</div>
										</fieldset>
			
			<span>
				<button class="btn btn-primary" name="close" id="close">Close</button>
			</span>
		</div>
		</form>
		<div class="col-md-9">
		<?php 
	//if(isset($sample_inward) && $sample_inward!=''){
			
			
			?>	
			<fieldset class="fsStyle">
				<legend  class="legendStyle"><?php if(isset($report_head) && $report_head!=''){ echo $report_head; } ?></legend>
					<div id="pageNavPosition"  align="center">
						</div>
						<div class="row">
								<div class="col-xs-12 col-sm-8 col-md-12 ">
								<h5 class="text-center">भारत सरकार/Goverment of India</h5>
									<h5 class="text-center">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
									<h5 class="text-center">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
									<h5 class="text-center">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
									<?php if($_SESSION['user_flag']=="CAL" ){?>
									<h5 class="text-center"><b>केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory</b></h5>
									<h5 class="text-center">उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010</h5>
									<?php } ?>
									<?php if($_SESSION['user_flag']=="RAL" ){?>
									<h5 class="text-center"><b>प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , <?php echo $_SESSION['location_name'];?></b></h5>
									<!--<h5 class="text-center"><?php echo $_SESSION['user_flag'].','.$_SESSION['location_name']; ?></h5>-->
									<?php }elseif(isset($sample_inward['0']['0']['location_desc']) && isset($ral_lab_name) && $ral_lab_name=='RAL'){?>
										<h5 class="text-center"><b>प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , <?php echo $sample_inward['0']['0']['location_desc'];?></b></h5>
									<?php }elseif(isset($sample_inward['0']['0']['location_desc']) && isset($ral_lab_name) && $ral_lab_name=='CAL'){ ?>
									<h5 class="text-center"><b>केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory</b></h5>
									<h5 class="text-center">उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010</h5>
									<?php }elseif(isset($lab_name) && $flag=="RAL"){ ?>
									<h5 class="text-center"><b>प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , <?php echo $lab_name;?></b></h5>
									<?php }elseif(isset($lab_name) && $flag=="CAL"){	?>
										<h5 class="text-center"><b>केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory</b></h5>
									<h5 class="text-center">उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010</h5>
									<?php } ?>
								</div>
							</div>
							<div class="table-responsive" id="avb">
							<table class="table table-bordered" id="tablepaging">
								<tbody>
								</tbody>
							</table>
							</div>	
				<!-- <div class="table-responsive" id="avb">
                        <table class="table table-bordered" id="tablepaging">
                            <tbody>
							
							<?php switch($report_name){
										case 'category': 	?>
									<tr>
										<th>S.No</th>
										<th>Commodity Category</th>
										<th >Commodity Category(हिंदी)</th>
									</tr>
								<?php
									$i = 1;
									foreach ($sample_inward as $res1): 
								?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['category_commodity']['category_name'] ?></td>
										<td><?php echo $res1['category_commodity']['l_category_name'] ?></td>
									</tr>
									<?php
										
									$i++;
									endforeach;
							break; 
							case 'test_type': ?>
								<tr>
							       	<th>S.No</th>
									<th>Test Type</th>
									<th>Test Type(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Test_type']['test_type_name'] ?></td>
										<td><?php echo $res1['Test_type']['test_type_lbl'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
								
								case 'test_fields': ?>
								<tr>
							       	<th>S.No</th>
									<th>Test Fields</th>
									<th>Test Fields(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Master_Test_Field']['field_name'] ?></td>
										<td><?php echo $res1['Master_Test_Field']['l_field_name'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									case 'test_method': ?>
								<tr>
							       	<th>S.No</th>
									<th>Test Method</th>
									<th>Test Method(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Test_Method']['method_name'] ?></td>
										<td><?php echo $res1['Test_Method']['l_method_name'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									case 'phy_appear': ?>
								<tr>
							       	<th>S.No</th>
									<th>Physical Appearance</th>
									<th>Physical Appearance(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Phy_Apperance']['phy_appear_desc'] ?></td>
										<td><?php echo $res1['Phy_Apperance']['l_phy_appear_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									case 'sam_cond': ?>
								<tr>
							       	<th>S.No</th>
									<th>Sample Condition</th>
									<th>Sample Condition(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Sample_Condition']['sam_condition_desc'] ?></td>
										<td><?php echo $res1['Sample_Condition']['l_sam_condition_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									case 'pakg_cond': ?>
								<tr>
							       	<th>S.No</th>
									<th>Package Condition</th>
									<th>Package Condition(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Parcel_Condition']['par_condition_desc'] ?></td>
										<td><?php echo $res1['Parcel_Condition']['l_par_condition_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									case 'method_homogen': ?>
								<tr>
							       	<th>S.No</th>
									<th>Homogenization</th>
									<th>Homogenization(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Method_Homogen']['homogen_desc'] ?></td>
										<td><?php echo $res1['Method_Homogen']['l_homogen_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									case 'general_obs': ?>
								<tr>
							       	<th>S.No</th>
									<th>Observations</th>
									<th>Observations(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['General_Obs']['general_obs_desc'] ?></td>
										<td><?php echo $res1['General_Obs']['l_general_obs_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									case 'unit_desc': ?>
								<tr>
							       	<th>S.No</th>
									<th>Unit Description</th>
									<th>Unit Description(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Unit_Desc']['unit_weight'] ?></td>
										<td><?php echo $res1['Unit_Desc']['l_unit_weight'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									case 'grade': ?>
								<tr>
							       	<th>S.No</th>
									<th>Grade Description</th>
									<th>Grade Description(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Grade_Desc']['grade_desc'] ?></td>
										<td><?php echo $res1['Grade_Desc']['l_grade_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									
									case 'sample_type': ?>
								<tr>
							       	<th>S.No</th>
									<th>Type Of Sample</th>
									<th>Type Of Sample(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Sample_Type']['sample_type_desc'] ?></td>
										<td><?php echo $res1['Sample_Type']['l_sample_type_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
									
									case 'location': ?>
								<tr>
							       	<th>S.No</th>
									<th>Master Location</th>
									<th>Master Location(हिंदी)</th>
                            	</tr>
								<?php $i = 1;
									foreach ($sample_inward as $res1): ?>		
                                    <tr>
									    <td><?php echo $i; ?></td>
                                     	<td><?php echo $res1['Location']['location_desc'] ?></td>
										<td><?php echo $res1['Location']['l_location_desc'] ?></td>
									</tr>
									<?php 
									$i++;
									endforeach;
									break;
								?>	
									
									
							<?php }	?>
						</tbody>
					</table>
				</div>-->
			</fieldset>
			<?php 

 //} 
	?>

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


 $("#close").click(function() {
		 location.href="<?php echo $home_url; ?>";
	 });





</script>	
<script type="text/javascript">
		
 var pager = new Pager('tablepaging', 10);
pager.init();
pager.showPageNav('pager', 'pageNavPosition');
pager.showPage(1); 

</script>