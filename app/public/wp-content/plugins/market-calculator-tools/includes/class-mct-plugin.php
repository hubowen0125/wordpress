<?php
/**
 * Main plugin coordinator.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MCT_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var MCT_Plugin|null
	 */
	private static ?MCT_Plugin $instance = null;

	/**
	 * Active provider.
	 *
	 * @var MCT_Rate_Provider_Interface
	 */
	private ?MCT_Rate_Provider_Interface $provider = null;

	/**
	 * Return the plugin instance.
	 *
	 * @return MCT_Plugin
	 */
	public static function instance(): MCT_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Prevent direct construction outside this class.
	 */
	private function __construct() {
	}

	/**
	 * Resolve the provider after all plugins have loaded.
	 */
	private function resolve_provider(): void {
		$provider = new MCT_Mock_Rate_Provider();

		/**
		 * Filter the active provider without changing consumers.
		 *
		 * @param MCT_Rate_Provider_Interface $provider Default provider.
		 */
		$filtered = apply_filters( 'mct_rate_provider', $provider );

		$this->provider = $filtered instanceof MCT_Rate_Provider_Interface ? $filtered : $provider;
	}

	/**
	 * Register all plugin hooks.
	 */
	public function init(): void {
		$this->resolve_provider();

		$pages      = new MCT_Pages();
		$rest_api   = new MCT_REST_API( $this->provider );
		$shortcodes = new MCT_Shortcodes( $this->provider );

		add_action( 'init', array( $shortcodes, 'register' ) );
		add_action( 'rest_api_init', array( $rest_api, 'register_routes' ) );
		add_action( 'wp_enqueue_scripts', array( $shortcodes, 'enqueue_assets' ) );
		add_filter( 'template_include', array( $pages, 'template_include' ) );
	}
}
