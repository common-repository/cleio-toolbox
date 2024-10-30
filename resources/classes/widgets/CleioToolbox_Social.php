<?php
 class CleioToolbox_Social extends WP_Widget {
 
	var $defaultLinks = Array(							
							'social-fb' 		=> Array('fa fa-facebook-square','Facebook Personal Account'),
							'social-fb-page' 	=> Array('fa fa-facebook-square','Facebook Page'),							
							'social-twitter' 	=> Array('fa fa-twitter','Twitter'),							
							'social-delicious' 	=> Array('fa fa-delicious','Delicious'),							
							'social-flickr' 	=> Array('fa fa-flickr',	'Flickr'),						
							'social-googleplus' => Array('fa fa-google-plus','Google +'),							
							'social-linkedin' 	=> Array('fa fa-linkedin','Linkedin'),							
							'social-skype' 		=> Array('fa fa-skype','Skype'),							
							'RSS' 				=> Array('fa fa-rss','RSS'),							
							'social-vimeo' 		=> Array('fa fa-vimeo-square','Vimeo'),						
							'social-pinterest'	=> Array('fa fa-pinterest','Pinterest'),						
							'social-email' 		=> Array('fa fa-envelope','E-mail'),			
							'social-telephone'	=> Array('fa fa-phone','Telephone'),		
							'social-foursquare'	=> Array('fa fa-foursquare','Foursquare'),					
							'social-instagram'	=> Array('fa fa-instagram','Instagram'),					
							'social-tumblr'		=> Array('fa fa-tumblr','Tumblr'),								
							'social-vine'		=> Array('fa fa-vine','Vine'),				
							'social-youtube'	=> Array('fa fa-youtube-play','YouTube')					
						);
						
    function __construct(){
	
        // Constructor
		parent::__construct(
			'cleiotoolbox_social', 
			__( 'CleioToolbox - Social','cleio' ), 
			array(
				"description" => __( 'Displays links to your social network profiles and contact data. Configure this in Cleio > Cleio Framework > Social &amp; Personal Data.','cleio' )
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
		$optLinks = $instance['links'];
		
		// Display before widget
		echo $before_widget;
		if ( $optTitle ) echo $before_title . $optTitle . $after_title;
		
		// Get the checked list
		if ( $optLinks ) {
			
			echo '<div class="cleiotoolbox-social">';
				echo '<ul>';
						
					// Get the selected links
					foreach( $optLinks as $link ) {
						
						if( $link != 'RSS') {
							if ( $link != "" ) {
								$href = get_option( $link );
								if( $link == 'social-email' ) $href = "mailto:" . $href;								
								echo '<li class="cleiotoolbox-social-item">';
									echo '<span class="' . $this->defaultLinks[$link][0]  . '"></span>&nbsp;&nbsp;<a href="' . $href . '" target="_blank" >' . $this->defaultLinks[$link][1] . '</a>';
								echo '</li>';	
															
								
							}
						}
						else {
							$rss2_url = get_bloginfo_rss('rss2_url');		
							echo '<li class="cleiotoolbox-social-item">';
								echo '<span class="' . $this->defaultLinks[$link][0]  . '"></span>&nbsp;&nbsp;<a href="' . $rss2_url . '" target="_blank" >' . $this->defaultLinks[$link][1] . '</a>';
							echo '</li>';								
						}
					}
					
					
				echo '</ul>';
			echo '</div>';
		}
		// Display after widget
		echo $after_widget;
		
	}
 
    function update($new_instance, $old_instance){
	
        // Update parameters
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['links'] = $new_instance['links'];

		return $instance;
    }
 
    function form($instance){
	
        // Paramters form
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$links = empty($instance['links']) ? Array() : $instance['links'];
		
		wp_enqueue_style('theme_styles');
		wp_enqueue_script( array ("jquery", "jquery-ui-core", "jquery-ui-sortable") );
		
		// Display form
		echo '<p>';
			echo '<label for="' . $this->get_field_id('title') . '">' . _e( 'Title:' ) . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title'). '" type="text" value="' . $title. '" />';
		echo '</p>';
		
		echo '<div id="sortableContainer">';
			echo '<label for="' . $this->get_field_id('links') . '">' . _e( 'Select links and set order (drag & drop)' ) . ':</label>';
			// Note on div content
			$listFull = true;
			foreach( $this->defaultLinks as $optKey => $optDesc ) {
				if ( $optKey != "RSS" ) {
					if ( !get_option( $optKey ) ) $listFull = false;
				}
			}
			$menu = CleioToolbox_Helpers::checkToolboxMenu() ? "cleio-toolbox" : "cleio-base";
			if ( !$listFull ) echo '<p class="description">' . __( 'To display more options, link more networks in','cleio') . ' <a href="' . admin_url('admin.php?page=' . $menu) . '&tab=social_settings"> ' . __( 'Cleio ToolBox Settings', 'cleio') . '</a></p>';
				
			echo '<ul>';
					
				// Get the selected links
				if ( $links ) {
					foreach( $links as $link ) {
						
						if ( $link != "" ) {
						
							echo '<li class="ui-state-default">';
								echo '<input type="checkbox" name="' . $this->get_field_name('links') . '[]" value="' . $link . '" checked="checked"/>&nbsp&nbsp' . $this->defaultLinks[$link][1];
							echo '</li>';	
							
						}
					}
				}
				
				// Draw the full list
				foreach( $this->defaultLinks as $optKey => $optDesc ) {
					
					if ( !in_array( $optKey,$links ) ) {
						
						if ( !get_option( $optKey ) || get_option( $optKey ) == 'http://' ) 	
							$disabled = true;
						else 													
							$disabled = false;
						
						// Particular case
						if( $optKey == 'RSS' ) $disabled = false;
						
						if ( !$disabled ) {
							echo '<li class="ui-state-default">';
								echo '<input type="checkbox" name="' . $this->get_field_name('links') . '[]" value="' . $optKey . '" />&nbsp&nbsp' . $optDesc[1];
							echo '</li>';
						}
					}
				}
				
				
			echo '</ul>';
		echo '</div>';
		
		echo 	'
			<script type="text/javascript">
				jQuery(function () {
					jQuery( "#sortableContainer ul" ).sortable({
							placeholder: "ui-state-highlight"
					});
					jQuery( "#sortableContainer ul" ).disableSelection();		
				});	
			</script>';
	
	}
	
} 
?>