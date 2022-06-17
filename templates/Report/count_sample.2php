<?php 
	echo $this->Html->Script('bootstrap-datepicker.min');
	echo $this->Html->script('report-functionality');
	//print  $this->Session->flash("flash", array("element" => "flash-message_new")); 
?>
<?php	echo $this->form->input('user_role_id', array('type'=>'hidden', 'id'=>'user_role_id', 'value'=>$_SESSION['role'],'label'=>false,)); ?>	

<script type="text/javascript">
	function exportDiv(){
		//window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $('div[id$=modalContent]').html()));
		window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $('div[id$=myModal]').html()));
		e.preventDefault();
	}
	
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
function printDiv() 
{	
	$('#mdFooter').hide();
	var divToPrint=document.getElementById('myModal');
	var newWin=window.open('','Print-Window');

	newWin.document.open();
	newWin.document.write('<html><head>');
	newWin.document.write('<style></style>');//#avb{overflow: visible;}
	newWin.document.write('<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css"></head>');
	newWin.document.write('	<body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
	newWin.document.close();
	setTimeout(function(){newWin.close();},10);
	$('#mdFooter').show();
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
		width: 600;
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
	}
}

.select2-container {
	box-sizing: border-box;
	display: inline-block;
	margin: 0;
	position: relative;
	vertical-align: middle;
	width: 210px !important;
}

#loading_con{display: none;} #loading_con .col-md-4{text-align: center;} .loader_img{width: 75px;}
	
</style>
<script>
	$(document).ready(function(){		 
		$("#chemist_code").change(function() {
			
			$("#Sample_code").attr("disabled", false); 
			
			var from_date = $("#from_date").val();
			var to_date=$("#to_date").val();
			
			var chemist_code = $("#chemist_code").val();				
			$.ajax({
				type: "POST",
				url: 'get_chemist_sample',
				data: {from_date: from_date,to_date:to_date,chemist_code:chemist_code},
				success: function (data) {
					$("#Sample_code").find('option').remove();
					$("#Sample_code").append("<option value='-1'>-----Select----- </option>");
					
					if(data=='NO_DATA')
					{
						var msg="Sample Not available !!!";
						errormsg(msg);
					}
					else{
						$("#Sample_code").append(data);
					}
				}
			});
			
		});	
	});
</script>
<script>
	function hideparameter() {
		$(".parameters").hide();
		$(".report-menu").css({'margin-left' : '25%'});	
	};
	$(document).ready(function(){				
		$(".parameters").hide();
		$("#paralast,#paraperiod,#parato_date,#paramonth,#paralab1,#pararal_lab,#parasample_type,#paraCategory,#paraCommodity,#paratest,#parauser_a,#parachemist_code,#parasample_code").hide();
		
		$("#from_date").val('');
		$("#to_date").val('');
		
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		
		var yyyy = today.getFullYear();
		var today = dd+'/'+mm+'/'+yyyy; 
		//$('#from_date').val(today); 
		//$('#to_date').val(today);
		$("#save").prop("disabled", true);
		$("#cancel").click(function(e)
		{
			removeall();
			$("#save").prop("disabled", true);				
		});		
			
		var hasChecked;	
		var selectIds = $('#panel10,#panel11,#panel12,#panel13,#panel14,#panel15,#panel7,#panel9,#panel8');
		
		$(function ($){
					selectIds.on('show.bs.collapse',function () {
					var id=$(this).attr('id');					
					$('#'+id+' label').on('click', function (event) {
					
						var selectedMenuTitle =  $('#label_name').val();
						
						// check whether the clicked menu already active or not
						if(selectedMenuTitle != $(this).attr('id')){
						
							$('#rpt_comm_sample input[type="text"]').val('');
							$('#rpt_comm_sample select').prop('selectedIndex',0);
							
							$(".report-menu").css({'margin-left' : ''});
							$('input:checkbox').removeAttr('checked');
							var id1=$(this).attr('id');
							$.ajax({
								type: "POST",
								url: 'get_lab_name',
								data: {id1: id1},
								success: function (data) {
								
								$("#lab").find('option').remove();
								$("#lab").append("<option value='-1'>-----Select----- </option>");
								
								 if(data)
								 {
									$("#lab").append(data).change();
								 }
						
								}
							});
							
							manageFilterAttributesReportWise(id1);
							
							$("#label_name").val(id1);
						}
					})
				})
				selectIds.on('show.bs.collapse hidden.bs.collapse', function () {
					$(this).prev().find('.glyphicon').toggleClass('glyphicon-plus glyphicon-minus');
				})
		}); 
		
		
		$('#from_date').datepicker({
			endDate: '+0d',    
			autoclose: true,
			todayHighlight: true,
			format: 'dd/mm/yyyy'
		}).on('changeDate', function (selected) {
			var minDate = new Date(selected.date.valueOf());
			$('#to_date').datepicker('setStartDate', minDate);
			$('#to_date').val('');
		});		
		
		$('#to_date').datepicker({
			endDate: '+0d',    
			autoclose: true, 
			todayHighlight: true,			  
			format: 'dd/mm/yyyy'
		}).on('changeDate', function (selected) {
			var maxDate = new Date(selected.date.valueOf());
			$('#from_date').datepicker('setEndDate', maxDate);
			if($('#from_date').val()==''){				
				$('#to_date').val('');
			}
		});		
		
	});
