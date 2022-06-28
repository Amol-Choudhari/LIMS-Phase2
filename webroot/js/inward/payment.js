   
    let all_section_status = $('#all_section_status_id').val();
    let final_submit_status = $('#final_submit_status_id').val();
    let payment_confirmation_status = $('#payment_confirmation_status_id').val();

    if(all_section_status == 1 && (final_submit_status == 'no_final_submit' || final_submit_status == 'referred_back')){ 
        $("#final_submit_btn").css('display','block');
    }

    if(final_submit_status != 'no_final_submit'){ 

        $("#form_outer_main :input").prop("disabled", true);
        $("#form_outer_main :input[type='radio']").prop("disabled", true);
        $("#form_outer_main :input[type='select']").prop("disabled", true);
    }


    if(payment_confirmation_status == 'not_confirmed'){

        $("#form_outer_main :input").prop("disabled", false);
        $("#form_outer_main :input[type='radio']").prop("disabled", false);
        $("#form_outer_main :input[type='select']").prop("disabled", false);
        $("#not_confirmed_reason").show();

    }