	

	$(function(){
		$("li a").addClass('disabled');  // to disable menubar
	})


	// Apply select2 functionality, pravin bhakare 21-12-2019
	$('#test').select2();

	$("#testclick").hide();
	//$("#testclick").attr("disabled", true);	

    $(document).ready(function () {

		$(document).keydown(function (e) {
            return (e.which || e.keyCode) != 116;
        });

		function formSuccess() {
            alert('Success!');
        }

        function formFailure() {
            alert('Failure!');
        }
		
      	/*var valid=$("#save_formula").validationEngine('attach',{

			// Name of the event triggering field validation
			validationEventTrigger: "focusout",
			promptPosition: 'inline',
			onFailure : function(){ alert('success'); }
			
        });*/ 
		
	});

	// Create functionality to get list of methods that are not present in formula table , pravin bhakare 21-12-2019
	function get_test_methods(){
		
		$("#method_code").find('option').remove();
		
		var test_name = $("#test").val();
		
		$.ajax({
			
			type: "POST",
			url: "get_test_methods_name",
			data: {test_name:test_name},
			beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
			success: function (data) {
				
				$("#method_code").append("<option value=''>--Select--</option>");													 
				$.each($.parseJSON(data), function(key, value) {

					$("#method_code").append("<option value='" + key + "'>" + value + "</option>");
				});
			}		
			
		});		
	}


 	// Function used to get string between square brackets, Done by pravin bhakare, 02-01-2020
	function get_string_between_two_characters(string){
		
		var found = [],          // an array to collect the strings that are found
		rxp = /\[(.*?)\]/g,
		str = string,
		curMatch;
		while( curMatch = rxp.exec( str ) ) {
			found.push( curMatch[1] );
		}
		
		return found;
	}
	
	// Function used to checked array1 value present in array2 or not, Done by pravin bhakare, 02-01-2020
	function check_value_in_array(arr1,arr2){
	
		var value_exists = 'yes';
		
		$.each(arr1, function(key, value) {
			
		    if(arr2.indexOf(value) == -1){
				value_exists = 'no';
			}
		});	

		return 	value_exists;
	}



	$("#start_date").change(function(){
		 $("#Save").attr("disabled", false);     
	});
	


	$("#select_test").change(function(){
		 display_record();
	});

	function display_record(){

		$("#avb tbody").find('tr').remove();
		$("#test_type").val('');
		$("#method_code").val('');
		$("#start_date").val('');
		$("#formula_text").show();
        $("#formula_text").empty();
        $("#fields").empty();
        $("#validation_range").val("");

		var test=$("#select_test").val();
		
		$("#method_code").attr("disabled", false);  
		$('#avb').show();

		$.ajax({
            type: "POST",         
            url: 'get_record',
            data:{test:test},
            beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
            success: function (data) {

				if(data==0){
					
					alert('Invalid Test Selected');		
							
				}else{
				
					var i = 1;
					$.each($.parseJSON(data), function(key, value) {

							$("#test_type").val(value['test_type_name']);
							if(value['id']){								

								var finalizeFlag	= '';

								if(value['status_flag']=='F')
									finalizeFlag = "Yes";
								else
									finalizeFlag = "No";

								$("#avb tbody").append("<tr class='cpoi' id='tr_" + value['id'] + "' testcode='" + value['test_code'] + "' testmod='"+ value['method_code'] +"' ><td>" + i + "</td><td>" + value['test_name'] + "</td><td>" + value['method_name'] + "</td><td>" + value['start_date'] + "</td><td>" +  value['test_formula1'] + "</td><td>" + finalizeFlag + "</td></tr>");
								
							}
							else{
								$("#avb tbody").append("<tr><td>Record Not Found</td></tr>");
							}

						i = i + 1;	 
					});
				}			
			}		
		});
	}



	function display_record1(){
			
		$("#avb tbody").find('tr').remove();
		$("#test_type").val('');
		$("#method_code").val('');
		$("#start_date").val('');
		$("#formula_text").show();
        $("#formula_text").empty();
        $("#fields").empty();
        $("#validation_range").val("");
		var test=$("#test").val();		
		
					   
		$("#method_code").attr("disabled", false);  
		$('#avb').show();
		
		
		$.ajax({
            type: "POST",
            url: 'get_record1', 
            data:{test:test},
            beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
            success: function (data) {
				if(data.indexOf('[error]') !== -1){
							var msg =data.split('~');
							errormsg(msg[1]); 
							return;
				}else{
			
					var i = 1;
					$.each($.parseJSON(data), function(key, value) {
							
							if(value[0]['test_type_lbl']=='Formula' )
							{								
								$("#unit").attr("disabled", false);
								
								$("#testclick").attr("disabled",false);
								$("#testclick").show();
								
							}else{
				  
			  
								$("#testclick").attr("disabled",true);
								$("#testclick").hide();			   
	   
											   
								$("#unit").attr("disabled", true);
							  
			
											  
										 
							  
							}
							 $("#test_type").val(value[0]['test_type_lbl']);
							
					});
				}
			}		
		});
	}
	
	$(".avbtable").on('click','.cpoi',function(){
		
		let test_code =  $(this).attr('testcode');
		let method_code = $(this).attr('testmod');

		$("#select_test").val(test_code);
		$("#method_code").val(method_code);

		get_test_field_new();


	});


    function test()
    {
	    var val1=$("#textareaid").val();
	    var pattern=new RegExp('[*%+-]+[*%+-]');
	    var res=pattern.test(val1);
	    var rx1 = /\[([^\]]+)]/;
		var rx2 = /\(([^)]+)\)/;
		var rx3 = /{([^}]+)}/;
	    return res;
    }


    $('#savebtn').click(function(){
		
		var formulaString = $("#textareaid").val();
		
		if(formulaString != ''){
			
			//Get list of formula test field name from input formula
			var fieldsArray1 = get_string_between_two_characters(formulaString);
			
			//Get list of formula test field name from defined master table
			var fieldsArray2 = [];
			$("#fields li").each(function() { fieldsArray2.push($(this).text()) });
	  
		  
											   
				   
					
			//Checked input formula test fields are same as defined test formula fields in master table 		
			var compare_result = check_value_in_array(fieldsArray1,fieldsArray2);
			
			if(compare_result == 'yes'){
				
				var type=$("#type").val();
				if(type=='f')
				{
						var validation=$("#validation_range").val();
						var formula=$("#formula").val();

						if(validation!="" && formula!="")
						{
							$("#save_formula").submit();

						}else if(formula==""){

							alert("please enter Formula first");
							
						}
						else{

							alert("please enter validatiopn range");
						 }
				}
				else{
				 $("#save_formula").submit();	
				}	
				
			}else{
				
				alert("Formula fields not matched with test fields");
			}	
			
		}else{
			alert("Please enter Formula first");
		}	

	});



	$("#add").click(function () {

		document.cookie = "type=add";
		$("#type").val("add");
		$("#add_div").show();
		$('#avb').show();
		$("#delete_div").hide();
		$("#update_div").hide();
	});


	$("#update").click(function () {
	 
		document.cookie = "type=update";
		 $("#type").val("update");
		$("#add_div").hide();
		$("#delete_div").hide();
		$("#update_div").show();
		$('#avb').show();
	});

	$("#delete").click(function () {

		$("#type").val("delete");
		$("#add_div").hide();
		$("#delete_div").show();
		$("#update_div").hide();
		$('#avb').show();
	});






	$("#Cancel").click(function(){

       
		    var val1=$('#textareaid').val();
			var isDisabled = $('#textareaid').prop('disabled');
		    var val2=$('#formula').val();

		    if(val1=="" || val2=="" || isDisabled)
		    {
		        //location.reload();
				 $('form#save_formula')[0].reset();
				 $('form#save_formula')[0].reset();
					  $("#field_div").hide();
					  $("#operator_div").hide();    
		              $("#formula_label").hide();                                        
		              $("#formula_text").hide();
					  $("#avb").hide();
					  $("#test").attr("disabled", false);
					  $("#method_code").attr("disabled", true);  
		    }
		    else
		    {
				 BootstrapDialog.confirm("You have unsave work,do you want continue exit anyway?", function(result){
		        
		        if(result)
		        {
					//location.reload();
					 $('form#save_formula')[0].reset();
					  $("#field_div").hide();
					  $("#operator_div").hide();    
		              $("#formula_label").hide();                                        
		              $("#formula_text").hide();
					  $("#avb").hide();
					    $("#test").attr("disabled", false);
						$("#method_code").attr("disabled", true);  
		        }
				 });
		    }

        // location.relaod;
    });



    $("#Close").click(function(){
       
	    var val1=$('#textareaid').val();
	    var val2=$('#formula').val();

	    if(val1=="" || val2=="")
	    {
	        window.location.href = "<?php echo $home_url;?>";
	    }
	    else
	    {
			BootstrapDialog.confirm("You have unsave work,do you want continue exit anyway?", function(result){
				if(result)
				{
					window.location.href = "<?php echo $home_url;?>";
				}
			});
	    }  // location.relaod;

    });
  


    $(document).ready(function () {
            
		$('#datePicker').datepicker({
			endDate: '+0d',    
			autoclose: true,
			todayHighlight: true,
			format: 'dd/mm/yyyy'
        });	

		$('#datePicker1').datepicker({			
			autoclose: true, 
            todayHighlight: true,			  
            format: 'dd/mm/yyyy'
        });	

			
        document.cookie = "type=add";
        initMenu();
           
        
		$("#finalize").hide();

      	$("#finalize").click(function(e)
		{
			e.preventDefault();
			BootstrapDialog.confirm("Do u want to finalize formula?", function(result){						
	
				if(result)
				{
					//alert("hello");
		        	var test_code=$("#test").val();
					var method_code=$("#method_code").val();

		       		 $.ajax({

			            type: "POST",
			            url: 'finalize_formula',
			            data:{test_code:test_code,method_code:method_code},
			            beforeSend: function (xhr) { // Add this line
				          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				    	},					            
			            success: function (data) {
							
				            if(data=='1')
							{
								var msg="Formula has been finalized";
								errormsg(msg);
									
								$('form#save_formula')[0].reset();
							  	$("#field_div").hide();
							 	$("#operator_div").hide();    
				            	$("#formula_label").hide();                                        
				            	$("#formula_text").hide();
							 	$("#avb").hide();
							    $("#test").attr("disabled", false);
								$("#method_code").attr("disabled", true);  

							}
							else{

								var msg="Error in formula finalization";
								errormsg(msg);
								$('form#save_formula')[0].reset();
							  	$("#field_div").hide();
							  	$("#operator_div").hide();    
				              	$("#formula_label").hide();                                        
				              	$("#formula_text").hide();
							  	$("#avb").hide();
							    $("#test").attr("disabled", false);
								$("#method_code").attr("disabled", true);  
									
							}   
	    				}
	    			});

				}
			});
			
		}); 
    });







    function delete_row()
    {
        var table = document.getElementById('form1');
    	var rowCount = table.rows.length;
   
		if(rowCount>0)
		{
		    table.deleteRow(rowCount-1);
		}
		if(rowCount==1)
		{
		    $("#formula").text("");
		}

 		var table1 = document.getElementById('form2');
    	var rowCount1 = table1.rows.length;
    
		if(rowCount1>0)
		{
		    table1.deleteRow(rowCount1-1);
		}
		if(rowCount1==1)
		{
		    $("#formula1").text("");
		}

	    display();
	    display1();
    }


    function add(key,val)
    {
     	var txt = $.trim($("#formula").text());
  		var box = $("#formula");
   		box.val(box.val() + val);
        display();
   		display1();
    
	}



	function display()
	{
	    var str="";
	    var table = document.getElementById('form1'),
	    rows = table.rows, 
	    rowcount = rows.length, r,
	    cells, cellcount, c, cell;

		for( r=0; r<rowcount; r++) {
		    cells = rows[r].cells;
		    cellcount = cells.length;
		    for( c=0; c<cellcount; c++) {
		        cell= cells[c];
		        str+=cell.innerHTML;		        
		        // now do something.
		    }
		    
		    $("#formula").text(str);

		}
	}



	function display1()
	{
	    var str="";
	    var table = document.getElementById('form2'),
	    rows = table.rows, rowcount = rows.length, r,
	    cells, cellcount, c, cell;

		for( r=0; r<rowcount; r++) {
		    cells = rows[r].cells;
		    cellcount = cells.length;
		    
		    for( c=0; c<cellcount; c++) {
		        cell= cells[c];
		        str+=cell.innerHTML;		        
		        // now do something.
		    }
	    
	    	$("#formula1").text(str);

		}
             //someFunction();
    }
		

	$(document).on('click','#testclick',function(){		

		var formula	= $("#textareaid").val();

		if(formula==''){
			errormsg("Please enter formula first!");
			return;
		}

		var isValidFormula	= parenthesesAreBalanced(formula); 
		var test_code		= $("#select_test").val();
		var formulaStr		= '';

		if(isValidFormula){

			$("#textareaid").css("text-decoration: none");
			$.ajax({
				type: "POST",
				url: 'test_formula',
				data:{formula:formula,test_code:test_code},
				beforeSend: function (xhr) { // Add this line
		          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		    	},
				success: function (data) {
					var array 		= data.split("~");
					formulaStr		= array[0];
					var fieldCount	= array[1];
					$("#input_parameter_text").empty();
					var arrFields  	= array[2].split("^");
					var arrAlfa  	= array[3].split("^");
					$('#myModal').modal('show');
					
					for(var a=1;a<arrFields.length;a++){
					
						$("#input_parameter_text").append('<div class="form-group row ml-2 mr-2"><div class="col-md-7 text-right"><label class="control-label" for="">' + arrFields[a] + '</label></div><div class="col-md-5"><input type="text" class="form-control input" id="'+arrAlfa[a]+'" placeholder="'+arrFields[a]+'"  name="'+arrAlfa[a]+'"   required/></div></div>');
					}
					
					$("#input_parameter_text").append('<div class="form-group row ml-2 mr-2"><div class="col-md-7 text-right"><label class="control-label" for="">Result</label></div><div class="col-md-5"><input type="text" class="form-control input" id="result" placeholder="Result" disabled /></div></div>');
					
					$("#calculate").click("on", function(e) {
						var string 		= '';
						var i			= 0;				
						while (i < formulaStr.length) {					
							var letters = /^[A-Za-z]+$/;
							if (formulaStr[i].match(letters)) {
								var val = parseFloat($("#" + formulaStr[i]).val());
								string 	= string + val;
							} else {
								string	= string + formulaStr[i];
							}
							i++;
						}
						var result = eval(string);
						$("#result").val(result);
					});
				}
			});	

		}else{
			$("#textareaid").css("text-decoration: underline dotted red;");
			alert("Missing brackets in your formula!");
		}
	});


	function parenthesesAreBalanced(string) {

	  	var parentheses = "[]{}()",
	    stack = [],
	    i, character, bracePosition;

		for(i = 0; character = string[i]; i++) {

		    bracePosition = parentheses.indexOf(character);

		    if(bracePosition === -1) {
		      continue;
		    }

		    if(bracePosition % 2 === 0) {
		      stack.push(bracePosition + 1); // push next expected brace position
		    } else {
		      if(stack.length === 0 || stack.pop() !== bracePosition) {
		        return false;
		      }
		    }
		}

	  return stack.length === 0;
	}


	$(document).on('click',".clearAlll",function(){
		document.getElementById('textareaid').value='';
	});   


	$(document).on('click',".getbackclk",function(){

		var inputString = $('#textareaid').val();
		var shortenedString = inputString.substr(0,(inputString.length -1));
		$('#textareaid').val(shortenedString);
	}); 


	$(document).on('click',".calcuval",function(){	
		
		var areaId = 'textareaid';		

		if($(this).attr('lival')){

			var text = $(this).attr('lival');

		}else{

			var text = $(this).text();
		}

   		var lastChar = text.substr(text.length - 1);

	    if(lastChar=="]")
	    {
			text="["+text;
	    }

        var txtarea = document.getElementById(areaId);
        if (!txtarea) { return; }

        var scrollPos = txtarea.scrollTop;
        var strPos = 0;
        var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
            "ff" : (document.selection ? "ie" : false ) );

        if (br == "ie") {
            txtarea.focus();
            var range = document.selection.createRange();
            range.moveStart ('character', -txtarea.value.length);
            strPos = range.text.length;
        } else if (br == "ff") {
            strPos = txtarea.selectionStart;
        }

        var front = (txtarea.value).substring(0, strPos);
        var back = (txtarea.value).substring(strPos, txtarea.value.length);
        txtarea.value = front + text + back;
        strPos = strPos + text.length;

        if (br == "ie") {
            txtarea.focus();
            var ieRange = document.selection.createRange();
            ieRange.moveStart ('character', -txtarea.value.length);
            ieRange.moveStart ('character', strPos);
            ieRange.moveEnd ('character', 0);
            ieRange.select();
        } else if (br == "ff") {
            txtarea.selectionStart = strPos;
            txtarea.selectionEnd = strPos;
            txtarea.focus();
        }

        txtarea.scrollTop = scrollPos;
        str=$("#textareaid").val();
        words = str.match(/[^[\]]+(?=])/g);        
    
    });


    $("#method_code").change(function(){

    	get_test_field_new();
    })

	function get_test_field_new()
	{ 
		
		$("#formula").remove();	
		$("#formula_label1").remove();
		$("#start_date").val("");
		$("#formula_text").find('textarea').remove();
		$("#type").val("");	
		$("#fields").empty();
		$("#validation_range").val("");

		var test_code = $("#select_test").val();
		var method_code = $("#method_code").val();

		$("#type").val("");
		$("#test_code").val(test_code);

		var type1="";
		var test_select=test_code;
   
		if(test_code =="")
		{		  
			alert("please select test first");
		}else{
		
			$.ajax({
				type: "POST",
				url: 'get_formula',                   
				data: {test_select: test_select,method_code:method_code},
				beforeSend: function (xhr) { // Add this line
		          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
		    	},
				success: function (data) {  
					
					var objLength = $.parseJSON(data).length;
					 
					$("#Save").attr("disabled", false);
					$("#finalize").attr("disabled", false);
					$("#type").val("f");					
					var i=1;
					var loopCount=0;
					$("#field_div").show();
					$("#validation_range").prop('disabled', false);
									
					$.each($.parseJSON(data), function (key1, value1) {
							
							var key = value1['test_type_name'];
						
							if(key=="Formula"){
								
								$("#testclick").attr("disabled", false);
								$("#testclick").show();
								$("#operator_div").show();    
								$("#formula_label").show();                                        
								$("#formula_text").append(' <div class="col-md-12" > <textarea name="formula" class="form-control validate[required]"id="textareaid"rows="5" cols="80"> </textarea></div>');
								$("#validation_range").prop('disabled', false);
							
								$("#type").val("f");                                          
								if(value1['test_formula1']!=null)
								{	
									$("#textareaid").val(value1['test_formula1']);
									$("#validation_range").val(value1['res_validation_range']);
								}                        
								
								var formulaValue = value1['test_formula1'];

								if(formulaValue!=null)
								{										
									$.ajax({
										
										type: "POST",
										url: 'get_formula_status',											 
										data: {test_select: test_select,method_code:method_code},
										beforeSend: function (xhr) { // Add this line
								          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
								    	},
										success: function (data) {											 
											if(data<1)
											{
											
												$("#finalize").show(); 
												$("#textareaid").attr("disabled", false);
												$("#Save").attr("disabled", false);
												$("#validation_range").attr("disabled", false);
											
											}else{
											  
												$("#finalize").hide();  
										  
										
		
												$("#textareaid").attr("disabled", true);
												$("#field_div").hide();
												$("#operator_div").hide();
												$("#Save").attr("disabled", true);
												$("#validation_range").attr("disabled", true);
											}
											get_method(test_select,method_code);
										}
									});
								}else{
									get_method(test_select,method_code);
								}
								
							}else if(key=="SV"){
								
								$("#validation_range").attr("disabled", true);									   
								$("#type").val("s");
								$("#field_div").hide();
								$("#formula_label").hide();
								$("#operator_div").hide();    
								$("#formula_text").append(' <label class="control-label col-md-4" id="formula_label1" for="commodity_name">Name Of Label</label><div class="col-md-6" > <input type="text" class="form-control " id="formula" name="formula"/></div>');
								get_method(test_select,method_code);
								//get_fields(test_select);
								
							}else if(key=="RT"){
								
								$("#validation_range").attr("disabled", true);									  
								$("#type").val("r");
								$("#field_div").hide();
								$("#formula_label").hide();
								$("#operator_div").hide();    
								$("#formula_text").append(' <label class="control-label col-md-4" id="formula_label1" for="commodity_name">Name Of Label</label><div class="col-md-6" > <input type="text" class="form-control " id="formula" name="formula"/></div>');
								get_method(test_select,method_code);
								//get_fields(test_select);
				
							}else if(key=="PN"){
						  
								$("#type").val("p");
								$("#field_div").hide();
								$("#formula_label").hide();
								$("#operator_div").hide(); 
								$("#textareaid").hide();							  
								$("#validation_range").attr("disabled", true);	
								get_method(test_select,method_code);
								//get_fields(test_select);	

							}else if(key=="PA"){
								 
								$("#validation_range").attr("disabled", true);
								$("#type").val("PA");
								$("#field_div").hide();
								$("#formula_label").hide();
								$("#operator_div").hide(); 
								$("#textareaid").hide();							  
								$("#validation_range").attr("disabled", true);	
								get_method(test_select,method_code);
								//get_fields(test_select);

							}else if(key=="YN"){
																	   
								$("#type").val("y");
								$("#field_div").hide();
								$("#formula_label").hide();
								$("#operator_div").hide();
								$("#textareaid").hide();							  
								$("#validation_range").attr("disabled", true);
								get_method(test_select,method_code);
							}
					 
						//})

						loopCount++;

					});

					if(loopCount==objLength){
						//get_fields(test_select);
					}	
				}
	   
			});			
		}	
	}
	        
        
 	function get_fields(test_select)
	{
		var test_code=test_select;

		$.ajax({
	        type: "POST",
	        url: 'get_test_parameter',
	        data: {test_select:test_select},
	        beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
	        success: function (data) {
	            var i=1;
	            var j=0;				

		        $.each($.parseJSON(data), function (key, value) {

		           	var classVal='';
					if(j%2==0)
						classVal='odd';
					else
						classVal='even';


		            var test_type=$("#type").val();
		           
					
		            if(test_type=="f")
		            {		                

		            	var value1=value+"]";		            
		            	var trim_val=$.trim(value);
		           		$("#fields").append("<li value='" + key + "' class='"+classVal+" ovr calcuval' lival='"+value1+"'>" + value + "</li>");
		           		
		           	}
		            
		            if(test_type=="s" || test_type=="r"){
						
				            $("#formula").val(value);

							if(value!=null)
							{
								$.ajax({
									type: "POST",
									url: 'get_formula_status',
									data: {test_select: test_select},
									beforeSend: function (xhr) { // Add this line
							          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
							    	},
							 		success: function (data) {
									
										 if(data<1)
										 {
											 $("#finalize").show(); 
											 $("#formula").attr("disabled", false);
										 }
										 else{
											 $("#finalize").hide();  
											$("#formula").attr("disabled", true);
											//$("#Save").attr("disabled", true);
											$("#field_div").hide();
											$("#operator_div").hide();
										 }
								 	}
								});
							}
						}

						j++;
					
				});	

				
	        }
    	});  
	}
	

	function formatDate (input) {

		var d = new Date(input);		 
		var curr_date = d.getDate();
		var curr_month = d.getMonth() + 1; //Months are zero based
		var curr_year = d.getFullYear();
		return curr_date + "/" + curr_month + "/" + curr_year;
	} 


	function get_method(test_select,method_code)
	{
		var test_code=test_select;
		//var method_code = $("#method_code").val();
					  
		$.ajax({
            type: "POST",
            url: 'get_method',
            data: {test_select:test_select,method_code:method_code},
            beforeSend: function (xhr) { // Add this line
	          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
	    	},
            success: function (data) {
				
				if(data.indexOf('[error]') !== -1){
					var msg =data.split('~');
					errormsg(msg[1]); 
					return;
					}else{
               			var i=1;
				
					$.each($.parseJSON(data), function (key, value) {
						
						//console.log(data);							
						 //	var start_date=formatDate(value[0]['start_date']);
						//	var end_date=formatDate(value[0]['end_date']);
						$("#method_code").val(value['method_code']);
						$("#unit").val(value['unit']);
						$("#start_date").val(value['start_date']);
						//$("#end_date").val(end_date); 
					});											
				}		

				get_fields(test_select);
			}
		});
	}

	$(document).ready(function () {
        $("#Save").prop("disabled",true);
      
    });





