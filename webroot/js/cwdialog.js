/****************************************                                          
*   Author: CodexWorld                  *
*	Published Date: 01-12-2014			*
*	Contact: contact@codexworld.com		*
****************************************/

// Global CWdialog variables
var $CWdialog = null,
		$overlay = null,
		$body = null,
		$window = null,
		$cA = null,
		CWdialogQueue = [];

// Add overlay and set opacity for cross-browser compatibility
$(function() {
	
	$CWdialog = $('<div class="cwdialog">');
	$overlay = $('<div class="cwdialog-overlay">');
	$body = $('body');
	$window = $(window);
	
	$body.append( $overlay.css('opacity', '.94') ).append($CWdialog);
});

function CWdialog(text, options) {
	
	// Restrict blank modals
	if(text===undefined || !text) {
		return false;
	}
	
	// Necessary variables
	var $me = this,
			$_inner = $('<div class="cwdialog-inner">'),
			$_buttons = $('<div class="cwdialog-buttons">'),
			$_input = $('<input type="text">');
	
	// Default settings (edit these to your liking)
	var settings = {
	
		animation: 700,	// Animation speed
		buttons: {
			confirm: {
				action: function() { $me.dissapear(); }, // Callback function
				className: null, // Custom class name(s)
				id: 'confirm', // Element ID
				text: 'Ok' // Button text
			}
		},
		input: false, // input dialog
		override: true // Override browser navigation while CWdialog is visible
	};
	
	// Merge settings with options
	$.extend(settings, options);
	
	// Close current CWdialog, exit
	if(text=='close') { 
		$cA.dissapear();
		return;
	}
	
	// If an CWdialog is already open, push it to the queue
	if($CWdialog.is(':visible')) {

		CWdialogQueue.push({text: text, options: settings});
	
		return;
	}
	
	// Width adjusting function
	this.adjustWidth = function() {
		
		var window_width = $window.width(), w = "20%", l = "40%";

		if(window_width<=800) {
			w = "90%", l = "5%";
		} else if(window_width <= 1400 && window_width > 800) {
			w = "70%", l = "15%";
		} else if(window_width <= 1800 && window_width > 1400) {
			w = "50%", l = "25%";
		} else if(window_width <= 2200 && window_width > 1800) {
			w = "30%", l = "35%";
		}
		
		$CWdialog.css('width', w).css('left', l);
		
	};
	
	// Close function
	this.dissapear = function() {
		
		$CWdialog.animate({
			top: '-100%'
		}, settings.animation, function() {
			
			$overlay.fadeOut(300);
			$CWdialog.hide();
			
			// Unbind window listeners
			$window.unbind("beforeunload");
			$window.unbind("keydown");

			// If in queue, run it
			if(CWdialogQueue[0]) { 
				CWdialog(CWdialogQueue[0].text, CWdialogQueue[0].options);
				CWdialogQueue.splice(0,1);
			}
		});
		
		return true;
	};
	
	// Keypress function
	this.keyPress = function() {
		
		$window.bind('keydown', function(e) {
			// Close if the ESC key is pressed
			if(e.keyCode===27) {
				
				if(settings.buttons.cancel) {
					
					$("#cwdialog-btn-" + settings.buttons.cancel.id).trigger('click');
				} else {
					
					$me.dissapear();
				}
			} else if(e.keyCode===13) {

				if(settings.buttons.confirm) {
					
					$("#cwdialog-btn-" + settings.buttons.confirm.id).trigger('click');
				} else {
					
					$me.dissapear();
				}
			}
		});
	};
	
	// Add buttons
	$.each(settings.buttons, function(i, button) {
		
		if(button) {
			
			// Create button
			var $_button = $('<button id="cwdialog-btn-' + button.id + '">').append(button.text);
			
			// Add custom class names
			if(button.className) {
				$_button.addClass(button.className);
			}
			
			// Add to buttons
			$_buttons.append($_button);
			
			// Callback (or close) function
			$_button.on("click", function() {
				
				// Build response object
				var response = {
					clicked: button, // Pass back the object of the button that was clicked
					input: ($_input.val() ? $_input.val() : null) // User inputted text
				};
				
				button.action( response );
				//$me.dissapear();
			});
		}
	});
	
	// Disabled browser actions while open
	if(settings.override) {
		$window.bind('beforeunload', function(e){ 
			return "An alert requires attention";
		});
	}
	
	// Adjust dimensions based on window
	$me.adjustWidth();
	
	$window.resize( function() { $me.adjustWidth() } );
	
	// Append elements, show CWdialog
	$CWdialog.html('').append( $_inner.append('<div class="cwdialog-content">' + text + '</div>') ).append($_buttons);
	$cA = this;
	
	if(settings.input) {
		$_inner.find('.cwdialog-content').append( $('<div class="cwdialog-input">').append( $_input ) );
	}
	
	$overlay.fadeIn(300);
	$CWdialog.show().animate({
		top: '20%'
	}, 
		settings.animation, 
		function() {
			$me.keyPress();
		}
	);
	
	// Focus on input
	if(settings.input) {
		$_input.focus();
	}
	
} // end CWdialog();


