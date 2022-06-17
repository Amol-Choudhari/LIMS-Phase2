$(document).ready(function(){

  $('#accepted_samples_list').DataTable();

  $('#stage_sample_code').change(function(){

    var sample_code = $('#stage_sample_code').val();
    $('#generate_letter_btn').attr('href','../SampleAccept/frd_letter_pdf/'+sample_code);
  });

  $('#stage_sample_code').change();

});
