//search application ajax call
//added on 25-10-2021 by Amol

$("#sampleSearchInPopup").hide();
$("#search_sample_btn").click(function(){

    var sample_code = $("#srch_sample_id").val();
	
	if(sample_code==''){
		$.alert("Please Enter Sample Code");
	}else{
		$.ajax({
				type: "POST",
				async:true,
				data:{sample_code:sample_code},
				url:"../AjaxFunctions/search_sample",
				beforeSend: function (xhr) { // Add this line
						xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				}, 
				success: function (response) {
					
					$("#sampleSearchInPopup").show();
					$("#srch_sample_content").html(response);
				}
		});
	}

});

$(".close").click(function(){
	
	$("#sampleSearchInPopup").hide();
    $("#srch_sample_content").html('');
				
});