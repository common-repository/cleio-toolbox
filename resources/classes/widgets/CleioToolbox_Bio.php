<?php
 class CleioToolbox_Bio extends WP_Widget {
 
    function __construct(){
	
        // Constructor
		parent::__construct(
			'cleiotoolbox_bio', 
			__( 'CleioToolbox - Bio','cleio' ), 
			array(
				"classname"	=> 'cleio-bio',
				"description" => __( 'Tell your readers who you are! With custom picture and Read More link.','cleio' )
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
		$optDesc 	= $instance['desc'];
		$optLink 	= $instance['link'];
		$optPictureId	= $instance['pictureid'];
		$optType	= $instance['typeImg'];
		$optAvatarUid	= $instance['avataruid'];
		
		// Display before widget
		echo $before_widget;
		if ( $optTitle ) echo $before_title . $optTitle . $after_title;
		
		// Display picture
		if ( $optType == 'image' ) {
			if ( $optPictureId ) echo wp_get_attachment_image( $optPictureId, array( 75, 75 ) );
		}
		else if ( $optType == 'avatar' ) {
			if ( $optAvatarUid ) echo get_avatar( $optAvatarUid, 75 );
		}
		
		if ( ( $optLink != "" ) || ( $optDesc != "" ) ){
		
				// Display description
				if ( $optDesc ) {
					echo '<p>' . $optDesc . '</p>';
				}
				
				// Display link
				if ( $optLink ) {
					echo '<p class="read-more">';
						echo '<a href="' . $optLink . '">';
							echo ' ' . __( 'Read more','cleio' ) . ' &rarr;';
						echo '</a>';
					echo '</p>';
				}			
			
		}
		
		// Display after widget
		echo $after_widget;
				
	}
 
    function update($new_instance, $old_instance){
        // Update parameters
		$instance = $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['desc'] 		= $new_instance['desc'];
		$instance['link'] 		= $new_instance['link'];
		$instance['pictureid']	= $new_instance['pictureid'];
		$instance['typeImg'] 	= $new_instance['typeImg'];
		$instance['avataruid'] 	= $new_instance['avataruid'];
		
		return $instance;
    }
 
    function form($instance){
			
        // Paramters form
		//Defaults
		$instance 	= wp_parse_args( (array) $instance, array( 'title' => '') );
		$title 		= esc_attr( $instance['title'] );
		$desc 		= esc_attr( $instance['desc'] );
		$link 		= $instance['link'] ? $instance['link'] : "http://";
		$pictureId 	= $instance['pictureid'];
		$typeImg 	= $instance['typeImg'];
		$avatarUid 	= $instance['avataruid'];
		
		if ( $typeImg == 'avatar'){
			$checkedAvatar = ' checked="checked" ';
			$checkedImage = '';
			$checkedNoImage = '';
		}
		else if( $typeImg == 'image' ) {
			$checkedAvatar = '';
			$checkedImage = ' checked="checked" ';
			$checkedNoImage = '';
		}
		else {
			$checkedAvatar = '';
			$checkedImage = '';
			$checkedNoImage = ' checked="checked" ';
		
		}
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('title') . '">' . __( 'Widget title (optional)','cleio' ) . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title'). '" type="text" value="' . $title. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('desc') . '">' . __( 'Short biography','cleio' ) . '</label><br />';
			echo '<textarea rows="4" cols="30" id="' . $this->get_field_id('desc') . '" name="' . $this->get_field_name('desc'). '">';
				echo $desc;
			echo '</textarea>';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('link') . '">' . __( 'Read more link (optional)','cleio' ) . '</label><br />';
			echo '<input class="widefat" id="' . $this->get_field_id('link') . '" name="' . $this->get_field_name('link'). '" type="text" value="' . $link. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('picture') . '">' . __( 'Portrait','cleio' ) . '&nbsp&nbsp&nbsp</label>';
			echo '<span><ul>';
				echo '<li><input type="radio" name="' . $this->get_field_name('typeImg') . '" value="image"' . $checkedImage . ' onchange="_select_bio( this );" class="exile-img-bio" />&nbsp' . __( 'Upload new picture','cleio') . '</li>';
				echo '<li><input type="radio" name="' . $this->get_field_name('typeImg') . '" value="avatar"' . $checkedAvatar . ' onchange="_select_bio( this );" class="exile-img-bio" />&nbsp' . __( 'Use Wordpress user avatar','cleio') . '</li>';
				echo '<li><input type="radio" name="' . $this->get_field_name('typeImg') . '" value="noimage"' . $checkedNoImage . ' onchange="_select_bio( this );" class="exile-img-bio" />&nbsp' . __( 'No image','cleio') . '</li>';
			echo '</ul></span>';
			
			if ( $typeImg == 'avatar' || $typeImg == 'noimage' || $typeImg == '' ) $styleImage = ' style="display:none;" ';
			else $styleImage = "";
			
			echo '<div id="' . $this->get_field_id('typeImg') . '-image"' . $styleImage . '>';
				echo '<div style="">';
					if ( $pictureId ) echo wp_get_attachment_image( $pictureId, array( 75, 75 ) );
				echo '</div>';
				echo '<p class="exile-media-control"
							data-title="Choose an Image for the Widget"
							data-update-text="Update Image"
							data-target=".image-id"
							data-select-multiple="false">
						<input type="hidden" name="' . $this->get_field_name('pictureid') . '" id="' . $this->get_field_id('pictureid') . '" value="' . $pictureId . '" class="image-id exile-media-control-target">';
						if ( $pictureId ) echo '<a href="#" style="display:none;" class="button exile-media-control-choose" id="' . $this->id . '-img-bt">Choose an Image</a>';
						else echo '<a href="#" class="button exile-media-control-choose" id="' . $this->id . '-img-bt">Choose an Image</a>';
				echo '</p>';
				echo '<p class="description">' . __( 'The picture preview is updated after saving','cleio' ) . '</p>';
			echo '</div>';
			
			if ( $typeImg == 'image' || $typeImg == 'noimage' || $typeImg == '' ) $styleAvatar = ' style="display:none;" ';
			else $styleAvatar = "";
			
			echo '<div id="' . $this->get_field_id('typeImg') . '-avatar"' . $styleAvatar . '>';
				if ( $avatarUid ) echo get_avatar( $avatarUid, 75 );
				$blogusers = get_users();
				echo '<select class="widefat" id="' . $this->get_field_id('avataruid') . '" name="' . $this->get_field_name('avataruid') . '">';
				foreach ($blogusers as $user) {
					if ( $user->ID == $avatarUid ) 	$selectedUid = " selected='selected'";
					else							$selectedUid = "";
					echo '<option value="' . $user->ID . '"' . $selectedUid . '>' . $user->user_nicename . '</option>';
				}
				echo '</select>';
			echo '</div>';
		echo '</p>';
		
		
		// Loading script (mediabox);
		wp_enqueue_media();
		wp_enqueue_script( 'admin-bio-js', plugins_url() . "/cleio-toolbox/resources/js/widget.admin-bio.js" );
		wp_localize_script( 'admin-bio-js', 'cleiovar', Array( 'typeImg' => $typeImg ) );
		
    }
	
} 
?>