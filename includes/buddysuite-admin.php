<?php

global $buddysuite_options;

$buddysuite_options = get_option('buddysuite_plugin_options');

function buddysuite_settings_menu() {
	add_options_page('BuddySuite', 'BuddySuite', 'administrator', 'buddysuite', 'buddysuite_plugin_options_page' );
}
add_action('admin_menu', 'buddysuite_settings_menu');
add_action('network_admin_menu', 'buddysuite_settings_menu');


function buddysuite_plugin_admin_init(){
	register_setting( 'buddysuite_plugin_options', 'buddysuite_plugin_options', 'buddysuite_plugin_options_validate' );
	add_settings_section('general_section', 'General Settings', 'section_general', __FILE__);
	add_settings_section('private_section', 'Privacy Settings', 'section_private', __FILE__);

	if ( wp_get_theme() == 'BuddyPress Default' ) {
	add_settings_field('responsive_css', 'Responsive CSS', 'buddysuite_setting_responsive_css', __FILE__, 'general_section');
	}

	add_settings_field('buddysuite_license_key', 'License Key', 'buddysuite_setting_buddysuite_license_key', __FILE__, 'general_section');

	add_settings_field('deprecated-code', 'Deprecated Code', 'buddysuite_setting_deprecated_code', __FILE__, 'general_section');
	add_settings_field('root-profile', 'Root Profiles', 'buddysuite_setting_root_profile', __FILE__, 'general_section');
	add_settings_field('disable-gravatar', 'Disable Gravatars', 'buddysuite_setting_disable_gravatar', __FILE__, 'general_section');
	add_settings_field('username-compat', 'Username Compatibility', 'buddysuite_setting_username_compat', __FILE__, 'general_section');
	add_settings_field('disable-mentions', '@ Mentions', 'buddysuite_setting_disable_mentions', __FILE__, 'general_section');
	add_settings_field('disable-adminbar', 'Disable Admin Bar', 'buddysuite_setting_disable_adminbar', __FILE__, 'general_section');
	add_settings_field('directory-username', 'Directory Username', 'buddysuite_setting_directory_username', __FILE__, 'general_section');
	add_settings_field('redirect-signup', 'Spam Signups', 'buddysuite_setting_redirect_signup', __FILE__, 'general_section');
	add_settings_field('auto-link', 'Profile Field Links', 'buddysuite_setting_auto_link', __FILE__, 'general_section');
	add_settings_field('thumbsize', 'Avatar Thumbnail Size', 'buddysuite_setting_thumbsize', __FILE__, 'general_section');
	add_settings_field('avisize', 'Avatar Size', 'buddysuite_setting_avisize', __FILE__, 'general_section');
	add_settings_field('max-avisize', 'Max Avatar Size', 'buddysuite_setting_max_avisize', __FILE__, 'general_section');
	//add_settings_field('rename-group', 'Rename Group', 'buddysuite_setting_rename_group', __FILE__, 'general_section');

	add_settings_field('disable-privacy-tab', 'Privacy Tab', 'buddysuite_setting_disable_privacy_tab', __FILE__, 'private_section');
	add_settings_field('track-viewer', 'Profile Viewers', 'buddysuite_setting_track_viewer', __FILE__, 'private_section');
	add_settings_field('walled-garden', 'Private Site', 'buddysuite_setting_walled_garden', __FILE__, 'private_section');
	add_settings_field('landing-page', 'Landing Page', 'buddysuite_setting_landing_page', __FILE__, 'private_section');
	add_settings_field('walled-login-page', 'Login Page', 'buddysuite_setting_walled_login_page', __FILE__, 'private_section');
	add_settings_field('login-logo', 'Login Logo', 'buddysuite_setting_login_logo', __FILE__, 'private_section');
	add_settings_field('login-page-text', 'Login Page Text', 'buddysuite_setting_login_page_text', __FILE__, 'private_section');
	add_settings_field('login-page-background', 'Login Page Background', 'buddysuite_setting_login_page_background', __FILE__, 'private_section');
	add_settings_field('login-page-text-color', 'Login Page Text Color', 'buddysuite_setting_login_page_text_color', __FILE__, 'private_section');

}
add_action('admin_init', 'buddysuite_plugin_admin_init');



// Define default option settings
function buddysuite_add_defaults() {
    $arr = array("responsive-css"=>"");
    update_option('buddysuite_plugin_options', $arr);
}
register_activation_hook(__FILE__, 'buddysuite_add_defaults');


function buddysuite_plugin_options_page() {
?>

	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>BuddySuite</h2>
		<form action="options.php" method="post">
		<?php settings_fields('buddysuite_plugin_options'); ?>
		<?php do_settings_sections(__FILE__); ?>

		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>

<?php
}

