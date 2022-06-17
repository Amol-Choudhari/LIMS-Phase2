$.widget.bridge('uibutton', $.ui.button);

$('#calendar').datepicker({

  format: "dd/mm/yyyy",
  autoclose: true
});

!function ($) {
    $(document).on("click","ul.nav li.parent > a > span.icon", function(){
        $(this).find('em:first').toggleClass("glyphicon-minus");
    });
    $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
}(window.jQuery);

$(window).on('resize', function () {
  if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
})
$(window).on('resize', function () {
  if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
})


$( document ).ready(function() {
 $("#Token_key_id").prop("disabled", false);
});



//below script used to disable all mouse events for 10 sec, if any submit click.
//to prevent user from clicking any where while submit in process.
//created on 24-11-2017 by Amol
/*		$(":submit").click(function() {
  $('.main_container').css('pointer-events','none');
  setTimeout(function(){ $('.main_container').css('pointer-events','visible'); },4000);
});
*/

//to disable right click of all anchor tags// on 14-02-2018
/*$(document).bind("contextmenu",function(e){
  return false;
});*/


$(".nav li a").click(function() {
$(".nav li").removeClass('active');
$(this).parent().addClass('active');
});
