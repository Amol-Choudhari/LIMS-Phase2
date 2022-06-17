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
