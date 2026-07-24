<?php
/**
 * Plugin page lifecycle.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MCT_Pages {
	public const PAGE_SLUG = 'currency-converter';

	/**
	 * Create the converter page if its slug does not already exist.
	 */
	public static function activate(): void {
		$existing = get_page_by_path( self::PAGE_SLUG, OBJECT, 'page' );

		if ( $existing ) {
			return;
		}

		wp_insert_post(
			array(
				'post_title'   => __( '汇率换算器', 'market-calculator-tools' ),
				'post_name'    => self::PAGE_SLUG,
				'post_content' => '[market_currency_converter]',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			),
			true
		);
	}

	/**
	 * Use the plugin page wrapper for the generated tool page.
	 *
	 * @param string $template Selected template.
	 * @return string
	 */
	public function template_include( string $template ): string {
		if ( is_page( self::PAGE_SLUG ) ) {
			return MCT_PLUGIN_DIR . 'templates/currency-converter-page.php';
		}

		return $template;
	}
}
