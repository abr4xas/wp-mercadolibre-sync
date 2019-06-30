<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/rg-
 * @since      1.0.0
 *
 * @package    Wp_Mercadolibre_Sync
 * @subpackage Wp_Mercadolibre_Sync/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Mercadolibre_Sync
 * @subpackage Wp_Mercadolibre_Sync/public
 * @author     Roberto García <roberto.jg@gmail.com>
 */
class Wp_Mercadolibre_Sync_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Mercadolibre_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Mercadolibre_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-mercadolibre-sync-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Mercadolibre_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Mercadolibre_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-mercadolibre-sync-public.js', array( 'jquery' ), $this->version, false );

	}

	/*

	TODO, add description, see also admin_init_settings() on admin hoocks, it´s quite same thing

	*/
	public function init_settings() {

		$WPMLSync = wp_mercadolibre_sync_settings();

		if( !empty($WPMLSync['access_token']) && !empty($WPMLSync['auto_token']) ) { 

			$_expire_test = false;
			$_check_expires_in = !empty($WPMLSync['expires_in']) ? $WPMLSync['expires_in'] : 0; 
			if( $_check_expires_in < time() || $_expire_test ) {	
				try {
					// Make the refresh proccess 

					$refresh_MELI = new Meli($WPMLSync['appId'], $WPMLSync['secretKey'], $WPMLSync['access_token'], $WPMLSync['refresh_token']);

					$refresh = $refresh_MELI->refreshAccessToken();  
					$_seller_id = wp_mercadolibre_sync_get_seller_id($refresh_MELI, $refresh['body']->access_token);

					wp_mercadolibre_sync_update_settings(array(
						'access_token' => $refresh['body']->access_token,
						'expires_in' => $refresh['body']->expires_in,
						'refresh_token' => $refresh['body']->refresh_token,
						'seller_id' => $_seller_id
					));
 
					update_option('wp_mercadolibre_sync_status', 5);

					$refresh_public_count = (null !== get_option('wp_mercadolibre_sync_refresh_public_count')) ? get_option('wp_mercadolibre_sync_refresh_public_count') : 0;
					update_option('wp_mercadolibre_sync_refresh_public_count', ($refresh_public_count + 1));

				} catch (Exception $e) {
				  	$Exception .= $e->getMessage(). "\n";
				}
			}

		}

	}

	public function template_get_item($template) {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * If current theme has a file like:
		 * '/wpsyncml-tempates/{custom-template-part}.php' 
		 * then that that template will be used instead.
		 *
		 * Default priority is 10, o you can also filter this on child-themes or similar.
		 */
		// wpsyncml-tempates
		if(file_exists( get_template_directory() . '/wpsyncml-tempates/get_item.php' )){
			$template = get_template_directory() . '/wpsyncml-tempates/get_item.php';
		}
		return $template;	

	}

}
