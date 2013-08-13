<?php

/* --- Exit if accessed directly --- */
if ( !defined( 'ABSPATH' ) ) exit;

/* --- Check if member is a friend  --- */
function bp_displayed_user_is_friend() {
	global $bp;

	if ( bp_is_active('friends') ) {

		if ( ('is_friend' != BP_Friends_Friendship::check_is_friend( $bp->loggedin_user->id, $bp->displayed_user->id )) && (bp_loggedin_user_id() != bp_displayed_user_id() ) )
		return true;
	}

	if ( !bp_follow_is_following()  && bp_loggedin_user_id() != bp_displayed_user_id() ) {
	return true;
	}
}


function bp_xprofile_privacy_is_set(){
	global $bp;
	if ( (get_user_meta($bp->displayed_user->id, 'bp-profile-privacy', 1) == 'profile') && (bp_loggedin_user_id() != bp_displayed_user_id()) )
	return true;
}


function bp_activity_privacy_is_set(){
	global $bp;
	if ( (get_user_meta($bp->displayed_user->id, 'bp-activity-privacy', 1) == 'activity') && (bp_loggedin_user_id() != bp_displayed_user_id()) )
	return true;
}


function bp_groups_privacy_is_set(){
	global $bp;
	if ( (get_user_meta($bp->displayed_user->id, 'bp-group-privacy', 1) == 'groups') && (bp_loggedin_user_id() != bp_displayed_user_id()) )
	return true;
}


function bp_friends_privacy_is_set(){
	global $bp;
	if ( (get_user_meta($bp->displayed_user->id, 'bp-friends-privacy', 1) == 'friends') && (bp_loggedin_user_id() != bp_displayed_user_id()) )
	return true;
}


function bp_forums_privacy_is_set(){
	global $bp;
	if ( ( get_user_meta($bp->displayed_user->id, 'bp-forums-privacy', 1 ) == 'forums' ) && ( bp_loggedin_user_id() != bp_displayed_user_id() ) )
	return true;
}


function bp_friends_privacy_set() {
	global $bp;
	if ( ( get_user_meta( $bp->displayed_user->id, 'bp-add-friend-privacy', 1 ) == 'block' ) )

	 remove_action( 'bp_member_header_actions', 'bp_add_friend_button', 5 );
}
add_action( 'bp_member_header_actions', 'bp_friends_privacy_set', 0 );


function bp_friends_privacy_button_loop() {

	ob_start();
	add_action( 'bp_directory_members_actions', 'bp_add_friends_button_remove' );

}
add_action( 'bp_directory_members_actions', 'bp_friends_privacy_button_loop', 0 );

	function bp_add_friends_button_remove() {

		global $members_template;

		$id = $members_template->member->id;

		$block = get_user_meta( $id, 'bp-add-friend-privacy', 1 );

		if ( $block == 'block' )

		ob_end_clean();

	}


function bp_private_message_privacy_set() {
	global $bp;
	if ( ( get_user_meta($bp->displayed_user->id, 'bp-private-message-privacy', 1) == 'block' ) )
	 remove_action( 'bp_member_header_actions',    'bp_send_private_message_button', 20 );
}
add_action( 'bp_member_header_actions', 'bp_private_message_privacy_set' );


function bp_public_message_privacy_set() {
	global $bp;
	if ( ( get_user_meta($bp->displayed_user->id, 'bp-public-message-privacy', 1) == 'block' ) )
	 remove_action( 'bp_member_header_actions',    'bp_send_public_message_button', 20 );
}
add_action( 'bp_member_header_actions', 'bp_public_message_privacy_set' );


/* --- function originated from r-a-y filters message recipients for non friends --- */
function bp_check_message_recipients( $message_info ) {
	global $bp;


	$recipients = $message_info->recipients;

	$u = 0; // # of recipients in the message that are not friends

	foreach ( $recipients as $key => $recipient ) {

		// if site admin, skip check
		//if( $bp->loggedin_user->is_site_admin == 1 ) {
			//continue;
		//}

		// make sure sender is not trying to send to themselves
		if ( $recipient->user_id == $bp->loggedin_user->id ) {
			unset( $message_info->recipients[$key] );
			continue;
		}

		if ( get_user_meta( $recipient->user_id, 'bp-private-message-privacy', 1) == 'block' ) {
			unset( $message_info->recipients[$key] );
			$u++;

		}

	}

		// if there are multiple recipients and if one of the recipients is not a friend, remove everyone from the recipient's list
		// this is done to prevent the message from being sent to anyone and is another spam prevention measure
		if ( count( $recipients ) > 1 && $u > 0 )
			unset( $message_info->recipients );

}
add_action( 'messages_message_before_save', 'bp_check_message_recipients' );


