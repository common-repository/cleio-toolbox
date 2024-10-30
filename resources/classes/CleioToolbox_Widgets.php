<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( !class_exists( 'CleioToolbox_Widgets' ) ) {

	// Load front-end widget files
	require_once( 'widgets/CleioToolbox_Bio.php' );
	require_once( 'widgets/CleioToolbox_FeaturedPhoto.php' );
	require_once( 'widgets/CleioToolbox_Social.php' );
	require_once( 'widgets/CleioToolbox_FacebookLikebox.php' );
	require_once( 'widgets/CleioToolbox_Instagram.php' );
	require_once( 'widgets/CleioToolbox_Twitter.php' );

	class CleioToolbox_Widgets{
		function __construct()
		{
			// Featured Photo
			add_action( 'widgets_init', create_function( '', 'register_widget( "cleiotoolbox_featuredphoto" );' ) );
			// Bio
			add_action( 'widgets_init', create_function( '', 'register_widget( "cleiotoolbox_bio" );' ) );
			// Social
			add_action( 'widgets_init', create_function( '', 'register_widget( "cleiotoolbox_social" );' ) );
			// Facebook LikeBox
			add_action( 'widgets_init', create_function( '', 'register_widget( "cleiotoolbox_facebooklikebox" );' ) );
			// Instagram			
			add_action( 'widgets_init', create_function( '', 'register_widget( "cleiotoolbox_instagram" );' ));
			// Twitter			
			add_action( 'widgets_init', create_function( '', 'register_widget( "cleiotoolbox_twitter" );' ));
		}
	}
}
?>