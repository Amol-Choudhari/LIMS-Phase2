
$(document).ready(function(){

	$("#frdoic").hide();

    // check zscore is not empty
    var zscore=$("#zscore").val();
    if(zscore!=''){
        $("#frdoic").show();
    }
    
    $("#save_zscore").submit(function(e) {
    e.preventDefault(); 
    });



	$('#finalized_samples_list').DataTable({"ordering": false});	

});




//above query added for validation of calculate zscore value by 21-07-2022 done shreeya

// $("#Eoutlire").hide();/*ilc z score eliminated outer*/
 

// var minLength = -2;
// var maxLength = 2;
// $(document).ready(function(){


//     $('#zscore').on('keydown keyup change', function(){
      
//         if($(this).val() > 2 || $(this).val() < -2){
//             $('#warning-message').text('Length is less than '+minLength+' or greater than '+maxLength+' ');
//             $("#Eoutlire").show();
//         }
// 		else{
//             $('#warning-message').text(' The Results Are Matched');
//         }
//     });
// });