</script>
	<?php //pr($user);  ?>
	<div class="row">	
		<div class="col-md-6 report-menu" style="margin-left: 25%;">
			<fieldset class="fsStyle">
				<legend  class="legendStyle">Title</legend>
				<div class="panel-group" id="accordion">					
					<?php foreach($label as $label1 ) : ?>
						<?php if($label1['label_code'] !=14){ ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a id="<?php echo $label1['label_desc']; ?>" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#panel<?php echo $label1['label_code'];?>" onclick="hideparameter();"><i class="glyphicon glyphicon-plus"></i> <?php echo $label1['label_desc']; ?></a>
								</h4>
							</div>								
							<div id="panel<?php echo $label1['label_code']; ?>" class="panel-collapse collapse">
								<div class="panel-body">
									<?php  foreach($report as $report1) :
										if($label1['label_code']==$report1['label_code']){
											
									?>
									<p><label for="<?php echo $report1[0]['report_desc'];?>" id="<?php echo $report1[0]['report_desc'];?>" class="<?php echo $report1[0]['report_desc'];?>  control-label"><?php echo $report1[0]['report_desc'];?></label></p>
										<?php  } endforeach; ?>
								</div>
							</div>								
						</div>	
						<?php } ?>	
					<?php endforeach; ?>	
				</div>
			</fieldset>
		</div>		
		<div class="col-md-6 parameters">
			<form id="rpt_comm_sample"  method="post" action="" autocomplete="off" target="_blank">
				<input type="hidden" class="form-control" name="label_name" id="label_name">
				<fieldset class="fsStyle">
					<legend  class="legendStyle">Parameters</legend>
					<?php  if( $_SESSION['role']=='Head Office' || $_SESSION['role']=='Admin'){ ?>
					<div class="row" id="paralast">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="last" class="validate[minCheckbox[2]] checkbox" value="" disabled>During Last</label>
							</div>
						</div>
						<div class="col-md-6">
							<label class="control-label" for="sel1" >days</label>
							<div class="col-md-4">
								<input type="text" class="form-control" id="days" name="days">
							</div>	
						</div>	
					</div>
					<?php } ?>
					<?php if($_SESSION['role']=='Lab Incharge' || $_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='RO/SO OIC' || $_SESSION['role']=='RO Officer' || $_SESSION['role']=='Jr Chemist' || $_SESSION['role']=='SO Officer' || $_SESSION['role']=='Admin' || $_SESSION['role']=='RO/SO OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='Sr Chemist' || $_SESSION['role']=='Cheif Chemist' || $_SESSION['role']=='DOL' || $_SESSION['role']=='RAL/CAL OIC'){ ?>
					<div class="row" id="paraperiod">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="period" class="validate[minCheckbox[2]] checkbox" name="period" value="period" value="" disabled>Period</label>
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<!--<label class="control-label col-md-4" for="sel1">From Date</label>-->
								<div class="col-md-8">
									<div class="input-group input-append date" id="datePicker">
										<input type="text" class="form-control" name="from_date" id="from_date" title="From Date" placeholder="Form (dd/mm/yyyy)" id="letr_date" />
										<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
									</div>
								</div>
								<!-- Hint for the search from date, Added by Pravin Bhakare on 07-06-2019-->
								<label class="control-label" for="sel1">(Select Range of 'Sample Registration' Date)</label>	
							</div>
						</div>		
					</div>
					<div class="row" id="parato_date">
						<div class="col-md-6">
							<div class="checkbox">
								
							</div>
						</div>
						<div class="col-xs-6 col-sm-6 col-md-6">
							<div class="form-group">
								<!--	<label class="control-label col-md-4" for="sel1">To date</label>-->
									<div class="col-md-8">
										<div class="input-group input-append date" id="datePicker1">
											<input type="text" class="form-control" name="to_date" id="to_date" title="To Date" placeholder="TO (dd/mm/yyyy)" id="letr_date" />
											<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
										</div>
									</div>
							</div>									
						</div>		
					</div><br>
					<?php }	?>
					<?php if($_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='Admin' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='DOL'){  ?>
					<div class="row" id="paramonth">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="month1" class="validate[minCheckbox[2]] checkbox" name="montho" value="montho" disabled>Month</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="month" name="month"  >
									<option value=''>----Select----</option>
									<option value='1'>Janaury</option>
									<option value='2'>February</option>
									<option value='3'>March</option>
									<option value='4'>April</option>
									<option value='5'>May</option>
									<option value='6'>June</option>
									<option value='7'>July</option>
									<option value='8'>August</option>
									<option value='9'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
							</select>
						</div>	
					</div>
					<?php } ?>
					<?php if($_SESSION['role']=='Lab Incharge' || $_SESSION['role']=='RO Officer' || $_SESSION['role']=='SO Officer' || $_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='Admin' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='DOL' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='RO/SO OIC'){  ?>
					
					<!-- show loading image until ajax respond with result, added on 18th JAN 2021 by Aniket Ganvir -->
					<div class="row" id="loading_con">
						<div class="col-md-6">
						</div>
						<div class="col-md-4">
							<?php echo $this->Html->image('other/loader.gif', array('class'=>'loader_img')); ?>
						</div>	
					</div>
					
					<div class="row" id="paralab1">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="lab1" class="validate[minCheckbox[2]] checkbox" name="labo" value="labo" disabled>Offices</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="lab" name="lab" onchange="getlab();">
								<option hidden="hidden" value=''>-----Select-----</option>
									<?php foreach ($user_flag as $user_flag1): 	?>
									<?php /*if($_SESSION['user_flag']=="RAL"){  ?>
									<option value="<?php echo $_SESSION['user_flag']; ?>" selected><?php echo $_SESSION['user_flag']; ?></option>
									<?php } */ ?>
									<option value="<?php echo $user_flag1[0]['user_flag']; ?>"><?php echo $user_flag1[0]['user_flag']; ?></option>
									<?php endforeach; ?>
							</select>
						</div>	
					</div>
					<?php } ?>					
					<?php if($_SESSION['role']=='Lab Incharge' || $_SESSION['role']=='RO Officer' || $_SESSION['role']=='SO Officer' || $_SESSION['role']=='Head Office' ||  $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='Admin' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='DOL' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='RO/SO OIC' || $_SESSION['role']=='DOL'){ ?>
					<div class="row" id="pararal_lab">
						<div class="col-md-6">
							<div class="checkbox">
								<label id="ral_lab2"><input type="checkbox" id="ral_lab1" class="validate[minCheckbox[2]] checkbox" name="ral_lab1" value="labo" disabled></label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="ral_lab" name="ral_lab" onchange="getuser();"  >
								<option hidden="hidden" value=''>-----Select-----</option>
								<option  value='all'>All</option>
									<?php //foreach ($ral_list as $ral_list): 	?>
									<!--<option value="<?php echo $ral_list['Offices_RO_SRO']['office_code']."~".$ral_list['Offices_RO_SRO']['office_type']." ,".$ral_list['Offices_RO_SRO']['office_addr']; ?>">
									<?php echo $ral_list['Offices_RO_SRO']['office_type'].",". $ral_list['Offices_RO_SRO']['office_addr']; ?></option>-->
									<?php //endforeach; ?>
									
									<?php foreach ($office as $office1):	?>
									<option value="<?php echo $office1['posted_ro_office']."~".$office1['user_flag']." ,".$office1['ro_office']; ?>">
									<?php echo $office1['user_flag'].",". $office1['ro_office']; ?></option>
									<?php endforeach; ?>
							</select>
						</div>	
					</div>
					<?php } ?>
					<?php if( $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='Head Office' || $_SESSION['role']=='Admin' || $_SESSION['role']=='DOL'){ ?>
					<div class="row" id="parasample_type">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="sample" class="validate[minCheckbox[2]] checkbox" name="sampleo" value="sampleo" disabled>Sample Type</label>
							</div>
						</div>						
						<div class="col-md-4">
							<select class="form-control validate[required]" id="sample_type" name="sample_type"  >
								<option hidden="hidden" value=''>-----Select-----</option>
									<?php foreach ($Sample_Type as $Sample_Type1):	?>
									<option value="<?php echo $Sample_Type1['Sample_Type']['sample_type_code']; ?>"><?php echo $Sample_Type1['Sample_Type']['sample_type_desc']; ?></option>
									<?php endforeach; ?>
							</select>
						</div>	
					</div>
					<?php } ?>					
					<?php if($_SESSION['role']=='RO Officer' || $_SESSION['role']=='SO Officer' || $_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='RO/SO OIC' || $_SESSION['role']=='Admin' || $_SESSION['role']=='DOL' ){ ?>
					<div class="row" id="paraCategory">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="cat" class="validate[minCheckbox[2]] checkbox" name="cat" value="cotto" disabled>Category</label>
							</div>
						</div>						
						<div class="col-md-4">
							<select class="form-control validate[required]" id="Category" name="Category" >
								<option hidden="hidden" value=''>-----Select-----</option>
									<?php /* <?php foreach ($category_commodity as $Category1):	?>
									<option value="<?php echo $Category1['category_commodity']['category_code']; ?>"><?php echo $Category1['category_commodity']['category_name']; ?></option>
									<?php endforeach; ?> */ ?>
							</select>
						</div>	
					</div>
					<?php } ?>
					<?php if($_SESSION['role']=='RO Officer' || $_SESSION['role']=='SO Officer' || $_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='RO/SO OIC' || $_SESSION['role']=='Admin' || $_SESSION['role']=='DOL'){ ?>
					<div class="row" id="paraCommodity">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="commo" class="validate[minCheckbox[2]] checkbox" name="commo" value="commo" disabled>Commodity</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="Commodity" name="Commodity" >
								<option hidden="hidden" value=''>-----Select-----</option>
									<?php foreach ($Commodity as $Commodity1):	?>
									<option value="<?php echo $Commodity1['Commodity']['commodity_code']; ?>"><?php echo $Commodity1['Commodity']['commodity_name']; ?></option>
									<?php endforeach; ?>
							</select>
						</div>	
					</div>
					<?php } ?>
					<?php if( $_SESSION['role']=='Head Office' || $_SESSION['role']=='Admin'){ ?>
					<div class="row" id="paratest">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="test1" class="validate[minCheckbox[2]] checkbox" name="testo"  value="testo" disabled>Test</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="test" name="test" >
								<option hidden="hidden" value=''>-----Select-----</option>
									<?php foreach($Test as $Test1):	?>
									<option value="<?php echo $Test1['Test']['test_code']; ?>"><?php echo $Test1['Test']['test_name']; ?></option>
									<?php endforeach; ?>
							</select>
						</div>	
					</div>
					<?php } ?>
					<?php if( $_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='Admin' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='DOL' || $_SESSION['role']=='DOL'){ ?>
					<div class="row" id="parauser_a">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="user1" class="validate[minCheckbox[2]] checkbox" name="user1" value="usero" disabled>User</label>
							</div>
						</div>
						<div class="col-md-4">
							<select class="form-control validate[required]" id="user_a" name="user" required>
								<option hidden="hidden" value=''>-----Select-----</option>
									<?php  foreach($user as $user1): 	?>
									<option value="<?php echo $user1['Dmi_user']['id']; ?>"><?php echo $user1['Dmi_user']['f_name']; ?> <?php echo $user1['Dmi_user']['l_name']; ?></option>
									<?php endforeach; ?>
							</select>
						</div>	
					</div>
					<?php } ?>
					<?php if( $_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='Admin' || $_SESSION['role']=='Jr Chemist' || $_SESSION['role']=='Sr Chemist' || $_SESSION['role']=='Cheif Chemist' ||  $_SESSION['role']=='DOL'){ ?>
					<div class="row" id="parachemist_code">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="code2" class="validate[minCheckbox[2]] checkbox" name="code2" value="codeoo" disabled>Sample Code Available at Chemist</label>
							</div>
						</div>
						
								<?php //pr($user_str); ?>		
						<div class="col-md-4">
							<select class="form-control" id="chemist_code"  name="chemist_code"  required>
								<option value="">-----Select-----  </option>		
							</select>
						</div>	
					</div>
					 <?php } ?>
					<?php if($_SESSION['role']=='DOL' ||  $_SESSION['role']=='Head Office' || $_SESSION['role']=='RAL/CAL OIC' || $_SESSION['role']=='Inward Officer' || $_SESSION['role']=='RO/SO OIC' || $_SESSION['role']=='Admin' || $_SESSION['role']=='RO Officer'){ ?>
					<div class="row" id="parasample_code">
						<div class="col-md-6">
							<div class="checkbox">
								<label><input type="checkbox" id="code1" class="validate[minCheckbox[2]] checkbox" name="code1" value="codeo" disabled>Sample Code</label>
							</div>
						</div>		
						<div class="col-md-4">
							<select class="form-control" id="Sample_code"  name="sample_code"  required>
								<option value="">-----Select-----  </option>
							</select>
						</div>	
					</div>
					<?php } ?>					
				</fieldset>	
			</form>	
			<div class="row parameters">
				<div class="col-md-6 col-md-offset-4 text-center" >
					<span>
						<button class="btn btn-primary"  type="submit" name="save" id="save">Generate Report</button>
					</span>
					<span>
						<button class="btn btn-primary" name="cancel" id="cancel">Cancel</button>
					</span>
					<span>
						<button class="btn btn-primary" name="close" id="close">Close</button>
					</span>
					<button type="reset" id="btn_reset" style="display: none;"></button>
				</div>
			</div>	
		</div>
	</div>		
				
		

	<?php  
		
		if(isset($sample_inward) && $sample_inward!=''){
			
			echo $this->element('report/sample_inward_view');
			
		} elseif(isset($sample_inward1)){
			
			echo $this->element('report/sample_inward_view_one');
			
		} elseif(isset($test_report)){  
			
			echo $this->element('report/test_report_view');			
		} 
	?>
	<!--						
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css" />
			
  

