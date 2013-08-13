<?php
if ( !is_admin() ) {
global $buddysuite_options;

$buddysuite_options = get_option('buddysuite_plugin_options');


// close site to non members
function buddysuite_walled_garden() {
	global $bp, $post, $buddysuite_options;


	$meta = get_post_meta($post->ID, 'public-page', true);

	$id = get_the_ID();

	$page =  !empty( $buddysuite_options['landing-page'] ) ? $buddysuite_options['landing-page'] : '' ;

	$walled = !empty( $buddysuite_options['walled-garden'] );

	if ( $page  ) {

		if ( $walled && ! is_user_logged_in() && ! $meta && $id != $page  || is_home() ) {
			wp_redirect( get_option('siteurl') . '/?page_id=' . $page );
		}

	} else {

		if( $walled && ! is_user_logged_in() && ! is_home() && ! is_front_page() && ! $meta ) {
			wp_redirect( get_option('siteurl') . '/' );
		}
	}

}
add_filter( 'get_header', 'buddysuite_walled_garden', 10, 2 );


function my_redirect() {

	if( ! is_user_logged_in() && ! is_home() && ! is_front_page() && ! bp_is_register_page() && ! bp_is_activation_page() || is_404() ) {
		header("Location: http://" . $_SERVER['HTTP_HOST'] . "");
	}
}


function buddysuite_filter_bp_template() {
	global $bp;

	if ( bp_is_register_page() ) {

		return load_template( BS_PLUGIN_DIR . "/includes/themes/walled/register.php");

	} else 	if ( bp_is_activation_page() ) {

		return load_template( BS_PLUGIN_DIR . "/includes/themes/walled/activate.php");

	} else {

		return load_template( BS_PLUGIN_DIR . "/includes/themes/walled/index.php");
	}

}


function buddysuite_filter_wp_template() {

	global $buddysuite_options;

	if ( is_home() || is_front_page() ) {

		return load_template( BS_PLUGIN_DIR . "/includes/themes/walled/index.php");

	} else {

		return load_template( BS_PLUGIN_DIR . "/includes/themes/walled/index.php");
	}

}

if ( ! is_user_logged_in() && !empty( $buddysuite_options['walled-login-page'] ) ) {
	add_filter( 'bp_located_template', 'buddysuite_filter_bp_template', 10, 2 );
	add_filter( 'template_include', 'buddysuite_filter_wp_template', 10, 2 );
}



//remove admin bar
if ( !empty( $buddysuite_options['disable-adminbar'] ) && $buddysuite_options['disable-adminbar'] == 'all' ) {
	show_admin_bar( false );
} else if ( !empty( $buddysuite_options['disable-adminbar'] ) && $buddysuite_options['disable-adminbar'] == 'users' && !current_user_can( 'update_core' ) ) {
	show_admin_bar( false );

}



//force wp-signup.php to bp register slug
if ( !empty( $buddysuite_options['redirect-signup'] ) ) {
	function buddysuite_splog_signup_redirect() {
	global $bp;
	$regurl = BP_REGISTER_SLUG;
		if ( strpos( $_SERVER['REQUEST_URI'], 'wp-signup.php' ) !== false ) {
	    	$url = '/'. $regurl;
	        wp_redirect($url);
	        exit;
	    }
	}
	add_action( 'init', 'buddysuite_splog_signup_redirect' );
}

//force wp-login.php to home page when username/password invalid
if ( !empty( $buddysuite_options['redirect-login'] ) ) {
	/**
	 * When a user logs out, send them back to the home page
	 */
	function buddysuite_catch_logout() {
	    wp_redirect(
	        home_url(),
	        302
	    );
	    exit();
	}
	add_action( 'wp_logout', 'buddysuite_catch_logout' );

}


function buddysuite_login_logo() {
	global $buddysuite_options;

    if( !empty( $buddysuite_options['login-logo'] ) ) {

    	$logo = $buddysuite_options['login-logo'];
    	echo '<img src="' . $logo  . '">';

    } else {
	   return bloginfo( "name" );
    }
}

function buddysuite_if_login_page() {
	global $buddysuite_options;

	if( !empty( $buddysuite_options['login-page-text'] ) ) {
		return true;
	}

}

function buddysuite_get_login_page() {
	global $buddysuite_options;

    $page = $buddysuite_options['login-page-text'];

    $args = array(
	'post_type'=> 'page',
	'page_id'    => $page
	);

    return $args;

}
}