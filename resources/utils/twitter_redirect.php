<?php
/**
 * redirect.php - Twitter frontend connection & redirection 
 *
 * Last update: 13/10/2013
 */
 
/* Start session and load library. */
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php');
require_once( 'twitteroauth/twitteroauth.php');
delete_transient( 'exile-widget-twitter' );

/* Build TwitterOAuth object with client credentials. */
$connection = new TwitterOAuth('aUdUfVxjfv0QBYpNKBxjqw', '8aKZIQMcKfEn24q5vutGcrZ58A5l2F527pYAeVq7l4');
 
/* Get temporary credentials. */
$request_token = $connection->getRequestToken( get_site_url() . '/wp-content/plugins/cleio-toolbox/resources/utils/exile_twitter_oauth.php');

/* Save temporary credentials to session. */
$token = $request_token['oauth_token'];
update_option( 'social-twitter-access-oauth-token', $token );
update_option( 'social-twitter-access-oauth-token-secret', $request_token['oauth_token_secret'] );


/* If last connection failed don't display authorization link. */
switch ($connection->http_code) {
  case 200:
    /* Build authorize URL and redirect user to Twitter. */
	$redirect_url = $connection->getAuthorizeURL( $token );
    header('Location: ' . $redirect_url); 
    break;
  default:
    /* Show notification if something went wrong. */
    echo 'Could not connect to Twitter. Refresh the page or try again later.';
    //header('Location: ' . $url); 
}

?>