<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>	
<script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>	

<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>	
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>	
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>	
  
-->
 


<script>

$('#myModal').modal('show');
$('#myModal1').modal('show');

/*var doc = new jsPDF();
var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
};
function myFunction() {
    window.print();
}
*/

function getuser()
    {
		// ajax call delayed to prevent from CSRF token mismatch issue, added on 16th JAN 2021
		setTimeout(getuserdelayed, 1000);
    }
	
function getuserdelayed() {
	
        var user_flag = $("#lab").val();
		var loc_id=$("#ral_lab").val();
		var result = loc_id.split("~");
		var loc_id1=result[0];
		var loc_name=result[1];
		//alert(loc_name);
		//var dist_code='user_flag=' + user_flag;
		$('#ral_lab2').text("");
        $.ajax({
            type: "POST",
				url:"<?php use cake\Routing\Router; echo Router::url(array('controller'=>'Report','action'=>'get_users'));?>",
            data: {user_flag: user_flag,loc_id:loc_id1},
            success: function (data) {
				//alert(data);	
				
				$('#user_a option').remove();
				$('#user_a').append("<option value=''>-----Select-----</option>");
                $('#user_a').append(data);
				//$("#save").prop("disabled", false);
            }
        });
}

 function getlab()
    {
        var user_flag = $("#lab").val();
		$("#ral_lab").prop("disabled", false);
		//var dist_code='user_flag=' + user_flag;
		$('#ral_lab2').text("");
        $.ajax({
            type: "POST",
			url:"<?php echo Router::url(array('controller'=>'Report','action'=>'get_lab'));?>",
            data: {user_flag: user_flag},
            success: function (data) {
				
				$('#ral_lab2').append(user_flag);	
				$('#ral_lab option').remove();
				//$('#ral_lab').append("<option value=''>-----Select-----</option>");
                $('#ral_lab').append(data).change();
				//$("#save").prop("disabled", false);
				
            }
        });
    }
