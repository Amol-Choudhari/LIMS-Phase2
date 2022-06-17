
$(window).on('load', function () {
	$('.form_spinner').hide('slow');
});

$(document).ready(function(){
	
	//for buttons and linsk with class btn
	$('.btn').click(function(){
		//checking if page started loading or not, to avoid showing loader on validations
		window.addEventListener('beforeunload', function() {
		  $('.form_spinner').show('slow');
		});
	});
	//for dashboard side menu's dropdown sub menu
	$('.bg-cyan p').click(function(){
		$('.form_spinner').show('slow');
	});
	//for dashboard right side profile menu list
	$('.nav-link span').click(function(){
		$('.form_spinner').show('slow');
	});
	
	//for dashboard side main link if not having child links under it.
	//$('.nav-item').click(function(){
	$('.nav-sidebar li').click(function(){
		if($("ul",this).hasClass("nav")){//if have ul with nav class
			//do nothing
		}else{
			$('.form_spinner').show('slow');
		}
	});

	// Show loader on AJAX execution
	$(document).ajaxStart(function() {
		$('.form_spinner').show('slow');
	});

	$(document).ajaxStop(function() {
		$('.form_spinner').hide('slow');
	});

});