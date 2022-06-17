
/* common input validations, added on 2020-09-11 by Aniket */

$(document).ready(function(){

	// allow only aplhabetical characters
	$('.txtOnly').on('keypress', function(e){
		var k;
		document.all ? k = e.keyCode : k = e.which;
		if((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32){

			var xn = $(this).attr('id');
			$('#error_' + xn).html('');
			$(this).removeClass('invalid-fld');
			$(this).addClass('valid-fld');

		} else {

			var xn = $(this).attr('id');
			$('#error_' + xn).html('Only alphabets allowed');
			$(this).removeClass('valid-fld');
			$(this).addClass('invalid-fld');
			e.preventDefault();

		}
	});


	$('.txtOnly').on('paste', (e) => {
	    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
	    if(/^[a-zA-Z- ]*$/.test(text) == false) {

			var xn = e.target.id;
			$('#error_' + xn).html('Only alphabets allowed');
			$('#'+xn).removeClass('valid-fld');
			$('#'+xn).addClass('invalid-fld');
			e.preventDefault();

	    } else {

			var xn = e.target.id;
			$('#error_' + xn).html('');
			$('#'+xn).removeClass('invalid-fld');
			$('#'+xn).addClass('valid-fld');

	    }
	});


	// restrict special characters
	$('.hindiFont').on('keypress', function(e){
		var k;
		document.all ? k = e.keyCode : k = e.which;
		var xv = e.target.value;
		if((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57)){

			var xn = $(this).attr('id');
			$('#error_' + xn).html('');
			$(this).removeClass('invalid-fld');
			$(this).addClass('valid-fld');

		} 
	});

	$('.hindiFont').on('paste', (e) => {
	    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
	    if((/^[\u0900-\u097F]+$/g.test(text) == true)) {

			var xn = e.target.id;
			$('#error_' + xn).html('');
			$('#'+xn).removeClass('invalid-fld');
			$('#'+xn).addClass('valid-fld');


	    } else if((/^[a-zA-Z0-9- ]+$/g.test(text) == true)) {


			var xn = e.target.id;
			$('#error_' + xn).html('');
			$('#'+xn).removeClass('invalid-fld');
			$('#'+xn).addClass('valid-fld');


	    } else {

			var xn = e.target.id;
			$('#error_' + xn).html('Special characters not allowed');
			$('#'+xn).removeClass('valid-fld');
			$('#'+xn).addClass('invalid-fld');
			e.preventDefault();

	    }
	});

	// allow only numerical values
	$('.numOnly').on('keypress', function(e){
		var k;
		document.all ? k = e.keyCode : k = e.which;
		if(k >= 48 && k <= 57){

			var xn = $(this).attr('id');
			$('#error_' + xn).html('');
			$(this).removeClass('invalid-fld');
			$(this).addClass('valid-fld');

		} else {

			var xn = $(this).attr('id');
			$('#error_' + xn).html('Only numeric values allowed');
			$(this).removeClass('valid-fld');
			$(this).addClass('invalid-fld');
			e.preventDefault();
			
		}
	});

	$('.numOnly').on('paste', (e) => {
	    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
	    if(/^[0-9-]*$/.test(text) == false) {

			var xn = e.target.id;
			$('#error_' + xn).html('Only numeric values allowed');
			$('#'+xn).removeClass('valid-fld');
			$('#'+xn).addClass('invalid-fld');
			e.preventDefault();

	    } else {

			var xn = e.target.id;
			$('#error_' + xn).html('');
			$('#'+xn).removeClass('invalid-fld');
			$('#'+xn).addClass('valid-fld');

	    }
	});

	// validation for financial year
	$('.finYear').on('keypress', function(e){
		var k;
		document.all ? k = e.keyCode : k = e.which;

		var regex = new RegExp("^[0-9-]");
		var key = String.fromCharCode(e.charCode ? e.which : e.charCode);
		if(!regex.test(key)) {

			var xn = $(this).attr('id');
			$('#error_' + xn).html('Only alphabets allowed');
			$(this).removeClass('valid-fld');
			$(this).addClass('invalid-fld');
			e.preventDefault();
		} else {

			var xn = $(this).attr('id');
			$('#error_' + xn).html('');
			$(this).removeClass('invalid-fld');
			$(this).addClass('valid-fld');
		}

	});


});