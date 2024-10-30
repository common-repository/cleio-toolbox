<?php
/**
 * exile_instagram_oauth.php - Instagram frontend authentication & redirection
 *
 * Last update: 13/10/2013
 */
 
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php');

if (isset($_GET['code'])) {

	$response = wp_remote_post("https://api.instagram.com/oauth/access_token",
		array(
			'body' => array(
				'code' => $_GET['code'],
				'response_type' => 'authorization_code',
				'redirect_uri' => 'http://cleio.co/exile_instagram_oauth.php?returnurl=' . plugins_url() . '/cleio-toolbox/resources/utils/exile_instagram_oauth.php' ,
				'client_id' => 'a08a7327c07f4300991b8e4aeea87f06',
				'client_secret' => '2826413f0daf4b74943bfcbada5cc535',
				'grant_type' => 'authorization_code',
			),
			'sslverify' => apply_filters('https_local_ssl_verify', false)
		)
	);
	delete_transient( 'exile-widget-instagram' );

	$access_token = null;
	$username = null;
	$image = null;

	$success = false;
	$errormessage = null;
	$errortype = null;

	if(!is_wp_error($response) && $response['response']['code'] < 400 && $response['response']['code'] >= 200):
		$auth = json_decode($response['body']);
		if(isset($auth->access_token)):
			$access_token = $auth->access_token;
			$user = $auth->user;			
			update_option('social-instagram-access-token', $access_token);
			update_option('social-instagram-account-username', $user->username);
			update_option('social-instagram-account-picture', $user->profile_picture);
			update_option('social-instagram-account-fullname', $user->full_name);			
			$success = true;
		endif;
        elseif(is_wp_error($response)):
                $error = $response->get_error_message();
                $errormessage = $error;
                $errortype = 'Wordpress Error';
	elseif($response['response']['code'] >= 400):
		$error = json_decode($response['body']);
		$errormessage = $error->error_message;
		$errortype = $error->error_type;
	endif;  
	
	if (!$access_token):
		delete_option('social-instagram-access-token');
	endif;
}

?>
<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		body, html {
			font-family: arial, sans-serif;
			padding: 30px;

			text-align: center;
		}
	</style>
</head>
<body>
<?php if ($success): ?>
	<script type="text/javascript">
		opener.location.href='<?php echo admin_url( "admin.php?page=cleio-toolbox&tab=social_settings" ); ?>';
   		self.close();
	</script>
<?php else: ?>
	<h1>An error occured</h1>
	<p>
		Type: <?php echo $errortype; ?>
		<br>
		Message: <?php echo $errormessage; ?>
	</p>
	<p>Please make sure you entered the right client details</p>
<?php endif; ?>
</body>
</html>


?>