<?php

if ( !is_admin() ) {
global $buddysuite_options;

$buddysuite_options = get_option('buddysuite_plugin_options');

// remove profile field links
if ( !empty( $buddysuite_options['auto-link'] ) ) {
	function remove_xprofile_links() {
		remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
	}
	add_action( 'init', 'remove_xprofile_links' );
}


//ignore old code
if ( !empty( $buddysuite_options['deprecated-code'] ) ) {
	define ( 'BP_IGNORE_DEPRECATED', true );
}

// root profiles
if ( !empty( $buddysuite_options['root-profile'] ) ) {
	add_filter( 'bp_core_enable_root_profiles', '__return_true' );
}

// turn off gravatar
if ( !empty( $buddysuite_options['disable-gravatar'] ) ) {
	add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );
}

//thumbnail sizes
if ( !empty( $buddysuite_options['thumbsize'] )  ) {
	define ( 'BP_AVATAR_THUMB_WIDTH',  $buddysuite_options['thumbsize'] );
	define ( 'BP_AVATAR_THUMB_HEIGHT',  $buddysuite_options['thumbsize'] );
}

// avatar sizes
if ( !empty( $buddysuite_options['avisize'] )  ) {
	define ( 'BP_AVATAR_FULL_WIDTH', $buddysuite_options['avisize'] );
	define ( 'BP_AVATAR_FULL_HEIGHT', $buddysuite_options['avisize'] );
}

// maximum avatar size
if ( !empty( $buddysuite_options['max-avisize'] )  ) {
	define ( 'BP_AVATAR_ORIGINAL_MAX_WIDTH', $buddysuite_options['max-avisize'] );
}

//allows special characters in usernames
if ( !empty( $buddysuite_options['username-compat'] ) ) {

	//function buddysuite_username_compatibility() {
		define( 'BP_ENABLE_USERNAME_COMPATIBILITY_MODE', true );
	//}
	//add_action( 'bp_init', 'buddysuite_username_compatibility' );
}

// silences nagging theme notice
if ( !empty( $buddysuite_options['theme-notice'] ) ) {
	define( 'BP_SILENCE_THEME_NOTICE', true );
}

// remove custom header option
if ( !empty( $buddysuite_options['custom-header'] ) ) {
	define( 'BP_DTHEME_DISABLE_CUSTOM_HEADER', true );
}

// default profile tab
if ( !empty( $buddysuite_options['profile-tab-default'] ) ) {
	define( 'BP_DEFAULT_COMPONENT', $buddysuite_options['profile-tab-default'] );
}

// default group tab
if ( !empty( $buddysuite_options['group-tab-default'] ) ) {
	define( 'BP_GROUPS_DEFAULT_EXTENSION', $buddysuite_options['group-tab-default'] );
}

// disables user @ mentions
if ( !empty( $buddysuite_options['disable-mentions'] ) ) {

	function remove_user_mentions() {
	// removes @mention links in updates, forum posts, etc.
	remove_filter( 'bp_activity_after_save', 'bp_activity_at_name_filter_updates' );
	remove_filter( 'groups_activity_new_update_content', 'bp_activity_at_name_filter' );
	remove_filter( 'pre_comment_content', 'bp_activity_at_name_filter' );
	remove_filter( 'group_forum_topic_text_before_save', 'bp_activity_at_name_filter' );
	remove_filter( 'group_forum_post_text_before_save', 'bp_activity_at_name_filter' );
	remove_filter( 'bp_activity_comment_content', 'bp_activity_at_name_filter' );

	// remove @mention email notifications
	remove_action( 'bp_activity_posted_update', 'bp_activity_at_message_notification', 10, 3 );
	remove_action( 'bp_groups_posted_update', 'groups_at_message_notification', 10, 4 );
	}
	add_action( 'bp_init', 'remove_user_mentions' );

}


