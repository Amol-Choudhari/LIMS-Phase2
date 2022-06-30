$(document).ready(function(){

  //$('#payment_progess_bar').hide();
  var sample_inward_form_status=$("#sample_inward_form_status").val();
  var sample_details_form_status=$("#sample_details_form_status").val();
  var payment_details_form_status=$("#payment_details_form_status").val();
  
  //for Sample Inward form
  if(sample_inward_form_status=="saved"){

    $('#sample_inward_prog_div').removeClass('bg-red').addClass('bg-success');
    $('#sample_inward_prog_span').removeClass('glyphicon-remove-sign').addClass('glyphicon-ok-sign');

  }else{

    $('#sample_inward_prog_div').removeClass('bg-success').addClass('bg-red');
    $('#sample_inward_prog_span').removeClass('glyphicon-ok-sign').addClass('glyphicon-remove-sign');
  }

  //for Sample details form
  if(sample_details_form_status=="saved"){

    $('#sample_details_prog_div').removeClass('bg-red').addClass('bg-success');
    $('#sample_details_prog_span').removeClass('glyphicon-remove-sign').addClass('glyphicon-ok-sign');

  }else{

    $('#sample_details_prog_div').removeClass('bg-success').addClass('bg-red');
    $('#sample_details_prog_span').removeClass('glyphicon-ok-sign').addClass('glyphicon-remove-sign');
  }


  //for payment
  if(payment_details_form_status=="saved"){

    $('#payment_details_prog_div').removeClass('bg-red').addClass('bg-success');
    $('#payment_details_prog_span').removeClass('glyphicon-remove-sign').addClass('glyphicon-ok-sign');

  }else if(payment_details_form_status=="pending"){

    $('#payment_details_prog_div').removeClass('bg-success').addClass('bg-warning');
    $('#payment_details_prog_span').removeClass('glyphicon-remove-sign').addClass('glyphicon-ok-sign');

  }else if(payment_details_form_status=="confirmed"){

    $('#payment_details_prog_div').removeClass('bg-success').addClass('bg-success');
    $('#payment_details_prog_span').removeClass('glyphicon-remove-sign').addClass('glyphicon-ok-sign');

  }

});
