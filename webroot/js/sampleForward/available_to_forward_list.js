$(document).ready(function(){

  $('#pages_list_table').DataTable();
  $('#forwarded_samples_list').DataTable();
  $('#avai_to_accpt_list').DataTable();

  // For date picker option
  $('#publish_date').datepicker({
    format: "dd/mm/yyyy"
  });

  $('#end_date').datepicker({
    format: "dd/mm/yyyy"
  });

});


//available for forwarded list ILC 04-07-2022
//after click the radio button
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