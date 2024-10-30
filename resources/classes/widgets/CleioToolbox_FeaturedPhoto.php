<?php
class CleioToolbox_FeaturedPhoto extends WP_Widget {
 
    function __construct(){	
        // Constructor
		parent::__construct(
			'cleiotoolbox_featuredphoto', 
			__( 'CleioToolbox - Latest Photo Post','cleio' ), 
			array(
				"classname"		=> 'cleio-featuredphoto',
				"description" => __( 'Display your latest Featured Photo.','cleio' )
			)
		);		
    }
 
    function widget($args, $instance){
		global $wpdb;			
		// Get current options
		extract( $args );
		$optTitle = apply_filters(
					'widget_title', 
					$instance['title'], 
					$instance, 
					$this->id_base
				);		
		$optPhotoTitle 	= !empty( $instance['photoTitle'] ) ? '1' : '0';	
				
		// Get the photo 		
		global $wpdb;
		$sql = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'photo' ORDER BY post_date DESC LIMIT 1";
		$photo = $wpdb->get_var( $sql );
		//$photo = wp_get_recent_posts( array( 'suppress_filters' => true, 'numberposts' => 1, 'post_status' => 'publish', 'post_type' => 'photo'  ) );
				
		if( $photo ) {
			// Replace the current global post
			global $post;
			$oldPost 	= $post;
			$post 		= get_post($photo);
			$permalink 	= get_permalink();
			$post_title = get_the_title();
						
			// Display before widget
			echo $before_widget;
			if ( $optTitle ) echo $before_title . $optTitle . $after_title;
			
			echo '<a href="' . $permalink . '" rel="bookmark" title="'. __("Permalink to") . ' ' . $post_title . '" >';
				the_post_thumbnail('large');
			echo '</a>';			
			if ( $optPhotoTitle ){
				echo '<p>';
					echo '<a href="' . $permalink . '" rel="bookmark" title="'. __("Permalink to") . ' ' . $post_title . '" >' . $post_title . ' &rarr;</a>';
				echo '</p>';
			}
			
			// Display after widget
			echo $after_widget;
			
			// Reload the global post
			$post = $oldPost;
		}
	}
 
    function update($new_instance, $old_instance){
	
        // Update parameters
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['photoTitle'] = !empty($new_instance['photoTitle']) ? 1 : 0;

		return $instance;
		
    }
 
    function form($instance){
        // Paramters form
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );	
		$photoTitle = isset( $instance['photoTitle'] ) ? (bool) $instance['photoTitle'] : false;
		
		if ( $photoTitle ) 	$checkedPhotoTitle = ' checked="checked" ';
		else 				$checkedPhotoTitle = '';	
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('title') . '">' . _e( 'Title:' ) . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title'). '" type="text" value="' . $title. '" />';
		echo '</p>';
		echo '<p>';
			echo '<input type="checkbox" class="checkbox" id="' .  $this->get_field_id('photoTitle'). '" name="' .  $this->get_field_name('photoTitle'). '"' . $checkedPhotoTitle . ' />&nbsp';
			echo '<label for="' .  $this->get_field_id('photoTitle'). '"> ' . _e( 'Display the photo title' ). '</label><br />';
		echo '</p>';
    }
	
} 
?>