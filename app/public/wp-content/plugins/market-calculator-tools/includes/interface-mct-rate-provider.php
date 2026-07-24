<?php
/**
 * Rate provider contract.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface MCT_Rate_Provider_Interface {
	/**
	 * Return rates relative to the USD base currency.
	 *
	 * @return array<string,float>
	 */
	public function get_rates(): array;

	/**
	 * Return the provider's data date.
	 *
	 * @return string
	 */
	public function get_updated_at(): string;

	/**
	 * Return a human-readable source name.
	 *
	 * @return string
	 */
	public function get_source_name(): string;

	/**
	 * Whether this provider contains test data.
	 *
	 * @return bool
	 */
	public function is_test_data(): bool;
}

