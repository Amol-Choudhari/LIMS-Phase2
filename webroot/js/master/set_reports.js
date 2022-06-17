$(document).ready(function(){

	$('#setreportrecord').DataTable();

	$('#reportnames').multiselect({
		includeSelectAllOption: true,
		placeholder :'Select Report',
		buttonWidth: '100%',
		maxHeight: 400,
	});

	$(".ms-options").addClass('mss');


	$(document).on('change','#reportcategory',function(){

		let userrole = $('#userrole').val();
		let reportid = $('#reportcategory').val();

		if(userrole != '' && reportid != ''){	

			$.ajax({

				type:'POST',
				url:'get-set-report-names',
				data:{userrole:userrole,reportid:reportid},
				beforeSend: function (xhr) { // Add this line
		          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		    	},
		    	success: function (data){

		    		if(data!=0){		    			
		    			$(".mss").html(data);
		    			
		    		}

		    		alreadysetreports();
		    	}

			});

		}else{

			$.alert('User role or Report Category options not selectd');
		}

	});
		

});


function alreadysetreports(){

	let userrole = $('#userrole').val();
	let reportid = $('#reportcategory').val();

	$.ajax({

		type:'POST',
		url:'get-already-set-reports',
		data:{userrole:userrole,reportid:reportid},
		beforeSend: function (xhr) { // Add this line
          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
    	},
    	success: function (data){
    		
    		$("#setreports").empty();

    		let setreporttables = "<table class='table table-striped' id='setreportrecord'>";
    			setreporttables += "<thead><tr><th class='w-25-p'>Sr.No</th><th>Assigned Reports</th></tr></thead>";
    			setreporttables += "<tbody>";

    			if(data != '[]'){

    				let i = 1;
    				let response = $.parseJSON(data);	

    				$.each(response,function(key,value){

    					setreporttables += "<tr><td>"+i+"</td><td>"+value.report_desc+"</td></tr>";

    					i++;
    				});
    			}

    			setreporttables += "</tbody></table>";
    			
    			$("#setreports").html(setreporttables);

    			$('#setreportrecord').DataTable();
    		
    	}

	});

}