function section_general() {
	_e('Use the help tab in the upper right for help with settings.', 'buddysuite');
}
function section_private() {

}


function buddysuite_plugin_options_validate($input) {

	return $input; // return validated input

}


//login settings
function buddysuite_setting_buddysuite_license_key() {
	global $buddysuite_options;

	$key = !empty( $buddysuite_options['buddysuite_license_key'] ) ? $buddysuite_options['buddysuite_license_key'] : '' ;

	echo "<input id='buddysuite_license_key' name='buddysuite_plugin_options[buddysuite_license_key]' size='40' type='text' value='$key' />  ";

	if ( get_option('buddysuite_license_status') == 'valid') {

		echo "<input id='edd_license_deactivate' name='edd_license_deactivate' type='checkbox' />";
		_e('   Deactivate License', 'buddysuite');

	} else {

		echo "<input id='edd_license_activate' name='edd_license_activate' type='checkbox' />";
		_e('   Activate License', 'buddysuite');

	}

}


function buddysuite_activate_license() {

	global $buddysuite_options;

	// retrieve the license from the database
	$license = trim( $buddysuite_options['buddysuite_license_key'] );

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( TAPTAPPRESS_ITEM_NAME_BS ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, TAPTAPPRESS_STORE_URL_BS ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'buddysuite_license_status', $license_data->license );

	} else

		// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_deactivate'] ) ) {


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( TAPTAPPRESS_ITEM_NAME_BS ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, TAPTAPPRESS_STORE_URL_BS ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'buddysuite_license_status', $license_data->license );

	}


}
add_action('admin_init', 'buddysuite_activate_license');


/*** General settings functions ***/
function buddysuite_setting_responsive_css() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['responsive-css']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='responsive_css' name='buddysuite_plugin_options[responsive-css]' type='checkbox' />  ";
	_e('Disable responsive CSS on BuddyPress Default theme.', 'buddysuite');

}

function buddysuite_setting_deprecated_code() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['deprecated-code']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='deprecated-code' name='buddysuite_plugin_options[deprecated-code]' type='checkbox' />  ";
	_e('Disable loading of deprecated code.', 'buddysuite');

}

function buddysuite_setting_root_profile() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['root-profile']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='root-profile' name='buddysuite_plugin_options[root-profile]' type='checkbox' />  ";
	_e('Show user profiles at site root url/membername', 'buddysuite') ;

}

function buddysuite_setting_disable_privacy_tab() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['disable-privacy-tab']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='disable-privacy-tab' name='buddysuite_plugin_options[disable-privacy-tab]' type='checkbox' />  ";
	_e('Disable privacy tab on user profiles.', 'buddysuite') ;

}

function buddysuite_setting_walled_garden() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['walled-garden']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='walled-garden' name='buddysuite_plugin_options[walled-garden]' type='checkbox' />  ";
	_e('Only allow logged in users can see this site. Set individual Page and Post privacy during content creation.', 'buddysuite');

}

function buddysuite_setting_walled_login_page() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['walled-login-page']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='walled-login-page' name='buddysuite_plugin_options[walled-login-page]' type='checkbox' />  ";
	_e('Block access to site and show a login page. This overrides landing page and private site settings.', 'buddysuite');

}

function buddysuite_setting_landing_page() {
	global $buddysuite_options;

	$landing = !empty( $buddysuite_options['landing-page'] ) ? $buddysuite_options['landing-page'] : '' ;

	$pages = get_pages();

	echo "<select id='landing-page' name='buddysuite_plugin_options[landing-page]'>";

	echo "<option value=''>Home</option>";

	foreach($pages as $page) {

		$title = $page->post_title;
		$id = $page->ID;

		$selected = ( $landing == $id ) ? 'selected="selected"' : '';

		echo "<option value='$id' $selected>$title</option>";
	}
	echo "</select>  ";

	_e('Choose a landing page for logged out users.', 'buddysuite');
}

function buddysuite_setting_disable_gravatar() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['disable-gravatar']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='disable-gravatar' name='buddysuite_plugin_options[disable-gravatar]' type='checkbox' />  ";
	_e('Turn off Gravatars.', 'buddysuite');

}

function buddysuite_setting_username_compat() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['username-compat']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='username-compat' name='buddysuite_plugin_options[username-compat]' type='checkbox' />  ";
	_e('Allow special characters and uppercase letters in usernames.', 'buddysuite');

}

function buddysuite_setting_disable_mentions() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['disable-mentions']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='disable-mentions' name='buddysuite_plugin_options[disable-mentions]' type='checkbox' />  ";
	_e('Disable @ mentions.', 'buddysuite');

}

