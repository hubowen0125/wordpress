<?php
/**
 * Currency rates REST endpoint.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MCT_REST_API {
	/**
	 * Active rate provider.
	 *
	 * @var MCT_Rate_Provider_Interface
	 */
	private MCT_Rate_Provider_Interface $provider;

	/**
	 * Constructor.
	 *
	 * @param MCT_Rate_Provider_Interface $provider Rate provider.
	 */
	public function __construct( MCT_Rate_Provider_Interface $provider ) {
		$this->provider = $provider;
	}

	/**
	 * Register public read-only routes.
	 */
	public function register_routes(): void {
		register_rest_route(
			'market-calculator/v1',
			'/currency-rates',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_currency_rates' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Return whitelisted rate data from the provider.
	 *
	 * @return WP_REST_Response
	 */
	public function get_currency_rates(): WP_REST_Response {
		$allowed = array( 'USD', 'EUR', 'GBP', 'JPY', 'CNY', 'HKD', 'SGD', 'AUD', 'CAD', 'CHF' );
		$rates   = array_intersect_key( $this->provider->get_rates(), array_flip( $allowed ) );

		// Keep decimal JSON values concise on hosts that use legacy float precision.
		if ( function_exists( 'ini_set' ) ) {
			ini_set( 'serialize_precision', '-1' );
		}

		$rates   = array_map(
			static function ( $rate ): float {
				return round( (float) $rate, 6 );
			},
			$rates
		);

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => array(
					'base'  => 'USD',
					'rates' => $rates,
				),
				'source'       => $this->provider->get_source_name(),
				'updated_at'   => $this->provider->get_updated_at(),
				'is_test_data' => $this->provider->is_test_data(),
			),
			200
		);
	}
}
