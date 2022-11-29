$(document).ready(function () { 
 
    $('#addselectbtn').hide();
    $('#flashnote').hide();
    $('#ral').hide();

    
    var count=0;
    var testCount=0;
   

    // jQuery select change event to add a row
    $("#dst_usr_cd").change(function(){

      count = $('#tbodyid tr').length+1;
      testCount = $('#TesTable tr').length+1;
      // count of 5 ral show save button
      if(count >= 5){
        if(testCount >= 2){/*add for check the count show save button 10-11-2022*/
          $('#addselectbtn').show();
          $('#flashnote').show();
         
        } 
      }else {
         $('#ral').hide();
         $('#addselectbtn').hide();
         
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
       var markup = "<tr><td class='text-center'>" + count + "</td><td id='"+locid+"' class='text-center'>" + dst_loc + "</td><td id='"+usrid+"' class='text-center'>" + dst_usr + "</td><td class='text-center'><input type='text' name='qty' required></td><td class='text-center'><button type='button' class='btn btn-danger remove'><i class='glyphicon glyphicon-remove'></i></td></tr>";
        $("#tbodyid").append(markup);
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

    /************************************************************************************************/
    //on click of add button call ajax 08/06/2022
    $("#addselectbtn").click(function() {
    
      //create array for all added rows values of two fields 09/06/2022
      var labName = [];
      var usrName = [];
      var qty = [];  /*new added 09-11-2022*/ 
    
   
      //******** send this array through data ajax to controller function 09/06/2022 ********
      var i=0;
      var checkQTy = true;
      var qtyCount = 0;

      $('#tbodyid tr').each(function(index, tr) {
        $(tr).find('td').each (function (index1, td) {
          
          if(index1==1){
            // labName[i] = $(this).html(); //for fetch labname text 
            labName[i] = $(this).attr('id'); //for fetch labname id 
         
          }
          if(index1==2){
            usrName[i] = $(this).attr('id');//for fetch usrName id
            
          }
          if(index1==3){

            if($(this).find('input').val() == ''){//check quantity is null on 15-11-2022

              checkQTy = false;
            
            }
            qty[i] = $(this).find('input').val(); //new added for fetch quantity valve 09-11-2022 
            qtyCount = qtyCount+parseInt($(this).find('input').val());//concatenate input value on 15-11-2022

           
            
          }

        });

        i++;
      
      });

      // ****************** added for check quantity is blank done by shreeya on 15-11-2022  ******************************************
      if(checkQTy == false){
        alert('Please Check Quantity For Selected Labs');
        return false;
      }
      //check total of input quantity  is greater that availavle qty 
      // done by shreeya on 15-11-2022
      if(qtyCount > $('#avai_qnt').text()){
        alert('Please Check... The Entered Quantity is exceeding the available quantity');
        return false;
      }
      // added for fetch testname id & create new array 09-11-2022 by shreeya
      var testname = [];

      var j=0;
      $('#TesTable tr').each(function(index, tr) {
        $(tr).find('td').each (function (index1, td) {
          
          if(index1==1){
            // labName[i] = $(this).html(); //for featch testname text 
            testname[j] = $(this).attr('id'); //for featch testname id 
          }

        });

        j++;
      });

      
      
      // ********  store the data using ajax on ilccontroller  ********************************
      var stage_sample_code  = $('#stage_sample_code').val();
      var sample_type        = $('#sample_type').val();

      $.ajax({

        type:'POST',
        url:'../IlcForward/save_select_list',
        data:{labName:labName,usrName:usrName,stage_sample_code:stage_sample_code,sample_type:sample_type,qty:qty,testname:testname},/* store new feild qty.testname 09-11-22*/
        async:true,
        cache:false,
        beforeSend: function (xhr) {
          xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
        },
        success: function (data) {

          alert('Successfully Added Please proceed to forward'); 
          $('#ral').show();
    
        }

      });


    });
    /****************************************************************************************************************************************************/
    /* display save button if table is blank 14/06/2022 */ 
    var getSavedList = $('#getSavedList').val();
    //alert($('#getSavedList').val());
    // when alert is undefined
    if(getSavedList != null){
      $('#addselectbtn').show();
    }

    
});

  


 











