<?php
class CleioToolbox_FacebookLikebox extends WP_Widget {
 						
    function __construct(){
	
        // Constructor
		parent::__construct(
			'cleiotoolbox_facebooklikebox', 
			__( 'Cleio Toolbox - Facebook Like Box','cleio' ), 
			array(
				"description" => __( 'The Facebook Like Box advertizes your Facebook Page and Fans.','cleio' )
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
		$optWidth 	= $instance['width'] == "" ? '260' : $instance['width'];
		$optHeight 	= $instance['height'];
		//$optBordercolor = $instance['bordercolor'];
		$optFaces 	= !empty( $instance['showfaces'] ) ? 'true' : 'false';
		$optHeader 	= !empty( $instance['showheader'] ) ? 'true' : 'false';
		$optStream 	= !empty( $instance['showstream'] ) ? 'true' : 'false';
		$optTheme 	= $instance['theme'];
		
		// Display before widget
		echo $before_widget;
		if ( $optTitle ) echo $before_title . $optTitle . $after_title;
		
		echo '	<div id="fb-root"></div>
				<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, \'script\', \'facebook-jssdk\'));
				</script>';
			
		echo '<div class="fb-like-box"';
			echo 'data-href="' . get_option( 'social-fb-page' ) . '" ';
			echo 'data-width="' . $optWidth . '" ';
			if ( $optHeight ) echo 'data-height="' . $optHeight . '" ';
			echo 'data-show-faces="' . $optFaces . '" ';
			echo 'data-colorscheme="' . $optTheme . '" ';
			echo 'data-stream="' . $optStream . '" ';
			//echo 'data-border-color="' . $optBordercolor . '" ';
			echo 'data-header="' . $optHeader . '">';
		echo '</div>';
		
		// Display after widget
		echo $after_widget;				
	}
 
    function update($new_instance, $old_instance){
	
        // Update parameters
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = $new_instance['width'];
		$instance['height'] = $new_instance['height'];
		$instance['showfaces'] = !empty($new_instance['showfaces']) ? 1 : 0;
		$instance['showstream'] = !empty($new_instance['showstream']) ? 1 : 0;
		$instance['showheader'] = !empty($new_instance['showheader']) ? 1 : 0;
		$instance['theme'] = $new_instance['theme'];
		//$instance['bordercolor'] = $new_instance['bordercolor'];

		return $instance;
    }
 
    function form($instance){
	
        // Paramters form
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$width = $instance['width'] ? $instance['width'] : 260;
		$height = $instance['height'];
		$showfaces = isset( $instance['showfaces'] ) ? (bool) $instance['showfaces'] : false;	
		$showstream = isset( $instance['showstream'] ) ? (bool) $instance['showstream'] : false;	
		$showheader = isset( $instance['showheader'] ) ? (bool) $instance['showheader'] : false;	
		$theme = $instance['theme'];
		//$bordercolor =  empty($instance['bordercolor']) ? '#000000' : $instance['bordercolor'];
		
		echo '<strong>';
		$menu = CleioToolbox_Helpers::checkToolboxMenu() ? "cleio-toolbox" : "cleio-base";
		if ( !get_option( 'social-fb-page' ) || get_option( 'social-fb-page' ) == "http://" ) {
			echo '<p><a href="' . admin_url('admin.php?page=' . $menu ) . '&tab=social_settings">' . __( 'Link your Facebook Page first!', 'cleio' ) . '</a></p>';
		}
		else {
			echo '<p>' . __( 'Your page', 'cleio' ) . ': ' . get_option( 'social-fb-page' ) . ' <a href="' . admin_url('admin.php?page=' . $menu) . '&tab=social_settings">' . __( 'edit', 'cleio' ) . '</a><p>';
		}
		echo '</strong>';
		
		if ( $showfaces ) 	$checkedFaces = ' checked="checked" ';
		else 			$checkedFaces = '';	
		
		if ( $showstream ) 	$checkedStream = ' checked="checked" ';
		else 			$checkedStream = '';
		
		if ( $showheader ) 	$checkedHeader = ' checked="checked" ';
		else 			$checkedHeader = '';
		
		$selectTheme['dark'] = ""; $select['light'] = "";
		$selectTheme[$theme] = "selected";
		
		// Display form	
		echo '<p>';
			echo '<label for="' . $this->get_field_id('title') . '">' . __( 'Title:', 'cleio' ) . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title'). '" type="text" value="' . $title. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('width') . '">' . __( 'Width (in pixels):', 'cleio' ) . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('width') . '" name="' . $this->get_field_name('width'). '" type="text" value="' . $width. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('height') . '">' . __( 'Height (in pixels):', 'cleio' ) . '</label>';
			echo '<input class="widefat" id="' . $this->get_field_id('height') . '" name="' . $this->get_field_name('height'). '" type="text" value="' . $height. '" />';
		echo '</p>';
		
		echo '<p>';
			echo '<label for="' . $this->get_field_id('theme') . '">' . __( 'Select theme:', 'cleio' ) . '</label>';
			echo '<select id="' . $this->get_field_id('theme') . '" name="' . $this->get_field_name('theme'). '">';
				echo '<option value="light" ' . $selectTheme['light'] . '>Light</option>';
				echo '<option value="dark" ' . $selectTheme['dark'] . '>Dark</option>';
			echo '</select>';
		echo '</p>';
		echo '<p>';
			echo '<input type="checkbox" class="checkbox" id="' .  $this->get_field_id('showfaces'). '" name="' .  $this->get_field_name('showfaces'). '"' . $checkedFaces . ' />&nbsp';
			echo '<label for="' .  $this->get_field_id('showfaces'). '"> ' . __( 'Show Faces', 'cleio' ). '</label><br />';
		echo '</p>';
		
		echo '<p>';
			echo '<input type="checkbox" class="checkbox" id="' .  $this->get_field_id('showstream'). '" name="' .  $this->get_field_name('showstream'). '"' . $checkedStream . ' />&nbsp';
			echo '<label for="' .  $this->get_field_id('showstream'). '"> ' . __( 'Show Stream', 'cleio' ). '</label><br />';
		echo '</p>';
		
		echo '<p>';
			echo '<input type="checkbox" class="checkbox" id="' .  $this->get_field_id('showheader'). '" name="' .  $this->get_field_name('showheader'). '"' . $checkedHeader . ' />&nbsp';
			echo '<label for="' .  $this->get_field_id('showheader'). '"> ' . __( 'Show Header', 'cleio' ). '</label><br />';
		echo '</p>';
		
	}
	
} 
?>