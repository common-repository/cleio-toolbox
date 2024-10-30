<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( !class_exists( 'CleioToolbox_Shortcodes' ) ) {
	class CleioToolbox_Shortcodes {

		function __construct()
		{			
			add_shortcode('cleio-archives', array( &$this, 'getArchives' ) );
			add_action( 'wp_ajax_cleiotoolbox_get_archives', array( &$this, 'ajaxGetArchives' ) );
			add_action( 'wp_ajax_nopriv_cleiotoolbox_get_archives', array( &$this, 'ajaxGetArchives' ) );
			add_shortcode('cleio-sitemap', array( &$this, 'getSitemap' ) );
		}

		function getSitemap( $atts, $content = null ) {

			extract( 
				shortcode_atts( 
					array(
						'categories'=> 1,
						'exclude'	=> array()
					), 
					$atts 
				) 
			);
			
			// Get parent page
			global $wpdb;
			$parentPageId = get_option( 'places-page-countries' );
			$exludeFromPages = $parendPageId;
			
			// Get the pages 
			$listPages = get_pages( array( 'child_of' => $parentPageId ) );
			
			// Display Countries
			$retCountries .= '<h3>' . __('Countries','cleio')  . '</h3>';
			$retCountries .=  '<ul name="cleioplaceslist">';			
				foreach ( $listPages as $page ) {

					// Increment the page to exclude from page loop
					$exludeFromPages .= ',' . $page->ID;
					
					// Get ID of location
					$idLoc = get_post_meta($page->ID, '_location', true);
					
					// Get count of associated content
					$sqlCount = "SELECT COUNT(P.ID)
								FROM $wpdb->posts As P, $wpdb->postmeta As PM 
								WHERE PM.post_id = P.ID 
									AND PM.meta_key = '_location' 
									AND PM.meta_value = '" . $idLoc . "'
									AND IF( P.post_type = 'addresses', ( SELECT PM3.meta_value FROM $wpdb->postmeta As PM3 WHERE PM3.meta_key = '_address_assoc' AND PM3.post_id = P.ID ) , 1) <> ''				
									AND P.post_status = 'publish'
								";
					$countContentLoc = $wpdb->get_var ( $sqlCount );
					$countContentLoc = intval($countContentLoc) - 1;
					
					$sqlCountAddress = "SELECT COUNT(P.ID)
								FROM $wpdb->posts As P, $wpdb->postmeta As PM 
								WHERE PM.post_id = P.ID 
									AND PM.meta_key = '_location' 
									AND PM.meta_value = '" . $idLoc . "'			
									AND P.post_status = 'publish'			
									AND P.post_type = 'address'
								";
					$countAddress = $wpdb->get_var ( $sqlCountAddress );
					if ( get_option( 'address-loop-display', '0') == '0')  $ctText = $countContentLoc - $countAddress;
					else $ctText = $countContentLoc;
					
					// Draw option
					if ( $ctText > 0) {
						$retCountries .=  '<li>';
							$retCountries .=  '<a href="' . get_page_link( $page->ID ) . '">';
								$retCountries .=  $page->post_title;
							$retCountries .= '</a>';
							$retCountries .=  " (" . $ctText . ")";
						$retCountries .= '</li>';
					}
				}		
			$retCountries .=  '</ul>';	
			
			// Display Pages
			$retPages .= '<h3>' . __('Pages','cleio')  . '</h3>';
			$retPages .= '<ul>';
				$retPages .= wp_list_pages('title_li=&echo=0&depth=0&exclude=' . $exludeFromPages . '&sort_column=menu_order' );
			$retPages .= '</ul>';
			
			// Display Categories
			$retCat .= '<h3>' . __('Categories','cleio')  . '</h3>';
			$retCat .= '<ul>';
				$retCat .= wp_list_categories('echo=0&title_li=&hierarchical=0&show_count=1');
			$retCat .= '</ul>';
			
			$ret .= ( $exclude[0] != 'pages' || !array_search( 'pages', $exclude ) ) 		? $retPages : "";
			$ret .= ( $categories ) ? $retCat : "";
			$ret .= ( $exclude[0] != 'countries' || !array_search( 'countries', $exclude ) ) 	? $retCountries : "";
			return $ret;
		}

		function getArchives( $atts ) 
		{
			
			global $post;
			global $wpdb;
			
			// Extract options
			extract( 
				shortcode_atts( 
					array(
						'paginate_year'		=> 1,
						'paginate_month' 	=> 0,
						'exclude'			=> array( 'countries', 'revision', 'nav_menu_item',  'attachment')
					), 
					$atts
				) 
			);

			if( !is_array( $exclude ) ) $exclude = explode(",", $exclude);
			if ( $paginate_month == 1 ) $paginate_year = 1;
			/*
			$post_type = Array();
			if ( !$exclude['addresses'] ) $post_type['addresses'] = "addresses";
			if ( !$exclude['address'] ) 	 $post_type['address'] = "address";
			if ( !$exclude['pages'] ) 	 $post_type['page'] = "page";
			if ( !$exclude['countries'] ) $post_type['countries'] = "countries";
			*/
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if ( is_plugin_active( 'cleio-maps/cleio-maps.php' ) ) {
				$get_content_type = get_post_types();
				if ( array_search( 'posts', $exclude ) ) 	unset( $get_content_type['post'] );
				if ( array_search( 'pages', $exclude ) ) 	unset( $get_content_type['page'] );
				if ( array_search( 'address', $exclude ) ) 	unset( $get_content_type['address'] );
				if ( array_search( 'addresses', $exclude ) ) 	unset( $get_content_type['addresses'] );
				if ( array_search( 'photos', $exclude ) ) 	unset( $get_content_type['photo'] );
				unset( $get_content_type['location'] );
				unset( $get_content_type['wpcf7_contact_form'] );
				unset( $get_content_type['nav_menu_item'] );
					
				if ( array_search( 'countries', $exclude ) || $exclude[0] == "countries" ) {
					// Handle the pages who was automatically created (Places and childrens)		
					// Get the id list to exclude	
					$sql = "NOT IN (SELECT DISTINCT(ID) FROM $wpdb->posts WHERE ( ( ID = " . get_option( 'places-page-countries' ) ." ) OR ( post_parent = " . get_option( 'places-page-countries' ) ." ) ) )";
				}
				else {
					if ( !array_search( 'page', $get_content_type) ) {
						array_push( $get_content_type, 'page' );									
						$sql = "NOT IN (SELECT DISTINCT(B.ID) FROM $wpdb->posts As B WHERE B.post_type = 'page' AND ( ( B.ID = " . get_option( 'places-page-countries' ) ." ) OR ( B.post_parent != " . get_option( 'places-page-countries' ) ." ) ) )";
					}
				}
				
				// build the where for cleio-plugin
				$whereCleio = "AND	ID " . $sql . " ";
			}
			else {
			
				// Just pick the post
				$get_content_type = Array();
				if ( array_search( 'posts', $exclude ) ) 	unset( $get_content_type['post'] );
				if ( array_search( 'pages', $exclude ) ) 	unset( $get_content_type['page'] );
				if ( array_search( 'address', $exclude ) ) 	unset( $get_content_type['address'] );
				if ( array_search( 'addresses', $exclude ) ) unset( $get_content_type['addresses'] );
				if ( array_search( 'photos', $exclude ) ) 	unset( $get_content_type['photo'] );
				unset( $get_content_type['location'] );
				unset( $get_content_type['wpcf7_contact_form'] );
				unset( $get_content_type['nav_menu_item'] );
				//$get_content_type[] = 'post';
				$whereCleio = " ";
			
			}
			$current_year  	= $wpdb->get_var("SELECT MAX(YEAR(post_date)) FROM $wpdb->posts WHERE post_status = 'publish' AND ID <> " . $post->ID . " " . $whereCleio . " AND post_type IN ('" . implode( "','", $get_content_type ) . "')");
			$current_month  = $wpdb->get_var("SELECT MAX(MONTH(post_date)) FROM $wpdb->posts WHERE post_status = 'publish' AND ID <> " . $post->ID . " " . $whereCleio . " AND post_type IN ('" . implode( "','", $get_content_type ) . "')");
			
			$ret = '<div class="exile-archives">'; 	// Start of the archives container
			
				// Place input hidden for the options and AJAX values
				$ret .= '<input type="hidden" name="exile-paginate-year" 		id="exile-paginate-year" 		value="' . $paginate_year . '" />';		// Store in this page if the year's pagination is needed (used to call AJAX)
				$ret .= '<input type="hidden" name="exile-paginate-month" 		id="exile-paginate-month" 		value="' . $paginate_month . '" />';	// Store in this page if the month's pagination is needed (used to call AJAX)
				$ret .= '<input type="hidden" name="exile-posttype-page" 		id="exile-posttype-page" 		value="' . $pages . '" />';				// Store in this page if the page contain the page post type (used to call AJAX)
				$ret .= '<input type="hidden" name="exile-posttype-addresses" 	id="exile-posttype-addresses" 	value="' . $addresses . '" />';			// Store in this page if the page contain the addresses post type (used to call AJAX)
				$ret .= '<input type="hidden" name="exile-posttype-countries" 	id="exile-posttype-countries" 	value="' . $countries . '" />';			// Store in this page if the page contain the countries post type (used to call AJAX)
				$ret .= '<input type="hidden" name="exile-posttype" 			id="exile-posttype" 			value="' . join(",", $exclude) . '" />';			// Store in this page if the page contain the countries post type (used to call AJAX)
				$ret .= '<input type="hidden" name="exile-current-year" 		id="exile-current-year" 		value="' . $current_year . '" />';		// Store in this page the current year to start with
				$ret .= '<input type="hidden" name="exile-current-month" 		id="exile-current-month" 		value="' . $current_month . '" />';		// Store in this page the current month to start with
				$ret .= '<input type="hidden" name="exile-current-page" 		id="exile-current-page" 		value="' . $post->ID . '" />';			// Store in this page ID to exclude
						
				// Call the first time list's
				$ret .= '<div class="exile-archives-container">' . $this->getArchivesContent( $paginate_year, $paginate_month, $current_year, $current_month, $post->ID, $exclude, 1 ) . '</div>';
				
			$ret .= '</div>'; 	// End of the archives container
			
			// load the javascript needed code
			wp_enqueue_script( 'exile-archives', plugins_url() . '/cleio-toolbox/resources/js/archives.js', array( 'wp-ajax-response' )  );
			wp_localize_script('exile-archives', 'exile_vars', array( 'ajaxurl' => admin_url('admin-ajax.php') ) );
			// return the firt content
			return $ret;
		}

		/*
		 * AJAX Callback to return the current HTML to display
		 */
		function ajaxGetArchives(){

			// Init vars
			$year 			= $_POST['year'];
			$changeYear 	= (bool) $_POST['changeYear'];
			$month 			= $_POST['month'];
			$paginate_month = $_POST['pMonth'];
			$paginate_year 	= $_POST['pYear'];
			$post_id 		= $_POST['postId'];
			$post_type		= Array();
			$exclude 		= explode( ",", $_POST['posttype_exclude'] );
			if ( $_POST['posttype_addresses'] ) $post_type['address'] = "address";
			if ( $_POST['posttype_page'] ) 		$post_type['page'] = "page";
			if ( $_POST['posttype_countries'] ) $post_type['countries'] = "countries";
				
			// Get content
			$ret = $this->getArchivesContent( $paginate_year, $paginate_month, $year, $month, $post_id, $exclude, $changeYear );
			echo $ret;
			exit;

		}

		/*
		 * This method return the HTML content filtered with parameters:
		 *		- $month : The index of the researched month (-1 = no filter)
		 *		- $year : The researched year (-1 = no filter) 
		 */
		function getArchivesContent( $pYear = -1, $pMonth = -1, $yearStart, $monthStart, $post_id = "", $post_type = Array(), $changeYear = 1 )
		{
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
			// Init vars
			global $wpdb;
			$ordered_posts 	= array();
			$ret 			= "";
			
			// Set query of posts
			// If there a cleio-plugin active
			if ( is_plugin_active( 'cleio-maps/cleio-maps.php' ) ) {
			
				$get_content_type = get_post_types();	
				if ( array_search( 'posts', $post_type ) ) 	unset( $get_content_type['post'] );
				if ( array_search( 'pages', $post_type ) ) 	unset( $get_content_type['page'] );
				if ( array_search( 'address', $post_type ) ) 	unset( $get_content_type['address'] );
				if ( array_search( 'addresses', $post_type ) ) unset( $get_content_type['addresses'] );
				if ( array_search( 'photos', $post_type ) ) 	unset( $get_content_type['photo'] );
				unset( $get_content_type['location'] );
				unset( $get_content_type['wpcf7_contact_form'] );
				unset( $get_content_type['nav_menu_item'] );
				
				if ( array_search( 'countries', $post_type ) || $post_type[0] == "countries" ) {
					// Handle the pages who was automatically created (COUNTRIES and childrens)		
					// Get the id list to exclude	
					$sql = "NOT IN (SELECT DISTINCT(ID) FROM $wpdb->posts WHERE ( ( ID = " . get_option( 'places-page-countries' ) ." ) OR ( post_parent = " . get_option( 'places-page-countries' ) ." ) ) )";
				}
				else {
					if ( !array_search( 'page', $get_content_type) ) {
						array_push( $get_content_type, 'page' );									
						$sql = "NOT IN (SELECT DISTINCT(B.ID) FROM $wpdb->posts As B WHERE B.post_type = 'page' AND ( ( B.ID = " . get_option( 'places-page-countries' ) ." ) OR ( B.post_parent != " . get_option( 'places-page-countries' ) ." ) ) )";
					}
				}
				
				// build the where for cleio-plugin
				$whereCleio = "AND	A.ID " . $sql . " ";
			}
			else {
			
				// Just pick the post
				$get_content_type = Array();
				if ( array_search( 'posts', $post_type ) ) 	unset( $get_content_type['post'] );
				if ( array_search( 'pages', $post_type ) ) 	unset( $get_content_type['page'] );
				if ( array_search( 'address', $post_type ) ) 	unset( $get_content_type['address'] );
				if ( array_search( 'addresses', $post_type ) ) unset( $get_content_type['addresses'] );
				if ( array_search( 'photos', $post_type ) ) 	unset( $get_content_type['photo'] );
				unset( $get_content_type['location'] );
				unset( $get_content_type['wpcf7_contact_form'] );
				unset( $get_content_type['nav_menu_item'] );
				$whereCleio = " ";
			
			}
				
			// Retrieve the first month of a year, if year changed	
			if ( $changeYear != "" ) {
				$monthStart = $wpdb->get_var("SELECT MAX(MONTH(A.post_date)) FROM $wpdb->posts As A WHERE A.post_status = 'publish' AND A.ID <> " . $post_id . " " . $whereCleio . " AND A.post_type IN ('" . implode( "','", $get_content_type ) . "') AND YEAR(A.post_date) = '" . $yearStart . "'" );		
			}
			
			$sqlPosts = "SELECT A.* "
					. 	"FROM 	$wpdb->posts As A "
					.	"WHERE 	A.post_status = 'publish' "
					.	"AND	A.post_type IN ('" . implode( "','", $get_content_type ) . "') "
					.	"AND	A.ID <> " . $post_id . " " . $whereCleio;
			if ( ( $pYear == 1 ) && ( $pMonth == -1 || $pMonth == 0 ) ) 	$sqlPosts .=  "AND 	A.post_date BETWEEN '" . $yearStart . "-01-01' AND '" . $yearStart . "-12-31' ";
			else if ( $pMonth == 1 && $pYear == 1 ) 						$sqlPosts .=  "AND 	A.post_date BETWEEN '" . $yearStart . "-" . $monthStart . "-01' AND '" . $yearStart . "-" . $monthStart . "-31' ";
			else if ( ( $pMonth == 1 ) && ( $pYear == -1 || $pYear == 0 ) )	$sqlPosts .=  "AND 	MONTH(A.post_date) = '" . $monthStart . "' ";
			$sqlPosts .= "ORDER BY A.post_date DESC ";
			
			$get_posts = $wpdb->get_results( $sqlPosts );
			
			// Loop through posts
			foreach ($get_posts as $single) {

				// Extract yeag & month from post
				$year  = mysql2date('Y', $single->post_date);
				$month = mysql2date('F', $single->post_date);

				// Order the posts
				$ordered_posts[$year][$month][] = $single;

			}
			
			if ( $pYear || $pMonth ) {
				
				$ret .= '<div class="exile-pagination">';
				
				if ( $pYear ) {
				
					$year_lists = $wpdb->get_results("SELECT DISTINCT(YEAR(A.post_date)) As yearText FROM $wpdb->posts As A WHERE A.post_status = 'publish' AND A.ID <> " . $post_id . " " . $whereCleio . " AND A.post_type IN ('" . implode( "','", $get_content_type ) . "') ORDER BY YEAR(A.post_date) DESC" );
					
					$ret .= '<span class="exile-pagination-years">';
					$ctYear = 0;
					foreach( $year_lists as $year ){
						
						$ret .= ( $ctYear > 0 ) ? ' | ' : '';
						if ( $year->yearText != $yearStart ) {
							$ret .= '<a class="exile-pagination-year" id="exile-year-' . $year->yearText . '" href="#">' . $year->yearText . '</a>';
						}
						else {
							$ret .= $year->yearText;
						}
						$ctYear++;
					
					}
					$ret .= "</span><br />";
					
				}
			
				if ( $pMonth ) {
					
					if ( $pYear ) 	$month_lists = $wpdb->get_results("SELECT DISTINCT(MONTH(A.post_date)) As monthText FROM $wpdb->posts As A WHERE A.post_status = 'publish' AND A.ID <> " . $post_id . " " . $whereCleio . " AND A.post_type IN ('" . implode( "','", $get_content_type ) . "') AND YEAR(A.post_date) = '" . $yearStart . "' ORDER BY MONTH(A.post_date) DESC" );
					else		 	$month_lists = $wpdb->get_results("SELECT DISTINCT(MONTH(A.post_date)) As monthText FROM $wpdb->posts As A WHERE A.post_status = 'publish' AND A.ID <> " . $post_id . " " . $whereCleio . " AND A.post_type IN ('" . implode( "','", $get_content_type ) . "') ORDER BY MONTH(A.post_date) DESC" );
				//AND YEAR(A.post_date) = '" . $yearStart . "' 
					$ret .= '<span class="exile-pagination-months">';
					$ctMonth = 0;
					foreach( $month_lists as $month ){
					
						$ret .= ( $ctMonth > 0 ) ? ' | ' : '';
						if ( $month->monthText != $monthStart ) {
							$ret .= '<a class="exile-pagination-month" id="exile-month-' . $month->monthText . '" href="#">' . __( date( 'F', mktime(0,0,0,$month->monthText) ) ) . '</a>';
						}
						else {
							$ret .= __( date( 'F', mktime(0,0,0,$month->monthText) ) );
						}
						$ctMonth++;
					
					}
					$ret .= "</span>";
					
				}
				
				$ret .= '</div>';
				
			}
			$ret .= '<ul class="exile-archives-years">'; // The list of years to present
				
				// Loop through ordered posts
				foreach ($ordered_posts as $year => $months) {
				
					$ret .= '<li class="exile-archives-year">';									// Start the content of this $year
						$ret .= '<h3>' . $year . '</h3>';										// Print the current $year
						
						$ret .= '<ul class="exile-archives-months">';							// The list of month during this $year
							
							// Loop through the month's post 
							foreach ($months as $month => $posts ) {
								
								// Init Loop vars
								$ctPosts = count($months[$month]);
								
								$ret .= '<li class="exile-archives-month">';						// Start the content of this $month
									$ret .= '<h4>' . $month . '&nbsp;(' . $ctPosts . ')' . '</h4>';	// Print the current $month
									$ret .= '<ul class="exile-archives-posts">';					// The list of posts during this $month
									
										// Loop through the posts
										foreach ($posts as $single ) {
											
											// Determine Post_type
											$post_type_text = $single->post_type;
											// Check if the page is a country
											if ( $post_type_text == "page" ){
												$name_post_parent = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE ID =" . $single->post_parent );
												if ( $name_post_parent == "countries" ) $post_type_text = "country";
											}
											// Handle Contact Form plugin exception
											if ( $post_type_text == "wpcf7_contact_form" ) $post_type_text = "page";
											
											if ( $post_type_text == "page" ) $post_type_text = "Page";
											else if ( $post_type_text == "post" ) $post_type_text = "Post";
											else if ( $post_type_text == "address" ) $post_type_text = "Review";
											else if ( $post_type_text == "addresses" ) $post_type_text = "Review List";
											else if ( $post_type_text == "photo" ) $post_type_text = "Photo";

											$ret .= '<li class="exile-archives-post">';																// Start the content of this $post
												$ret .= '<span class="exile-archives-post-day">' . mysql2date('d', $single->post_date) . '</span>';	// Print the day of the post was writed
												$ret .= '&nbsp;<span class="exile-archives-posttype">[' . $post_type_text . ']</span>';				// Print the content type
												$ret .= '&nbsp;<a href="' . get_permalink($single->ID) . '">' . get_the_title($single->ID) . '</a>';// Print the title with link to the post
												$ret .= '&nbsp;<span class="exile-archives-comment-count">(' . $single->comment_count  . ')</span>';// Print the count of comment into the post
											$ret .= '</li>'; 																						// End of li.exile-archives-post
											
										} // End of foreach $posts
									
									$ret .= '</ul>'; 											// End of ul.exile-archives-posts
								$ret .= '</li>'; 												// End of li.exile-archives-month
							
							} // End of foreach $months 
					
						$ret .= '</ul>'; 														// End of ul.exile-archives-months
					$ret .= '</li>'; 															// End of li.exile-archives-year
				
				} // End of foreach $ordered_posts
			
			$ret .= '</ul>'; 																	// End of ul.exile-archives-years	

			// Return the HTML content
			return $ret;			
		}

	}
}
?>