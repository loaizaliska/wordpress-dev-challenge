<?php

/**
 *
 * The plugin bootstrap file
 *
 * This file is responsible for starting the plugin using the main plugin class file.
 *
 * @since 0.0.1
 * @package Liska_Link_Checker
 *
 * @wordpress-plugin
 * Plugin Name:     Liska Link Checker
 * Description:     Plugin to check wrong links in posts.
 * Version:         0.0.1
 * Author:          Liska Loaiza
 * Author URI:      https://www.example.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     plugin-name
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

if ( ! class_exists( 'liska_link_checker' ) ) {

	/*
	 * main liska_link_checker class
	 *
	 * @class liska_link_checker
	 * @since 0.0.1
	 */
	class liska_link_checker {

		/*
		 * liska_link_checker plugin version
		 *
		 * @var string
		 */
		public $version = '4.7.5';

		/**
		 * The single instance of the class.
		 *
		 * @var liska_link_checker
		 * @since 0.0.1
		 */
		protected static $instance = null;

		/**
		 * Main liska_link_checker instance.
		 *
		 * @since 0.0.1
		 * @static
		 * @return liska_link_checker - main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * liska_link_checker class constructor.
		 */
		public function __construct() {
			$this->load_plugin_textdomain();
			$this->define_constants();
			$this->includes();
			$this->define_actions();
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'liska-link-checker', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Include required core files
		 */
		public function includes() {
      
			// Load custom functions and hooks
			require_once __DIR__ . '/includes/includes.php';
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Define liska_link_checker constants
		 */
		private function define_constants() {
			define( 'LISKA_LINK_CHECKER_PLUGIN_FILE', __FILE__ );
			define( 'LISKA_LINK_CHECKER_PLUGIN_DIR', __DIR__ );
			define( 'LISKA_LINK_CHECKER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'LISKA_LINK_CHECKER_VERSION', $this->version );
			define( 'LISKA_LINK_CHECKER_PATH', $this->plugin_path() );
		}

		/**
		 * Define liska_link_checker actions
		 */
		public function define_actions() {

			require_once( LISKA_LINK_CHECKER_PLUGIN_DIR . '/includes/functions/ActivationPlugin.php' );
			register_activation_hook(__FILE__, array('ActivationPlugin', 'activate'));


				require_once( LISKA_LINK_CHECKER_PLUGIN_DIR . '/includes/hooks/CronJobHook.php' );

				new CronJobHook();
		}

		/**
		 * Define liska_link_checker menus
		 */
		public function define_menus() {
            //
		}
	}

	$liska_link_checker = new liska_link_checker();
}
