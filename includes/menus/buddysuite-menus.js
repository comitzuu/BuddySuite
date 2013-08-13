(function ($, document, undefined) {

    $(document).on('click', 'li.menu-item-type-buddypress  a', function(event){
    
 
       	var linkrel = $(this).attr("rel");
       	
    	$.cookie('bp-activity-oldestpage', '1', { path: '/' } );
    	
    	switch(linkrel){
	    	case 'bp-directory-activity':
	    		$.cookie('bp-activity-scope', 'all');
	    		return true;
	    	break;
	    	case 'bp-directory-all-activity':
	    		$.cookie('bp-activity-scope', 'all');
	    		return true;
	    	break;
	    	case 'bp-user-directory-my-friends-activity':
	    		$.cookie('bp-activity-scope', 'friends' );
	    		return true;
	    	break;
	    	case 'bp-user-directory-my-groups-activity':
	    		$.cookie('bp-activity-scope', 'groups' );
	    		return true;
	    	break;
	    	case 'bp-user-directory-my-favorites-activity':
	    		$.cookie('bp-activity-scope', 'favorites' );
	    		return true;
	    	break;
	    	case 'bp-user-directory-my-mentions-activity':
	    		$.cookie('bp-activity-scope', 'mentions' );
	    		return true;
	    	break;
	    	case 'bp-directory-members':
	    		$.cookie('bp-members-scope', 'all' );
	    		return true;
	    	break;
	    	case 'bp-directory-all-members':
	    		$.cookie('bp-members-scope', 'all' );
	    		return true;
	    	break;
	    	case 'bp-user-directory-my-friends-members':
	    		$.cookie('bp-members-scope', 'personal' );
	    		return true;
	    	break;
	    	case 'bp-user-directory-all-topics-forums':
	    		$.cookie('bp-forums-scope', 'all' );
	    		return true;
	    	break;
	    	case 'bp-user-directory-my-topics-forums':
	    		$.cookie('bp-forums-scope', 'personal' );
	    		return true;
	    	break;
	    	case 'bp-directory-groups':
	    		$.cookie('bp-groups-scope', 'all' );
	    		return true;
	    	break;
	    	case 'bp-directory-all-groups-group':
	    		$.cookie('bp-groups-scope', 'all' );
	    		return true;
	    	break;
	    	case 'bp-user-directory-my-groups-group':
	    		$.cookie('bp-groups-scope', 'personal' );
	    		return true;
	    	break;	 	   	   	    	
	    	default:
	    		return true;
    	}
    	
    	return false;
    	    
    });

})(jQuery, document);

/*!
 * jQuery Cookie Plugin v1.3
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function ($, document, undefined) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (value === null) {
				options.expires = -1;
			}

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			if (decode(parts.shift()) === key) {
				var cookie = decode(parts.join('='));
				return config.json ? JSON.parse(cookie) : cookie;
			}
		}

		return null;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== null) {
			$.cookie(key, null, options);
			return true;
		}
		return false;
	};

})(jQuery, document);