//disable responsive css
if ( !empty( $buddysuite_options['responsive-css'] ) ) {
	function buddysuite_enqueue_styles() {
		remove_theme_support( 'bp-default-responsive' );
	}
	add_action( 'wp_enqueue_scripts', 'buddysuite_enqueue_styles', 5 );
}

function buddysuite_change_profile_tabs() {
	global $bp, $buddysuite_options;

	//$bp->bp_nav['activity']['link'] = $bp->activity->slug . '/my-stream-activity';

	if ( bp_is_user() ) {

	if ( !empty( $buddysuite_options['profile-tab-arrange'] ) ) {
		$bp->bp_nav['profile']['position'] = $buddysuite_options['profile-tab-arrange'];
	}
	if ( !empty( $buddysuite_options['profile-tab-text'] ) ) {
		$bp->bp_nav['profile']['name'] = $buddysuite_options['profile-tab-text'];
	}

	if ( !empty( $buddysuite_options['activity-tab-arrange'] ) ) {
		$bp->bp_nav['activity']['position'] = $buddysuite_options['activity-tab-arrange'];
	}
	if ( !empty( $buddysuite_options['activity-tab-text'] ) ) {
		$bp->bp_nav['activity']['name'] = $buddysuite_options['activity-tab-text'];
	}

	if ( !empty( $buddysuite_options['messages-tab-arrange'] ) ) {
		$bp->bp_nav['messages']['position'] = $buddysuite_options['messages-tab-arrange'];
	}
	if ( !empty( $buddysuite_options['messages-tab-text'] ) ) {
		$bp->bp_nav['messages']['name'] = sprintf( $buddysuite_options['messages-tab-text'] . ' <span>%s</span>' , bp_get_total_unread_messages_count() ) ;
	}


	if ( !empty( $buddysuite_options['groups-tab-arrange'] ) ) {
		$bp->bp_nav['groups']['position'] = $buddysuite_options['groups-tab-arrange'];
	}
	if ( !empty( $buddysuite_options['groups-tab-text'] ) ) {
		$bp->bp_nav['groups']['name'] = sprintf( $buddysuite_options['groups-tab-text'] . ' <span>%s</span>' , bp_get_total_group_count() ) ;
	}

	if ( !empty( $buddysuite_options['friends-tab-arrange'] ) ) {
		$bp->bp_nav['friends']['position'] = $buddysuite_options['friends-tab-arrange'];
	}
	if ( !empty( $buddysuite_options['friends-tab-text'] ) ) {
		$bp->bp_nav['friends']['name'] = sprintf( $buddysuite_options['friends-tab-text'] . ' <span>%s</span>' , bp_get_total_friend_count() ) ;
	}

	if ( !empty( $buddysuite_options['settings-tab-arrange'] ) ) {
		$bp->bp_nav['settings']['position'] = $buddysuite_options['settings-tab-arrange'];
	}
	if ( !empty( $buddysuite_options['settings-tab-text'] ) ) {
		$bp->bp_nav['settings']['name'] = $buddysuite_options['settings-tab-text'];
	}

	if ( !empty( $buddysuite_options['forums-tab-arrange'] ) ) {
		$bp->bp_nav['forum']['position'] = $buddysuite_options['forums-tab-arrange'];
	}
	if ( !empty( $buddysuite_options['forums-tab-text'] ) ) {
		$bp->bp_nav['forum']['name'] = $buddysuite_options['forums-tab-text'];
	}

	}
}
add_action( 'bp_init', 'buddysuite_change_profile_tabs', 999 );