$( "#search" ).keyup(function() {
	// Set a timeout
	clearTimeout($.data(this, 'timer'));
	
	// Search String
	var search_string = $(this).val();
	//alert(search_string);
	 $.ajax({
		type: "POST",
		url: 'search_value',
		data: {search_string: search_string},
		success: function (data) {
			alert(data);
		}
	 });
	// Search
	/* if (search_string == '') {
		$("table#resultTable tbody").fadeOut(50);
	}else{
		$("table#resultTable tbody").fadeIn(50);
		$(this).data('timer', setTimeout(search, 100));
	}; */
});
	$("#save").click(function () {
		
		var from_date1 = $("#from_date").val();
		var to_date2= $("#to_date").val();
		
		if(from_date1 !='' && to_date2 == ''){			
			errormsg('Please Select "TO DATE" ');
		}else{			
			$('form#rpt_comm_sample').submit();
		}	
		
	});
	$("#cancel").click(function() {
		 $('form#rpt_comm_sample')[0].reset();
	});
	$("#close").click(function() {
		 location.href="<?php echo $home_url; ?>";
	});
	 
/* $('#pdf').click(function () {	
		
	//alert("adads");
	var html_design="<body><h1>भारत सरकार/कृषि एवं किसान कल्याण मंत्रालय Pune</h1></body>";
	var file_name="abc";
	$.ajax({
		type: "POST",
		url: 'create_pdf',
		data: {html_design: html_design,file_name:file_name},
		success: function (data) {
			//alert(data);
			data = data.replace("home/", "");
			data = document.location.hostname+data;  
			var array = data.split(">>>>");
			data	= "http://"+document.location.hostname+array[1];
			alert(data)

		}
	});	
}) */;