//This function is created here, because this .js file is called in almost all layouts
//so this function can be called from anywhere.
//created on 05-07-2018 by Amol
function CDAC_esign_ajax_calls(){

//	var once_no = $("#once_no").val();
	var once_no = '000000000000';//added on 27-08-2018, currently not required
	var return_value = '';
	
/*	if(once_no==''){
		$("#error_aadhar_no").show().text("Please Enter Aadhar/VID/Token Id.");
		$("#error_aadhar_no").css({"color":"red","font-size":"12px","font-weight":"500","text-align":"left"});
		setTimeout(function(){ $("#error_aadhar_no").fadeOut();},5000);
		$("#esign_otp").click(function(){$("#error_aadhar_no").hide().text;});
		return_value = 'false';
	}else{
		//updated limit to 72 and alphanumeric for aadhar/VID/Token acceptance, on 18-06-2018
		if(once_no.match(/^(?=.*[a-zA-Z0-9])[a-zA-Z0-9]{12,72}$/g)){
*/		
			//added this ajax code from below to here on 31-05-2018 by Amol
			$.ajax({
				type:'POST',
				async: true,
				cache: false,
				data:{once_no:once_no},				
				url: "../esign/request_esign_otp",
				beforeSend: function (xhr) { // Add this line
					xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
				},
				success: function(response){
					
					//window.location.replace('../esign/request_esign');
					
					var token_session_id = response;
					$('#Token_key_id').val(token_session_id);
					
					//below ajax added on 27-06-2018 to generate xml with signature	
					$.ajax({
						type:'POST',
						async: true,
						cache: false,
						beforeSend: function (xhr) { // Add this line
							xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
						},
						url: "../esign/create_esign_xml_ajax",
						success: function(response_xml){//alert(response_xml); 
							var xml_content = response_xml;
							
							window.location.replace('../esign/request_esign');
							
						//below ajax added on 27-06-2018 to request esig OTP, if success then redirect to CDAC server.
						//with CORS functionality
							/* $.ajax({
								type: 'POST',
								crossDomain: true,
								xhrFields: {
										withCredentials: true
								},
								//url:'https://es-staging.cdac.in/esignleveltwo/2.0/signdoc',
								//url:'https://196.1.113.67/esign/2.1/signdoc',
								//url:'https://es-staging.cdac.in/esign2.1level2/2.1/signdoc',
								url:'https://esignservice.cdac.in/esignservice2.1/2.1/signdoc',
								 
								data: xml_content,
								contentType: 'application/xml',
								//dataType: "json",

								success : function(esignRes) {
									
									//redirecting to CDAC url for OTP authentication
									var espResp = esignRes.responseXml; //alert(esignRes.responseXml);
									var aspUrl = esignRes.responseUrl; 
									var status = esignRes.status; 
									if (status == 1) {

										alert("You are now redirecting to another domain.");                     
										window.location.replace(aspUrl);   ///OTP  Page Url 
									} else if (status == 0) { 

										alert("Sorry.. We found some error in the process."); 
										if (aspUrl == 'NA') { 
											//handle the Error Cases Here 
										} 
								   
									}
									
								}														
							});  */

						}
						
					});
				}
				
			});
			
			return_value = 'false';

/*		}else{				
			
			$("#error_aadhar_no").show().text("Min. length is 12");
			$("#error_aadhar_no").css({"color":"red","font-size":"12px","font-weight":"500","text-align":"left"});
			$("#once_no").focusout(function(){$("#error_aadhar_no").hide().text;});
			return_value = 'false';
		}
	}
*/	
	return return_value;
}
