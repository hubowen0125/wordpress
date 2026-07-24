<?php
/**
 * OKX public market data integration.
 *
 * @package Market_Pulse
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetches and normalizes OKX spot ticker and candle data.
 */
final class Market_Pulse_OKX_API {
	const REST_NAMESPACE     = 'market-pulse/v1';
	const REST_ROUTE         = '/crypto-market';
	const BASE_URL           = 'https://www.okx.com';
	const TICKER_CACHE_TTL    = 10;
	const CANDLES_CACHE_TTL   = 60;
	const LAST_GOOD_CACHE_TTL = HOUR_IN_SECONDS;

	/**
	 * Supported spot instruments. This is the single source of truth for OKX pairs.
	 *
	 * @return array
	 */
	public static function get_markets() {
		return array(
			'btc' => array(
				'name'    => 'Bitcoin',
				'symbol'  => 'BTC',
				'inst_id' => 'BTC-USDT',
			),
			'eth' => array(
				'name'    => 'Ethereum',
				'symbol'  => 'ETH',
				'inst_id' => 'ETH-USDT',
			),
		);
	}

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_route' ) );
	}

	/**
	 * Register the public, read-only REST endpoint.
	 */
	public static function register_route() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'handle_request' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'symbols' => array(
						'default'           => 'btc,eth',
						'sanitize_callback' => array( __CLASS__, 'sanitize_symbols' ),
						'validate_callback' => array( __CLASS__, 'validate_symbols' ),
					),
				),
			)
		);
	}

	/**
	 * Normalize a comma-separated symbol list.
	 *
	 * @param mixed $value Requested symbols.
	 * @return string
	 */
	public static function sanitize_symbols( $value ) {
		return strtolower( trim( (string) $value ) );
	}

	/**
	 * Only allow the two server-side aliases.
	 *
	 * @param mixed $value Requested symbols.
	 * @return bool
	 */
	public static function validate_symbols( $value ) {
		$symbols = array_filter( array_map( 'trim', explode( ',', strtolower( (string) $value ) ) ) );

		return ! empty( $symbols ) && empty( array_diff( $symbols, array_keys( self::get_markets() ) ) );
	}

	/**
	 * Serve normalized market data.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function handle_request( WP_REST_Request $request ) {
		// Preserve market decimal values without exposing binary float artifacts in JSON.
		ini_set( 'serialize_precision', '-1' );

		$query_keys = array_keys( $request->get_query_params() );
		if ( array_diff( $query_keys, array( 'symbols' ) ) ) {
			return new WP_Error(
				'market_pulse_invalid_parameter',
				__( '请求包含不支持的参数。', 'market-pulse' ),
				array( 'status' => 400 )
			);
		}

		$requested = array_values(
			array_unique(
				array_filter(
					array_map( 'trim', explode( ',', $request->get_param( 'symbols' ) ) )
				)
			)
		);
		$markets   = self::get_markets();
		$data      = array();
		$is_stale  = false;
		$updated   = 0;

		foreach ( $requested as $key ) {
			if ( ! isset( $markets[ $key ] ) ) {
				continue;
			}

			$result = self::get_market_data( $key, $markets[ $key ] );
			if ( is_wp_error( $result ) ) {
				continue;
			}

			$data[ $key ] = $result['data'];
			$is_stale     = $is_stale || $result['stale'];
			$updated      = max( $updated, $result['data']['timestamp'] );
		}

		if ( empty( $data ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'BTC 和 ETH 行情数据暂时无法加载，请稍后重试。', 'market-pulse' ),
					'data'    => array(),
				),
				503
			);
		}

		return new WP_REST_Response(
			array(
				'success'            => true,
				'source'             => 'OKX',
				'quote_currency'     => 'USDT',
				'updated_at'         => $updated,
				'updated_at_display' => self::format_timestamp( $updated ),
				'stale'              => $is_stale,
				'data'               => $data,
			),
			200
		);
	}

	/**
	 * Combine cached ticker and candles for one market.
	 *
	 * @param string $key    Market alias.
	 * @param array  $market Market configuration.
	 * @return array|WP_Error
	 */
	private static function get_market_data( $key, $market ) {
		$ticker = self::get_ticker( $key, $market['inst_id'] );
		if ( is_wp_error( $ticker ) ) {
			return $ticker;
		}

		$candles = self::get_candles( $key, $market['inst_id'] );
		$trend   = is_wp_error( $candles ) ? array() : $candles['data'];
		$stale   = $ticker['stale'] || ( ! is_wp_error( $candles ) && $candles['stale'] );
		$value   = $ticker['data'];
		$change  = round( $value['last'] - $value['open_24h'], 8 );
		$percent = $value['open_24h'] > 0 ? round( ( $change / $value['open_24h'] ) * 100, 8 ) : null;

		if ( $change > 0 ) {
			$status = 'up';
		} elseif ( $change < 0 ) {
			$status = 'down';
		} else {
			$status = 'flat';
		}

		return array(
			'stale' => $stale,
			'data'  => array(
				'name'               => $market['name'],
				'symbol'             => $market['symbol'],
				'inst_id'            => $market['inst_id'],
				'currency'           => 'USDT',
				'last'               => $value['last'],
				'open_24h'           => $value['open_24h'],
				'high_24h'           => $value['high_24h'],
				'low_24h'            => $value['low_24h'],
				'change'             => $change,
				'change_percent'     => null !== $percent && is_finite( $percent ) ? $percent : null,
				'change_status'      => $status,
				'bid'                => $value['bid'],
				'ask'                => $value['ask'],
				'volume_24h'         => $value['volume_24h'],
				'timestamp'          => $value['timestamp'],
				'updated_at_display' => self::format_timestamp( $value['timestamp'] ),
				'stale'              => $stale,
				'trend'              => $trend,
			),
		);
	}

	/**
	 * Get a ticker using a short cache and a one-hour last-good fallback.
	 *
	 * @param string $key     Market alias.
	 * @param string $inst_id OKX instrument ID.
	 * @return array|WP_Error
	 */
	private static function get_ticker( $key, $inst_id ) {
		$cache_key = 'market_pulse_okx_ticker_' . sanitize_key( str_replace( '-', '_', $inst_id ) );

		return self::get_cached_resource(
			$cache_key,
			self::TICKER_CACHE_TTL,
			function () use ( $key, $inst_id ) {
				$response = self::request(
					'/api/v5/market/ticker',
					array( 'instId' => $inst_id )
				);
				if ( is_wp_error( $response ) ) {
					return $response;
				}

				$row      = $response[0];
				$required = array( 'instId', 'last', 'open24h', 'high24h', 'low24h', 'vol24h', 'volCcy24h', 'bidPx', 'askPx', 'ts' );
				if ( array_diff( $required, array_keys( $row ) ) || $row['instId'] !== $inst_id ) {
					return new WP_Error( 'market_pulse_invalid_ticker', 'Invalid ticker data.' );
				}

				$numeric_fields = array( 'last', 'open24h', 'high24h', 'low24h', 'vol24h', 'volCcy24h', 'bidPx', 'askPx', 'ts' );
				foreach ( $numeric_fields as $field ) {
					if ( ! self::is_valid_number( $row[ $field ] ) ) {
						return new WP_Error( 'market_pulse_invalid_ticker_number', 'Invalid ticker number.' );
					}
				}

				$timestamp = (int) $row['ts'];
				if ( $timestamp <= 0 ) {
					return new WP_Error( 'market_pulse_invalid_timestamp', 'Invalid ticker timestamp.' );
				}

				return array(
					'last'          => (float) $row['last'],
					'open_24h'      => (float) $row['open24h'],
					'high_24h'      => (float) $row['high24h'],
					'low_24h'       => (float) $row['low24h'],
					'volume_24h'    => (float) $row['vol24h'],
					'volume_quote'  => (float) $row['volCcy24h'],
					'bid'           => (float) $row['bidPx'],
					'ask'           => (float) $row['askPx'],
					'timestamp'     => $timestamp,
					'market_alias'  => $key,
				);
			}
		);
	}

	/**
	 * Get 24 hourly candle closes.
	 *
	 * @param string $key     Market alias.
	 * @param string $inst_id OKX instrument ID.
	 * @return array|WP_Error
	 */
	private static function get_candles( $key, $inst_id ) {
		$cache_key = 'market_pulse_okx_candles_' . sanitize_key( str_replace( '-', '_', $inst_id ) ) . '_1h_24';

		return self::get_cached_resource(
			$cache_key,
			self::CANDLES_CACHE_TTL,
			function () use ( $key, $inst_id ) {
				$response = self::request(
					'/api/v5/market/candles',
					array(
						'instId' => $inst_id,
						'bar'    => '1H',
						'limit'  => 24,
					)
				);
				if ( is_wp_error( $response ) ) {
					return $response;
				}

				$trend = array();
				foreach ( $response as $candle ) {
					if ( ! is_array( $candle ) || count( $candle ) < 9 ) {
						continue;
					}
					if ( ! self::is_valid_number( $candle[0] ) || ! self::is_valid_number( $candle[4] ) ) {
						continue;
					}
					if ( ! in_array( (string) $candle[8], array( '0', '1' ), true ) ) {
						continue;
					}

					$timestamp = (int) $candle[0];
					if ( $timestamp <= 0 ) {
						continue;
					}

					$trend[] = array(
						'timestamp' => $timestamp,
						'close'     => (float) $candle[4],
						'confirmed' => '1' === (string) $candle[8],
					);
				}

				if ( empty( $trend ) ) {
					return new WP_Error( 'market_pulse_invalid_candles', 'Invalid candle data.' );
				}

				usort(
					$trend,
					function ( $a, $b ) {
						return $a['timestamp'] <=> $b['timestamp'];
					}
				);

				return array_slice( $trend, -24 );
			}
		);
	}

	/**
	 * Resolve a resource from fresh cache, upstream, or last-good cache.
	 *
	 * @param string   $cache_key Cache key.
	 * @param int      $ttl       Fresh cache lifetime.
	 * @param callable $loader    Remote loader.
	 * @return array|WP_Error
	 */
	private static function get_cached_resource( $cache_key, $ttl, $loader ) {
		$cached = get_transient( $cache_key );
		if ( false !== $cached && is_array( $cached ) ) {
			return array(
				'data'  => $cached,
				'stale' => false,
			);
		}

		$loaded = call_user_func( $loader );
		if ( ! is_wp_error( $loaded ) ) {
			set_transient( $cache_key, $loaded, $ttl );
			set_transient( $cache_key . '_last_good', $loaded, self::LAST_GOOD_CACHE_TTL );

			return array(
				'data'  => $loaded,
				'stale' => false,
			);
		}

		$last_good = get_transient( $cache_key . '_last_good' );
		if ( false !== $last_good && is_array( $last_good ) ) {
			return array(
				'data'  => $last_good,
				'stale' => true,
			);
		}

		return new WP_Error( 'market_pulse_market_unavailable', 'Market data unavailable.' );
	}

	/**
	 * Make and validate a public OKX request.
	 *
	 * @param string $path  Fixed OKX path.
	 * @param array  $query Fixed, server-generated query values.
	 * @return array|WP_Error
	 */
	private static function request( $path, $query ) {
		$url      = add_query_arg( $query, self::BASE_URL . $path );
		$response = wp_remote_get(
			$url,
			array(
				'timeout'     => 8,
				'redirection' => 2,
				'headers'     => array(
					'Accept'     => 'application/json',
					'User-Agent' => 'MarketPulseWordPress/1.0',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'market_pulse_remote_error', 'Remote request failed.' );
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'market_pulse_http_error', 'Unexpected remote status.' );
		}

		$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
			return new WP_Error( 'market_pulse_json_error', 'Invalid remote JSON.' );
		}

		if ( ! isset( $decoded['code'], $decoded['data'] ) || '0' !== (string) $decoded['code'] || ! is_array( $decoded['data'] ) || empty( $decoded['data'] ) || ! isset( $decoded['data'][0] ) ) {
			return new WP_Error( 'market_pulse_api_error', 'Invalid remote response.' );
		}

		return $decoded['data'];
	}

	/**
	 * Verify numeric strings without accepting INF or NAN.
	 *
	 * @param mixed $value Candidate value.
	 * @return bool
	 */
	private static function is_valid_number( $value ) {
		return ( is_string( $value ) || is_int( $value ) || is_float( $value ) )
			&& '' !== (string) $value
			&& is_numeric( $value )
			&& is_finite( (float) $value );
	}

	/**
	 * Format an OKX millisecond timestamp in the WordPress site timezone.
	 *
	 * @param int $timestamp_milliseconds Millisecond timestamp.
	 * @return string
	 */
	private static function format_timestamp( $timestamp_milliseconds ) {
		$timestamp_seconds = (int) floor( (int) $timestamp_milliseconds / 1000 );

		return wp_date( 'Y-m-d H:i:s', $timestamp_seconds, wp_timezone() );
	}
}

Market_Pulse_OKX_API::init();
