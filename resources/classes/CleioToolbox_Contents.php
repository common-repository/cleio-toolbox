<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'CleioToolbox_Contents' ) ) {

	class CleioToolbox_Contents {

		function __construct() 
		{
			add_action( 'init', array( &$this, 'registerPhotoType' ) );
			add_action( 'add_meta_boxes', array( &$this, 'initCleioSettings' ) );
			add_action( 'save_post', array( &$this, 'saveCleioSettings' ) );
			add_action( 'edit_post', array( &$this, 'saveCleioSettings' ) );
			add_action( 'pre_get_posts', array( &$this, 'filterPostsQuery' ) );
		}

		function filterPostsQuery( $q ) 
		{
			global $wpdb;
			if ( $q->is_main_query() ) {

				// Remove countries from default archives page
				if( is_archive() ) {
					$sql 		= "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE ( ( ID = " . get_option( 'places-page-countries' ) . " ) OR ( post_parent = " . get_option( 'places-page-countries' ) . " ) );";
					$pageIds 	= $wpdb->get_col( $sql );	
					if ( empty($q->query_vars['post__not_in']) ) 	$q->query_vars['post__not_in'] = $pageIds;
					else $q->query_vars['post__not_in'] = array_merge( $q->query_vars['post__not_in'], $pageIds );  
				}
				
				if ( !$q->is_feed ) {
					if ( !is_admin() ){
						global $post;
						
						if ( is_home() && $q->query_vars['meta_key'] != '_location' ) {

							// Define post types to exclude
							//$get_content_type_base_exclude = Array( 'attachment', 'revision', 'nav_menu_item', 'location' );
						    $cleio_type_to_include = get_option( 'loop-content-filter', array( 'address', 'addresses', 'posts', 'photos' ) );
						    //$get_content_type_base_exclude = array_merge( $get_content_type_base_exclude, $cleio_type_to_exclude );

						    // Get all posts types
							//$get_content_type = get_post_types();
							//$get_content_type['countries'] = 'countries';
							$get_content_type = Array();
						    //$get_content_type[] = 'post';

						    foreach( $cleio_type_to_include as $type ){
						    	/*
						    	if( $type == "pages" ) unset( $get_content_type['page'] );
						    	else if( $type == "posts") unset( $get_content_type['post'] );
						    	else if( $type == "photos") unset( $get_content_type['photo'] );
						    	else unset( $get_content_type[$type] );
						    	*/
						    	
						    	if( $type == "pages" ) $get_content_type['page'] = 'page';
						    	else if( $type == "posts") $get_content_type['post'] = 'post';
						    	else if( $type == "photos") $get_content_type['photo'] = 'photo';
						    	else $get_content_type[$type] = $type;
						    }

							// Add some custom post type by default
							if ( empty($q->query_vars['post_type']) ) {

								$i = 0;			
								$addCountryPage = array_search("countries", $get_content_type);				
								foreach( $get_content_type as $type ) {
								   if ( !post_type_exists( $type ) ) unset( $get_content_type[$type] );
								   $i++;
								}

								$q->set('post_type', $get_content_type);
							}					

							$sqlReq = "SELECT P.ID
									   FROM $wpdb->posts As P, $wpdb->postmeta As PM 
									   WHERE PM.post_id = P.ID
									   AND PM.meta_key = '_content_excluded_loop'";
							$exluded_content = $wpdb->get_results( $sqlReq );
							$tabExclude = array();
							foreach( $exluded_content as $content ) {
							   $tabExclude[] = $content->ID;
							}			
							$q->set('post__not_in', $tabExclude );

							if ( $addCountryPage === false ) {
							   // Get the id list to exclude	
							   $sql 		= "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE ( ( ID = " . get_option( 'places-page-countries' ) . " ) OR ( post_parent = " . get_option( 'places-page-countries' ) . " ) );";
							}
							else {
							    if ( !array_search( 'page', $get_content_type) ) {
								   if ( is_array($q->query_vars['post_type']) ) array_push($q->query_vars['post_type'], 'page' );
								   else {
									   $q->set('post_type', $get_content_type);
									   array_push($q->query_vars['post_type'], 'page' );							
								   }							
								   $sql = "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE post_type = 'page' AND ( ( ID = " . get_option( 'places-page-countries' ) . " ) OR ( post_parent != " . get_option( 'places-page-countries' ) . " ) );";
							    }
							}
							
						   
							// Remove Sticky post if selected in highlight content
							if ( get_option( 'home-highlight' ) == 'post' ){
								$sticky = get_option('sticky_posts');
								if ( isset( $sticky[0] ) ) {
								   if ( empty($q->query_vars['post__not_in']) ) 	$q->query_vars['post__not_in'] = Array( $sticky[0] );
								   else 											$q->query_vars['post__not_in'] = array_merge( $q->query_vars['post__not_in'], Array( $sticky[0] ) );
								}
							}		
						}
						else {
							
							if( !empty($post) && $post->ID ) {
								$sql = "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE ( ( ID = " . get_option( 'places-page-countries' ) . " ) OR ( post_parent = " . get_option( 'places-page-countries' ) . " ) ) AND ID <> " . $post->ID . ";";
						   
								if ( empty($q->query_vars['post_type']) ) {
									if ( ( get_option( 'address-loop-display') == '1') || $post->post_type == 'address' || $post->post_type == 'addresses' ) $q->set('post_type', array('page','guide','photo','addresses','post','address') );
									else $q->set('post_type', array('page','guide','photo','addresses','post') );
							 
								}
							}
							else {
								/*
								$sql = "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE ( ( ID = " . get_option( 'places-page-countries' ) . " ) OR ( post_parent = " . get_option( 'places-page-countries' ) . " ) );";
								*/
								if ( empty($q->query_vars['post_type']) ) {
									$q->set('post_type', array('page','guide','photo','addresses','post') );					 
								}
								
							}
							
							if( is_single() ) {
								$q->set('post_type', array('page','guide','photo','addresses','post','address') );
							}

							if ($q->is_search || is_archive() ) {
								$q->query_vars['post_type'][] = "photo";
						    }
							
						}
						
						if( $sql ) {
							$pageIds 	= $wpdb->get_col( $sql );	
							if ( empty($q->query_vars['post__not_in']) ) 	$q->query_vars['post__not_in'] = $pageIds;
							else 						$q->query_vars['post__not_in'] = array_merge( $q->query_vars['post__not_in'], $pageIds );
						}
						
						// Remove the current page
						if ($post->post_type == 'page') {
						
							// Remove the current page to the list
							if ( empty($q->query_vars['post__not_in']) ) {
								$q->query_vars['post__not_in'] = array( $post->ID );
							} else {
								$q->query_vars['post__not_in'][] = $post->ID;
							}
							
							
						}
					}					
				}
				else {					   
					global $wpdb;
					$sqlReq = "SELECT P.ID
							   FROM $wpdb->posts As P, $wpdb->postmeta As PM 
							   WHERE PM.post_id = P.ID
							   AND PM.meta_key = '_content_excluded_rss'";
					$exluded_content = $wpdb->get_results( $sqlReq );
					$tabExclude = array();
					foreach( $exluded_content as $content ) {
					   $tabExclude[] = $content->ID;
					}		
					$q->set('post__not_in', $tabExclude );

					if ( empty($q->query_vars['post_type']) ) {

						// Define post types to exclude
						$get_content_type_base_exclude = Array( 'attachment', 'revision', 'nav_menu_item', 'location' );
					    $cleio_type_to_include_rss = get_option( 'rss-content-filter', array( 'address', 'addresses', 'posts', 'photos' ) );
						$get_content_type = Array();
					    foreach( $cleio_type_to_include_rss as $type ){
					    	if( $type == "pages" ) $get_content_type['page'] = 'page';
					    	else if( $type == "posts") $get_content_type['post'] = 'post';
					    	else if( $type == "photos") $get_content_type['photo'] = 'photo';
					    	else $get_content_type[$type] = $type;
					    }

						// Add some custom post type by default
						$i = 0;			
						$addCountryPage = array_search("countries", $get_content_type);				
						foreach( $get_content_type as $type ) {
						   if ( !post_type_exists( $type ) ) unset( $get_content_type[$type] );
						   $i++;
						}

						$q->set('post_type', $get_content_type);

						if ( !$addCountryPage ) {
						   // Handle the pages who was automatically created (Places and childrens)		
						   // Get the id list to exclude	
						   $sql 		= "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE ( ( ID = " . get_option( 'places-page-countries' ) . " ) OR ( post_parent = " . get_option( 'places-page-countries' ) . " ) );";
						}
						else {
						   if ( !array_search( 'page', $get_content_type) ) {
							   $q->query_vars['post_type'][] = "page";
							   $sql 		= "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE post_type = 'page' AND ( ( ID = " . get_option( 'places-page-countries' ) . " ) OR ( post_parent != " . get_option( 'places-page-countries' ) . " ) );";
						   }
						}

						$pageIds 	= $wpdb->get_col( $sql );	
						if ( empty($q->query_vars['post__not_in']) ) 	$q->query_vars['post__not_in'] = $pageIds;
						else 											$q->query_vars['post__not_in'] = array_merge( $q->query_vars['post__not_in'], $pageIds );
					   
					}
				}			
			}
		}


		/**
		 * Register the Location post types
		 */
		function registerPhotoType()
		{
			$labels_photo_item = array(    
				'add_new_item'       => __('Add new', 'cleio'),
				'edit_item'          => __('Edit Featured Photo', 'cleio'),
				'new_item'           => __('New Featured Photo', 'cleio'),
				'view_item'          => __('Preview Featured Photo', 'cleio'),
				'search_items'       => __('Search Featured Photos', 'cleio'),
				'not_found'          => __('No Featured Photos found.', 'cleio'),
				'not_found_in_trash' => __('No Featured Photos found in Trash.', 'cleio'),
				'menu_name'          => __('Photos Posts', 'cleio'),
				'add_new'            => __('Add new', 'cleio')
			);

			register_post_type(
				'photo', 
				array(
					'labels'            => $labels_photo_item,
					'label'             => __('Featured Photo', 'cleio'),
					'singular_label'    => __('Featured Photo Item', 'cleio'),
					'public'            => true,
					'show_ui'           => true, 
					'_builtin'			=> false,
					'rewrite'           => true,
					'capability_type'   => 'post',
					'show_in_admin_bar' => true,
					'hierarchical'      => false,
					'supports'          => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt' ),
					'menu_icon'         => 'dashicons-format-gallery'
				)
			);
			//flush_rewrite_rules( false );
		}

		function initCleioSettings(){			
			$post_types=get_post_types(); 
			unset($post_types['attachment']);
			unset($post_types['revision']);
			unset($post_types['nav_menu_item']);
			unset($post_types['location']);
			foreach ($post_types as $post_type ) { add_meta_box('cleio-display-settings', __('Cleio Settings','cleio'), array( &$this, 'setCleioSettingsField' ), $post_type, 'side', 'default'	);	}		
		}

		function setCleioSettingsField( $post )
		{
			if( $post->post_type == "photo" ) $post_type = "photos";
			if( $post->post_type == "post" ) $post_type = "posts";
			if( $post->post_type == "page" ) $post_type = "pages";
			if( $post->post_type == "addresses" ) $post_type = "addresses";
			// Loop Option
			$optChecked = get_post_meta( $post->ID, '_content_excluded_loop', true );
			if ( $optChecked == "1" ) $optLoopChecked = 'checked="checked" ';
			else  $optLoopChecked = "";			
			$loopContentType = get_option( 'loop-content-type', array() );
			//$rssContentType[] = 'post';			
			if ( array_search( $post_type, $loopContentType ) === false ) $disabledLoop = '';
			else $disabledLoop = 'disabled';
			// Layout options
			echo '<p>';
				echo '<input type="checkbox" name="field_optloop" id="field_optloop" value="1" ' . $optLoopChecked . $disabledLoop . '/>';
				echo '&nbsp;<span><label for="field_optloop" class="' . $disabledLoop . '">' . __( "Don't show in home/blog loop",'cleio' ) . '</label></span>';
			echo '</p>';

			// RSS Option
			$optChecked = get_post_meta( $post->ID, '_content_excluded_rss', true );
			if ( $optChecked == "1" ) $optRssChecked = 'checked="checked" ';
			else  $optRssChecked = "";			
			$rssContentType = get_option( 'rss-content-type', array() );
			//$rssContentType[] = 'post';			
			if ( array_search( $post_type, $rssContentType ) === false ) $disabledRss = '';
			else $disabledRss = 'disabled';
			// Layout options
			echo '<p>';
				echo '<input type="checkbox" name="field_optrss" id="field_optrss" value="1" ' . $optRssChecked . $disabledRss . '/>';
				echo '&nbsp;<span><label for="field_optrss" class="' . $disabledRss . '">' . __( "Don't show in RSS feed",'cleio' ) . '</label></span>';
			echo '</p>';
		}

		function saveCleioSettings( $post_ID )
		{
			if (wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))	return;
			
			// Check if not an autosave
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )  return;			
			
			if(isset($_POST['field_optrss'])) update_post_meta($post_ID, '_content_excluded_rss', '1');
			else delete_post_meta($post_ID,'_content_excluded_rss');

			if(isset($_POST['field_optloop'])) update_post_meta($post_ID, '_content_excluded_loop', '1');
			else delete_post_meta($post_ID,'_content_excluded_loop');
		}

	}

}

