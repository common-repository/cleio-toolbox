<?php
abstract class Cleio_FrameworkFields {

	function sitemapShortcodeGenerator( $field )
	{
		echo '<p class="cleio-description">';
			echo __( "Select your desired settings, then copy/paste the generated shortcode in any page.", "cleio" );
		echo '</p>';

		echo '<p><input type="checkbox" name="gen-hide-cat" value="hidecat" />&nbsp;<label>' . __( "Hide blog categories", "cleio" ) . '</label><p>';

		echo '<p>';
			$exclude = Array( 'attachment', 'revision', 'nav_menu_item', 'location' );
			echo '<h5>'.__("Filter something out?","cleio").'</h5>';
			echo '<div>';
				echo '<input type="checkbox" name="gen-sitemap-content[]" value="pages" checked="checked" />&nbsp;<label>' . __('WordPress Pages', 'cleio') . '</label>';
			echo '</div>';
			if ( is_plugin_active( 'cleio-maps/cleio-maps.php' ) ) {
				echo '<div>';
					echo '<input type="checkbox" name="gen-sitemap-content[]" value="countries" checked="checked" />&nbsp;<label>' . __('Country Pages', 'cleio') . '</label>';
				echo '</div>';
			}
		echo '</p>';

		echo '<p>';
			echo '<h4>' . __('Generated shortcode','cleio') . '</h4>';
			echo '<div class="gen-code"><code id="gen-sitemap-shortcode"></code></div>';
			echo '<a href="#" class="button" id="gen-sitemap-shortcode-paste" data-clipboard-target="gen-sitemap-shortcode">' . __( "Copy shortcode", "cleio" ) . '</a>';
		echo '</p>';

		wp_enqueue_script( 'sitemapgenerator-copy', plugins_url( '/js/ZeroClipboard.js', dirname(dirname(__FILE__)) )  );
		wp_enqueue_script( 'sitemapgenerator-script', plugins_url( '/js/options.sitemapgenerator.js', dirname(dirname(__FILE__)) ) );
	}

