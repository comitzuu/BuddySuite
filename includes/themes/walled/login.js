$(document).ready( function() {

	$('.field-visibility-settings').hide();
	$('.visibility-toggle-link').on( 'click', function() {
		var toggle_div = $(this).parent();

		$(toggle_div).fadeOut( 600, function(){
			$(toggle_div).siblings('.field-visibility-settings').slideDown(400);
		});

		return false;
	} );

	$('.field-visibility-settings-close').on( 'click', function() {
		var settings_div = $(this).parent();

		$(settings_div).slideUp( 400, function(){
			$(settings_div).siblings('.field-visibility-settings-toggle').fadeIn(800);
		});

		return false;
	} );

});