/* 
 * IMPORTANT : Filter the permalink for the custom post type "photo" 
 */
/*
add_filter( 'post_type_link', 'photoPermalink', 10, 3);
function photoPermalink($permalink, $post_id, $leavename) {
	
	if( 'photo' == $post_id->post_type && '' != $permalink && !in_array( $post->post_status, array('draft', 'pending', 'auto-draft') ) ) {
		
		$post = get_post($post_id);
		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			$leavename? '' : '%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			$leavename? '' : '%pagename%',
		);

		$unixtime = strtotime($post->post_date);
     
        $category = '';
        if ( strpos($permalink, '%category%') !== false ) {
            $cats = get_the_category($post->ID);
            if ( $cats ) {
                usort($cats, '_usort_terms_by_ID'); // order by ID
                $category = $cats[0]->slug;
                if ( $parent = $cats[0]->parent )
                    $category = get_category_parents($parent, false, '/', true) . $category;
            }
            // show default category in permalinks, without
            // having to assign it explicitly
            if ( empty($category) ) {
                $default_category = get_category( get_option( 'default_category' ) );
                $category = is_wp_error( $default_category ) ? '' : $default_category->slug;
            }
        }
     
        $author = '';
        if ( strpos($permalink, '%author%') !== false ) {
            $authordata = get_userdata($post->post_author);
            $author = $authordata->user_nicename;
        }
     
        $date = explode(" ",date('Y m d H i s', $unixtime));
        $rewritereplace =
        array(
            $date[0],
            $date[1],
            $date[2],
            $date[3],
            $date[4],
            $date[5],
            $post->post_name,
            $post->ID,
            $category,
            $author,
            $post->post_name,
        );
        $permalink = str_replace($rewritecode, $rewritereplace, get_option('permalink_structure'));
		$permalink = user_trailingslashit(home_url($permalink));
	}
	return $permalink;
} 
add_filter( 'preview_post_link', 'photoPreviewLink');
function photoPreviewLink($link) {
	global $post;	
	if( ('photo' == $post->post_type) && in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) $link = wp_get_shortlink() . "&preview=true";
	return $link;
}
function parsePhotoPostType( $query ) { 
    // Only noop the main query
    if ( ! $query->is_main_query() ) return; 
    // Only noop our very specific rewrite rule match
    if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) return;
    if ( ! empty( $query->query['name'] ) ) {
    	if ( is_array($query->query_vars['post_type']) ) array_push($query->query_vars['post_type'], 'photo' );
		else {
		   $query->set('post_type', array('post','photo'));						
		}
    }
}
add_action( 'pre_get_posts', 'parsePhotoPostType' );
*/
?>