<?php my_redirect(); ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="initial-scale=1.0">
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		
		<link rel="stylesheet" type="text/css" href="<?php echo plugins_url(); ?>/buddysuite/includes/themes/walled/style.css" media="screen" />
		
				
		<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
		
		<script type="text/javascript" src="<?php echo plugins_url(); ?>/buddysuite/includes/themes/walled/login.js"></script>
		
		<?php bs_head(); ?>

	</head>
	<body>