	function archivesShortcodeGenerator( $field )
	{
		echo '<p class="cleio-description">';
			echo __( "Select your desired settings, then copy/paste the generated shortcode in any page.", "cleio" );
		echo '</p>';

		echo '<p>';
			echo '<h5>'.__("Pagination","cleio").'</h5>';
			echo '<input type="radio" name="gen-paginate" value="year" checked="checked" /><label>' . __( "Yearly", "cleio" ) . '</label><br />';
			echo '<input type="radio" name="gen-paginate" value="year-month" /><label>' . __( "Yearly and Monthly", "cleio" ) . '</label>';
		echo '</p>';

		echo '<p>';
			$exclude = Array( 'attachment', 'revision', 'nav_menu_item', 'location' );
			echo '<h5>'.__("Filter something out?","cleio").'</h5>';
			foreach( get_post_types() as $postType ) {
				if( !in_array($postType, $exclude) ){
					if( $postType == "post" || $postType == "photo" ) {
						$checkedType = '';
					}
					else {					
						$checkedType = ' checked="checked"';
					}
					$postTypeVal = $postType;
					if($postType == "post") {
						$postTypeVal = "posts";
						$postType = "WordPress Posts";
					}
					if($postType == "page") {
						$postTypeVal = "pages";
						$postType = "WordPress Pages";
					}
					if($postType == "photo") {
						$postTypeVal = "photos";
						$postType = "Photo Posts";
					}
					if($postType == "addresses") {
						$postTypeVal = "addresses";
						$postType = "Review Lists";
					}
					if($postType == "address") {
						$postTypeVal = "address";
						$postType = "Reviews";
					}
					echo '<div>';
						echo '<input type="checkbox" name="gen-archives-content[]" value="' . $postTypeVal . '" ' . $checkedType . '/>&nbsp;<label>' . ucfirst($postType) . '</label>';
					echo '</div>';
				}
			}
			if ( is_plugin_active( 'cleio-maps/cleio-maps.php' ) ) {
				echo '<div>';
					echo '<input type="checkbox" name="gen-archives-content[]" value="countries" checked="checked" />&nbsp;<label>' . __('Country Pages', 'cleio') . '</label>';
				echo '</div>';
			}
		echo '</p>';
		echo '<p>';
			echo '<h4>' . __('Generated shortcode','cleio') . '</h4>';
			echo '<div class="gen-code"><code id="gen-archives-shortcode"></code></div>';
			echo '<a href="#" class="button" id="gen-archives-shortcode-paste" data-clipboard-target="gen-archives-shortcode">' . __( "Copy shortcode", "cleio" ) . '</a>';
		echo '</p>';

		wp_enqueue_script( 'sitemapgenerator-copy', plugins_url( '/js/ZeroClipboard.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'archivesgenerator-script', plugins_url( '/js/options.archivesgenerator.js', dirname(dirname(__FILE__)) ) );
	}

	function button( $field )
	{
		echo '<a href="#" class="button ' . $field['class'] . '"';
			if( $field['onclick'] ) echo ' onclick="' . $field['onclick'] . '";';
		echo '>' . $field['label'] . '</a>';
	}

	function calendar( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		if ( $field['inline'] != 1 ) echo '<div>';
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label>';
		echo '<input type="text" style="width:150px;" class="datepicker" id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . $field['value'] . '"></input>';
		if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
		if ( $field['inline'] != 1 ) echo '</div>';
	}

	function contentTypeList( $field )
	{
		$exclude = Array( 'attachment', 'revision', 'nav_menu_item', 'location' );
		$typeToExclude = get_option( $field['name'], $field['defVal'] );
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label> ';
		foreach( get_post_types() as $postType ) {
			if( !in_array($postType, $exclude) ){
				$postTypeVal = $postType;
				if($postType == "post") {
					$postTypeVal = "posts";
					$postType = "WordPress Posts";
				}
				if($postType == "page") {
					$postTypeVal = "pages";
					$postType = "WordPress Pages";
				}
				if($postType == "photo") {
					$postTypeVal = "photos";
					$postType = "Photo Posts";
				}
				if($postType == "addresses") {
					$postTypeVal = "addresses";
					$postType = "Review Lists";
				}
				if($postType == "address") {
					$postTypeVal = "address";
					$postType = "Reviews";
				}
				if( $typeToExclude ) {
					if ( in_array( $postTypeVal, $typeToExclude) ) $checkedType = ' checked="checked"';
					else $checkedType = '';
				}
				else {					
					if ( $postTypeVal != 'post' ) $checkedType = ' checked="checked"';
					else $checkedType = '';
				}
				echo '<div>';
					echo '<input type="checkbox" name="' . $field['name']  . '[]" value="' . $postTypeVal . '" ' . $checkedType . '/>&nbsp;<label>' . ucfirst($postType) . '</label>';
				echo '</div>';
			}
		}
		if ( is_plugin_active( 'cleio-maps/cleio-maps.php' ) ) {
			/*
			echo '<div>';
				if( $typeToExclude ) {
					if ( in_array( 'check-ins', $typeToExclude) ) $checkedType = ' checked="checked"';
					else $checkedType = '';
				}
				else {					
					$checkedType = ' checked="checked"';
				}
				echo '<input type="checkbox" name="' . $field['name']  . '[]" value="check-ins" ' . $checkedType . ' />&nbsp;<label>' . __('Locations that are not linked to any content (check-ins)', 'cleio') . '</label>';
			echo '</div>';
			*/
			echo '<div>';
				if( $typeToExclude ) {
					if ( in_array( 'countries', $typeToExclude) ) $checkedType = ' checked="checked"';
					else $checkedType = '';
				}
				else {					
					$checkedType = ' checked="checked"';
				}
				echo '<input type="checkbox" name="' . $field['name']  . '[]" value="countries" ' . $checkedType . ' />&nbsp;<label>' . __('Country Pages', 'cleio') . '</label>';
			echo '</div>';
		}
		if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
	}

	function checkbox( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		if ( $field['value'] == 1) $checked = ' checked="checked"';
		else $checked = '';
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label>';
		if ( $field['inline'] != 1 )  echo '<div>';
		echo '<input type="checkbox" id="' . $field['name'] . '" name="' . $field['name'] . '" class="cbYesNo"' . $checked . '/>';								
		echo '&nbsp;<label for="' . $field['name'] . '">' . $field['txtVal'] . '</label>';	
		if ( $field['inline'] != 1 ) echo '</div>';
		if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
	}

	function checkboxList( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label>';	
		echo '<input type="hidden" name="' . $field['name'] . '[]" value="default" />';
		foreach ( $field['data'] as $optKey => $optText ){                                                
			if ( $field['value'] ) {
				if ( is_array( $field['value'] ) ) {
					if ( in_array( $optKey, $field['value']) ) $checked = ' checked="checked"';
					else $checked = '';
				}
				else {
					if ( $optKey == $field['value'] ) $checked = ' checked="checked"';
					else $checked = '';
				}
			}
			else $checked = '';
			echo '<div>';
			echo '<input type="checkbox" name="' . $field['name'] . '[]" value="' . $optKey . '"' . $checked . '/>';
			echo '&nbsp;<label>' . $optText . '</label>';
			echo '</div>';
		}
		if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';        
	}

	function colorPicker( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		echo '<div>';
			if ( $field['label'] ) echo '<label>' . $field['label'] . '</label> ';
			echo '<input type="text" id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . $field['value'] . '" class="cleio-colorpicker"></input>';
			if ( $field['after_input'] ) echo ' <label>' . $field['after_input'] . '</label>';
			if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
		echo '</div>';
	}

	function fieldsetStart( $field )
	{
		echo '<fieldset>';
		if ( $field['legend'] != "" ) echo '<legend>' . $field['legend'] . '</legend>';    
	}

	function fieldsetEnd( $field ) { echo '</fieldset>'; }

	function group( $field ) { echo '<h4><label>' . $field['label'] . '</label></h4>'; }

	function customField( $field ) { include( $field['path'] ); }

	function info( $field )	{ echo '<p>' . $field['text'] . '</p><br />'; }

	function instagramLogin( $field )
	{
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label>';
		$access_token = get_option( 'social-instagram-access-token' );
		if ( !$access_token ) { 		
			$url = 'http://cleio.co/exile_instagram_oauth.php?redirect_uri=http://cleio.co/exile_instagram_oauth.php&return_uri=' . plugins_url() . '/cleio-toolbox/resources/utils/exile_instagram_oauth.php&response_type=code&client_id=a08a7327c07f4300991b8e4aeea87f06&display=touch';
			echo '<p id="instagram-log-control">' . __( "It seems that your access token has not been configured yet or has expired. You should", "cleio" );
				echo '<a href="' . $url . '" target="_blank">&nbsp;' . __( "set it now", "cleio" ) . '!</a>';
			echo '</p>';			
		}
		else {			
			echo '<p id="instagram-log-control"><strong>';
				echo __('Account name','cleio') . ': ' . get_option( 'social-instagram-account-username' );
			echo '</strong>&nbsp;';
			echo '<a href="#" class="cleio-instagram-logout">' . __('Logout','cleio') .'</a></p>';			
		}
	}

	function loginCleio( $field )
	{
		if( !is_callable('curl_init') )	{
			echo '<div class="error">' . __( "Warning! The PHP extension cURL is not activated on your server. Please contact your host in order to activate it.", "cleio" ) . '</div>';
			return false;
		}
		$styleForm = "";	
		if( get_option( "cleio-user-exilethemes", "" ) != "" ){
			$styleForm = ' style="display: none;"';
			echo '<p class="cleio-logout-form">' . __( "Hello", "cleio" ) . ' ' . get_option( "cleio-user-exilethemes" ) .'!';
				echo '<br />' . __( "You're already connected to Cleio&Co", "cleio" ) . ', <a href="#" id="cleio-change-account">change account?</a>';
			echo '</p>';
		}
		echo '<p class="exile-login-form"' . $styleForm . '>';
			echo __( 'Username','cleio' ) . '<br /><input type="text" name="cleio-auth-login" id="cleio-auth-login" style="width: 250px;" /><br />';
			echo __( 'Password','cleio' ) . '<br /><input type="password" name="cleio-auth-pwd" id="cleio-auth-pwd" style="width: 250px;" /><br />';
			echo '<input type="button" value="Connect me!" name="cleio-auth-button" id="cleio-auth-button" />';
		echo '</p>';		
		echo '<p class="message message-connect" style="display: none;"></p>';
	}

	function mapCompleteFields( $field )
	{
		echo '<p class="cleio-description">';
			echo __( "These settings define the style of the optional location map that appears in your posts and pages.", "cleio" );
		echo '</p>';
		echo '<div class="clear cleio-map-preview">';
			echo '<div>';
				echo '<label>' . __("Map skin","cleio") . '</label> ';
				echo '<select id="' . $field['name']  . '-style" name="' . $field['name']  . '-style" class="map-style">';
					foreach ( Cleio_FrameworkFields::getMapStyles() as $opt ){
						if( $opt['key'] == get_option( $field['name']  . '-style' ) ) $selected = ' selected="selected"';
						else $selected = '';
						echo '<option value="' . $opt['key'] . '" class="' . $opt['group'] . '"' . $selected . '>';
							echo $opt['text'];
						echo '</option>';
					}
				echo '</select>';
			echo '</div>';
			if( $field['nozoom'] == false ){
				echo '<div>';
					echo '<label id="' . $field['name']  . '-zoom-label">' . __("Zoom", "cleio") . ' : <strong>1</strong></label>';
					echo '<div class="cleio-slider" data-min="1" data-max="10" data-inputassoc="' . $field['name']  . '-zoom" data-value="' . get_option( $field['name']  . '-zoom', '1' ) .'"></div>';
					echo '<input type="hidden" id="' . $field['name']  . '-zoom" name="' . $field['name']  . '-zoom" class="zoom" value="' . get_option( $field['name']  . '-zoom', '1' ) .'"/>';
				echo '</div>';
			}
			$msg = "";
			$color = get_option( $field['name']  . '-pins-color', '#000000' );
			if( is_dir(get_template_directory() . "/images/cleio-pins/" ) ) {
				$dirContent = glob( get_template_directory() . "/images/cleio-pins/*" );
				$ctFiles = count( $dirContent );
				if ( $ctFiles > 13 ) {
					foreach( $dirContent as $files ) {
						$msg .= '<img src="' . get_template_directory_uri() . '/images/cleio-pins/' . basename( $files ) . '">&nbsp';
					}
				}
				else $msg = '<em>' . __( 'Some pins seem to be missing. Did you','cleio') . ' <a href="http://cleio.co/documentation/cleio-maps/pins/" target="_blank">' . __('upload them properly', "cleio" ) . '</a>?</em>';
			}
			else $msg = '<em>' . __( 'We did not find your pin set. Did you','cleio') . ' <a href="http://cleio.co/documentation/cleio-maps/pins/" target="_blank">' . __('upload it in the right place', "cleio" ) . '</a>?</em>';
			echo '<div id="gens-pins-selector" class="cleio-pinsselector-container">';
				$checkedCleioPins = get_option( $field['name']  . '-pins', 'cleio-pins' ) == 'cleio-pins' ? 'checked="checked"' : '';
				$checkedUserPins = get_option( $field['name']  . '-pins', 'cleio-pins' ) == 'user-pins' ? 'checked="checked"' : '';
				echo '<h5>'.__("Pin set","cleio").'</h5>';
				echo '<input type="radio" name="' . $field['name']  . '-pins" value="cleio-pins" ' . $checkedCleioPins .' class="cleio-pinsselector"/><label>' . __( "Use Cleio Maps default pin set (customizable color)", "cleio" ) . '</label><br />';
				echo '<input type="radio" name="' . $field['name']  . '-pins" value="user-pins" ' . $checkedUserPins .' class="cleio-pinsselector"/><label>' . __( "Use your own pin set", "cleio" ) . '</label>';
				echo '<div>';
					echo '<br /><label>' . __( 'Pin color', 'cleio' ) . '</label> <input  class="pins-color" type="text" id="' . $field['name']  . '-pins-color" name="' . $field['name']  . '-pins-color" value="' . $color . '"></input>';
				echo '</div>';
				echo '<div>';
					echo '<p>' . $msg . '</p>';
				echo '</div>';
			echo '</div>';		
			echo '<div>';
				echo '<h5>'.__("Dimensions","cleio").'</h5>';
				echo '<label>' . __("Width", "cleio") . '</label> ';
				echo '<input type="text" id="' . $field['name']  . '-width" name="' . $field['name']  . '-width" value="' . get_option( $field['name']  . '-width', '100%' ) .'" style="width: 70px;"></input>';
			echo '</div>';
			echo '<div>';
				echo '<label>' . __("Height", "cleio") . '</label> ';
				echo '<input type="text" id="' . $field['name']  . '-height" name="' . $field['name']  . '-height" value="' . get_option( $field['name']  . '-height', '300px' ) .'" style="width: 70px;"></input>';
			echo '</div>';
		echo '</div>';
		echo '<div id="' . $field['name'] . '" class="map-preview"></div>';

		if( $field['filter'] != 'post' ) {
			echo '<fieldset class="clear"><legend>' . __( "Exclude content types", "cleio" ) . '</legend>';
				$exclude = Array( 'attachment', 'revision', 'nav_menu_item', 'location' );
				$typeToExclude = get_option( $field['name']  . '-content-type' );
				foreach( get_post_types() as $postType ) {
					if( !in_array($postType, $exclude) ){

						$postTypeVal = $postType;
						if($postType == "post") {
							$postTypeVal = "posts";
							$postType = "WordPress Posts";
						}
						if($postType == "page") {
							$postTypeVal = "pages";
							$postType = "WordPress Pages";
						}
						if($postType == "photo") {
							$postTypeVal = "photos";
							$postType = "Photo Posts";
						}
						if($postType == "addresses") {
							$postTypeVal = "addresses";
							$postType = "Review Lists";
						}
						if($postType == "address") {
							$postTypeVal = "address";
							$postType = "Reviews";
						}

						if ( in_array( $postTypeVal, $typeToExclude) ) $checkedType = ' checked="checked"';
						else $checkedType = '';

						echo '<div>';
							echo '<input type="checkbox" name="' . $field['name']  . '-content-type[]" value="' . $postTypeVal . '" ' . $checkedType . '/>&nbsp;<label>' . ucfirst($postType) . '</label>';
						echo '</div>';
					}
				}
				echo '<div>';
					if ( in_array( 'check-ins', $typeToExclude) ) $checkedType = ' checked="checked"';
					else $checkedType = '';
					echo '<input type="checkbox" name="' . $field['name']  . '-content-type[]" value="check-ins" ' . $checkedType . ' />&nbsp;<label>' . __('Locations that are not linked to any content (check-ins)', 'cleio') . '</label>';
				echo '</div>';
				echo '<div>';
					if ( in_array( 'countries', $typeToExclude) ) $checkedType = ' checked="checked"';
					else $checkedType = '';
					echo '<input type="checkbox" name="' . $field['name']  . '-content-type[]" value="countries" ' . $checkedType . ' />&nbsp;<label>' . __('Country Pages', 'cleio') . '</label>';
				echo '</div>';
			echo '</fieldset>';
			echo '<fieldset class="clear"><legend>' . __( "Filter out by date or amount of content", "cleio" ) . '</legend>';
				$checkedMax = get_option( $field['name']  . '-filter', 'filter-amount' ) == 'filter-amount' ? 'checked="checked"' : '';
				$checkedDate = get_option( $field['name']  . '-filter', 'filter-amount' ) == 'filter-date' ? 'checked="checked"' : '';
				echo '<input type="radio" name="' . $field['name']  . '-filter" value="filter-amount" '. $checkedMax .' />';
				echo '<label>' . __("Amount of (latest) content to show", "cleio") . '&nbsp;</label><input type="text" id="' . $field['name']  . '-max-pins" name="' . $field['name']  . '-max-pins" value="' . get_option( $field['name']  . 'max-pins', '0' ) .'" style="width: 70px;"></input>';
				echo '<p><em>' . __('0 = No limits, all your content will be displayed.', 'cleio') . '</em></p>';
				echo '<input type="radio" name="' . $field['name']  . '-filter" value="filter-date" '. $checkedDate .' />';
				echo '&nbsp;<label for="gen-filterdate">' . __( 'Hide content prior to', 'cleio' ) . '</label>&nbsp;';
				echo '<input type="text" style="width:150px;" class="datepicker" id="' . $field['name']  . '-filterdatevalue" name="' . $field['name']  . '-filterdatevalue" value="' . get_option( $field['name']  . '-filterdatevalue' ) .'" />';
			echo '</fieldset>';
		}
		
		$keyApi = get_option('gmap_key_api');
		$lang = get_bloginfo('language');	
		wp_enqueue_script( 'gmap-maps-admin', 'http://maps.googleapis.com/maps/api/js?key=' . $keyApi . '&libraries=places&sensor=false' );
		wp_enqueue_script( 'stamen-maps', 'http://maps.stamen.com/js/tile.stamen.js?v1.3.0' );
		wp_enqueue_style(  'leaflet-css', 'http://cdn.leafletjs.com/leaflet-0.6.3/leaflet.css' );
		wp_enqueue_script( 'leaflet-js', 	'http://cdn.leafletjs.com/leaflet-0.6.3/leaflet.js' );
		wp_enqueue_script( 'leaflet-iconmarker', plugins_url( '/js/leaflet.awesome-markers.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'cleio-general-leaflet-js', plugins_url( '/js/cleio.front.leaflet.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'mapgenerator-iconmarker', plugins_url( '/js/fontawesome-markers.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'mapfield-admin', plugins_url( '/js/options.mapfield.js', dirname(dirname(__FILE__)) )  );
		wp_localize_script( 'mapfield-admin', 'cleiomaps', array( 'provider' => get_option( 'maps-provider', 'osm' ) ) );

	}

	function mapShortcodeGenerator( $field )
	{
		echo '<div id="cleio-mapgenerator" class="clear">';
			echo '<div>';
				echo '<label>' . __("Map skin","cleio") . '</label> ';
				echo '<select id="gen-map-style">';
					foreach ( Cleio_FrameworkFields::getMapStyles() as $opt ){
						if( $opt['key'] == "google.maps.MapTypeId.ROADMAP" ) $selected = ' selected="selected"';
						else $selected = '';
						echo '<option value="' . $opt['key'] . '" class="' . $opt['group'] . '"' . $selected . '>';
							echo $opt['text'];
						echo '</option>';
					}
				echo '</select>';
			echo '</div>';
			echo '<div>';
				echo '<label id="gen-zoom-label">' . __("Zoom", "cleio") . ' : <strong>1</strong></label>';
				echo '<div class="cleio-slider" data-min="1" data-max="10" data-inputassoc="gen-zoom" data-value="1"></div>';
				echo '<input type="hidden" id="gen-zoom" name="gen-zoom" value="1"/>';
			echo '</div>';
			$msg = "";
			$color = "#000000";
			if( is_dir(get_template_directory() . "/images/cleio-pins/" ) ) {
				$dirContent = glob( get_template_directory() . "/images/cleio-pins/*" );
				$ctFiles = count( $dirContent );
				if ( $ctFiles > 13 ) {
					foreach( $dirContent as $files ) {
						$msg .= '<img src="' . get_template_directory_uri() . '/images/cleio-pins/' . basename( $files ) . '">&nbsp';
					}
				}
				else $msg = '<em>' . __( 'Some pins seem to be missing. Did you','cleio') . ' <a href="http://cleio.co/documentation/cleio-maps/pins/" target="_blank">' . __('upload them properly', "cleio" ) . '</a>?</em>';
			}
			else $msg = '<em>' . __( 'We did not find your pin set. Did you','cleio') . ' <a href="http://cleio.co/documentation/cleio-maps/pins/" target="_blank">' . __('upload it in the right place', "cleio" ) . '</a>?</em>';
			echo '<div id="gens-pins-selector" class="cleio-pinsselector-container">';
				echo '<h5>'.__("Pin set","cleio").'</h5>';
				echo '<input type="radio" name="gen-pins" value="cleio-pins" checked="checked" class="cleio-pinsselector"/><label>' . __( "Use Cleio Maps default pin set (customizable color)", "cleio" ) . '</label><br />';
				echo '<input type="radio" name="gen-pins" value="user-pins" class="cleio-pinsselector"/><label>' . __( "Use your own pin set", "cleio" ) . '</label>';
				echo '<div>';
					echo '<br /><p><label>' . __( 'Pin color', 'cleio' ) . '</label> <input type="text" id="gen-pins-color" name="gen-pins-color" value="' . $color . '"></input></p>';
				echo '</div>';
				echo '<div>';
					echo '<p>' . $msg . '</p>';
				echo '</div>';
			echo '</div>';
			echo '<div>';
				$color = "#FFFFFF";
				//echo '<h5>'.__("Clusterer","cleio").'</h5>';
				echo '<p><input type="checkbox" name="gen-cluster" value="cluster" checked="checked" />&nbsp;<label>' . __( "Activate group markers", "cleio" ) . '</label><br />';
				echo '<label>' . __("Group markers text color", "cleio") . '</label> <input type="text" id="gen-cluster-color" name="gen-cluster-color" value="' . $color . '"></input></p>';
			echo '</div>';	
			echo '<div>';
				echo '<h5>'.__("Dimensions","cleio").'</h5>';
				echo '<label>' . __("Width", "cleio") . '</label> ';
				echo '<input type="text" id="gen-width" name="gen-width" value="100%" style="width: 70px;"></input>';
			echo '</div>';	
			echo '<div>';
				echo '<label>' . __("Height", "cleio") . '</label> ';
				echo '<input type="text" id="gen-height" name="gen-height" value="300px" style="width: 70px;"></input>';
			echo '</div>';
		echo '</div>';
		echo '<div id="map-generator-preview"></div>';
		echo '<fieldset class="clear"><legend>' . __( "Exclude content types", "cleio" ) . '</legend>';
			$exclude = Array( 'attachment', 'revision', 'nav_menu_item', 'location', 'wpcf7_contact_form' );
			foreach( get_post_types() as $postType ) {
				if( !in_array($postType, $exclude) ){
					if($postType == "post") {
						$postTypeVal = "posts";
						$postType = "WordPress Posts";
					}
					else if($postType == "page") {
						$postTypeVal = "pages";
						$postType = "WordPress Pages";
					}
					else if($postType == "photo") {
						$postTypeVal = "photos";
						$postType = "Photo Posts";
					}
					else if($postType == "addresses") {
						$postTypeVal = "addresses";
						$postType = "Review Lists";
					}
					else if($postType == "address") {
						$postTypeVal = "address";
						$postType = "Reviews";
					}
					else {
						$postTypeVal = $postType;
					}
					echo '<div>';
						echo '<input type="checkbox" name="gen-content-type[]" value="' . $postTypeVal . '" />&nbsp;<label>' . ucfirst($postType) . '</label>';
					echo '</div>';
				}
			}
			echo '<div>';
				echo '<input type="checkbox" name="gen-content-type[]" value="check-ins" />&nbsp;<label>' . __('Locations that are not linked to any content (check-ins)', 'cleio') . '</label>';
			echo '</div>';
			echo '<div>';
				echo '<input type="checkbox" name="gen-content-type[]" value="countries" />&nbsp;<label>' . __('Country Pages', 'cleio') . '</label>';
			echo '</div>';
		echo '</fieldset>';
		echo '<fieldset class="clear"><legend>' . __( "Filter out by date or amount of content", "cleio" ) . '</legend>';
			echo '<input type="radio" id="gen-filtere" name="gen-filter" class="cbYesNo" value="filter-amount">';
			echo '<label>' . __("Amount of (latest) content to show", "cleio") . '&nbsp;</label><input type="text" id="gen-max-pins" name="gen-max-pins" value="0" style="width: 70px;"></input>';
			echo '<p><em>' . __('0 = No limits, all your content will be displayed.', 'cleio') . '</em></p>';
			echo '<input type="radio" id="gen-filter" name="gen-filter" class="cbYesNo" value="filter-date">';
			echo '&nbsp;<label for="gen-filterdate">' . __( 'Hide content prior to', 'cleio' ) . '</label>&nbsp;';
			echo '<input type="text" style="width:150px;" class="datepicker" id="gen-filterdatevalue" name="gen-filterdatevalue" />';
		echo '</fieldset>';
		echo '<h4>' . __('Add this map anywhere','cleio') . '</h4>';
		echo '<em>' . __('Copy and paste the following shortcode into any page or post.','cleio') . '</em>';
		echo '<div class="gen-code"><code id="gen-shortcode"></code></div>';
		echo '<a href="#" class="button" id="gen-shortcode-paste" data-clipboard-target="gen-shortcode">' . __( "Copy shortcode", "cleio" ) . '</a>';

		$keyApi = get_option('gmap_key_api');
		$lang = get_bloginfo('language');	
		wp_enqueue_script( 'gmap-maps-admin', 'http://maps.googleapis.com/maps/api/js?key=' . $keyApi . '&libraries=places&sensor=false' );
		wp_enqueue_script( 'stamen-maps', 'http://maps.stamen.com/js/tile.stamen.js?v1.3.0' );
		wp_enqueue_style(  'leaflet-css', 'http://cdn.leafletjs.com/leaflet-0.6.3/leaflet.css' );
		wp_enqueue_script( 'leaflet-js', 	'http://cdn.leafletjs.com/leaflet-0.6.3/leaflet.js' );
		wp_enqueue_script( 'leaflet-iconmarker', plugins_url( '/js/leaflet.awesome-markers.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'cleio-general-leaflet-js', plugins_url( '/js/cleio.front.leaflet.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'mapgenerator-iconmarker', plugins_url( '/js/fontawesome-markers.js', dirname(dirname(__FILE__)) ) );
		wp_enqueue_script( 'mapgenerator-places-admin', plugins_url( '/js/options.mapgenerator.js', dirname(dirname(__FILE__)) ) );
		wp_localize_script( 'mapgenerator-places-admin', 'cleiomaps', array( 'provider' => get_option( 'maps-provider', 'osm' ) ) );
		wp_enqueue_script( 'mapgenerator-copy', plugins_url( '/js/ZeroClipboard.js', dirname(dirname(__FILE__)) ) );
	}

	function media( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label>';
		echo '';
		if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
	}

	function pinsSelector( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		$msg = "";
		$color = "";
		if( $field['value'] == "cleio-pins" ) {
			$checkedCleio = ' checked="checked"';
			$checked = "";
			$color = Cleio_FrameworkFields::getFieldValue( array( 'name' => $field['name']."-color", 'defVal' => "#000000" ) );
		}
		else {
			$checkedCleio = '';
			$checked = ' checked="checked"';
		}
		if( is_dir(get_template_directory() . "/images/cleio-pins/" ) ) {
			$dirContent = glob( get_template_directory() . "/images/cleio-pins/*" );
			$ctFiles = count( $dirContent );
			if ( $ctFiles > 13 ) {
				foreach( $dirContent as $files ) {
					$msg .= '<img src="' . get_template_directory_uri() . '/images/cleio-pins/' . basename( $files ) . '">&nbsp';
				}
			}
			else $msg = '<em>' . __( 'Some pins seem to be missing. Did you','cleio') . ' <a href="http://cleio.co/documentation/cleio-maps/pins/" target="_blank">' . __('upload them properly', "cleio" ) . '</a>?</em>';
		}
		else $msg = '<em>' . __( 'We did not find your pin set. Did you','cleio') . ' <a href="http://cleio.co/documentation/cleio-maps/pins/" target="_blank">' . __('upload it in the right place', "cleio" ) . '</a>?</em>';
		echo '<div class="cleio-pinsselector-container">';
			echo '<label for="' . $field['name'] . '">' . $field['label'] . '</label>';
			echo '<input type="radio" name="' . $field['name'] . '" value="cleio-pins"' . $checkedCleio . ' class="cleio-pinsselector"/>&nbsp;<label>' . __( "Use Cleio Maps default pin set and customize the color", "cleio" ) . '</label>';
			echo '&nbsp;&nbsp;<input type="radio" name="' . $field['name'] . '" value="user-pins"' . $checked . ' class="cleio-pinsselector"/>&nbsp;<label>' . __( "Use your own pin set", "cleio" ) . '</label>';
			echo '<div>';
				echo '<label>' . __( 'Pin color', 'cleio' ) . '</label> <input type="text" id="' . $field['name'] . '-color" name="' . $field['name'] . '-color" value="' . $color . '" class="cleio-colorpicker"></input>';
			echo '</div>';
			echo '<div>';
				echo '<p>' . $msg . '</p>';
			echo '</div>';
			if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
		echo '</div>';		
	}

	function radio( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		if ( $field['label'] ) echo '<label for="' . $field['name'] . '">' . $field['label'] . '</label>';
		foreach ( $field['data'] as $optKey => $optText ){
			if ($optKey == $field['value']) $checked = ' checked="checked"';
			else 							$checked = '';
			echo '<div>';
				echo '<input type="radio" name="' . $field['name'] . '" value="' . $optKey . '"' . $checked . '/>';
				echo '&nbsp;<label>' . $optText . '</label>';
			echo '</div>';
		}
		if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
	}

	function select( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		echo '<div>';
			if ( $field['label'] ) echo '<label for="' . $field['name'] . '">' . $field['label'] . '</label>';
			echo '<select id="' . $field['name'] . '" name="' . $field['name'] . '">';
				foreach ( $field['data'] as $optKey => $optText ){
					if ($optKey == $field['value']) $selected = ' selected="selected"';
					else 							$selected = '';
					echo '<option value="' . $optKey . '"' . $selected . '>';
						echo $optText;
					echo '</option>';
				}
			echo '</select>';
			if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
		echo '</div>';
	}

	function selectGWebFont( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		$result = get_transient( 'gwebfontslist' );
		if ( !$result ) {		
			$url = "https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=" . get_option( 'gmap_key_api' ) ;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_REFERER, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$result = curl_exec($ch);
			curl_close($ch);
			set_transient( 'gwebfontslist', $result, 3600*12 );
		}
		$tabFonts = json_decode( $result );
		echo '<div>';
			if ( $field['label'] ) echo '<label for="' . $field['name'] . '">' . $field['label'] . '</label>';
			echo '<select id="' . $field['name'] . '" name="' . $field['name'] . '" style="width: 250px;">';											
				foreach ( $tabFonts->items as $opt ){
				
					if ($opt->family == $field['value']) $selected = ' selected="selected"';
					else 							$selected = '';
					#; Display option
					echo '<option value="' . $opt->family . '"' . $selected . '>';
						echo $opt->family;
					echo '</option>';
				}
			echo '</select>';										
			echo '<a href="#" class="button gwebfonts-button">' . __( 'Preview' ) . '</a>';
			echo '<div id="gwebfonts-preview"></div>';
			if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
		echo '</div>';
	}

	function selectMapStyle( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		echo '<div>';
			if ( $field['label'] ) echo '<label for="' . $field['name'] . '">' . $field['label'] . '</label>';
			echo '<select id="' . $field['name'] . '" name="' . $field['name'] . '">';
				foreach ( Cleio_FrameworkFields::getMapStyles() as $opt ){
					if ($opt['key'] == $field['value']) $selected = ' selected="selected"';
					else 							$selected = '';
					echo '<option value="' . $opt['key'] . '"' . $selected . ' class="' . $opt['group'] . '">';
						echo $opt['text'];
					echo '</option>';
				}
			echo '</select>';
			if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
		echo '</div>';
	}

	function selectPage( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		global $wpdb;
		$sql 		= "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE post_parent = " . $field['value'] . ";";		
		$pageIds 	= $wpdb->get_col( $sql );
		$pages = get_pages( array( 'exclude' => implode( ",", $pageIds ) ) );		
		echo '<div>';											
			echo '<select name="' . $field['name'] . '" id="' . $field['name'] . '" class="selectpages"  style="width:350px;" tabindex="1">';					
				foreach ( $pages as $sPage ){				
					if ( $sPage->ID == $field['value'] )  $selected = ' selected="selected"';
					else $selected = '';					
					if ( $sPage->post_parent ) $title = "-- " . $sPage->post_title;
					else $title = $sPage->post_title;
					echo '<option value="' . $sPage->ID . '"' . $selected . '>';
						echo $title;
					echo '</option>';
				}						
			echo '</select>';			
		echo '</div>';
	}

	function slider( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		echo '<div>';
			if ( $field['label'] ) {
				$text = $field['value'] == 0 ? __( "Auto", "cleio" ) : $field['value'];
				echo '<label id="' . $field['name'] . '-label">' . $field['label'] . ' : <strong>' . $text . '</strong></label>';
			}
			echo '<div class="cleio-slider" data-min="' . $field['minval'] . '" data-max="' . $field['maxval'] . '" data-inputassoc="' . $field['name'] . '" data-value="' . $field['value'] . '"></div>';
			echo '<input type="hidden" id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . $field['value'] . '"/>';
		echo '</div>';
	}

	function text( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		echo '<div>';
			if ( $field['label'] ) echo '<label>' . $field['label'] . '</label> ';
			if ( $field['width'] ) $styleText = 'style="width: '. $field['width'] .'"';
			else $styleText = "";
			if ( $field['icon'] ) echo '<span class="' . $field['icon'] . ' social-icon"></span>';
			echo '<input type="text" id="' . $field['name'] . '" name="' . $field['name'] . '" value="' . $field['value'] . '" '. $styleText .'></input>';
			if ( $field['after_input'] ) echo ' <label>' . $field['after_input'] . '</label>';
			if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
		echo '</div>';
	}

	function textarea( $field )
	{
		$field['value'] = Cleio_FrameworkFields::getFieldValue( $field );
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label>';
		echo '<textarea cols="100" rows="15" id="' . $field['name'] . '" name="' . $field['name'] . '">' .  $field['value']  . '</textarea>';
		if ( $field['tooltip'] ) echo '<p>' . $field['tooltip'] . '</p>';
	}

	function twitterLogin( $field )
	{
		if ( $field['label'] ) echo '<label>' . $field['label'] . '</label>';
		if( !is_callable('curl_init') ){
			echo '<div class="error">' . __( "Warning! The PHP extension cURL is not activated on your server. Please contact your host in order to activate it.", "cleio" ) . '</div>';
		}
		$access_token = get_option( 'social-twitter-access-token' );
		if ( !$access_token ) { 
			echo '<p id="twitter-log-control">' . __( "It seems that your access token has not been configured yet or has expired. You should", "cleio" );
				echo '<a href="' . plugins_url() . '/cleio-toolbox/resources/utils/twitter_redirect.php">&nbsp;' . __( "set it now", "cleio" ) . '!</a>';
			echo '</p>';											
		}
		else {
			echo '<p id="twitter-log-control"><strong>';
				echo __('Account name','cleio') . ': ' . $access_token['screen_name'];
			echo '</strong>&nbsp;';
			echo '<a href="#" class="cleio-twitter-logout">' . __('Logout','cleio') .'</a></p>';
		}
	}
	
	function getFieldValue( $field ){
		$value = get_option( $field['name'] ) != '' ? get_option( $field['name'] ) : $field['defVal'];
		if ( !is_array( $value ) ) $value = stripslashes($value);
		return $value;
	}

	function getMapStyles()
	{
		$maps = array(
			array(	'key' => 'Toner', 			 'text' => 'Stamen - Toner', 		'group' => 'gmap osm' ),
			array(	'key' => 'Watercolor', 		 'text' => 'Stamen - Watercolor', 	'group' => 'gmap osm' ),
			array(	'key' => 'Roadmap', 		 'text' => 'Roadmap', 				'group' => 'gmap osm' ),
			array(	'key' => 'Satellite', 		 'text' => 'Satellite', 			'group' => 'gmap' ),
			array(	'key' => 'Night', 			 'text' => 'Night', 				'group' => 'gmap' ),
			array(	'key' => 'Red', 			 'text' => 'Red', 					'group' => 'gmap' ),
			array(	'key' => 'Blue', 			 'text' => 'Blue', 					'group' => 'gmap' ),
			array(	'key' => 'Pale Dawn', 		 'text' => 'Pale Dawn', 			'group' => 'gmap' ),
			array(	'key' => 'Retro', 			 'text' => 'Retro', 				'group' => 'gmap' ),
			array(	'key' => 'Light Monochrome', 'text' => 'Light Monochrome', 		'group' => 'gmap' ),
			array(	'key' => 'Chilled', 		 'text' => 'Chilled', 				'group' => 'gmap' ),
			array(  'key' => 'Blue Water',		 'text' => 'Blue Water',			'group'	=> 'gmap' ),
			array(	'key' => 'standardosm', 	 'text' => 'Standard',				'group' => 'osm' ),
			array(	'key' => 'cycle', 			 'text' => 'Cycle Map',				'group' => 'osm' ),
			array(	'key' => 'transport', 		 'text' => 'Transport Map',			'group' => 'osm' ),
			array(	'key' => 'aerial', 			 'text' => 'Aerial', 				'group' => 'osm' )
		);		
		return $maps;
	}
}
?>