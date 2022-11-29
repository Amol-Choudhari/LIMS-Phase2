$(document).ready(function(){


	$("#stage_sample_code").change();
	
	 $("#save").click(function (e) {

		e.preventDefault();
		$("#button").val('add');
		var button=$("#button").val();
		$("#array").val(' ');
		$("#array").val(arr);

		var category_code=$("#category_code").val();
		var commodity_code=$("#commodity_code").val();
		var sample_code=$("#sample_code").val();
		var grd_standrd= $("#grd_standrd").val();
		var remark=$("#remark").val();
		var tran_date=$("#tran_date").val();
		var result_flg= $('input[name=result_flg]:checked', '#frm_final_grading').val();
		var login_timestamp=$("#login_timestamp").val();
		

		// To check sub grading value is checked or not ,
		if(commodity_code==''){
			var msg="Please Select Sample Commodity!!";
			$.alert(msg);
			return;
		}
		else if(sample_code==''){
			var msg="Please Select Sample !!";
			$.alert(msg);
			return;
		}
		else if(grd_standrd==''){
			var msg="Please Select Grade Standard !!";
			$.alert(msg);
		}/* Apply empty grade_code validation*/
		else if(remark==''){
			var msg="Please enter remark!!";
			$.alert(msg);
			return;
		}

		if(result_flg == 'F' || result_flg == 'R' || result_flg == 'N'){

		}else{
			var msg="Please Check one of the action from given!!";
			$.alert(msg);
			return;
		}

		// Add one new filed "subgrade" to add subgrading value in data array */
		$.ajax({

			type: 'POST',
			url: 'ilc-grading-by-inward',
			data: {result_flg:result_flg,remark:remark,login_timestamp:login_timestamp,
				   button:button,grd_standrd:grd_standrd,category_code:category_code,
				   commodity_code:commodity_code,sample_code:sample_code,
				   tran_date:tran_date},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {

				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween ## from response

				if(resArray.indexOf('[error]') !== -1){
					var msg =resArray.split('~');
					alert(msg[1]);
					$("#sample_code").val('');
					return;

				}else{
					$.alert({
						content: resArray,
						closeIcon: true,
						columnClass: 'm',
						onClose: function () {
							window.location = 'available_for_grading_to_inward';
						}
					});
				}
			}
		});


	});




	

});
