	//call to login validations
	$('#save').click(function (e) {
		
		if (sample_details_form_validations() == false) {
			e.preventDefault();
		} else {
		$('#frm_sample_inward').submit();  
		}     
	});



function sample_details_form_validations(){

    var org_sample_code = $("#org_sample_code").val();
    var sample_type_code=$("#sample_type_code").val();
    var smpl_drwl_dt = $("#smpl_drwl_dt").val();
    var drawal_loc = $("#drawal_loc").val();
    var shop_name = $("#shop_name").val();
    var shop_address = $("#shop_address").val();
    var mnfctr_nm = $("#mnfctr_nm").val();
    var mnfctr_addr = $("#mnfctr_addr").val();
    var pckr_nm = $('#pckr_nm').val();
    var pckr_addr = $('#pckr_addr').val();
    var grade = $('#grade').val();
    var tbl = $('#tbl').val();
    var pack_size = $('#pack_size').val();
    var lot_no = $('#lot_no').val();
    var no_of_packets = $('#no_of_packets').val();
    var remark = $('#remark').val();

    var value_return = 'true';

    // Sample Code
    if(org_sample_code==""){

        $("#error_org_sample_code").show().text("Please Enter Sample Code !");
        $("#org_sample_code").addClass("is-invalid");
        $("#org_sample_code").click(function(){$("#error_org_sample_code").hide().text;$("#org_sample_code").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Sample Type
    if(sample_type_code==""){

        $("#error_sample_type_code").show().text("Please Enter Sample Type !");
        $("#sample_type_code").addClass("is-invalid");
        $("#sample_type_code").click(function(){$("#error_sample_type_code").hide().text;$("#sample_type_code").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Drawing Sample Date
    if(smpl_drwl_dt==""){

        $("#error_smpl_drwl_dt").show().text("Please Enter Drawing Sample Date !");
        $("#smpl_drwl_dt").addClass("is-invalid");
        $("#smpl_drwl_dt").click(function(){$("#error_smpl_drwl_dt").hide().text;$("#smpl_drwl_dt").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Drawing Location
    if(drawal_loc==""){

        $("#error_drawal_loc").show().text("Please Enter Drawing Location !");
        $("#drawal_loc").addClass("is-invalid");
        $("#drawal_loc").click(function(){$("#error_drawal_loc").hide().text;$("#drawal_loc").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Shop Name
    if(shop_name==""){

        $("#error_shop_name").show().text("Please Select Shop Name !");
        $("#shop_name").addClass("is-invalid");
        $("#shop_name").click(function(){$("#error_shop_name").hide().text;$("#shop_name").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Shop Address
    if(shop_address==""){

        $("#error_shop_address").show().text("Please Select Shop Address !");
        $("#shop_address").addClass("is-invalid");
        $("#shop_address").click(function(){$("#error_shop_address").hide().text;$("#shop_address").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Manufacturer Name
    if(mnfctr_nm==""){

        $("#error_mnfctr_nm").show().text("Please Select Manufacturer Name !");
        $("#mnfctr_nm").addClass("is-invalid");
        $("#mnfctr_nm").click(function(){$("#error_mnfctr_nm").hide().text;$("#mnfctr_nm").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Manufacturer Address
    if(mnfctr_addr==""){

        $("#error_mnfctr_addr").show().text("Please Select Manufacturer Address !");
        $("#mnfctr_addr").addClass("is-invalid");
        $("#mnfctr_addr").click(function(){$("#error_mnfctr_addr").hide().text;$("#mnfctr_addr").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Packer Name
    if(pckr_nm==""){

        $("#error_pckr_nm").show().text("Please Select Packer Name !");
        $("#pckr_nm").addClass("is-invalid");
        $("#pckr_nm").click(function(){$("#error_pckr_nm").hide().text;$("#pckr_nm").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Packer Address
    if(pckr_addr==""){

        $("#error_pckr_addr").show().text("Please Enter Packer Address !");
        $("#pckr_addr").addClass("is-invalid");
        $("#pckr_addr").click(function(){$("#error_pckr_addr").hide().text;$("#pckr_addr").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Grade
    if(grade==""){

        $("#error_grade").show().text("Please Select Grade !");
        $("#grade").addClass("is-invalid");
        $("#grade").click(function(){$("#error_grade").hide().text;$("#grade").removeClass("is-invalid");});
        value_return = 'false';
    }

    // TBL
    if(tbl==""){

        $("#error_tbl").show().text("Please Select TBL !");
        $("#tbl").addClass("is-invalid");
        $("#tbl").click(function(){$("#error_tbl").hide().text;$("#tbl").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Packet Size
    if(pack_size==""){

        $("#error_pack_size").show().text("Please Select Packet Size !");
        $("#pack_size").addClass("is-invalid");
        $("#pack_size").click(function(){$("#error_pack_size").hide().text;$("#pack_size").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Lot No
    if(lot_no==""){

        $("#error_lot_no").show().text("Please Enter Lot Number !");
        $("#lot_no").addClass("is-invalid");
        $("#lot_no").click(function(){$("#error_lot_no").hide().text;$("#lot_no").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Number of Packets
    if(no_of_packets==""){

        $("#error_no_of_packets").show().text("Please Enter Number of Packets !");
        $("#no_of_packets").addClass("is-invalid");
        $("#no_of_packets").click(function(){$("#error_no_of_packets").hide().text;$("#no_of_packets").removeClass("is-invalid");});
        value_return = 'false';
    }

    // Remark
    if(remark==""){

        $("#error_remark").show().text("Please Enter Remark !");
        $("#remark").addClass("is-invalid");
        $("#remark").click(function(){$("#error_remark").hide().text;$("#remark").removeClass("is-invalid");});
        value_return = 'false';
    }




    if(value_return == 'false'){

        var msg = "Please check some fields are missing or not proper.";
        renderToast('error', msg);
        return false;
    } else {
        exit();
    }

}