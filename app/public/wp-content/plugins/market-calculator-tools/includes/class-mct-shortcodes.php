<?php
/**
 * Shortcode and frontend assets.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MCT_Shortcodes {
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
	 * Register shortcodes.
	 */
	public function register(): void {
		add_shortcode( 'market_currency_converter', array( $this, 'render_currency_converter' ) );
	}

	/**
	 * Load converter assets only on pages that need them.
	 */
	public function enqueue_assets(): void {
		if ( ! $this->should_enqueue_assets() ) {
			return;
		}

		$css_path = MCT_PLUGIN_DIR . 'assets/css/currency-converter.css';
		$js_path  = MCT_PLUGIN_DIR . 'assets/js/currency-converter.js';

		wp_enqueue_style(
			'mct-currency-converter',
			MCT_PLUGIN_URL . 'assets/css/currency-converter.css',
			array(),
			file_exists( $css_path ) ? (string) filemtime( $css_path ) : MCT_VERSION
		);
		wp_enqueue_script(
			'mct-currency-converter',
			MCT_PLUGIN_URL . 'assets/js/currency-converter.js',
			array(),
			file_exists( $js_path ) ? (string) filemtime( $js_path ) : MCT_VERSION,
			true
		);
		wp_localize_script(
			'mct-currency-converter',
			'mctCurrencyConverter',
			array(
				'endpoint' => esc_url_raw( rest_url( 'market-calculator/v1/currency-rates' ) ),
				'defaults' => array(
					'amount' => 100,
					'from'   => 'USD',
					'to'     => 'CNY',
				),
				'strings'  => array(
					'loading'       => __( '正在加载汇率数据……', 'market-calculator-tools' ),
					'loadError'     => __( '测试汇率数据加载失败，请刷新页面后重试。', 'market-calculator-tools' ),
					'invalidAmount' => __( '请输入大于或等于 0 的有效金额。', 'market-calculator-tools' ),
					'resultLabel'   => __( '换算结果', 'market-calculator-tools' ),
					'rateLabel'     => __( '当前测试汇率', 'market-calculator-tools' ),
					'reverseLabel'  => __( '反向汇率', 'market-calculator-tools' ),
					'sourceLabel'   => __( '数据来源：', 'market-calculator-tools' ),
					'updatedLabel'  => __( '测试数据更新时间：', 'market-calculator-tools' ),
				),
			)
		);
	}

	/**
	 * Render the converter.
	 *
	 * @return string
	 */
	public function render_currency_converter(): string {
		$currencies = array_keys( $this->provider->get_rates() );
		$heading    = is_page( MCT_Pages::PAGE_SLUG ) ? 'h1' : 'h2';
		$source     = $this->provider->get_source_name();
		$updated_at = $this->provider->get_updated_at();

		ob_start();
		include MCT_PLUGIN_DIR . 'templates/currency-converter.php';

		return (string) ob_get_clean();
	}

	/**
	 * Check the current singular page for the tool slug or shortcode.
	 *
	 * @return bool
	 */
	private function should_enqueue_assets(): bool {
		if ( is_page( MCT_Pages::PAGE_SLUG ) ) {
			return true;
		}

		$post = get_queried_object();

		return $post instanceof WP_Post && has_shortcode( $post->post_content, 'market_currency_converter' );
	}
}
