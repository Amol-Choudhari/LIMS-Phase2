$(document).ready(function(){

  $('#allocated_sample_table').DataTable();
  $('#forwarded_sample_table').DataTable();
  $('#avai_to_appr_results').DataTable();
  $('#avai_to_verify').DataTable();
  $('#ilc_avai_to_verify').DataTable();
  

});

$("#F_list").hide();
$("#A_list").show();

$('.type').change(function(){

    var type=$(".type:checked").val();
    if(type=='A')
    {
      $("#F_list").hide();
      $("#A_list").show();

    }else{
      $("#A_list").hide();
      $("#F_list").show();

    }


});


// added for ilc flow done 07-07-2022 by shreeya

$('#ILC_list').hide();
$('#ALL_list').show();

$('.type').change(function(){

  var type=$(".type:checked").val();
      if(type=='all')
      {
        $('#ILC_list').hide();
        $('#ALL_list').show();

      }else if(type=='ilc'){
        $('#ALL_list').hide();
        $('#ILC_list').show();

      }

});