/* --- function originated from r-a-y. This ovverides bp language file to show proper error message --- */
function buddysuite_override_bp_l10n() {
	global $l10n;

	$slug = 'Team';

	$mo = new MO();
	$mo->add_entry( array( 'singular' => 'There was an error sending that message, please try again', 'translations' => array( __ ('A person(s) you are attempting to send a message to has blocked private messages.  Your message has not been sent.', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'There was a problem sending that reply. Please try again.', 'translations' => array( __ ('A person(s) you are attempting to send a message to has blocked private messages.  Your message has not been sent.', 'buddysuite' ) ) ) );

	/*$mo->add_entry( array( 'singular' => '', 'translations' => array( __ ('', 'buddysuite' ) ) ) );

	$mo->add_entry( array( 'singular' => 'Group', 'translations' => array( __ ('Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Groups Directory', 'translations' => array( __ ('Teams Directory', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Group Admins', 'translations' => array( __ ('Team Admins', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Create a Group', 'translations' => array( __ ('Create Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Search Groups...', 'translations' => array( __ ('Search Teams...', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Groups <span>%d</span>', 'translations' => array( __ ('Teams <span>%d</span>', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Groups <span class=\"count\">%s</span>', 'translations' => array( __ ('Teams <span class=\"count\">%s</span>', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Public Group', 'translations' => array( __ ('Public Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Hidden Group', 'translations' => array( __ ('Hidden Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Private Group', 'translations' => array( __ ('Private Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Viewing group %1$s to %2$s (of %3$s groups)', 'translations' => array( __ ('Viewing team %1$s to %2$s (of %3$s teams)', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Join Group', 'translations' => array( __ ('Join Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Leave Group', 'translations' => array( __ ('Leave Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Delete Group', 'translations' => array( __ ('Delete Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Edit Group', 'translations' => array( __ ('Edit Team', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'My Groups <span>%s</span>', 'translations' => array( __ ('My Teams <span>%s</span>', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'All Groups <span>%s</span>', 'translations' => array( __ ('All Teams <span>%s</span>', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'New Groups', 'translations' => array( __ ('New Teams', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Group Name (required)', 'translations' => array( __ ( $slug . ' Name (required)', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => 'Group Description (required)', 'translations' => array( __ ( $slug . ' Description (required)', 'buddysuite' ) ) ) );
	$mo->add_entry( array( 'singular' => '%1$s posted an update in the group %2$s', 'translations' => array( __ ('%1$s posted an update in the team %2$s', 'buddysuite' ) ) ) ); */

	if ( isset( $l10n['buddypress'] ) )
		$mo->merge_with( $l10n['buddypress'] );

	$l10n['buddypress'] = &$mo;
	unset( $mo );
}
add_action( 'init', 'buddysuite_override_bp_l10n', 9 );


function bp_setup_privacy_nav() {
	global $bp;

	$buddysuite_options = get_option('buddysuite_plugin_options');

	if ( bp_is_active('settings') && empty( $buddysuite_options['disable-privacy-tab'] )  )
		// Add a nav item for this
	bp_core_new_subnav_item( array(
		'name' => __( 'Privacy', 'bp-profile-privacy' ),
		'slug' => 'privacy',
		'parent_slug' => $bp->settings->slug,
		'parent_url' => $bp->displayed_user->domain . $bp->settings->slug . '/',
		'screen_function' => 'bp_privacy_screen_settings_menu',
		'position' => 40,
		'user_has_access' => bp_admin_my_profile() // Only the logged in user and admin can access this on his/her profile
	) );
}
add_action( 'bp_setup_nav', 'bp_setup_privacy_nav');


function bp_admin_my_profile() {
    global $bp;

    if ( is_user_logged_in() && bp_is_user() && $bp->loggedin_user->id == $bp->displayed_user->id || is_user_logged_in() && current_user_can( 'manage_options' ) ) {
        $my_profile = true;
    } else {
        $my_profile = false;
    }
    return apply_filters( 'bp_admin_my_profile', $my_profile );
}


function bp_privacy_screen_settings_menu() {

	global $bp, $current_user, $bp_settings_updated, $pass_error;

		if ( isset( $_POST['submit'] ) ) {

			$comps = $bp->bp_nav;

			foreach (  $comps as $comp => $slug ) {

				$compy = $slug['slug'];

				if ( isset( $_POST['bp-' . $compy . '-privacy'] ) && $bp->displayed_user->id == $bp->loggedin_user->id || isset( $_POST['bp-' . $compy . '-privacy'] ) && current_user_can( 'manage_options' )  ) {
					update_user_meta( $bp->displayed_user->id, 'bp-' . $compy . '-privacy', $compy );
				} else {
					delete_user_meta( $bp->displayed_user->id, 'bp-' . $compy . '-privacy', '' );
				}

			}

			if ( isset( $_POST['bp-private-message-privacy'] ) ) {
				update_user_meta( $bp->displayed_user->id, 'bp-private-message-privacy', 'block' );
			} else {
				delete_user_meta( $bp->displayed_user->id, 'bp-private-message-privacy', '' );
			}

			if ( isset( $_POST['bp-public-message-privacy'] ) ) {
				update_user_meta( $bp->displayed_user->id, 'bp-public-message-privacy', 'block' );
			} else {
				delete_user_meta( $bp->displayed_user->id, 'bp-public-message-privacy', '' );
			}

			if ( isset( $_POST['bp-add-friend-privacy'] ) ) {
				update_user_meta( $bp->displayed_user->id, 'bp-add-friend-privacy', 'block' );
			} else {
				delete_user_meta( $bp->displayed_user->id, 'bp-add-friend-privacy', '' );
			}

		 	bp_core_add_message( __( 'Settings updated!', 'bp-profile-privacy' ) );
		 	bp_core_redirect( bp_displayed_user_domain() . $bp->settings->slug . '/privacy' );

	 	}

	add_action( 'bp_template_content', 'bp_privacy_screen_settings_menu_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

}


function bp_privacy_screen_settings_menu_content() {
	global $bp;

	$comps = $bp->bp_nav;

	foreach( $comps as $comp => $slug  ) {

		if( $slug['slug'] == 'settings' || $slug['slug'] == 'messages' ) {
			unset( $comps[$comp] );
		}
	}
?>

	<form action="" method="post" id="standard-form" name="settings-form">

	<table>
		<thead><tr><th class="title"><?php _e( 'Profile Privacy', 'buddysuite' ) ?></th></tr></thead>
		<tbody>
	<?php
		foreach( $comps as $comp => $slug  ) {

			$compy = $slug['slug'];
	?>
		<tr><td><input name="bp-<?php echo $compy ?>-privacy" type="checkbox" id="bp-<?php echo $compy ?>-privacy" value="<?php echo $compy ?>" <?php if (get_user_meta($bp->displayed_user->id, "bp-" . $compy . "-privacy",1) == $compy ) echo 'checked="checked"' ?> /> <?php echo sprintf( __( 'only show %s page to friends' ), $compy ); ?></td></tr>

	<?php } ?>
		</tbody>
	</table>


	<table>
		<thead><tr><th class="title"><?php _e( 'Message Privacy', 'buddysuite' ) ?></th></tr></thead>
		<tbody>

	<tr><td><input name="bp-private-message-privacy" type="checkbox" id="bp-private-message-privacy" value="block" <?php if (get_user_meta($bp->displayed_user->id, "bp-private-message-privacy",1) == 'block' ) echo 'checked="checked"' ?> /> <?php _e( 'only allow friends to send you private messages' ); ?></td></tr>

	<tr><td><input name="bp-public-message-privacy" type="checkbox" id="bp-public-message-privacy" value="block" <?php if (get_user_meta($bp->displayed_user->id, "bp-public-message-privacy",1) == 'block' ) echo 'checked="checked"' ?> /> <?php _e( 'only allow friends to send you public messages' ); ?></td></tr>
		</tbody>
	</table>


	<table>
		<thead><tr><th class="title"><?php _e( 'Friend Privacy', 'buddysuite' ) ?></th></tr></thead>
		<tbody>

	<tr><td><input name="bp-add-friend-privacy" type="checkbox" id="bp-add-friend-privacy" value="block" <?php if (get_user_meta($bp->displayed_user->id, "bp-add-friend-privacy",1) == 'block' ) echo 'checked="checked"' ?> /> <?php _e( 'block users from adding you as a friend' ); ?></td></tr>
		</tbody>
	</table>


	<div class="submit" style="margin-top:10px;">
	<input type="submit" name="submit" id="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" />
	</div>
	</form>
<?php
} ?>