$(document).ready(function(){


    var homocategory = $('#homocategory').val();
    var homocommodity = $('#homocommodity').val();

    if(homocategory != '' && homocommodity != ''){
      getHomogenizationFields();
    }

    function getCommodity(){

      var csrfToken = $('[name="_csrfToken"]').val();
    	var default_option = "<option value=''>--Select--</option>"
    	var categoryCode = $('#category').val();
    	$('#commodity').html(default_option);

    	$.ajax({
    		type: 'POST',
    		url: 'get_commodity_fields',
    		data: {'category_code': categoryCode},
    		beforeSend: function (xhr) {
    				xhr.setRequestHeader('X-CSRF-Token', csrfToken);
    		},
    		success: function(data) {

    			$('#commodity').append(data);

    		}
    	});

    }


  $("#category").change(function(e){

  	if(getCommodity()==false){
  		e.preventDefault();
  	}

  });


  $("#commodity").change(function(e){
    getHomogenizationFields();
  });

});


function getHomogenizationFields(){
  
    var csrfToken = $('[name="_csrfToken"]').val();
    var categoryCode = $('#category').val();
    var commodityCode = $('#commodity').val();

    if(categoryCode != '' && commodityCode != ''){
      console.log('hello3'); console.log(categoryCode); console.log(commodityCode);
      $.ajax({
        type: 'POST',
        url: 'get_homogenization_fields',
        data: {'category_code': categoryCode,'commodity_code': commodityCode},
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
        success: function(data) {

            let objdata =   JSON.parse(data);

            $.each(objdata,function(key,value){
                $("#"+value).attr({'checked':true,'disabled':true});
            });
        }
      });

    }else{
      alert("Select Category and Commodity First");
    }
}
