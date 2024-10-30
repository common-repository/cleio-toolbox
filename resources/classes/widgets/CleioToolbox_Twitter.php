<?php
/**
 * twitter.php - Widget twitter
 *
 * last update: 13/11/2013
 */
 
class CleioToolbox_Twitter extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'cleiotoolbox_twitter', 
			__( 'CleioToolbox - Twitter','cleio' ), 
			array(
				"classname"		=> 'exile-twitter',
				"description" 	=> __( 'Simply display your latest tweets!','cleio' )
			)
		);
		
	}

	function widget( $args, $instance ) {
				
		// Get current options
		extract( $args );
		$optTitle = apply_filters(
					'widget_title', 
					$instance['title'], 
					$instance, 
					$this->id_base
				);

		$optAccount = get_option( 'social-twitter' );
		$nbTweets	=  $instance['nbtweets']*10; 
		$count		=  $instance['nbtweets'];
		$hideReplies = (bool) $instance['hidereplies'];
		$titlelink = $instance['titlelink'];
		
		// Default options
		if ( empty($optAccount) ) 	return;		
		if ( empty($optTitle) ) 	$optTitle = __( 'Twitter Updates' );
		if ( $nbTweets > 200 || !$nbTweets ) $nbTweets = 200;
		
		// Display before widget
		echo $before_widget;
		
		// Display content
		if ( $optTitle ) 	echo $before_title . $optTitle . $after_title;
		if ( $titlelink ) 	echo '<a href="' . esc_url( $optAccount ) . '" class="exile-twitter-account"><span class="fa fa-twitter fa-lg"></span>&nbsp;&nbsp;' . esc_html( $titlelink ) . '</a>';
		
		$tweets = exile_get_tweets( $this->number, $hideReplies, $nbTweets );
		
		// If we got answer from Twitter
		if ( 'error' != $tweets ) {
		
			// List which contain all tweets
			echo '<ul class="exile-twitter-tweets">' . "\n";

				// Init Vars
				$tweets_out = 0;

				// Loop on Tweets retrieved
				foreach ( $tweets as $tweet ) {
				
					// Check counter
					if ( $tweets_out >= $count )	break;
					
					// Check text
					if ( empty( $tweet->text ) ) continue;
					
					// Filter the context of "text" and customize
					$text = make_clickable( esc_html( $tweet->text) );
					$text = preg_replace_callback('/(^|[^0-9A-Z&\/]+)(#|\xef\xbc\x83)([0-9A-Z_]*[A-Z_]+[a-z0-9_\xc0-\xd6\xd8-\xf6\xf8\xff]*)/iu',  array($this, 'exile_twitter_hashtag'), $text);
					$text = preg_replace_callback('/([^a-zA-Z0-9_]|^)([@\xef\xbc\xa0]+)([a-zA-Z0-9_]{1,20})(\/[a-zA-Z][a-zA-Z0-9\x80-\xff-]{0,79})?/u', array($this, 'exile_twitter_username'), $text);
					
					// Retrieve ID of tweets
					if ( isset( $tweet->id_str ) ) $tweet_id = urlencode($tweet->id_str);
					else $tweet_id = urlencode($tweet->id);
					
					// Display the tweet
					echo '<li class="exile-twitter-tweet">';
						echo '<span class="exile-twitter-text">' . $text . '</span>';
						echo '&nbsp;&nbsp;<a href="' . esc_url( 'http://twitter.com/' . $optAccount . '/statuses/' . $tweet_id ) . '" class="exile-twitter-timelink">' . str_replace(' ', '&nbsp;', $this->exile_time_link(strtotime($tweet->created_at))) . '&nbsp;ago</a>';
					echo '</li>';
					
					// Clear data & increment counter
					unset($tweet_id);
					$tweets_out++;
					
				}

			echo "</ul>"; // End of Tweets list
		}
		
		// Handle Error message
		else {
			if ( 401 == $response_code ) {
				echo '<p>' . esc_html( sprintf( __( 'Error: Please make sure the Twitter account is <a href="%s">public</a>.'), 'http://support.twitter.com/forums/10711/entries/14016' ) ) . '</p>';
			}
			else {
				echo '<p>' . esc_html__('Error: Twitter did not respond. Please wait a few minutes and refresh this page.') . '</p>';
			}
		}
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		delete_transient( 'exile-widget-twitter' );
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['nbtweets'] = absint($new_instance['nbtweets']);
		$instance['hidereplies'] = isset($new_instance['hidereplies']);
		$instance['titlelink'] = strip_tags(stripslashes($new_instance['titlelink']));
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('account' => '', 'title' => '', 'show' => 5, 'hidereplies' => false) );
		$title = esc_attr($instance['title']);
		$show = absint($instance['nbtweets']);
		if ( $show < 1 || 20 < $show )
			$show = 5;
		$hidereplies = (bool) $instance['hidereplies'];
		$titlelink = $instance['titlelink'] ? esc_attr($instance['titlelink']) : __("Follow me on Twitter", "cleio");
		
		// Get option
		$optAccount = get_option( 'social-twitter-access-token' );
		echo '<strong>';
		if ( !$optAccount ) {
			echo '<p><a href="' . admin_url('admin.php?page=exile-themeoptions') . '&tab=social_settings">' . __( 'Login and authorize the application first!', 'cleio' ) . '</a></p>';
		}
		else {
			echo '<p>' . __( 'Your account', 'cleio' ) . ': @' . $optAccount['screen_name'] . ' <a href="' . admin_url('admin.php?page=exile-themeoptions') . '&tab=social_settings">' . __( 'edit', 'cleio' ) . '</a><p>';
		}
		echo '</strong>';
		
		echo '<p><label for="' . $this->get_field_id('title') . '">' . esc_html__('Title:') . '
		<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />
		</label></p>
		<p><label for="' . $this->get_field_id('nbtweets') . '">' . __('Maximum number of tweets to show:') . '
			<select id="' . $this->get_field_id('nbtweets') . '" name="' . $this->get_field_name('nbtweets') . '">';

		for ( $i = 1; $i <= 20; ++$i )
			echo "<option value='$i' " . ( $show == $i ? "selected='selected'" : '' ) . ">$i</option>";

		echo '		</select>
		</label></p>
		<p><label for="' . $this->get_field_id('hidereplies') . '"><input id="' . $this->get_field_id('hidereplies') . '" class="checkbox" type="checkbox" name="' . $this->get_field_name('hidereplies') . '"';
		if ( $hidereplies )
			echo ' checked="checked"';
		echo ' /> ' . __('Hide replies') . '</label></p>';
		
		
		
		echo '<p><label for="' . $this->get_field_id('titlelink') . '">' . __('Text for link to Twitter account:') . '
		<input class="widefat" id="' . $this->get_field_id('titlelink') . '" name="' . $this->get_field_name('titlelink') . '" type="text" value="' . $titlelink . '" />
		</label></p>';
		
	}

	/**
	 * Link a Twitter user mentioned in the tweet text to the user's page on Twitter.
	 */
	function exile_twitter_username( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]@<a href='" . esc_url( 'http://twitter.com/' . urlencode( $matches[3] ) ) . "'>$matches[3]</a>";
	}

	/**
	 * Link a Twitter hashtag with a search results page on Twitter.com
	 */
	function exile_twitter_hashtag( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]<a href='" . esc_url( 'http://twitter.com/search?q=%23' . urlencode( $matches[3] ) ) . "'>#$matches[3]</a>";
	}
	
	/**
	 * Link a Twitter tweets on time displayed
	 */
	function exile_time_link( $original, $do_more = 0 ) {
        // array of time period chunks
        $chunks = array(
                array(60 * 60 * 24 * 365 , 'year'),
                array(60 * 60 * 24 * 30 , 'month'),
                array(60 * 60 * 24 * 7, 'week'),
                array(60 * 60 * 24 , 'day'),
                array(60 * 60 , 'hour'),
                array(60 , 'minute'),
        );

        $today = time();
        $since = $today - $original;

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
                $seconds = $chunks[$i][0];
                $name = $chunks[$i][1];

                if (($count = floor($since / $seconds)) != 0)
                        break;
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

        if ($i + 1 < $j) {
                $seconds2 = $chunks[$i + 1][0];
                $name2 = $chunks[$i + 1][1];

                // add second item if it's greater than 0
                if ( (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) && $do_more )
                        $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
        return $print;
	}

}

