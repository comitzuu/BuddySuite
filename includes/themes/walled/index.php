<?php include 'header.php'; ?>

	<div id="wrapper">
		<div id="login-logo">
		<h1><?php buddysuite_login_logo(); ?></h1>
		</div>
	
		<div id="login-text">
		<?php  if( buddysuite_if_login_page() ) : ?>
			<?php  query_posts( buddysuite_get_login_page() );  ?>
			 <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<h2><?php the_title(); ?></h2>
				<p><?php the_content(); ?></p>
			 <?php endwhile; ?>
			 <?php endif; ?>
			 
		<?php else : ?>
			 <h2><?php _e('Welcome!', 'buddysuite'); ?></h2>
			  <p><?php _e('This is your welcome text. Create a page and then choose it as the login text in the BuddySuite settings.', 'buddysuite'); ?></p>
		 <?php endif; ?>
		</div>
		
		
		<div id="login">

			<form name="login-form" id="login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ); ?>" method="post">
			
				<input type="text" name="log" id="user-login" class="input" placeholder="<?php _e( 'Username', 'buddysuite' ); ?>" value="<?php if ( isset( $user_login) ) echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>
	
				<input type="password" name="pwd" id="user-pass" class="input" placeholder="<?php _e( 'Password', 'buddysuite' ); ?>" value="" tabindex="98" /></label>
	
				<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'buddysuite' ); ?></label></p>
	
				<?php do_action( 'bp_sidebar_login_form' ); ?>
				<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e( 'Log In', 'buddysuite' ); ?>" tabindex="100" />
				<input type="hidden" name="testcookie" value="1" />
			</form>
			
			<?php if ( bp_get_signup_allowed() ) : ?>
				<p id="signup-text">
					<?php printf( __( 'Please <a href="%s" title="Create an account">create an account</a> to get started.', 'buddysuite' ), bp_get_signup_page() ); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>

</body>
</html