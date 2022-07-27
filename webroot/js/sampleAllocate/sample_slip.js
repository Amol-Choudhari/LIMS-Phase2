
    $("#sample").change();

    $("#sample").click(function(e){

        if(get_sample()==false){
            e.preventDefault();
        }
    });


    function get_sample(){

        var sample=$("#sample").val();
        $('#sample_code').find('option').remove();

        $.ajax({
            type: "POST",
            url: 'get_sample',
            data: { sample: sample },
            beforeSend: function (xhr) { // Add this line
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(data) {

                var resArray = data.match(/~([^']+)~/)[1];//getting data bitween ~..~ from response
                resArray = JSON.parse(resArray);//response is JSOn encoded to parse JSON

                if(resArray=='[error]'){
                    var msg="incorrect values";
                    alert(msg);
                }else{
                    $.each(resArray, function(key, val) {
                        $("#sample_code").append('<option value='+val['chemist_code']+'>'+val['chemist_code']+'</option>');
                    });
                }
            }
        });
    }
