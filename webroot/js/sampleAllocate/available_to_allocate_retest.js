$(document).ready(function(){

  $('#avai_to_allocate_list').DataTable();
  $('#avai_to_forward_list').DataTable();
  $('#returned_by_chem_list').DataTable();

});

$("#RC_list").hide();
$("#F_list").hide();
$("#A_list").show();

$('.type').change(function(){

    var type=$(".type:checked").val();
    if(type=='A')
    {
      $("#F_list").hide();
      $("#RC_list").hide();
      $("#A_list").show();

    }else if(type=='F'){
      $("#A_list").hide();
      $("#RC_list").hide();
      $("#F_list").show();

    }else if(type=='RC'){
      $("#A_list").hide();
      $("#F_list").hide();
      $("#RC_list").show();

    }


});
