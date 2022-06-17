$(document).ready(function(){
    $("#modalAbandonedCart").show();
    //$(".modal").show();

    $("#okbtn").click(function(){
          
      window.location = $("#webroot").val()+'app/proceedEvenMultipleLogin';
    });
      
    $("#cancelbtn").click(function(){
        
        window.location = '';
    });
    
  });