<?php
	echo $this->Html->script('jquery_validationui');
	echo $this->Html->script('languages/jquery.validationEngine-en');
	echo $this->Html->script('jquery.validationEngine');
	echo $this->Html->script('jspdf.debug');

	echo $this->Html->css('validationEngine.jquery');
	echo $this->Html->Script('bootstrap-datepicker.min');
	print  $this->Session->flash("flash", array("element" => "flash-message_new"));		
?>
<style>
    .Absolute-Center {
        margin: auto;
        border: 1px solid #ccc;
        background-color: #f3fafe;
        padding: 20px;
        border-radius: 3px;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }
    .no-margin {
        margin: 0;
    }
</style>
<!--<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.js"></script>-->

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>-->
<script type="text/javascript">
$(function () {
	$('#pdf').click(function () {
		var date = new Date();
		var n = date.toDateString();
		var time = date.toLocaleTimeString();
		$('#donotprint').hide();
		var doc = new jsPDF();
		doc.addHTML($('#divtoprint'), 15, 15, {
		'background': '#fff',
		'border':'2px solid white',
		}, function() {
			doc.save('sample-inward-'+n+' '+time+'.pdf');
		});
		
		$('#donotprint').show();
	});
});
</script>
<script>
  $(document).ready(function() {
	   $("#parel_size").attr("disabled", true); 
	   $('#stage_sample_code1').select2();
	   
		$.ajax({			 
				type: "POST",
				url: 'get_sample_code1',			   
				success: function (data) {
					if(data!=''){
						$("#stage_sample_code1").find('option').remove();
						$("#stage_sample_code1").append("<option value='-1'>-----Select----- </option>");
						$.each($.parseJSON(data), function (key, value) {							
							$("#stage_sample_code1").append("<option value="+value[0]['stage_sample_code']+">"+value[0]['stage_sample_code']+"</option>");							
						});
					}
					else
					{
						 var msg="Sample Code Not available !!!";
						errormsg(msg);
					}
				}
		});
	
		$("#collected_by_div").hide();
		$("#drawal_loc_div").hide();
		$("#shop_name_div").hide();
		$("#shop_address_div").hide();
		$("#blend_div").hide();
		$("#valdt_dt_div").hide();
		$("#tbl_div").hide();
		$("#pack_size_div").hide();
		$("#replica_serial_no_div").hide();
		$("#dispatch_dt_div").hide();
		$("#ral_lab_code_div").hide();
		$("#stage_sample_code_div").hide();
		$("#sample_type_code_div").hide();
		$("#add_inw_ltr").click(function(e){
			 location.href = "<?php echo $base_url;?>Inward_details/gnrt_inward_ltr";
		});
		<?php if(isset($_SESSION['sample'])){?>
	$('#sample_type_code').val(<?php echo $_SESSION['sample'];?>);
	$("#stage_sample_code_div").show();
		$("#sample_type_code_div").show();
		<?php }?>
		//$("#sample1").val(sample1);
		$("#stage_sample_code1").on("change",function()
		{
			var stage_sample_code=$("#stage_sample_code1").val();
			stage_sample_code=stage_sample_code.split("~");
			var stage_sample_code1=$("#stage_sample_code1 option:selected").text();
			//alert(stage_sample_code[0]);
			$("#stage_sample_code").val(stage_sample_code1[0]);
			$("#sample_type_code1").val(stage_sample_code[1]);
			$("#inward_id").val(stage_sample_code[1]);
			$("#loc_id").val(stage_sample_code[2]);
		});
		$("#close").click(function() {
           
           location.href="<?php echo $home_url; ?>";
        });
		$("#add1").click(function(e) {
            $('#myModal').modal('show');
        });
		
		  $("#myModal").on("hide.bs.modal", function() {
            if ($('#close1').data('clicked')) {} else if ($('#close').data('clicked')) {} else {
                return false;
            }
            $('#close1').data('clicked', false);
            $('#close').data('clicked', false);
            $('form#test')[0].reset();
            var x = getCookie("sample");
            //$('form#test')[0].val(x);

            $("#sample_code").val(x).change();
            //$('#test_select').val('na');
        });
		$("#view").on("click",function(e)
		{
			$("#avb").show();
		});
		$("#save1").on("click",function(e)
		{
		//e.preventDefault();	
		//var sample_type=$("#stage_sample_code option:selected").text();
		
		var sample_type_code1=$("#sample_type_code1").val();
		var stage_sample_code=$("#stage_sample_code").val();
		//alert(stage_sample_code);
		if(sample_type_code1=="")
		{
			
		alert("please select original sample code first");
		}
		else{
			var sample_type_code=$("#sample_type_code1").val();
		var inward_id=$("#inward_id").val();
		var loc_id=$("#loc_id").val();
		
		var sample=sample_type_code;
		//alert(sample);
		
		$.ajax({
				type:"POST",
				url:"<?php echo Router::url(array('controller'=>'Inward','action'=>'set_session'));?>",
				data:{sample:sample,stage_sample_code:stage_sample_code,inward_id:inward_id,loc_id:loc_id},
				cache:false,
				success : function(data)
				{
					location.reload();
				}
		});
		//document.cookie = "sample_type="+sample;
	
		}}
		);
		$("#save").on("click",function()
		{
			$("#modal_test").submit();
		}
		);
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
	
            <?php
			if(isset($sample_fi))
			{
			foreach($sample_fi as $row): ?>
               var id = '<?php echo $row['b']['inw_field_name'];?>';
				var myElem = document.getElementById(id + "_div");
				if (myElem != null) {
                $('#' + id + "_div").show();
                $('#' + id).prop('required', true);
            }
			<?php endforeach;
			}
else{?>
//alert("hello");
 $('#myModal').modal('show');
 //$('#frm_sample_inward_details').find('div').show();
 <?php
}	?>
//$('#frm_sample_inward_details').find( ":hidden" ).remove();	
			  $("#button_div").show();
        });
    function get_commodity() {
        $("#commodity_code").find('option').remove();
        $("#commodity_code").append("<option value=''>-----Select---- </option>");
        var category_code = $("#category_select").val();
        $.ajax({
            type: "POST",
            url: 'get_commodity',
            data: {
                category_code: category_code
            },
            success: function(data) 
			{
                $("#commodity_code").append(data);
            }
        });
    }

		// var dateToday = new Date(); 
	
