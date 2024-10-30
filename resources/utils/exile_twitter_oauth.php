<?php
/**
 * exile_twitter_oauth.php - Twitter frontend connection & redirection 
 *
 * Last update: 13/10/2013
 */
 
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php');
require_once( 'twitteroauth/twitteroauth.php');
$access_token = get_option( 'social-twitter-access-token' );
$oauth_token = get_option( 'social-twitter-access-oauth-token' );
$oauth_token_secret = get_option( 'social-twitter-access-oauth-token-secret' );

if ( !$access_token ) { 
	// Les tokens d'accès ne sont pas encore stockés, il faut vérifier l'authentification
	
	/* On créé la connexion avec twitter en donnant les tokens d'accès en paramètres.*/ 
	$connection = new TwitterOAuth('aUdUfVxjfv0QBYpNKBxjqw', '8aKZIQMcKfEn24q5vutGcrZ58A5l2F527pYAeVq7l4', $oauth_token, $oauth_token_secret );
	
	/* On vérifie les tokens et récupère le token d'accès */
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	
	/* On stocke en session les token d'accès et on supprime ceux qui ne sont plus utiles. */
	update_option( 'social-twitter-access-token', $access_token );
	
	
}
header('Location: ' . admin_url( 'admin.php?page=cleio-toolbox&tab=social_settings' ) ); 
?>