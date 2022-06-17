<?php
	echo $this->Html->script('jquery_validationui');
	echo $this->Html->script('languages/jquery.validationEngine-en');
	echo $this->Html->script('jquery.validationEngine');
	echo $this->Html->css('validationEngine.jquery');
	echo $this->Html->Script('bootstrap-datepicker.min');
	echo $this->Html->script('jspdf.debug');
	print  $this->Session->flash("flash", array("element" => "flash-message_new"));		
?>

<style>

.container-fluid {
	overflow-x: hidden;
}	
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
		doc.addFont('KrutiDev714', 'Kruti Dev 714', 'Normal');
		doc.setFont('KrutiDev714');
		doc.addHTML($('#divtoprint'), 15, 15, {
		'background': '#fff',
		'border':'2px solid white',
		}, function() {
			doc.save('Inward-details-'+n+' '+time+'.pdf');
		});
		
		$('#donotprint').show(); 
	});
});
</script>
<script>
  $(document).ready(function() {
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
		 
	$('#rec_to_dt').datepicker('setDate', 'today');
	$('#rec_from_dt').datepicker('setDate', 'today');
	$( "#rec_from_dt" ).datepicker({ format: 'dd/mm/yyyy' });
	$( "#rec_to_dt" ).datepicker({ format: 'dd/mm/yyyy' });

	//$('#rec_from_dt').change(function() {
	var datePicker = $('#rec_from_dt').datepicker().on('changeDate', function(ev) {
		
	var rec_from_dt1 = $("#rec_from_dt").val();
	var rec_to_dt2=$("#rec_to_dt").val();
	
	var rec_from_dt = $('#rec_from_dt').datepicker('getDate');
    var rec_to_dt   = $('#rec_to_dt').datepicker('getDate');
	
	if (rec_from_dt<=rec_to_dt) {
		
		$("#stage_sample_code1").attr("disabled", false); 
	        $.ajax({
                type: "POST",
                //url: 'get_sample_code1',
				url:"<?php echo Router::url(array('controller'=>'InwardDetails','action'=>'get_sample_code1'));?>",
	               data: {rec_from_dt: rec_from_dt1,rec_to_dt:rec_to_dt2},
	               success: function (data) {
					 //console.log(data);
					if(data!=''){
						
				$("#stage_sample_code1").find('option').remove();
				$("#stage_sample_code1").append("<option value='-1'>-----Select----- </option>");
					$.each($.parseJSON(data), function (key, value) {
						//alert(data);
							
							$("#stage_sample_code1").append("<option value="+$.trim(value[0]['stage_sample_code'])+"~"+$.trim(value[0]['sample_type_code'])+">"+$.trim(value[0]['stage_sample_code'])+"</option>");
							
							//$("#sample_type_code1").append("<option value="+value[0]['stage_sample_code']+">"+value[0]['stage_sample_code']+"</option>");
							
							
							// $("#stage_sample_code1").append("<option value='" + key + "'>" + value + "</option>");
							//arrtestalloc.push(key);
						});
					}
				else
				{
					 var msg="Sample Not available !!!";
					errormsg(msg);
				}
				    
               
            }
			});
}
else {
	//
	var msg="Please select from date before To Date";
	errormsg(msg);
					
   //var date = new Date();
  //var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
	//$('#rec_from_dt').datepicker('setDate', today);
 // $('#rec_from_dt').datepicker('setDate', 'today');
  	//return false;
}
});	

	$('#rec_to_dt').datepicker().on('changeDate', function(ev) {
		var StartDate = $('#rec_from_dt').datepicker('getDate');
		var EndDate   = $('#rec_to_dt').datepicker('getDate');
		  if(StartDate > EndDate)
			{
			//alert("Please ensure that the End Date is greater than or equal to the Start Date.");
			var msg="Please ensure that To Date must be greater than From Date.";
							errormsg(msg);
							
			$('#rec_to_dt').datepicker('setDate', 'today');				
		  //$('#rec_from_dt').val("");
		 
			//return false;
			}
			else{
				$("#stage_sample_code1").prop("disabled", false);
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
		$("#close1").click(function(e) {
            e.preventDefault();
           location.href = "<?php echo $base_url;?>users/home";
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
	/* $('#rec_from_dt').datepicker().on('changeDate', function(ev) {
		var StartDate = $('#rec_from_dt').datepicker('getDate');
		var EndDate   = $('#rec_to_dt').datepicker('getDate');
		  if(StartDate <= EndDate)
			{
			//alert("Please ensure that the End Date is greater than or equal to the Start Date.");
			var msg="Please ensure that the To Date is greater than or equal to the From Date.";
							errormsg(msg);
		  //$('#rec_from_dt').val("");
		 
			//return false;
			}
		
		
	});
	 */
/* function enable_cate()
{
	//alert('hi');
	
 // var eDate = new Date(EndDate);
 // var sDate = new Date(StartDate);
 // alert(EndDate);
  
	$("#stage_sample_code1").attr("disabled", false); 
} */

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
</script>
<html>

<body>

   <?php echo "error page"; ?>
</body>

</html>