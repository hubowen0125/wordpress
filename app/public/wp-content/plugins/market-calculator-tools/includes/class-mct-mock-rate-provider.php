<?php
/**
 * Fixed test rate provider.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MCT_Mock_Rate_Provider implements MCT_Rate_Provider_Interface {
	/**
	 * Central test data date.
	 */
	private const UPDATED_AT = '2026-07-23';

	/**
	 * Return test rates relative to USD.
	 *
	 * @return array<string,float>
	 */
	public function get_rates(): array {
		return array(
			'USD' => 1.000000,
			'EUR' => 0.920000,
			'GBP' => 0.780000,
			'JPY' => 157.000000,
			'CNY' => 7.200000,
			'HKD' => 7.800000,
			'SGD' => 1.350000,
			'AUD' => 1.500000,
			'CAD' => 1.370000,
			'CHF' => 0.890000,
		);
	}

	/**
	 * Return the fixed test data date.
	 *
	 * @return string
	 */
	public function get_updated_at(): string {
		return self::UPDATED_AT;
	}

	/**
	 * Return the test source name.
	 *
	 * @return string
	 */
	public function get_source_name(): string {
		return __( '测试数据', 'market-calculator-tools' );
	}

	/**
	 * Mark this provider as test data.
	 *
	 * @return bool
	 */
	public function is_test_data(): bool {
		return true;
	}
}