function  buddysuite_setting_disable_adminbar() {
	global $buddysuite_options;
	$checked = '';
	$checked2 = '';

	if (  !empty( $buddysuite_options['disable-adminbar'] ) ) {
		if( $buddysuite_options['disable-adminbar'] == 'all' ) { $checked = ' checked="checked" '; }
		if( $buddysuite_options['disable-adminbar'] == 'users' ) { $checked2 = ' checked="checked" '; }
	}

	echo "<input ". $checked  ." type='radio' id='disable-adminbar-all' name='buddysuite_plugin_options[disable-adminbar]' value='all' />   All including Admin       ";
	echo "<input ". $checked2 ." type='radio' id='disable-adminbar-users' name='buddysuite_plugin_options[disable-adminbar]' value='users' />   Only Users";
}


function buddysuite_setting_directory_username() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['directory-username']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='directory-username' name='buddysuite_plugin_options[directory-username]' type='checkbox' />  ";
	_e('Usernames in directory instead of profile name field.', 'buddysuite');

}

function buddysuite_setting_track_viewer() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['track-viewer']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='track-viewer' name='buddysuite_plugin_options[track-viewer]' type='checkbox' />  ";
	_e('Allow users to see the last 20 members who have viewed their profile.', 'buddysuite');

}


function buddysuite_setting_redirect_signup() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['redirect-signup']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='redirect-signup' name='buddysuite_plugin_options[redirect-signup]' type='checkbox' />  ";
	_e('Prevent spam sign ups by redirecting wp-signup.php to BuddyPress register slug.', 'buddysuite');

}

