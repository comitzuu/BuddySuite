/*!
* color picker code originated by
* @author: Rachel Baker ( rachel@rachelbaker.me )
*/

(function($) {
   
    function pickBackgroundColor(color) {
        $("#login-page-background").val(color);
    }
    function toggle_text() {
        link_color = $("#login-page-background");
        if ("" === link_color.val().replace("#", "")) {
            link_color.val(default_color);
            pickBackgroundColor(default_color);
        } else pickBackgroundColor(link_color.val());
    }
    var default_color = "fbfbfb";
    $(document).ready(function() {
        var link_color = $("#login-page-background");
        link_color.wpColorPicker({
            change: function(event, ui) {
                pickBackgroundColor(link_color.wpColorPicker("color"));
            },
            clear: function() {
                pickBackgroundColor("");
            }
        });
        $("#login-page-background").click(toggle_text);
        toggle_text();
    });

   
    function pickTextColor(color) {
        $("#login-page-text-color").val(color);
    }
    function toggle_text_color() {
        link_color = $("#login-page-text-color");
        if ("" === link_color.val().replace("#", "")) {
            link_color.val(default_color);
            pickTextColor(default_color);
        } else pickTextColor(link_color.val());
    }
    var default_color = "000000";
    $(document).ready(function() {
        var link_color = $("#login-page-text-color");
        link_color.wpColorPicker({
            change: function(event, ui) {
                pickTextColor(link_color.wpColorPicker("color"));
            },
            clear: function() {
                pickTextColor("");
            }
        });
        $("#login-page-text-color").click(toggle_text_color);
        toggle_text_color();
    });
    
    
	$(document).ready(function($){
		  var _custom_media = true,
		      _orig_send_attachment = wp.media.editor.send.attachment;
		  $('.settings_page_buddysuite-includes-buddysuite-admin .button').click(function(e) {
		    var send_attachment_bkp = wp.media.editor.send.attachment;
		    var button = $(this);
		    var id = button.attr('id').replace('_button', '');
		    _custom_media = true;
		    wp.media.editor.send.attachment = function(props, attachment){
		      if ( _custom_media ) {
		        $("#login-logo").val(attachment.url);
		      } else {
		        return _orig_send_attachment.apply( this, [props, attachment] );
		      };
		    }
		    wp.media.editor.open(button);
		    return false;
		  });
		  $('.add_media').on('click', function(){
		    _custom_media = false;
		  });
	});
    
    
})(jQuery);