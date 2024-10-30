<?php
require_once( 'Cleio_FrameworkFields.php' );
abstract class Cleio_Framework {
	function loadScripts()
	{
		wp_register_style( 'theme_styles', plugins_url( '/css/exile-wp-cleio.css', dirname(dirname(__FILE__)) ), false, false, 'screen' );
		wp_enqueue_style( 'fontawesome_styles',  plugins_url( '/css/font-awesome.css', dirname(dirname(__FILE__)) ), false, false, 'screen' );
		wp_enqueue_script( 'cleio-options-form', plugins_url( '/js/options.js', dirname(dirname(__FILE__)) ) , array( 'jquery-ui-tabs', 'jquery-ui-datepicker', 'jquery-ui-slider', 'wp-ajax-response' ) );
		wp_enqueue_script( 'cleio-chosen-form', plugins_url( '/js/chosen.jquery.min.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'cleio-spectrum', plugins_url( '/js/spectrum.js', dirname(dirname(__FILE__)) ) );
		wp_localize_script( 'cleio-options-form', 'cleiovar', array( 'mapsProvider' => get_option( 'maps-provider', 'osm' ), 'msgFiles' => '', 'loadingurl' => plugins_url( '/images/wpspin.gif', dirname(dirname(__FILE__)) ) , 'txtValid' => __( 'Valid','cleio' ), 'txtCancel' => __( 'Cancel','cleio' ) ) );
	}

	function loadStyles()
	{
		wp_enqueue_style( 'exile_styles' );
		wp_enqueue_style( 'google_fonts', 'http://fonts.googleapis.com/css?family=Quicksand:400,700', false, false, 'screen' );
		wp_enqueue_style( 'chosen-styles', plugins_url( '/css/chosen.min.css', dirname(dirname(__FILE__)) ) );
		wp_enqueue_style( 'spectrum-styles', plugins_url( '/css/spectrum.css', dirname(dirname(__FILE__)) ) );
	}

	function getPanes( $panes )
	{
		echo '<ul id="cleio-nav">';
			foreach( $panes as $pane) echo '<li class="' . $pane['class']  . '"><a href="#' . $pane['id'] . '" title="' . $pane['title'] . '">' . $pane['label'] . '</a></li>';
		echo '</ul>';
	}

	function getFields( $options )
	{
		echo '<div id="cleio-content">';
		foreach ( $options as $paneId => $paneOptions ){
			echo '<div id="' . $paneId . '" class="cleio-content">';
				// Display title
				if ( $paneOptions['label'] != "") echo '<h3>' . $paneOptions['label'] . '</h3>';
				// Display description
				if ( $paneOptions['desc'] != "") echo '<p>' . $paneOptions['desc'] . '</p>';
				// Display list of fields
				if ( $paneOptions['fields'] != "") {
					foreach( $paneOptions['fields'] as $field ){
						call_user_func( array( 'Cleio_FrameworkFields', $field['type']  ), $field );
					}
				}
			echo '</div>';
		}
		echo '</div>';
	}

	function displayFramework( $panes, $options, $fwTitle, $fwVersion )
	{
		echo '<div id="cleio-framework" style="display:none;">';

				if ( $_GET['tab'] ) {
					$i = 0;
					foreach( $panes as $paneDefault){			
						$iTabDef++;			
						if ( $paneDefault['id'] == $_GET['tab'] ) $index = $iTabDef;			
					}					
					echo '<input type="hidden" value="' . $index . '" id="defTab" />';
				}

				echo '<div id="cleio-header">';
					echo '<h2>' . $fwTitle . ' ' . __( "Settings", "cleio" ) . ' ' . $fwVersion . '</h2>';
					echo '<div class="cleio-save-big">';
						echo '<span class="message"></span>';
						echo '<span class="submit">';
							echo '<input name="action" type="button" value="' . __('Save changes','cleio') . '" class="cleio-options-save" />';
						echo '</span>';
					echo '</div>'; // END .cleio-save
				echo '</div>'; // END #cleio-header


				echo '<form method="post" id="cleio-options-form">';
					echo '<div id="tabsOptions">';
						Cleio_Framework::getPanes( $panes );
						Cleio_Framework::getFields( $options );
					echo '</div>'; // END #tabsOptions
				echo '</form>'; // END #cleio-options-form

				echo '<div id="cleio-footer">';
					echo '<ul id="footer-links">';
						echo '<li><a href="http://cleio.co">Cleio&Co</a></li>';
						echo '<li><a href="http://cleio.co/documentation/">'.__('Documentation','cleio').'</a></li>';
						echo '<li><a href="http://cleio.co/support/">'.__('Support','cleio').'</a></li>';
						echo '<li><a href="http://eepurl.com/KK2Yj">'.__('Newsletter','cleio').'</a></li>';
					echo '</ul>'; // END #footer-links
					echo '<ul id="footer-social">'; 
						echo '<li><a class="fb" href="https://www.facebook.com/cleioco"></a></li>';
						echo '<li><a class="twitter" href="http://twitter.com/cleioco"></a></li>';
						//echo '<li><a class="news" href="http://eepurl.com/KK2Yj"></a></li>';
					echo '</ul>'; // END #footer-social
					echo '<div class="cleio-save-big">';
						echo '<span class="message"></span>';
						echo '<span class="submit">';
							echo '<input name="action" type="button" value="'.__('Save changes','cleio').'" class="cleio-options-save" />';
						echo '</span>';
					echo '</div>'; // END .cleio-save
				echo '</div>'; // END #cleio-footer

		echo '</div>'; // END #cleio-framework

		Cleio_Framework::loadScripts();
		Cleio_Framework::loadStyles();
	}
}

If ( !function_exists( 'cleioOptionsSave' ) ) {
	add_action('wp_ajax_cleio_options_save', 'cleioOptionsSave');
	function cleioOptionsSave(){
	    $isOk = true;
		global $wpdb;
	    $data 	= $_POST['data'];
	    parse_str($data, $options);
		// Required process to move page under right country page selected
		
		if ( $options['places-page-countries'] && ( $options['places-page-countries'] != get_option( 'places-page-countries' ) ) ) {
			$countriesToUpdate = get_pages( array( 'child_of' => get_option( 'places-page-countries' ) ) );
			foreach( $countriesToUpdate as $country ) {
				if( get_post_meta( $country->ID, '_countrycode' ) ) {
					$wpdb->get_var( $wpdb->prepare("UPDATE $wpdb->posts SET post_parent = %d WHERE ID = %d", $options['places-page-countries'], $country->ID) );
				}
			}			
		}
		
		$refresh = false;
	    foreach ($options as $key => $value) {
	    	if( $key == "maps-provider" ){
	    		if( get_option( "maps-provider", "osm" ) != $value ) $refresh = true;
	    	}
			if( is_array($value) ) update_option( $key, $value );
			else update_option( $key, stripslashes($value) );
		}
		
		$thumbnailToPurge = get_option( "exile-thumbnail-purge", Array() );
		if ( $options['ss-width'] ) {
			if ( !array_search( $options['ss-width'] . "x" . $options['ss-height'], $thumbnailToPurge ) ) $thumbnailToPurge[$options['ss-width'] . "x" . $options['ss-height']] = $options['ss-width'] . "x" . $options['ss-height'];
		}		
		if ( $options['sticky-width'] ) {
			if ( !array_search( $options['sticky-width'] . "x" . $options['sticky-height'], $thumbnailToPurge ) ) $thumbnailToPurge[$options['sticky-width'] . "x" . $options['sticky-height']] = $options['sticky-width'] . "x" . $options['sticky-height'];
		}
		update_option( "exile-thumbnail-purge", $thumbnailToPurge );
		
		if ( $isOk ) $response = array( 'status'		=>  "OK", 'message'	=> __('Options updated!'), 'refresh' => $refresh );
	    else $response = array( 'status'		=> "NOK", 'message'	=> __('Options not saved!'), 'refresh' => $refresh );
	    
	   	echo json_encode( $response );
	   	exit;
	}
}
If ( !function_exists( 'cleioLogoutInstagram' ) ) {
	add_action('wp_ajax_exile_logout_instagram', 'cleioLogoutInstagram');
	function cleioLogoutInstagram(){
	   
	    #; Init var 
	    $isOk = true;
	    
	    #; Get Data transmit by the AJAX Request
		delete_option( 'social-instagram-access-token');
		delete_option( 'social-instagram-account-username' );
		delete_option( 'social-instagram-account-picture' );
		delete_option( 'social-instagram-account-fullname' );
		delete_transient( 'exile-widget-instagram' );
		
	    $url = 'https://api.instagram.com/oauth/authorize/?redirect_uri=' . plugins_url() . '/cleio-toolbox/resources/utils/exile_instagram_oauth.php' .'&response_type=code&client_id=a08a7327c07f4300991b8e4aeea87f06&display=touch';
		$ret = "";
		$ret .= __( "It seems that your access token has not been configured yet or has expired. You should", "cleio" );
		$ret .=  '<a href="' . $url . '" target="_blank">&nbsp;' . __( "set it now", "cleio" ) . '!</a>';
												
	        		
		$response = array(
		   'what'=>'logout_instagram',
		   'action'=>'logout_instagram',
		   'id'=>1,
		   'data'=> $ret
		);
	        
	    
	    #; Send response and exit
	   	echo json_encode( $response );
	   	exit;
	    
	}
}

If ( !function_exists( 'cleioLogoutTwitter' ) ) {
	add_action('wp_ajax_exile_logout_twitter', 'cleioLogoutTwitter');
	function cleioLogoutTwitter(){
	   
	    #; Init var 
	    $isOk = true;
	    
	    #; Get Data transmit by the AJAX Request
		delete_option( 'social-twitter-access-token');
		delete_option( 'social-twitter-oauth-token');
		delete_option( 'social-twitter-oauth-token-secret');
		delete_transient( 'exile-widget-twitter' );
		
	    $ret .=  '<p id="instagram-log-control">' . __( "It seems that your access token has not been configured yet or has expired. You should", "cleio" );
			$ret .= '<a href="' . plugins_url() . '/cleio-toolbox/resources/utils/twitter_redirect.php">&nbsp;' . __( "set it now", "cleio" ) . '!</a>';
		$ret .= '</p>';						
	        		
		$response = array(
		   'what'=>'logout_twitter',
		   'action'=>'logout_twitter',
		   'id'=>1,
		   'data'=> $ret
		);
	        
	    
	    #; Send response and exit		
	    echo json_encode($response);
	    exit;
	    
	}
}
?>