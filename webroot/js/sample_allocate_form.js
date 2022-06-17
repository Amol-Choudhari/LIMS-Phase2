

var arr=new Array();
var allarr=new Array();
var arrtestalloc=new Array();
var arrtestOrigin=new Array();

	  
	function getuserdetail_new(){
		
		var type=$("#type").val();
		$(".test_n_r").attr("disabled", true); 
		if(type=='F')
		{
			var sample_code=$("#stage_sample_code").val();
			//var alloc_to_user_code=$("#alloc_to_user_code").val();
			var test_n_r= $('input[name=test_n_r]:checked', '#frm_sample_allocate').val();
			var re_test=$('input[name=re_test]:checked', '#frm_sample_allocate').val();
			
			$.ajax({
				type: "POST",
				url: 'getuserdetail_new',
				data: {sample_code:sample_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
				
					if(resArray>0)
					{
							alert("Sorry! This sample once forwarded to same user.");
					}				   
				}
			});
		
		}
	}


	function getalloctest1()
	{
		var sample_code=$("#stage_sample_code").val();
		$("#test_select1").find('option').remove();
		$("#test_select1").append("<option value='-1' disabled>------Select----- </option>");
		$("#test_select").find('option').remove();
		$("#test_select").append("<option value='-1' disabled>------Select----- </option>");
		
		$.ajax({
				type: "POST",
				url: 'get_alloc_test1',
				data: {sample_code: sample_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
				
					if(resArray.indexOf('[error]') !== -1){
						var msg =resArray.split('~');
						alert(msg[1]); 
						return;
					}else{
						$.each(resArray, function (key, value) {
							$("#test_select1").append("<option value='" + key + "'>" + value + "</option>");
							arrtestalloc.push(key);
						});
					}
				}
			});
	}


	function getflag()
	{
		var sample_code=$("#stage_sample_code").val();
		var type=$("#type").val();
		$("#flg").empty();
		$.ajax({
			type:"POST",
			url:'get_flag',
			data:{sample_code:sample_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success:function(data){
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
					
				if(resArray.indexOf('[error]') !== -1){
					var msg =resArray.split('~');
					alert(msg[1]); 
					
					return;
					
				}else{
					$.each(resArray, function (key, value) {
						
						if(type!='F')
						{									
							$("#flg").append("  "+value['flg1']+"");
						}else{
							document.getElementById("flg").innerHTML = "";
						}
									
					});	
				}				 
			}
		});
		
	}


	function gettest(){

		var test_select= $("#test_select").val();
		var test_text= $("#test_select :selected").text();
		var selText=new Array();

		$("#test_select option:selected").each(function () {
			var $this = $(this);
			if ($this.length) {
				selText.push($this.text());
			}
		});
		
		for(var i=0;i<test_select.length;i++){
			var commodity_code_id = $("#commodity_code").val();
			var test_code_id = test_select[i];
			var test_name =	selText[i];
			
			/*check commodity grading before allocated the test to any chemist,*/
			$.ajax({
				type: "POST",
				url: 'check_commodity_grading_for_test',
				data: {commodity_code_id:commodity_code_id,test_code_id:test_code_id},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {	

					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
				
				if(resArray==1)
				{
						$("#test_select :selected").remove();
						$("#test_select1").append("<option value='" + test_code_id + "'>" + test_name + "</option>");
						arr.push(test_select[i]);
						arrtestalloc.push(test_code_id);
						if(arrtestOrigin.length<=0)
							arrtestOrigin.push(test_code_id);
						if(!arrtestOrigin.includes(test_code_id))
							arrtestOrigin.push(test_code_id);		
				}else{
					alert("Sorry! Grading is not defined for this test");
				}				   
				}
			});
			
			/**/
		}

		var test_select= $('#test_select option').length;
		if(test_select==1)
			$('#moveleft').attr('disabled',true);
		else
			$('#moveleft').attr('disabled',false);
		
		var test_select= $('#test_select1 option').length;
		if(test_select==1)
			$('#moveright').attr('disabled',true);
		else
			$('#moveright').attr('disabled',false);
		

	}



	function move_left(){

		$('#moveleft').attr('disabled',true);
		$('#moveright').attr('disabled',false);

		var test_text= $("#test_select :selected").text();
		var selText=new Array();
		var test_select=new Array();
		$("#test_select option").each(function () {
			var $this = $(this);
			if ($this.length) {

				selText.push($this.text());
				test_select.push($this.val());
			}
		});
		var arrtestalloc	= new Array();
		for(var i=1;i<test_select.length;i++){

			$("#test_select option").remove();
			$("#test_select").append("<option value='" + test_select[0] + "' disabled>" + selText[0] + "</option>");
			$("#test_select1").append("<option value='" + test_select[i] + "'>" + selText[i] + "</option>");
			arr.push(test_select[i]);
			arrtestalloc.push(test_select[i]);
			if(!arrtestOrigin.includes(test_select[i]))
				arrtestOrigin.push(test_select[i]);
			
		}

	}


	function move_right(){

		$('#moveright').attr('disabled',true);
		$('#moveleft').attr('disabled',false);

		var test_text= $("#test_select1 :selected").text();
		var selText=new Array();
		var test_select=new Array();
		$("#test_select1 option").each(function () {
			var $this = $(this);
			if ($this.length) {

				selText.push($this.text());
				test_select.push($this.val());
			}
		});
		var arrtestalloc	= new Array();
		for(var i=1;i<test_select.length;i++){
			$("#test_select1 option").remove();
			$("#test_select1").append("<option value='" + test_select[0] + "' disabled>" + selText[0] + "</option>");
			$("#test_select").append("<option value='" + test_select[i] + "'>" + selText[i] + "</option>");
			arr.push(test_select[i]);
			arrtestalloc.push(test_select[i]);
			
		}
		var length= $('#test_select1 option').length;
		if(length==1){

		}else{

		}
		arrtestOrigin.length=0;

	}



	function removetest(){
				
		var test_select= $("#test_select1").val();
		var test_text= $("#test_select1 :selected").text();
		var selText=new Array();

		$("#test_select1 option:selected").each(function () {
		var $this = $(this);
		if ($this.length) {

			selText.push($this.text());
			
		}
		});
		for(var i=0;i<test_select.length;i++){
			$("#test_select1 :selected").remove();
			$("#test_select").append("<option value='" + test_select[i] + "'>" + selText[i] + "</option>");
			arr.push(test_select[i]);
			var index = arrtestalloc.indexOf(test_select[i]);
			var index1 = arrtestOrigin.indexOf(test_select[i]);
			
			if (index > -1) {
				arrtestalloc.splice(index, 1);
				
			}
			if(arrtestOrigin.includes(test_select[i])){

				var index = arrtestOrigin.indexOf(test_select[i]);
				arrtestOrigin.splice(index, 1);
			}

		}
		var test_select= $('#test_select1 option').length;
		if(test_select==1){
			$('#moveright').attr('disabled',true);

		}else{
			$('#moveright').attr('disabled',false);

		}
		
		var test_select= $('#test_select option').length;
		if(test_select==1)
			$('#moveleft').attr('disabled',true);
		else
			$('#moveleft').attr('disabled',false);

	}



	function get_users()
	{
		$("#alloc_to_user_code").attr("disabled", false); 
		$("#alloc_to_user_code").find('option').remove();
		$("#alloc_to_user_code").append("<option value='-1'>-----Select----- </option>");
		var user_type = $("#user_type").val();
	var posted_ro_office=$("#posted_ro_office").val();
	
	$.ajax({
			type: "POST",
			url: 'get_users',
			data: {user_type: user_type,posted_ro_office:posted_ro_office},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {
				
				if(data.indexOf('[error]') !== -1){
					
					var msg =data.split('~');
					alert(msg[1]); 
					$("#user_type").val('');
					return;
					
				}else{
					$("#alloc_to_user_code").append(data);
				}
			}
		});
	}


	//to check if sample is already allocated with chemist, qty, tests and date
	function getalloctest()
	{	
		arrtestalloc=new Array();
		arrtestOrigin = new Array();
		
		$("#test_select").attr("disabled", false); 
		$("#test_select1").attr("disabled", false); 
		$("#test_alloc").attr("disabled", false); 
		$("#sample_qnt").attr("disabled", false); 
		$("#parcel_size").attr("disabled", false); 
		//arrtestalloc.length = 0;
		var sample_code=$("#stage_sample_code").val();				
		var alloc_to_user_code=$("#alloc_to_user_code").val();
		var nameuser= $("#alloc_to_user_code :selected").text();
		
		var category_code = $("#category_code").val();
		var rec_from_dt=$("#rec_from_dt").val();
		var rec_to_dt=$("#rec_to_dt").val();
		$("#labelalloc").text("Select / Unselect Tests for "+nameuser);
		
		$.ajax({
			type:"POST",
			url: 'get_details',
			data: {sample_code:sample_code,alloc_to_user_code:alloc_to_user_code},
			cache:false,
			async:false,
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success : function(data){
				
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
				
				if(resArray.indexOf('[error]') !== -1){
					var msg =resArray.split('~');
					alert(msg[1]); 
					return;
				
				}else{
					if(resArray!='NO_DATA'){
					
						resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
						
						$.each(resArray, function (key, value) {
							
							if((key=='test_n_r')&&(value=='N'))
							{
								var $radios = $('input:radio[id=test_n_r]');
								$radios.filter('[value=N]').prop('checked', true);
							}
							if((key=='test_n_r')&&(value=='R'))
							{
								var $radios = $('input:radio[id=test_n_r]');
								$radios.filter('[value=R]').prop('checked', false);
							}
							if(key=='sample_qnt')
							{
								$("#sample_qnt").val(value);
							}
							if(key=='sample_unit')
							{
								$("#sample_unit").val(value);
							}
							if(key=='expect_complt'){
								
								dteSplit = value.split("-");
								yr = dteSplit[0];
								month = dteSplit[1];
								day = dteSplit[2];
								
								var expect_complt_dt = day+"/"+month+"/"+yr;
								$("#expect_complt").val(expect_complt_dt);
								
							}
							$("#update").attr("disabled",false);
							$("#delete").attr("disabled",false);
							$("#save").attr("disabled",true);
							$("#add").attr("disabled",true);
						});
					} 
					else{

						var $radios = $('input:radio[id=test_n_r]');
						$radios.filter('[value=N]').prop('checked', false);
						var $radios = $('input:radio[id=test_n_r]');
						$radios.filter('[value=R]').prop('checked', false);
							$("#sample_qnt").val('');
							$("#update").attr("disabled",true);
							$("#delete").attr("disabled",true);
							$("#save").attr("disabled",false);
							$("#add").attr("disabled",true);
					}
				}
			}	
		});		
		
		if (category_code != "")
		{
			var commodity_code = $("#commodity_code").val();
		}
		
		$("#test_select1").find('option').remove();
		$("#test_select1").append("<option value='-1' disabled>------Select----- </option>");
		$("#test_select").find('option').remove();
		$("#test_select").append("<option value='-1' disabled>------Select----- </option>");
		
		//check, if already allocated
		$.ajax({
			type: "POST",
			url: 'get_alloc_test',
			async:false,
			data: {sample_code: sample_code, alloc_to_user_code: alloc_to_user_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
				
				if(resArray.indexOf('[error]') !== -1){
					
						var msg =resArray.split('~');
						alert(msg[1]); 
						$("#stage_sample_code").val("");
						return;
						
				}else{
					
					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
					
					$("#select_test_div").show();//if already allocated
					$.each(resArray, function (key, value) {
						$("#test_select1").append("<option value='" + value['test_code'] + "'>" + value['test_name'] + "</option>");

						arrtestOrigin.push(value['test_code']);
						
					});
				}
			}
		});

			
		$.ajax({
			type: "POST",
			url: 'get_test_by_commodity_id',
			async:false,
			data: {commodity_code: commodity_code, category_code: category_code,sample_code: sample_code, alloc_to_user_code: alloc_to_user_code},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {
				
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
				
				if(resArray.indexOf('[error]') !== -1){
					
					var msg =resArray.split('~');
					alert(msg[1]); 
					$("#stage_sample_code").val("");
					$("#commodity_code").val("");
					$("#stage_sample_code").val('');
					return;
					
				}else{
					
					$("#select_test_div").show();
					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
					var j=0;
					$.each(resArray, function (key, value) {
					$("#test_select").append("<option value='" + value['test_code'] + "'>" + value['test_name'] + "</option>");
					$("#allo_all_test_msg").text("Note: It is prefer to perform all tests from the list for selected commodity.");//this line added on 18-06-2019 by Amol
						allarr.push({ code: value['test_code'],text:value['test_name'] });
					});
					$("#all").attr("disabled", false); 
					}
				}
		});
	}


	function getchem_code()
	{
		var user_type = $("#user_type").val();
		$.ajax({
			type: "POST",
			url: 'get_chem_li_code',
			data: {user_type: user_type},
			beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
			},
			success: function (data) {
				
				var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
				
				if(resArray.indexOf('[error]') !== -1){
					var msg =resArray.split('~');
					alert(msg[1]); 
					$("#user_type").val(' ');
					return;
				
				}else{
					var chem_li_code =resArray.split('~');
					$("#chemist_code").val(chem_li_code[0]);
					$("#li_code").val(chem_li_code[1]);
					
					$('.form_spinner').hide('slow');//added on 30-05-2022 by Amol intentionally to hide loader
				}
			}
		});
	}


	function getuserdetail(){
					
		var type=$("#type").val();
		$(".test_n_r").attr("disabled", true); 
		
		if(type=='F')//for forwarding window
		{
			var sample_code=$("#stage_sample_code").val();
			var alloc_to_user_code=	$("#alloc_to_user_code").val();
			var test_n_r= $('input[name=test_n_r]:checked', '#frm_sample_allocate').val();
			var re_test=$('input[name=re_test]:checked', '#frm_sample_allocate').val();
			
			$.ajax({
				type: "POST",
				url: 'getuserdetail',
				data: {sample_code:sample_code,alloc_to_user_code:alloc_to_user_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
				
				if(resArray>0)
				{
						alert("Sorry! This sample once forwarded to same user.");
				}			   
				}
			});
		
		}
	}


	function chk_qnt(){
		//total_qnt_sample
		$(".test_n_r_n_r").attr("disabled", false); 
		$(".test_n_r").attr("disabled", false); 
		var val=$(".test_n_r").val();
				
		var $radios = $('input:radio[id=test_n_r]');
		$radios.filter('[value=N]').prop('checked', true);
		
		if(val=="undefined")
		{
			var $radios = $('input:radio[id=test_n_r]');
			$radios.filter('[value=N]').prop('checked', true);	
		}
		
		var sample_code = $("#stage_sample_code").val();
		var category_code = $("#category_code").val();
		var type=$("#type").val();
		var sample_qnt = $("#sample_qnt").val();
		var commodity_code = $("#commodity_code").val();
		$(".test_n_r").attr("disabled", false); 
		
		if(sample_qnt=="")
		{
			var msg="please enter sample quantity!!";
			alert(msg);
			return;
		}
		
		$.ajax({
				type: "POST",
				url: 'get_ttl_qnt',
				data: {sample_code:sample_code,type:type,commodity_code: commodity_code,category_code:category_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
					
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
					
					if(resArray.indexOf('[error]') !== -1){
						var msg =resArray.split('~');
						alert(msg[1]); 
						$("#stage_sample_code").val("");
						$("#category_code").val("");
						$("#commodity_code").val("");
						return;
					}else{

						var tot_qnt=parseInt(resArray);
						var tot_alloc_qnt=parseInt(sample_qnt);
						if(tot_qnt!=0){
						if(tot_qnt<tot_alloc_qnt)
							{
								var msg="  Sample quantity for allocation exceeds total quantity,Please enter valid quantity..!!!";
									alert(msg);
								$("#sample_qnt").val('');
								$("#sample_qnt").focus();
								return;
							}
						}else{
							alert("Quantity not available!");
						}
					}
				}
			});
	}



	function getqty(){
		
		$("#stage_sample_code").attr("disabled", false); 
		//$("#user_type").attr("disabled", false); 
		$("#expect_complt").attr("disabled", false); 
		$("#parel_size").attr("disabled", true); 
		
		var type=$("#type").val();
		$("#qty").empty();
		$("#unit").empty();
		
		var category_code = $("#category_code").val();
		var sample_code=$("#stage_sample_code").val();
		
		//$("#user_type").val($("#user_type option:first").val());
		//$("#alloc_to_user_code").val($("#user_type option:first").val());

		$("#sample_qnt").val("");
		
		/* remove old option value from sample unit drop down and append the sample unit of selected sample, */
		$("#sample_unit").find('option').remove();
		
		if (sample_code != "")
		{
			var commodity_code = $("#commodity_code").val();
			$.ajax({
				type: "POST",
				url: 'get_qty',
				data: {sample_code:sample_code,type:type,commodity_code: commodity_code, category_code: category_code},
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function (data) {
				
					var resArray = data.match(/#([^']+)#/)[1];//getting data bitween #..# from response
					resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON
					
					if(resArray.indexOf('[error]') !== -1){
						var msg =resArray.split('~');								
						return;
						
					}else{
						$.each(resArray, function (key, value) {
							if(type!='F')
							{
									
								if(value['sample_total_qnt']){
									$("#qty").append("Available Qty: "+value['total']+' ' +value['unit_weight']+""); //updated value['actual_received_qty'] to value['total'] on 04-05-2022
									/* append the sample unit like gram, */
									$("#sample_unit").append("<option value='"+value['parcel_size']+"'>"+value['unit_weight']+"</option>");
								}
								else if(value['sample_total_qnt']==0){
									$("#qty").append("Available Qty: "+value['total']+""); //updated value['actual_received_qty'] to value['total'] on 04-05-2022
									/* append the sample unit like gram, */
									$("#sample_unit").append("<option value='"+value['parcel_size']+"'>"+value['unit_weight']+"</option>");

								}
								else{
									$("#qty").append("Available Qty: "+value['total']+ ' ' +value['unit_weight']+""); //updated value['actual_received_qty'] to value['total'] on 04-05-2022
									/* append the sample unit like gram, */
									$("#sample_unit").append("<option value='"+value['parcel_size']+"'>"+value['unit_weight']+"</option>");
								}
							}else{
							
								document.getElementById("qty").innerHTML = "";

							}
							
						});
					}

				}
			});
		}
		else {
			var msg="Select Sample Code first!";
			//$("#user_type").val($("#user_type option:first").val());
			//$("#alloc_to_user_code").val($("#user_type option:first").val());
			$("#sample_qnt").val("");
			
			alert(msg);
		}
	}