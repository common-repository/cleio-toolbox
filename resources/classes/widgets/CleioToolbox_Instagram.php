<?php
class CleioToolbox_Instagram extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'cleiotoolbox_instagram', 
			__( 'CleioToolbox - Instagram','cleio' ), 
			array(
				"classname"		=> 'exile-instagram',
				"description" 	=> __( 'Simply display your latest pictures!','cleio' )
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
		$optAccount = get_option( 'social-instagram-account-username' );
		$count		=  $instance['nbpictures'];
		$titlelink 	= $instance['titlelink'];
		$displaylink= !empty( $instance['displaylink'] ) ? 1 : 0;	
		$size = $instance['size'] ? $instance['size'] : 90;
		$instance['access_token'] = get_option( 'social-instagram-access-token' );
		// Display before widget
		echo $before_widget;
		
		// Display content
		if ( $optTitle ) 	echo $before_title . $optTitle . $after_title;
		if ( $displaylink ) 	echo '<a href="' . esc_url( 'http://instagram.com/' . $optAccount ) . '" class="exile-instagram-account"><span class="fa fa-instagram fa-lg"></span>&nbsp;&nbsp;' . esc_html( $titlelink ) . '</a>';
			
		// Get images to display
		
		$pictures = $this->instagram_get_latest( $instance );	
		if ( !is_array($pictures) ){
			$array_pictures = unserialize($pictures);
		}
		else {
			$array_pictures = $pictures;		
		}
		echo '<ul class="exile-instagram-container">';
		foreach($array_pictures as $image){
		
			echo '<li class="exile-instagram-item">';
				echo '<a href="'.$image['image_large'].'" title="'.$image['title'].'" class="selector-magnific-popup">';
					echo '<img src="'.$image['image_small'].'" alt="'.$image['title'].'" style="width:'.$size.'px;height:'.$size.'px;" />';
				echo '</a>';
			echo '</li>';
			
		}
		echo '</ul>';

		// Display after widget
		echo $after_widget;
		
		// Enqueue script
		wp_enqueue_script( 'mp-js', plugins_url() . '/cleio-toolbox/resources/js/jquery.magnific-popup.min.js', array( 'jquery' ) );
		wp_enqueue_style( 'mp-css', plugins_url() . '/cleio-toolbox/resources/css/magnific-popup.css');
		wp_enqueue_script( 'cleio-mp-js', plugins_url() . '/cleio-toolbox/resources/js/cleio.magnific-popup.js' );
		
	}
		
	function instagram_get_latest($instance){
		
		$images = array();
		$images = get_transient( 'exile-widget-instagram' );
		if ( !$images ) {
			if($instance['access_token'] != null){
				$expire = 1200;
				if(isset($instance['hashtag']) && trim($instance['hashtag']) != "" && preg_match("/[a-zA-Z0-9_\-]+/i", $instance['hashtag'])):
					$hashtag = $instance['hashtag'];
					if (substr($hashtag, 0, 1) == '#'):
						$hashtag = substr($hashtag, 1);
					endif;
					$apiurl = "https://api.instagram.com/v1/tags/".$hashtag."/media/recent?count=".$instance['nbpictures']."&access_token=".$instance['access_token'];
				else:
					$apiurl = "https://api.instagram.com/v1/users/self/media/recent?count=".$instance['nbpictures']."&access_token=".$instance['access_token'];
				endif;
				
				$response = wp_remote_get($apiurl,
					array(
						'sslverify' => apply_filters('https_local_ssl_verify', false)
					)
				);
				
				if(!is_wp_error($response) && $response['response']['code'] < 400 && $response['response']['code'] >= 200){
					$data = json_decode($response['body']);
					if($data->meta->code == 200){
						
						foreach($data->data as $item){

							if(isset($instance['hashtag'], $item->caption->text)) $image_title = $item->user->username.': &quot;'.filter_var($item->caption->text, FILTER_SANITIZE_STRING).'&quot;';
							elseif(isset($instance['hashtag']) && !isset($item->caption->text))	$image_title = "instagram by ".$item->user->username;
							else $image_title = filter_var($item->caption->text, FILTER_SANITIZE_STRING);
						
							$images[] = array(
								"id" => $item->id,
								"title" => CleioToolbox_Instagram::remove_emoji( $image_title ),
								"image_small" => $item->images->thumbnail->url,
								"image_middle" => $item->images->low_resolution->url,
								"image_large" => $item->images->standard_resolution->url
							);
						}
						
					}
				}
				else {
					$images = __("Error: Instagram did not respond. Please wait a few minutes and refresh this page.", "cleio");
					$expire = 600;
				}
				
				set_transient( 'exile-widget-instagram', $images, $expire );
			}
			else {
				
				$images = __("Error: Instagram did not respond. Please wait a few minutes and refresh this page.", "cleio");
				$expire = 600;				
				set_transient( 'exile-widget-instagram', $images, $expire );
				
			}
			update_option( 'exile-widget-instagram', $images );
		}
		else {
			$images = get_option( 'exile-widget-instagram' );
		}
		return $images;
		
	}
	
	function remove_emoji($text){
		return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['titlelink'] = strip_tags(stripslashes($new_instance['titlelink']));
		$instance['nbpictures'] = absint($new_instance['nbpictures']);
		$instance['size'] = absint($new_instance['size']);
		$instance['displaylink'] = !empty($new_instance['displaylink']) ? 1 : 0;
		delete_transient( 'exile-widget-instagram' );
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title' => '', 'nbpictures' => 5) );
		$title = esc_attr($instance['title']);
		$titlelink = esc_attr($instance['titlelink']);
		$nbpictures = absint($instance['nbpictures']);
		$size = absint($instance['size']) ? absint($instance['size']) : 90;
		$displaylink = isset( $instance['displaylink'] ) ? (bool) $instance['displaylink'] : false;		
		if ( $displaylink ) 	$checkedLink = ' checked="checked" ';
		else 				$checkedLink = '';	
		
		// Get access token, if not say it to user
		$access_token = get_option( 'social-instagram-access-token' );
		echo '<strong>';
		if ( !$access_token ) {
			$menu = CleioToolbox_Helpers::checkToolboxMenu() ? "cleio-toolbox" : "cleio-base";
			echo '<p><a href="' . admin_url('admin.php?page='.$menu) . '&tab=social_settings">' . __( 'Log into your account & authorize the application first!', 'cleio' ) . '</a></p>';
		}
		else {
			$menu = CleioToolbox_Helpers::checkToolboxMenu() ? "cleio-toolbox" : "cleio-base";
			echo '<p>' . __( 'Your account', 'cleio' ) . ': ' . get_option( 'social-instagram-account-username' ) . ' <a href="' . admin_url('admin.php?page='.$menu) . '&tab=social_settings">' . __( 'edit', 'cleio' ) . '</a><p>';
		}
		echo '</strong>';
		
		// Get option
		echo '<p>';
			echo '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'cleio') . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('nbpictures') . '">' . __('Number of pictures:', 'cleio') . '</label>';
			echo '<select id="' . $this->get_field_id('nbpictures') . '" name="' . $this->get_field_name('nbpictures') . '">';
				for ( $i = 1; $i <= 20; ++$i ) echo "<option value='$i' " . ( $nbpictures == $i ? "selected='selected'" : '' ) . ">$i</option>";
			echo '</select>';
		echo '</p>';
		
		echo '<p>';
			echo '<input id="' . $this->get_field_id('displaylink') . '" name="' . $this->get_field_name('displaylink') . '" type="checkbox" class="checkbox" '. $checkedLink .' />&nbsp;';
			echo '<label for="' . $this->get_field_id('displaylink') . '">' . __('Display link to Instagram', 'cleio') . '</label>';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('titlelink') . '">' . __('Link Title:', 'cleio') . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('titlelink') . '" name="' . $this->get_field_name('titlelink') . '" type="text" value="' . $titlelink . '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('size') . '">' . __('Thumbnail size (proportional, in pixels)', 'cleio') . ':</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('size') . '" name="' . $this->get_field_name('size') . '" type="text" value="' . $size . '" />';
		echo '</p>';
		
	}
	
}


?>