</script>	
<script type="text/javascript">
/*  $(document).ready(function() {
    $('#example').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'pdfHtml5'
        ],
		header: true
    } );
} );  */
 var pager = new Pager('tablepaging', 10);
pager.init();
pager.showPageNav('pager', 'pageNavPosition');
pager.showPage(1); 


$('#pdf').click(function () {
			
			<?php
			/* if($sample_inward){
			$html_design='<script>document.getElementById("myModal")</script>';
			$file_name=$report_name;
			//exit;
			echo $html_design;
			exit;
			
			$page_size = 'A4';
			$waterMark = 'DMIQC'; 
			$display_flag = 'D';
		
			error_reporting(0);
            $this->autoRender = FALSE;
            Configure::write('debug', 0);
			//App::import('Vendor', 'mpdf/mpdf');
            $mpdf = new mPDF('utf-8', $page_size);
			$mpdf->allow_charset_conversion=true;
			$mpdf->charset_in='UTF-8';
			$mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;
            $mpdf->autoLangToFont = true;
			$stylesheet = file_get_contents($this->webroot.'/css/bootstrap.min.css');
			$mpdf->setFooter('{PAGENO} / {nb}');
			$mpdf->WriteHTML($stylesheet,2);
					
			$pdfname="Report_".$file_name.".pdf";
			//$html = mb_convert_encoding($html_design, 'UTF-8', 'UTF-8');
			$mpdf->WriteHTML($html_design,2);
			//$mpdf->Output($pdfFilePath, "F");
			
			$mpdf->Output($pdfname,"D");
			} */
			?>
	
});
</script>