function exile_get_tweets( $transient_id, $hideReplies, $nbTweets ){

	$tweets = get_transient( 'exile-widget-twitter' );
	if ( !$tweets ) {
		// Retrieve Tweets
		// Set params for the request
		$params = array(
			'screen_name'		=> $access_token['screen_name'],
			'trim_user'			=> true,
			'include_entities'	=> false,
			'exclude_replies' 	=> $hideReplies,
			'count' 			=> $nbTweets
		);
		
		// Define the twitter URL	
		require_once( dirname( dirname( plugin_dir_path( __FILE__ ) ) ) . "/utils/twitteroauth/twitteroauth.php" );
		$access_token = get_option( 'social-twitter-access-token' );

		/* On créé la connexion avec twitter en donnant les tokens d'accès en paramètres.*/ 
		$access_token = get_option( 'social-twitter-access-token' );
		$connection = new TwitterOAuth('aUdUfVxjfv0QBYpNKBxjqw', '8aKZIQMcKfEn24q5vutGcrZ58A5l2F527pYAeVq7l4', $access_token['oauth_token'], $access_token['oauth_token_secret']);	
		$response = $connection->get("statuses/user_timeline", $params );
		
		// Depend on error message
		if ( 200 == $connection->http_code ) {
		
			// Get the body of the response request
			$tweets = $response;
			$expire = 1200;			
						
		} else {
		
			$expire = 1200;		
			$tweets = 'error';	
			
		}
		
		set_transient( 'exile-widget-twitter', $tweets, $expire );
	}
	
	return $tweets;
}

?>