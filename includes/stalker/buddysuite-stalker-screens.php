<?php
global $buddysuite_options;

$buddysuite_options = get_option('buddysuite_plugin_options');

function buddysuite_saveUserViews(){
 	global $bp, $wpdb, $buddysuite_options;

	if( !empty( $buddysuite_options['track-viewer'] ) ) {
	 	$who_veiewed = get_user_meta( $bp->displayed_user->id, 'bp-stalker', true );

	 	$arrayData[] = unserialize( $who_veiewed );

	 	$count_viewers = count( $arrayData );

	 	unset( $arrayData[0] );

	 	if ( $count_viewers && $count_viewers == 20 )

	 		unset( $arrayData[0] );

	 	$arrayData = array_values( $arrayData );

	 	if ( bp_is_my_profile() || !is_user_logged_in() || in_array( $bp->loggedin_user->id, $arrayData ) == true )
	 	return false;

	 	$arrayData[] = $bp->loggedin_user->id;

		$serializedData = serialize( $arrayData );

		//delete_user_meta( $bp->displayed_user->id, 'bp-stalker' );

	  	update_user_meta( $bp->displayed_user->id, 'bp-stalker', $serializedData );
	}


}
add_action( 'bp_profile_header_meta', 'buddysuite_saveUserViews' );


function buddysuite_stalker_setup_nav() {
		global $buddysuite_options;

	if( !empty( $buddysuite_options['track-viewer'] ) ) {
		bp_core_new_subnav_item( array(
			'name' 		  => __( 'Viewers', 'bp-example' ),
			'slug' 		  => 'viewers',
			'parent_slug'     => bp_get_friends_slug(),
			'parent_url' 	  => trailingslashit( bp_loggedin_user_domain() . bp_get_friends_slug() ),
			'screen_function' => 'buddysuite_stalker_screen_one',
			'position' 	  => 40,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		) );
	}


}
add_action( 'bp_setup_nav', 'buddysuite_stalker_setup_nav' );


function buddysuite_stalker_screen_one() {
	global $bp;
	add_action( 'bp_template_content', 'buddysuite_stalker_screen_one_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function buddysuite_stalker_screen_one_title() {
	global $bp;
}

function buddysuite_stalker_screen_one_content() {
	global $bp;


	$who_veiewed = get_user_meta( $bp->displayed_user->id, 'bp-stalker', true );

	$arrayDefaultData = unserialize( $who_veiewed ) ;


	if ( $arrayDefaultData ) {

		$arrayData = array_reverse( $arrayDefaultData );
		$count_viewers = count( $arrayData );

	} else {

		$arrayData = array_reverse(  array( 0 ) );
		$count_viewers = 0 ;

	}


	echo '<div id="pag-top" class="pagination"><div class="pag-count" id="member-dir-count-top">';
		_e( "Viewing profile visitors  ", "bp-stalker" );
		_e('(last '. $count_viewers .' visitors)', 'bp-stalker');
	echo '</div><div class="pagination-links" id="member-dir-pag-top"></div></div>';
	echo '<ul id="members-list" class="item-list" role="main">';

	if ( $arrayData[0] == 0 ) :
	echo _e( 'No recent viewers', 'bp-stalker' );
	else :
	foreach ( $arrayData as $key => $value ) {

		$value = (string) $value;

    	echo '<li style="min-height:55px";>
    	<div class="item-avatar">
    	<a href="'. bp_core_get_user_domain( $value ) .'">'. bp_core_fetch_avatar( array( 'item_id' => $value, 'type' => 'thumb' ) ) .'</a>
    	</div>
    	<div class="item">
    	<div class="item-title"><a href="'. bp_core_get_user_domain( $value ) .'">'. bp_core_get_username( $value ) .'</a></div>
    	<div class="item-meta"><span class="activity">'. bp_core_get_last_activity( bp_get_user_meta( $value, 'last_activity', true ), __('active %s', 'bp-stalker') ) .'</span></div></li>';
	}
	endif;
	echo '</ul>';
}