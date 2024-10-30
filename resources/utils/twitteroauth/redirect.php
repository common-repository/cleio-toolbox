<?php

/* Start session and load library. */
session_start();
require_once('twitteroauth/twitteroauth.php');

/* Build TwitterOAuth object with client credentials. */
$connection = new TwitterOAuth('2TjxDbMNSpIOpPyBQrJddg', '7QlaKSQTu6gpXURrYzNNEut0bO05LUh1Cvy9yFQz0c');
 
/* Get temporary credentials. */
//$request_token = $connection->getRequestToken('http://exile-labs.com/cleio13/wp-content/plugins/cleio-plugin/exile_twitter_oauth.php');
$request_token = $connection->getRequestToken( plugins_url() . '/cleio-plugin/exile_twitter_oauth.php' );

/* Save temporary credentials to session. */
$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
 
/* If last connection failed don't display authorization link. */
switch ($connection->http_code) {

  case 200:
    /* Build authorize URL and redirect user to Twitter. */
    $url = $connection->getAuthorizeURL( $token );
    header('Location: ' . $url); 
    break;
	
  default:
    /* Show notification if something went wrong. */
    echo 'Could not connect to Twitter. Refresh the page or try again later.';
	
}?>