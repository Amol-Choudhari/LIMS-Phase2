$(document).ready(function () { 
 
    $('#addselectbtn').hide();
    $('#flashnote').hide();
    $('#ral').attr('disabled',false);

    var count=0;
    // jQuery select change event to add a row
    $("#dst_usr_cd").change(function(){
    
      count = $('#tbodyid tr').length+1;
      // count of 5 ral show save button
      if (count >= 5) {
        $('#ral').attr('disabled',false);
        $('#addselectbtn').show();
        $('#flashnote').show();
      }else{
        $('#ral').attr('disabled',true);
      }
      
      var dst_loc = $('#dst_loc_id option:selected').text();
      var dst_usr = $('#dst_usr_cd option:selected').text();
      var locid = $('#dst_loc_id').val();
      var usrid = $('#dst_usr_cd').val();

      /* check duplicate records in table done 15/06/2022 by shreeya*/
      //  duplicate validation for dyanamic select option 
      var checkralexit = '';
      $('#tbodyid tr').each(function(index, tr) {
        $(tr).find('td').each (function (index1, td) {

          if (locid == $(this).attr('id')) {
            alert('This inward is already selected');
            checkralexit = "yes";
            return false;
          }
          
        });

      });
      if (checkralexit == '') {
       var markup = "<tr><td class='text-center'>" + count + "</td><td id='"+locid+"' class='text-center'>" + dst_loc + "</td><td id='"+usrid+"' class='text-center'>" + dst_usr + "</td><td class='text-center'><button type='button' class='btn btn-danger remove'><i class='glyphicon glyphicon-remove'></i></td></tr>";
        $("table tbody").append(markup);
      }
     
 
    });
   

    // jQuery button click event to remove a row.
    $('#tbodyid').on('click', '.remove', function () {

      // Removing the current row.
      $(this).closest('tr').remove();

      count = $('#tbodyid tr').length+1;
      if (count <= 5) {
        $('#ral').attr('disabled',true);
        $('#addselectbtn').hide();
        $('#flashnote').hide();
      }else{
        $('#ral').attr('disabled',false);
      }
      
    });


    //on click of add button call ajax 08/06/2022
    $("#addselectbtn").click(function() {
   
      //create array for all added rows values of two fields 09/06/2022
      var labName = [];
      var usrName = [];
    
      //send this array through data ajax to controller function 09/06/2022
      var i=0;
      $('#tbodyid tr').each(function(index, tr) {
        $(tr).find('td').each (function (index1, td) {
          
          if(index1==1){
            // labName[i] = $(this).html(); //for featch labname text 
            labName[i] = $(this).attr('id'); //for featch labname id 
          }
          if(index1==2){
            usrName[i] = $(this).attr('id');//for featch usrName id
          }
      
        });

        i++;
      });
      
      // store the data using ajax on ilccontroller
      var stage_sample_code = $('#stage_sample_code').val();
      var sample_type = $('#sample_type').val();
     
      $.ajax({

        type:'POST',
        url:'../IlcForward/save_select_list',
        data:{labName:labName,usrName:usrName,stage_sample_code:stage_sample_code,sample_type:sample_type},
        async:true,
        cache:false,
        beforeSend: function (xhr) {
          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
        },
        success: function (data) {
          alert('Successfully Added Please proceed to forward'); 
          // window.location("sample-forward");
        },
        
       
      });


    });

    /* display save button if table is blank 14/06/2022 */ 
    var getSavedList = $('#getSavedList').val();
    // when alert is undefined
    if(getSavedList != null){
      $('#addselectbtn').show();
    }

   
  
});

  


 











