
$("#stage_sample_code").change(function(e){

    if(getsampledetails()==false){
      e.preventDefault();
    }else{

        if(getuserdetail_new()==false){
           e.preventDefault();
        }else{

            if(getflag()==false){
                e.preventDefault();
             }
        }
    }
  
});

  $("#sample_type").change(function(e){
     
    if(change_user_type()==false){
        e.preventDefault();
     }

  });

  $("#user_type").change(function(e){
      if (get_users()==false) {
          e.preventDefault();
      }
  });


  $("#alloc_to_user_code").change(function(e){
      
    if(getalloctest()==false){
        e.preventDefault();
    }else{

        if(getchem_code()==false){
            e.preventDefault();
        }else{
            
            if(getuserdetail()==false){
                e.preventDefault();
            }
        }
    }
  });
  