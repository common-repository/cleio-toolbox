<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( !class_exists( 'CleioToolbox_Admin' ) ) {
	class CleioToolbox_Admin {
		/**
		 * Version
		 *
		 * @var string Current version
		 */
		var $version = '1.0.0';
				
		function __construct() 
		{

			// Define version
			define( 'cleiotoolbox_version', $this->version );
			
			// Load translation
			load_plugin_textdomain( 'cleio', false, basename( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/languages/' );

			// Actions & Filters
			add_action( 'admin_menu', array( &$this, 'initMenu' ) );
			add_action( 'admin_init', array( &$this, 'installationRedirection' ) );
			add_action( 'wpmu_new_blog', array( &$this, 'activate' ), 10, 6); 	
			add_action( 'wp_enqueue_scripts', array( &$this, 'scriptJs' ), 99 );
			add_action( 'wp_enqueue_scripts', array( &$this, 'scriptCss' ) );
		
			// AJAX action
			
		}

		function installationRedirection()
		{
            //flush_rewrite_rules();
		    if ( get_option( 'cleio_do_activation_redirect', false ) ) {
		        delete_option( 'cleio_do_activation_redirect' );
		        if( !isset( $_GET['activate-multi'] ) ) wp_redirect("index.php");
		    }			
		}

		/**
		 * Activation hook
		 */
		function activate()
		{
            // Initializing the activation options	
            add_option( 'cleio_do_activation_redirect', true );
		}

		function scriptCss()
		{
			wp_enqueue_style( 'fontawesome_styles',  plugins_url() . '/cleio-toolbox/cleio-resources/css/font-awesome.css', false, false, 'screen' );
			wp_enqueue_style( 'cleiotoolbox_frontendstyles',  plugins_url() . '/cleio-toolbox/resources/css/cleiotoolbox-frontend.css', false, false, 'screen' );
		}

		function scriptJs()
		{
			
		}

		/**
		 * Menu initialisation, add the needed menu to WordPress
		 */
		function initMenu()
		{
			if( !$this->checkCleioMenu() ){
				if( function_exists('add_object_page') ) add_object_page( 	__('Cleio', 'cleio'), __('Cleio', 'cleio'), 'administrator', 'cleio-base', array( &$this, 'contentAdminPage' ), 'dashicons-location', 6 );
				else add_menu_page( __('Cleio', 'cleio'), __('Cleio', 'cleio'), 'administrator', 'cleio-base', array( &$this, 'contentAdminPage' ), 'dashicons-location', 6 );
				add_submenu_page( 'cleio-base', __('Cleio - Toolbox', 'cleio'), __('Cleio Toolbox', 'cleio'), 'administrator', 'cleio-base', array( &$this, 'contentAdminPage' ) );
			}
			else {
				add_submenu_page( 'cleio-base', __('Cleio - Toolbox', 'cleio'), __('Cleio Toolbox', 'cleio'), 'administrator', 'cleio-toolbox', array( &$this, 'contentAdminPage' ) );
			}
		}


		/**
		 * Cleio Maps admin page content
		 */		
		function contentAdminPage()
		{
			$panes = array(			
				array(
					'id'		=> 'loop_settings',
					'label' 	=> __( 'Loop filter','cleio' ),
					'title' 	=> __( 'Loop filter','cleio' ),
					'class' 	=> 'cloop'
				),
				array(
					'id'		=> 'rss_settings',
					'label' 	=> __( 'RSS filter','cleio' ),
					'title' 	=> __( 'RSS filter','cleio' ),
					'class' 	=> 'crss'
				),
				array(
					'id'		=> 'social_settings',
					'label' 	=> __( 'Social & Data','cleio' ),
					'title' 	=> __( 'Social & Data','cleio' ),
					'class' 	=> 'csocial'
				),
				array(
					'id'		=> 'archives_settings',
					'label' 	=> __( 'Archives Generator','cleio' ),
					'title' 	=> __( 'Archives Generator','cleio' ),
					'class' 	=> 'carchives'
				),
				array(
					'id'		=> 'sitemap_settings',
					'label' 	=> __( 'Sitemap Generator','cleio' ),
					'title' 	=> __( 'Sitemap Generator','cleio' ),
					'class' 	=> 'csitemap'
				)
			);
			
			$options = array(

				'loop_settings'	=> array(
					'label' => __( 'Loop filter','cleio'),
					'desc' => '<em>' . __('Controls what is published in your homepage or blog feed.','cleio') . '</em>',
					'fields' => array(
						array(
							'type'   => 'fieldsetStart',
							'legend' => __( 'Content types to display in the home/blog loop', 'cleio' )
						),
						array(
							'name'   => 'loop-content-filter',
							'type'   => 'contentTypeList',
							'defVal' => array( 'address', 'addresses', 'posts', 'photos' )											
						),							
						array(
							'type'   => 'fieldsetEnd'
					    )
					)
				),

				'rss_settings'	=> array(
					'label' => __( 'RSS filter','cleio'),
					'desc' => '<em>' . __('Controls what is published in your RSS feed.','cleio') . '</em>',
					'fields' => array(
						array(
							'type'   => 'fieldsetStart',
							'legend' => __( 'Content types to display in the RSS feed', 'cleio' )
						),
						array(
							'name'   => 'rss-content-filter',
							'type'   => 'contentTypeList',
							'defVal' => array( 'address', 'addresses', 'posts', 'photos' )											
						),							
						array(
							'type'   => 'fieldsetEnd'
					    )
					)
				),

				'social_settings'	=> array(
					'label' => __( 'Social & Data','cleio'),
					'desc' => '<em>' . __('Input your data here in order to use Cleio Social widgets.','cleio') . '</em>',
					'fields' => array(
						array(
					        'type'      => 'fieldsetStart',
					        'legend'    => __( 'Contact information', 'cleio')
					    ),	
						array(
							'label'		=> __( 'E-mail', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-email',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-envelope fa-lg',
							'defVal' 	=> ''				
						),
						array(
							'label'		=> __( 'Telephone', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-tel',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-phone fa-lg',
							'defVal' 	=> ''				
						),
						array(
							'label'		=> __( 'Skype ID', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-skype',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-skype fa-lg',
							'defVal' 	=> ''				
						),
					    array(
					        'type'      => 'fieldsetEnd',
					    ),
					    array(
					        'type'      => 'group',
					        'label'     => __( 'Social Networks', 'cleio')
					    ),
					    array(
					        'type'      => 'fieldsetStart',
					        'legend'    => __( 'Facebook', 'cleio')
					    ),
						array(
							'label'		=> __( 'Facebook Personal Account', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-fb',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-facebook-square fa-lg',
							'defVal' 	=> ''				
						),
						
						array(
							'label'		=> __( 'Facebook Page', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-fb-page',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-facebook-square fa-lg',
							'defVal' 	=> 'http://'						
						),
						array(
					        'type'      => 'fieldsetEnd',
					    ),
					    array(
					        'type'      => 'fieldsetStart',
					        'legend'    => __( 'Twitter', 'cleio')
					    ),
						array(
							'label'		=> __( 'Twitter', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-twitter',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-twitter fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Twitter Login', 'cleio'),
							'type' 		=> 'twitterLogin',	
						),
						array(
					        'type'      => 'fieldsetEnd',
					    ),
					    array(
					        'type'      => 'fieldsetStart',
					        'legend'    => __( 'Instagram', 'cleio')
					    ),
					    array(
							'label'		=> __( 'Instagram', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-instagram',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-instagram fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Instagram Login', 'cleio'),
							'type' 		=> 'instagramLogin'	
						),

					    array(
					        'type'      => 'fieldsetEnd',
					    ),
					    array(
					        'type'      => 'fieldsetStart',
					        'legend'    => __( 'Other social networks', 'cleio')
					    ),
						array(
							'label'		=> __( 'Delicious', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-delicious',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-delicious fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Flickr', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-flickr',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-flickr fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Google +', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-googleplus',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-google-plus fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'LinkedIn', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-linkedin',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-linkedin fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Vimeo', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-vimeo',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-vimeo-square fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Youtube', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-youtube',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-youtube-play fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Pinterest', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-pinterest',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-pinterest fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Foursquare', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-foursquare',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-foursquare fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Tumblr', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-tumblr',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-tumblr fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
							'label'		=> __( 'Vine', 'cleio'),
							'tooltip'	=> '',
							'name'		=> 'social-vine',
							'type' 		=> 'text',
							'width'		=> '300px',
							'icon'		=> 'fa fa-vine fa-lg',
							'defVal' 	=> 'http://'				
						),
						array(
					        'type'      => 'fieldsetEnd',
					    )
					)
				),

				'archives_settings'	=> array(
					'label' => __( 'Archives Generator','cleio'),
					'desc' => '',
					'fields' => array(
						array(
							'type' => 'archivesShortcodeGenerator'
						)
					)
				),

				'sitemap_settings'	=> array(
					'label' => __( 'Sitemap Generator','cleio'),
					'desc' => '',
					'fields' => array(
						array(
							'type' => 'sitemapShortcodeGenerator'
						)
					)
				)

			);

			Cleio_Framework::displayFramework( $panes, $options, 'Cleio Toolbox', '1.0' );
		}

		/**
		 * Check if the Cleio base menu already exists
		 * @return boolean Return true if the menu already exist
		 */
		function checkCleioMenu()
		{
			global $menu;
			$exist = false;
			foreach($menu as $item) { if(strtolower($item[0]) == strtolower('cleio')) { $exist = true; } }
			return $exist;
		}

	}
}
?>