
var redirect_to = $("#redirect_to").val();
$("#redirectto").click(function(e){
  redirectTo();
});

$(document).ready(function() {
	$('.mod').fadeIn('slow');
})

function redirectTo() {
	$('.mod').fadeOut('slow');

	setTimeout(function(){
		window.location = redirect_to;
	}, 0000);
}