function buddysuite_setting_auto_link() {
	global $buddysuite_options;
	$checked = '';

	if( !empty( $buddysuite_options['auto-link']) ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='auto-link' name='buddysuite_plugin_options[auto-link]' type='checkbox' />  ";
	_e('Prevent auto linking of profile fields.', 'buddysuite');

}

function buddysuite_setting_thumbsize() {
	global $buddysuite_options;

	$thumbsize = !empty( $buddysuite_options['thumbsize'] ) ? $buddysuite_options['thumbsize'] : '' ;

	$sizes = array( '25', '30', '35', '40', '45', '50', '60', '65' );

	echo "<select id='thumbsize' name='buddysuite_plugin_options[thumbsize]'>";

	$default = __( 'Default', 'buddysuite' );

	echo "<option value='50'>$default</option>";

	foreach( $sizes as $size ) {

		$selected = ( $thumbsize == $size ) ? 'selected="selected"' : '';

		echo "<option value='$size' $selected>$size</option>";
	}
	echo "</select>  ";

	_e('Choose avatar thumbnail size.', 'buddysuite');
}

function buddysuite_setting_avisize() {
	global $buddysuite_options;

	$avisize = !empty( $buddysuite_options['avisize'] ) ? $buddysuite_options['avisize'] : '' ;

	//var_dump($buddysuite_options);
	$sizes = array( '25', '50', '75', '100', '150', '175', '200', '225' );

	echo "<select id='avisize' name='buddysuite_plugin_options[avisize]'>";

	$default = __( 'Default', 'buddysuite' );

	echo "<option value='150'>$default</option>";

	foreach( $sizes as $size ) {

		$selected = ( $avisize == $size ) ? 'selected="selected"' : '';

		echo "<option value='$size' $selected>$size</option>";
	}
	echo "</select>  ";

	_e('Choose avatar size.', 'buddysuite');
}

function buddysuite_setting_max_avisize() {
	global $buddysuite_options;

	$maxavisize = !empty( $buddysuite_options['max-avisize'] ) ? $buddysuite_options['max-avisize'] : '' ;

	//var_dump($buddysuite_options);
	$sizes = array( '150', '300', '640' );

	echo "<select id='max-avisize' name='buddysuite_plugin_options[max-avisize]'>";

	$default = __( 'Default', 'buddysuite' );

	echo "<option value='640'>$default</option>";

	foreach( $sizes as $size ) {

		$selected = ( $maxavisize == $size ) ? 'selected="selected"' : '';

		echo "<option value='$size' $selected>$size</option>";
	}
	echo "</select>  ";

	_e('Choose max avatar size.', 'buddysuite');
}

function buddysuite_setting_rename_group() {
	global $buddysuite_options;

	$text = !empty( $buddysuite_options['rename-group'] ) ? $buddysuite_options['rename-group'] : '' ;

	echo "<input id='rename-group' name='buddysuite_plugin_options[rename-group]' size='20' type='text' value='$text' />  ";
}



//login settings
function buddysuite_setting_login_logo() {
	global $buddysuite_options;

	wp_enqueue_media();

	$text = !empty( $buddysuite_options['login-logo'] ) ? $buddysuite_options['login-logo'] : '' ;

	$admin = admin_url() . 'media-new.php';

	echo "<input id='login-logo' name='buddysuite_plugin_options[login-logo]' size='40' type='text' value='$text' />  ";
	echo "<input type='button' class='button' name='buddysuite-logo-image' id='buddysuite-logo-image' value='Upload' />";
}
do_action( 'media_buttons', 'login-logo' );


function buddysuite_setting_login_page_text() {
	global $buddysuite_options;

	$landing = !empty( $buddysuite_options['login-page-text'] ) ? $buddysuite_options['login-page-text'] : '' ;

	//var_dump($buddysuite_options);

	$pages = get_pages();

	echo "<select id='login-page-text' name='buddysuite_plugin_options[login-page-text]'>";

	$none = __( 'None', 'buddysuite' );

	echo "<option value=''>$none</option>";

	foreach($pages as $page) {

		$title = $page->post_title;
		$id = $page->ID;

		$selected = ( $landing == $id ) ? 'selected="selected"' : '';

		echo "<option value='$id' $selected>$title</option>";
	}
	echo "</select>  ";

	_e('Choose a page for logged out welcome text.', 'buddysuite');
}

function buddysuite_setting_login_page_background() {
	global $buddysuite_options;

	$value = !empty( $buddysuite_options['login-page-background'] ) ? $buddysuite_options['login-page-background'] : '' ;

	echo "<input id='login-page-background' name='buddysuite_plugin_options[login-page-background]' size='20' type='text' value='$value' />";
}

function buddysuite_setting_login_page_text_color() {
	global $buddysuite_options;

	$value = !empty( $buddysuite_options['login-page-text-color'] ) ? $buddysuite_options['login-page-text-color'] : '' ;

	echo "<input id='login-page-text-color' name='buddysuite_plugin_options[login-page-text-color]' size='20' type='text' value='$value' />";
}



function buddysuite_add_contextual_help( $screen = '' ) {

	$screen = get_current_screen();

	//print_r($screen);

	switch ( $screen->id ) {

		// Compontent page
		case 'settings_page_buddysuite/includes/buddysuite-admin';

			// help tabs
			$screen->add_help_tab( array(
				'id'      => 'buddysuite-overview',
				'title'   => __( 'Overview' ),
				'content' => buddysuite_add_contextual_help_content( 'buddysuite-overview' )
			) );

			$screen->add_help_tab( array(
				'id'      => 'buddysuite-private-site',
				'title'   => __( 'Private Site' ),
				'content' => buddysuite_add_contextual_help_content( 'buddysuite-private-site' )
			) );

			$screen->add_help_tab( array(
				'id'      => 'buddysuite-landing-page',
				'title'   => __( 'Landing Page' ),
				'content' => buddysuite_add_contextual_help_content( 'buddysuite-landing-page' )
			) );

			$screen->add_help_tab( array(
				'id'      => 'buddysuite-login-page',
				'title'   => __( 'Login Page' ),
				'content' => buddysuite_add_contextual_help_content( 'buddysuite-login-page' )
			) );

			$screen->add_help_tab( array(
				'id'      => 'buddysuite-login-logo',
				'title'   => __( 'Login Logo' ),
				'content' => buddysuite_add_contextual_help_content( 'buddysuite-login-logo' )
			) );
			$screen->add_help_tab( array(
				'id'      => 'buddysuite-login-text',
				'title'   => __( 'Login Welcome Text' ),
				'content' => buddysuite_add_contextual_help_content( 'buddysuite-login-text' )
			) );

		break;


	}
}
add_action( 'contextual_help', 'buddysuite_add_contextual_help' );


function buddysuite_add_contextual_help_content( $tab = '' ) {

	switch ( $tab ) {
		case 'buddysuite-overview' :
			return '<p>' . __( 'BuddySuite adds many features an settings required to run a professional social site. ' ) . '</p>';
		break;
		case 'buddysuite-private-site' :
			return '<p>' . __( 'This setting will redirect logged out users to a page you choose in the Landing Page settings. If no page is chosen users get redirected to the site root. You can set individual posts and pages to be visible to users who are logged out. Page and post visiibility setting is in upper right when creating content.' ) . '</p>';
		break;
		case 'buddysuite-landing-page' :
			return '<p>' . __( 'Choose a page to redirect logged out users when Private Site setting is checked. You create this page in the WordPress admin.' ) . '</p>';
		break;
		case 'buddysuite-login-page' :
			return '<p>' . __( 'This setting closes off access to logged out users and displays a customizable login page instead of the active theme.' ) . '</p>';
		break;
		case 'buddysuite-login-logo' :
			return '<p>' . __( 'Upload an image you want to use as a logo. If no logo is specified login page will show the site name.' ) . '</p>';
		break;
		case 'buddysuite-login-text' :
			return '<p>' . __( 'Create a page that will contain your login page welcome text. Then choose this page from the drop down.' ) . '</p>';
		break;

		default:
			return false;
			break;
	}
}

?>