function buddysuite_remove_xprofile_tabs(){
	global $bp, $buddysuite_options;

	if ( bp_is_user() ) {

	if ( !empty( $buddysuite_options['forums-tab-remove'] ) ) {
		bp_core_remove_nav_item( 'forums' );
	}

	if ( !empty( $buddysuite_options['activity-tab-remove'] ) ) {
		bp_core_remove_nav_item( 'activity' );
	}

	if ( !empty( $buddysuite_options['groups-tab-remove'] ) ) {
		bp_core_remove_nav_item( 'groups' );
	}

	if ( !empty( $buddysuite_options['settings-tab-remove'] ) ) {
		bp_core_remove_nav_item( 'settings' );
	}

	if ( !empty( $buddysuite_options['friends-tab-remove'] ) ) {
		bp_core_remove_nav_item( 'friends' );
	}

	if ( !empty( $buddysuite_options['messages-tab-remove'] ) ) {
		bp_core_remove_nav_item( 'messages' );
	}

	if ( !empty( $buddysuite_options['profile-tab-remove'] ) ) {
		bp_core_remove_nav_item( 'profile' );
	}

	if ( !empty( $buddysuite_options['disable-mentions'] ) ) {
		bp_core_remove_subnav_item( $bp->activity->slug, 'mentions' );
	}

	}
}
add_action( 'bp_setup_nav', 'buddysuite_remove_xprofile_tabs', 15 );


function buddysuite_change_group_tab_order() {
	global $bp, $buddysuite_options;

	if ( bp_is_group() ) {

	if ( !empty( $buddysuite_options['group-home-tab'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['home']['position'] = $buddysuite_options['group-home-tab'];
	}
	if ( !empty( $buddysuite_options['group-home-tab-text'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['home']['name'] = $buddysuite_options['group-home-tab-text'];
	}

	if ( !empty( $buddysuite_options['group-forum-tab'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['forum']['position'] = $buddysuite_options['group-forum-tab'];
	}
	if ( !empty( $buddysuite_options['group-forum-tab-text'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['forum']['name'] = $buddysuite_options['group-forum-tab-text'];
	}

	if ( !empty( $buddysuite_options['group-invites-tab'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['send-invites']['position'] = $buddysuite_options['group-invites-tab'];
	}
	if ( !empty( $buddysuite_options['group-invites-tab-text'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['send-invites']['name'] = $buddysuite_options['group-invites-tab-text'];
	}

	if ( !empty( $buddysuite_options['group-members-tab'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['members']['position'] = $buddysuite_options['group-members-tab'];
	}
	if ( !empty( $buddysuite_options['group-members-tab-text'] ) ){
		$bp->bp_options_nav[$bp->groups->current_group->slug]['members']['name'] = $buddysuite_options['group-members-tab-text'];
	}

	if ( !empty( $buddysuite_options['group-admin-tab'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['admin']['position'] = $buddysuite_options['group-admin-tab'];
	}
	if ( !empty( $buddysuite_options['group-admin-tab-text'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['admin']['name'] = $buddysuite_options['group-admin-tab-text'];
	}

	}

}
add_action( 'bp_init', 'buddysuite_change_group_tab_order' );


function buddysuite_remove_group_tabs() {
	global $bp, $buddysuite_options;

	if ( bp_is_group() ) {

	if ( !empty( $buddysuite_options['group-forum-tab-remove'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['forum'] = false;
	}

	if ( !empty( $buddysuite_options['group-home-tab-remove'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['home'] = false;
	}

	if ( !empty( $buddysuite_options['group-invites-tab-remove'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['send-invites'] = false;
	}

	if ( !empty( $buddysuite_options['group-members-tab-remove'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['members'] = false;
	}

	if ( !empty( $buddysuite_options['group-admin-tab-remove'] ) ) {
		$bp->bp_options_nav[$bp->groups->current_group->slug]['admin'] = false;
	}

	}
}
add_action( 'bp_init', 'buddysuite_remove_group_tabs' );


// display username in member directory
if ( !empty( $buddysuite_options['directory-username'] ) ) {
	function buddysuite_directory_username() {
		global $members_template;
		return $members_template->member->user_login;
	}
	add_filter( 'bp_member_name','buddysuite_directory_username' );
}




}