function enable_cate()
{
	$("#stage_sample_code1").attr("disabled", false); 
}

    function commodity_test() {
        $("#stage_sample_code").find('option').remove();
        var category_code = $(category_select).val();
        if (category_code != "") {
            var commodity_code = $("#commodity_code").val();
            $.ajax({
                type: "POST",
                url: 'get_sample_code',
                data: {
                    commodity_code: commodity_code,
                    category_code: category_code
                },
                success: function(data) {
                    //  alert(data);
                    $.each($.parseJSON(data), function(key, value) {

                        $("#stage_sample_code").append("<option value='" + key + "'>" + value + "</option>");
                    });
                }
            });
        } else {
            alert("Select commodity category first!")
        }
    }
	
	//  pravin bhakare 29-10-2019
	function getsampledetails(){
		
		$("#commodity_category").find('option').remove();
		$("#commodity_name").find('option').remove();
		$("#sample_type").find('option').remove();
		
		var sample_code=$("#stage_sample_code1").val();		
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
				}
			});
	}
</script>
<html>

<body>

    <?php ?>
		
   
	<?php if(isset($str_data)){ //pr($str_data);exit; ?>	
		<div class="row">
			<div class='col-md-6 col-md-offset-3' id="divtoprint">
				<div class="row">
					<h5 class="text-center">भारत सरकार/Goverment of India</h5>
					<h5 class="text-center">कृषि एवं किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare</h5>
					<h5 class="text-center">कृषि एवं सहकारिता विभाग / Department of Agriculture and cooperation</h5>
					<h5 class="text-center">विपणन एवं निरीक्षण निदेशालय / Directorate of Marketing and Inspection</h5>
					<?php if($_SESSION['user_flag']=="RO"){ ?>
					<h5 class="text-center">Regional Office,<?php echo $_SESSION['location_name']; ?></h5>
					<?php } ?>
				</div>
				<div class="row">
					<h5 class="text-right">Dated :- <?php echo date('d/m/Y');?></h5>
				</div>
				<div class='row'>
					<div class='text-left'><p>To,<br><?php echo $user_data[0][0]['f_name'].' '.$user_data[0][0]['l_name']; ?>,<br><?php echo $user_data[0][0]['role']; ?>,<br><?php echo $user_data[0][0]['user_flag'].','.$user_data[0][0]['ro_office'];?></p><br></div></div>
					
					
				<div class='row'>
					<div class='text-left'>
						<p>Subject: Analysis of <?php echo $str_data[0]['b']['sample_type_desc']; ?> sample of <?php echo $str_data[0]['m']['commodity_name'];?> bearing Code No.<?php echo $str_data[0]['w']['stage_smpl_cd'];?></p>
					</div>
				</div>
				<div class='row'>
					<p>Sir,</p>
				</div>
				<div class='row'>
					<p class="text-justify">With reference to subject cited above, I am sending herewith <?php echo $str_data[0]['Sample_Inward']['sample_total_qnt']; ?>
					<?php echo $str_data[0]['a']['unit_weight']; ?> of <?php echo $str_data[0]['b']['sample_type_desc']; ?> sample of <?php echo $str_data[0]['m']['commodity_name'];?> bearing Code No.<?php echo $str_data[0]['w']['stage_smpl_cd']; ?> for analysis.
					The quantity of sample is <?php echo $str_data[0]['Sample_Inward']['sample_total_qnt'];?> <?php echo $str_data[0]['a']['unit_weight']; ?>,
					packed in <?php echo $str_data[0]['c']['container_desc'];?>.
					It is requested that the sample may be analyzed for all parameters and analytical report may be sent to
					this office within stipulated time.</p>
				</div>
				<div class='row'>
					<div class='col-md-12'> 
						<br/>
						<p class="text-left" style="margin:0px 0px 2px;">Encl:as above</p>
						<p class="text-right" style="margin:0px 0px 2px;">Your's Faithfully,</p><br/>
						<!-- below code added on 25-10-2019 by Amol, to show Officer Incharge Name & Designation in 'From' at bottom
							Either sent by any officer, name should be dsplay only of Incharge -->						
						<p class="text-right" style="margin:0px 0px 2px;"><?php echo $src_user_data[0][0]['f_name'];?> <?php echo $src_user_data[0][0]['l_name'];?></p>
						<p class="text-right" style="margin:0px 0px 2px;"><?php echo $src_user_data[0][0]['role'];?> </p>
						<p class="text-right" style="margin:0px 0px 2px;"><?php echo $src_user_data[0][0]['user_flag'].','.$src_user_data[0][0]['ro_office'];?> </p>
						<br/>
					</div>
				</div>
				<div class='row' id="donotprint">
					<div class='col-md-10 text-center'>
					<span><button class='btn btn-primary'  id='pdf'>PDF</button></span>
					<span><a href='<?php echo $base_url;?>users/home' class='btn btn-primary'  id='close'>Close</a></span>
					
					</div>
					
				</div>
			</div>
		</div>
			
	<?php } else{ //pr($res); ?>
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-12">
            <form method="post" id="modal_test" name="modal_test" class="form-horizontal" action="" autocomplete="off">
				
                    <div class="modal-body">
							<!--<input type="hidden"  class="form-control"  name="token" id="token"   value="<?php echo $_SESSION['token']; ?>" >	-->
                           <div class="row">
						       <!--<div class="col-xs-3 col-sm-3 col-md-3">
									<div class="form-group">
									<label class="control-label col-md-4" for="sel1"> From</label>
									<div class="col-md-8">
									 <div class="input-group input-append date" id="datePicker">
												<input type="text" class="form-control" name="rec_from_dt" placeholder="dd/mm/yyyy" id="rec_from_dt"  onchange="enable_cate()" required  />
												<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>											
											</div>										
									</div>
									<!-- Hint for the search from date, Added by Pravin Bhakare on 07-06-2019
									<label class="control-label" for="sel1" style="margin-left: 74px;">(Select Range of 'Sample Registration' Date)</label>
									</div>
								</div>
								<div class="col-xs-3 col-sm-3 col-md-3">
									<div class="form-group">
									<label class="control-label col-md-4" for="sel1"> To </label>
									<div class="col-md-8">
									  <div class="input-group input-append date" id="datePicker1">
										<input type="text"  class="form-control"  name="rec_to_dt" id="rec_to_dt"    placeholder="dd/mm/yyyy" onchange="enable_cate()"    required >	
										<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
										</div>
									   </div>
									</div>
								</div>-->
			        <div class="col-xs-3 col-sm-3 col-md-3" >
						<div class="form-group">
							<label class="control-label col-md-4" for="sel1">Sample Code</label>
							<div class="col-md-6">
								<select class="form-control validate[required]" id="stage_sample_code1" name="stage_sample_code1" onchange="getsampledetails();" >
									<option value=''>-----Select-----</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3" >
						<div class="form-group">
							<label class="control-label col-md-4" for="sel1">Category</label>
							<div class="col-md-6">
								<select class="form-control validate[required]" id="commodity_category" name="category_code" >	
								</select>
							</div>
						</div>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3" >
						<div class="form-group">
							<label class="control-label col-md-4" for="sel1">Commodity</label>
							<div class="col-md-6">
								<select class="form-control validate[required]" id="commodity_name" name="commodity_code" >
								</select>
							</div>
						</div>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3">
						<div class="form-group">
						<label class="control-label col-md-4" for="sel1">Sample Type </label>
						<div class="col-md-6">
						  <select class="form-control validate[required]" id="sample_type" name="sample_type_code1" >
							</select>
						</div>
						</div>
					</div>

			   </div>
                    </div>
                 
                </form> 
        </div>
   

			<div class="row" >
				<div class="col-lg-12 text-center" >								
					<span>
						<button class="btn btn-primary"  id="save" >View Letter</button>
					</span>								
					<span>
						<button class="btn btn-primary" id="close">Close</button>
					</span>								
				</div>
			</div>

   
    <script>
      
    </script>
</div>
	<?php }?>
</body>

</html>