<?php
/**
 * Plugin Name: Essential SEO
 * Plugin URI: http://seamlessthemes.com/
 * Description: A very basic, yet powerful, SEO Plugin.
 * Version: 0.2.1
 * Author: James Geiger
 * Author URI: http://seamlessthemes.com
 *
 * @version 0.2.0
 * @author James Geiger <james@seamlessthemes.com>
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, James Geiger and Justin Tadlock
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


class Essential_SEO {

	/* Setup
	------------------------------------------ */

	public static function setup() {

		add_action( 'plugins_loaded', array('Essential_SEO', 'essential_seo_constants'));

		add_action( 'plugins_loaded', array('Essential_SEO','essential_seo_plugins_loaded' ));
                
	}


	/* Constants
	------------------------------------------ */

	public static function essential_seo_constants() {

		/* Set plugin version constant. */
		define( 'ESSENTIAL_SEO_VERSION', '0.2.0' );

		/* Set constant path to the plugin directory. */
		define( 'ESSENTIAL_SEO_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );

		/* Set the constant path to the plugin directory URI. */
		define( 'ESSENTIAL_SEO_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

	}

	/* Plugins Loaded
	------------------------------------------ */

	/**
	 * Load all plugins functions
	 * 
	 * @since 0.1.0
	 * @return null
	 */
	public static function essential_seo_plugins_loaded(){

		/* Load settings and functions */
		require_once( ESSENTIAL_SEO_PATH . 'inc/counter.php' );
		require_once( ESSENTIAL_SEO_PATH . 'inc/essential-seo.php' );
		require_once( ESSENTIAL_SEO_PATH . 'inc/meta-box-post-seo.php' );
        require_once( ESSENTIAL_SEO_PATH . 'inc/essential-seo-settings.php');
	}

}

Essential_SEO::setup();