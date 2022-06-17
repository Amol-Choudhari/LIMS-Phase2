$(document).ready(function() {

    $('#from_date').datepicker({
        endDate: '+0d',
        autoclose: true,
        todayHighlight: true,
        format: 'dd/mm/yyyy'
    }).on('changeDate', function(selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#to_date').datepicker('setStartDate', minDate);
        $('#to_date').val('');
    });

    $('#to_date').datepicker({
        endDate: '+0d',
        autoclose: true,
        todayHighlight: true,
        format: 'dd/mm/yyyy'
    }).on('changeDate', function(selected) {
        var maxDate = new Date(selected.date.valueOf());
        $('#from_date').datepicker('setEndDate', maxDate);
        if ($('#from_date').val() == '') {
            $('#to_date').val('');
        }
    });

    //To get Category by From & To date
    $("#to_date").change(function() {
        var changedValueTo = $(this).val();
        var changedValueFrom = $('#from_date').val();
        $.ajax({
            url: '../ajax-functions/get-category-by-date-array',
            type: "POST",
            data: ({
                to_date: changedValueTo,
                from_date: changedValueFrom
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#Category')
                    .find('option')
                    .remove();
                var mySelect = $('#Category');
                mySelect.append(resp);
            }
        });
    });

    //To get Commodity by Category
    $("#Category").change(function() {
        var changedValue = $(this).val();
        $.ajax({
            url: '../ajax-functions/get-commodity-by-category-array',
            type: "POST",
            data: ({
                Category: changedValue,
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#Commodity')
                    .find('option')
                    .remove();
                var mySelect = $('#Commodity');
                mySelect.append(resp);
            }
        });
    });

    $("#close").click(function() {
        location.href = "../report/index";
    });


    $('#rpt_comm_sample').attr("target", "_blank");

    //To get Commodity by From & To date
    $("#to_date").change(function() {
        var changedValueTo = $(this).val();
        var changedValueFrom = $('#from_date').val();
        $.ajax({
            url: '../ajax-functions/get-commodity-by-date-array',
            type: "POST",
            data: ({
                to_date: changedValueTo,
                from_date: changedValueFrom
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#Commodity')
                    .find('option')
                    .remove();
                var mySelect = $('#Commodity');
                mySelect.append(resp);
            }
        });
    });

    //To get Sample code by commodity , From & To date
    $("#Commodity").change(function() {
        var changedValueCommodity = $(this).val();
        var changedValueFrom = $('#from_date').val();
        var changedValueTo = $('#to_date').val();
        var changedValueRalLabList = $('#ral_lab_list').val();
        $.ajax({
            url: '../ajax-functions/get-sample-code-by-commodity-date-array',
            type: "POST",
            data: ({
                Commodity: changedValueCommodity,
                to_date: changedValueTo,
                from_date: changedValueFrom,
                ral_lab_list: changedValueRalLabList
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#sample_code')
                    .find('option')
                    .remove();
                var mySelect = $('#sample_code');
                mySelect.append(resp);
            }
        });
    });

    //Get Ral Lab on Lab Change
    $("#lab").change(function() {
        var changedValue = $(this).val();
        $.ajax({
            url: '../ajax-functions/get-rallab-by-lab-array',
            type: "POST",
            data: ({
                lab: changedValue,
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#ral_lab_list')
                    .find('option')
                    .remove();
                var mySelect = $('#ral_lab_list');
                mySelect.append(resp);
            }
        });
    });

    //To Set Report Tilte in Session variable
    $(".labelName").click(function() {
        var labelName = $(this).text();
        $.ajax({
            type: 'POST',
            url: 'set-title-in-session',
			async:false,   
            data: {
                labelName: labelName,
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function() {}
        });
    });

    //To Set Chemist Code by From date, To date & Ral_lab
    $("#to_date").change(function() {
        var changedValueTodate = $(this).val();
        var changedValueFromdate = $('#from_date').val();
        var changedValueRalLabList = $('#ral_lab_list').val();
        $.ajax({
            url: '../ajax-functions/get-chemist-code-by-date-ral-lab-array',
            type: "POST",
            data: ({
                to_date: changedValueTodate,
                from_date: changedValueFromdate,
                ral_lab_list: changedValueRalLabList
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#chemist_code')
                    .find('option')
                    .remove();
                var mySelect = $('#chemist_code');
                mySelect.append(resp);
            }
        });

    });

    //To Set Sample Code by From date, To date & Chemist Code
    $("#chemist_code").change(function() {
        var changedValueChemist = $(this).val();
        var changedValueFromdate = $('#from_date').val();
        var changedValueTodate = $('#to_date').val();
        $.ajax({
            url: '../ajax-functions/get-sample-code-by-date-chemist-array',
            type: "POST",
            data: ({
                chemist_code: changedValueChemist,
                from_date: changedValueFromdate,
                to_date: changedValueTodate,
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#sample_code')
                    .find('option')
                    .remove();
                var mySelect = $('#sample_code');
                mySelect.append(resp);
            }
        });

    });

    //To Set Users by Changing RalLab
    $("#ral_lab_list").change(function() {
        var changedValueRal = $(this).val();
        var changedValuelab = $('#lab').val();
        $.ajax({
            url: '../ajax-functions/get-user-by-ral-lab-array',
            type: "POST",
            data: ({
                ral_lab_list: changedValueRal,
                lab: changedValuelab,
            }),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
            },
            success: function(resp) {
                $('#users')
                    .find('option')
                    .remove();
                var mySelect = $('#users');
                mySelect.append(resp);
            }
        });

    });
});

$(document).ajaxStart(function() {
    // Show image container
    $("#loading_con").show();
});
$(document).ajaxComplete(function() {
    // Hide image container
    $("#loading_con").hide();
});

//To get RalLab by Selected Lab
$('document').ready(function() {
    var lab = $("#lab").val();

    $.ajax({
        url: '../ajax-functions/get-rallab-by-lab-array',
        type: "POST",
        data: ({
            lab: lab,
        }),
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
        },
        success: function(resp) {
            $('#ral_lab_list')
                .find('option')
                .remove();
            var mySelect = $('#ral_lab_list');
            mySelect.append(resp);
            getUserList(); // This is used to get users on selected Ral Lab
        }
    });

});

//To get User List on selected Ral lab
function getUserList() {
    var ral_lab = $('#ral_lab_list').val();
    var lab = $('#lab').val();

    $.ajax({
        url: '../ajax-functions/get-user-by-ral-lab-array',
        type: "POST",
        data: ({
            lab: lab,
            ral_lab_list: ral_lab,
        }),
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
        },
        success: function(resp) {
            $('#users')
                .find('option')
                .remove();
            var mySelect = $('#users');
            mySelect.append(resp);
        }
    });
}

$(document).ready(function() {
    $('#testReport').DataTable();
});


$(document).ready(function() {
    $('#sample_type_dropdown').multiselect({
        columns: 1,
        placeholder: '---Please Select---',
        // search: true,
        selectAll: true
    });
});