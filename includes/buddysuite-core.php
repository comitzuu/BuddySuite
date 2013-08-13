<?php

global $buddysuite_options;

$buddysuite_options = get_option('buddysuite_plugin_options');

/* Define a constant that can be checked to see if the plugin is installed or not. */
define ( 'BUDDYSUITE_IS_INSTALLED', 1 );

/* Define a constant that will hold the current version number of the plugin */
define ( 'BUDDYSUITE_VERSION', '1.0' );

if ( file_exists( dirname( __FILE__ ) . '/languages/buddysuite-' . get_locale() . '.mo' ) )
	load_plugin_textdomain( 'buddysuite', dirname( __FILE__ ) . '/languages/buddysuite-' . get_locale() . '.mo' );

	// Path and URL
if ( !defined( 'BS_PLUGIN_DIR' ) ) {
	define( 'BS_PLUGIN_DIR',  trailingslashit( WP_PLUGIN_DIR . '/buddysuite' )  );
}

if ( !defined( 'BS_PLUGIN_URL' ) ) {

	$plugin_url = WP_PLUGIN_URL . '/buddysuite' ;

	// If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
	if ( is_ssl() )
			$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
			define( 'BS_PLUGIN_URL', $plugin_url );
}


/**
* filters profile page content based on user settings
*
*/
function profile_privacy_filter_start() {
	ob_start();
}
add_action('bp_before_member_body', 'profile_privacy_filter_start', 0);


function profile_privacy_filter_end() {
	global $bp;

	$comp = $bp->current_component;

	$privacy = 'bp_' . $comp . '_privacy_is_set()';

	$privacy_meta = get_user_meta( $bp->displayed_user->id, 'bp-' . $comp . '-privacy', 1 );

	if ( $privacy && $privacy_meta ===  $comp && bp_displayed_user_is_friend() ) {

		ob_end_clean();

		if ( is_user_logged_in() ) {
			printf( __( '<div id="message" class="info"><p>You must be friends in order to access this user\'s %s.</p></div>' ), $comp );
		} else {
			printf( __( '<div id="message" class="info"><p>You must be logged in and friends in order to access this user\'s %s.</p></div>' ), $comp );
		}

	}

}
if ( bp_plugin_is_bp_default() ) {
add_action('bp_after_member_body', 'profile_privacy_filter_end', 0);
}


function my_template_part_filter( $templates, $slug, $name ) {
	global $bp;

	$comp = $bp->current_component;
	$privacy = 'bp_' . $comp . '_privacy_is_set()';

	$privacy_meta = get_user_meta( $bp->displayed_user->id, 'bp-' . $comp . '-privacy', 1 );


	if ( $privacy && $privacy_meta ===  $comp && bp_displayed_user_is_friend() ) {


	if ( 'members/single/'. $comp != $slug )
	    return $templates;

	    echo '<ul class="options-nav"></ul></div><div id="item-body">';

	   	if ( is_user_logged_in() ) {
			return printf( __( '<div id="message" class="info"><p>You must be friends in order to access this user\'s %s.</p></div>' ), $comp );
		} else {
			return printf( __( '<div id="message" class="info"><p>You must be logged in and friends in order to access this user\'s %s.</p></div>' ), $comp );
		}
	}

	 return $templates;
}

if ( !bp_plugin_is_bp_default() && bp_is_user() ) {
add_filter( 'bp_get_template_part', 'my_template_part_filter', 10, 3 );
}

function bp_plugin_is_bp_default() {

    // if active theme is BP Default or a child theme, then we return true
        // as i was afraid a BuddyPress theme that is not relying on BP Default might
        // be active, i added a BuddyPress version check.
        // I imagine that once version 1.7 will be released, this case will disappear.

    if( in_array( 'bp-default', array( get_stylesheet(), get_template() ) ) )
        return true;
    else
        return false;
}


function buddysuite_page_meta_box() {
	global $buddysuite_options;

	if( !empty( $buddysuite_options['walled-garden'] ) ) {
		$title = __( 'Privacy', 'buddysuite' );

		add_meta_box( 	'buddysuite-meta-box-id', 		// ID attribute of metabox
                  		$title,       						// Title of metabox visible to user
                  		'buddysuite_meta_box_callback',	// Function that prints box in wp-admin
                  		'page',              				// Show box for posts, pages, custom, etc.
                  		'side',            					// Where on the page to show the box
                  		'high' );           				// Priority of box in display order

		add_meta_box( 	'buddysuite-meta-box-id', 		// ID attribute of metabox
                  		$title,       						// Title of metabox visible to user
                  		'buddysuite_meta_box_callback',	// Function that prints box in wp-admin
                  		'post',              				// Show box for posts, pages, custom, etc.
                  		'side',            					// Where on the page to show the box
                  		'high' );
   }
}
add_action('add_meta_boxes','buddysuite_page_meta_box');


function buddysuite_meta_box_callback( $post_id ) {
	global $post;

	$meta = get_post_meta($post->ID, 'public-page', true);

?>
 	<input type="checkbox" name="public-page" value="public" <?php if ( $meta ) echo 'checked="checked"' ?> /> <?php _e( 'logged out users can view this page', 'buddysuite' ) ?>

<?php

	echo '<input type="hidden" name="buddysuite_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

}


function buddysuite_save_postdata( $post_id ) {
  // verify if this is an auto save routine.
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !isset( $_POST['buddysuite_meta_box_nonce'] ) )
  	return false;

  if ( ! wp_verify_nonce( $_POST['buddysuite_meta_box_nonce'], basename(__FILE__) ) ) {
        return $post_id;
  }

  // Check permissions
  if ( 'page' == $_POST['post_type'] ) {

	  if ( !current_user_can( 'edit_page', $post_id ) )
        return $post_id;

      } else {

	  if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;
  }


  if ( isset( $_POST['public-page'] ) ) {
   	$mydata = $_POST['public-page'];
  	add_post_meta( $post_id, 'public-page', $mydata );
  } else {
	 delete_post_meta( $post_id, 'public-page' );
  }

}
add_action( 'save_post', 'buddysuite_save_postdata' );


function buddysuite_admin_enqueue_scripts() {

    wp_enqueue_script( 'wp-color-picker' );
    // load the minified version of custom script
    wp_enqueue_script( 'buddysuite-custom', plugins_url( 'color-pick.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '1.1', true );
    wp_enqueue_style( 'wp-color-picker' );
}
if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'buddysuite' ) ) {
	add_action( 'admin_enqueue_scripts', 'buddysuite_admin_enqueue_scripts' );
}


function buddysuite_custom_styles() {

	$buddysuite_options = get_option('buddysuite_plugin_options');

	echo "<style type='text/css'>";

	if ( !empty( $buddysuite_options['login-page-background'] ) ) {

		$color = $buddysuite_options['login-page-background'];

		echo "html { background-color: $color !important; }";
	}

	if ( !empty( $buddysuite_options['login-page-text-color'] ) ) {

		$color = $buddysuite_options['login-page-text-color'];

		echo " html { color: $color !important; } a { color: $color !important; }";
	}

		echo '</style>';

}
add_action( 'bs_head', 'buddysuite_custom_styles' );


function bs_head() {
	do_action('bs_head');
}


?>