<?php
abstract class Cleio_Helpers {

	/**
	 * Return the thumbnail or the default placeholder
	 * @param  array $args Arguments
	 */
	function getThumbnails( $args ) 
	{
		global $post;
		// Extract arguments from array
		$defaultArgs = array(
			'link'           => true,
			'linkclass'      => false,
			'echo'           => true,
			'id'			 => $post->ID,
			'linkid'		 => false,
			'name'           => ''
		);
		$args = wp_parse_args( $args, $defaultArgs );
		extract( $args );
		if ( $id != $post->ID ) {
			$saved_post = $post;
			$post       = get_post( $id );
			setup_postdata( $post );
		}
		// Define link (if other post to load, linkid will not be empty)
		if( !$linkid ) $linkid = $id;
		// Prepare content
		$ret = "";
		if ( $link ) {
			$permalink = get_permalink( $linkid );
			$ret .= '<a style="display: block!important;" href="' . $permalink . '" rel="bookmark" title="'. __("Permalink to") . ' ' . get_the_title() . '"';
			if ( $linkclass ) $ret .= ' class="' . $linkclass . '">';
			else $ret .= '>';
		}
		if ( has_post_thumbnail() ) $ret .= get_the_post_thumbnail( $post->ID, $name ); 
		else {
			if( $name == "" ) {
				$width = get_option( 'thumbnail_size_w' );
				$height = get_option( 'thumbnail_size_h' );
			}
			else if ( $name == "thumbnail" || $name == "large" || $name == "medium" ){
				$width = get_option( $name . '_size_w' );
				$height = get_option( $name . '_size_h' );				
			}
			else {
				global $_wp_additional_image_sizes;
				$width = $_wp_additional_image_sizes[ $name ]['width'];
				$height = $_wp_additional_image_sizes[ $name ]['height'];
			}
			$ret .= '<img width="' . $width . '" height="' . $height . '" src="'. plugins_url( '/images/placeholder.jpg', dirname(__FILE__) ) .'" alt="*" />';
		}
		if ( $link ) $ret .= '</a>';
		// Reload current post
		if ( $saved_post && ($saved_post->ID != $post->ID) ) $post = $saved_post;
		// Display content
		if ( $echo ) echo $ret;
		else return $ret;
	}

}
?>