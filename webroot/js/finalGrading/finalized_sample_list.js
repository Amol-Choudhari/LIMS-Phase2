
$(document).ready(function(){

	$("#save").hide();

    // check zscore is not empty
    var zscore=$("#zscore").val();
    if(zscore!=''){
        $("#save").show();
    }
    
    $("#save_zscore").submit(function(e) {
    e.preventDefault(); 
    });



	$('#finalized_samples_list').DataTable({"ordering": false});	

});








