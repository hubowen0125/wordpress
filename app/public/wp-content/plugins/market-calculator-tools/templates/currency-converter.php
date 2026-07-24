<?php
/**
 * Currency converter shortcode view.
 *
 * @var array<int,string> $currencies Currency codes.
 * @var string            $heading    Heading element.
 * @var string            $source     Provider source name.
 * @var string            $updated_at Provider data date.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="mct-converter" data-mct-currency-converter>
	<header class="mct-hero">
		<p class="mct-eyebrow"><?php esc_html_e( '测试版金融工具', 'market-calculator-tools' ); ?></p>
		<<?php echo esc_attr( $heading ); ?> class="mct-title"><?php esc_html_e( '汇率换算器', 'market-calculator-tools' ); ?></<?php echo esc_attr( $heading ); ?>>
		<p class="mct-intro"><?php esc_html_e( '输入金额并选择两种货币，即可计算对应的换算结果。当前页面处于开发测试阶段，使用固定测试汇率，不代表真实市场数据。', 'market-calculator-tools' ); ?></p>
	</header>

	<aside class="mct-test-notice" role="note">
		<strong><?php esc_html_e( '测试数据提示', 'market-calculator-tools' ); ?></strong>
		<span><?php esc_html_e( '当前使用测试汇率数据，不代表真实市场价格。', 'market-calculator-tools' ); ?></span>
	</aside>

	<section class="mct-calculator-card" aria-labelledby="mct-calculator-heading">
		<h2 id="mct-calculator-heading" class="mct-section-title"><?php esc_html_e( '开始换算', 'market-calculator-tools' ); ?></h2>
		<p class="mct-load-status" data-mct-status role="status" aria-live="polite"><?php esc_html_e( '正在加载汇率数据……', 'market-calculator-tools' ); ?></p>

		<form class="mct-form" data-mct-form novalidate>
			<div class="mct-field mct-field--amount">
				<label for="mct-amount"><?php esc_html_e( '换算金额', 'market-calculator-tools' ); ?></label>
				<input id="mct-amount" data-mct-amount type="number" min="0" step="any" inputmode="decimal" value="100" required>
				<p class="mct-field-error" data-mct-error aria-live="polite"></p>
			</div>

			<div class="mct-currency-fields">
				<div class="mct-field">
					<label for="mct-from"><?php esc_html_e( '原始货币', 'market-calculator-tools' ); ?></label>
					<select id="mct-from" data-mct-from>
						<?php foreach ( $currencies as $currency ) : ?>
							<option value="<?php echo esc_attr( $currency ); ?>" <?php selected( 'USD', $currency ); ?>><?php echo esc_html( $currency ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<button class="mct-swap-button" data-mct-swap type="button" aria-label="<?php esc_attr_e( '交换原始货币和目标货币', 'market-calculator-tools' ); ?>">
					<span aria-hidden="true">⇄</span>
					<span><?php esc_html_e( '交换币种', 'market-calculator-tools' ); ?></span>
				</button>

				<div class="mct-field">
					<label for="mct-to"><?php esc_html_e( '目标货币', 'market-calculator-tools' ); ?></label>
					<select id="mct-to" data-mct-to>
						<?php foreach ( $currencies as $currency ) : ?>
							<option value="<?php echo esc_attr( $currency ); ?>" <?php selected( 'CNY', $currency ); ?>><?php echo esc_html( $currency ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="mct-actions">
				<button class="mct-primary-button" data-mct-submit type="submit" disabled><?php esc_html_e( '开始换算', 'market-calculator-tools' ); ?></button>
				<button class="mct-secondary-button" data-mct-reset type="reset"><?php esc_html_e( '重置', 'market-calculator-tools' ); ?></button>
			</div>
		</form>

		<noscript>
			<p class="mct-noscript"><?php esc_html_e( '该工具需要启用 JavaScript 才能进行换算。', 'market-calculator-tools' ); ?></p>
		</noscript>

		<div class="mct-result" data-mct-result aria-live="polite" aria-atomic="true">
			<p class="mct-result-label"><?php esc_html_e( '换算结果', 'market-calculator-tools' ); ?></p>
			<p class="mct-result-value" data-mct-result-value>—</p>
			<div class="mct-rate-details">
				<p><span><?php esc_html_e( '当前测试汇率', 'market-calculator-tools' ); ?></span><strong data-mct-rate>—</strong></p>
				<p><span><?php esc_html_e( '反向汇率', 'market-calculator-tools' ); ?></span><strong data-mct-reverse>—</strong></p>
			</div>
			<div class="mct-source">
				<span data-mct-source><?php echo esc_html( sprintf( __( '数据来源：%s', 'market-calculator-tools' ), $source ) ); ?></span>
				<span data-mct-updated><?php echo esc_html( sprintf( __( '测试数据更新时间：%s', 'market-calculator-tools' ), $updated_at ) ); ?></span>
			</div>
		</div>
	</section>

	<div class="mct-information-grid">
		<section class="mct-info-card">
			<h2><?php esc_html_e( '使用方法', 'market-calculator-tools' ); ?></h2>
			<ol>
				<li><?php esc_html_e( '输入大于或等于 0 的金额。', 'market-calculator-tools' ); ?></li>
				<li><?php esc_html_e( '选择原始货币和目标货币。', 'market-calculator-tools' ); ?></li>
				<li><?php esc_html_e( '点击“开始换算”查看测试结果。', 'market-calculator-tools' ); ?></li>
			</ol>
		</section>

		<section class="mct-info-card">
			<h2><?php esc_html_e( '计算公式', 'market-calculator-tools' ); ?></h2>
			<p><code><?php esc_html_e( '目标汇率 = 目标货币相对 USD 汇率 ÷ 原始货币相对 USD 汇率', 'market-calculator-tools' ); ?></code></p>
			<p><code><?php esc_html_e( '换算结果 = 输入金额 × 目标汇率', 'market-calculator-tools' ); ?></code></p>
		</section>
	</div>

	<section class="mct-info-card mct-supported">
		<h2><?php esc_html_e( '支持的货币', 'market-calculator-tools' ); ?></h2>
		<ul>
			<?php foreach ( $currencies as $currency ) : ?>
				<li><?php echo esc_html( $currency ); ?></li>
			<?php endforeach; ?>
		</ul>
	</section>

	<section class="mct-info-card mct-faq">
		<h2><?php esc_html_e( '常见问题', 'market-calculator-tools' ); ?></h2>
		<details>
			<summary><?php esc_html_e( '这里显示的是实时汇率吗？', 'market-calculator-tools' ); ?></summary>
			<p><?php esc_html_e( '不是。当前版本仅使用固定测试汇率，用于验证页面和换算逻辑。', 'market-calculator-tools' ); ?></p>
		</details>
		<details>
			<summary><?php esc_html_e( '相同货币之间如何换算？', 'market-calculator-tools' ); ?></summary>
			<p><?php esc_html_e( '相同货币的换算汇率为 1，输入金额不会发生变化。', 'market-calculator-tools' ); ?></p>
		</details>
		<details>
			<summary><?php esc_html_e( '后续接入正式接口需要修改页面吗？', 'market-calculator-tools' ); ?></summary>
			<p><?php esc_html_e( '不需要。正式接口将通过新的数据提供器接入，页面、REST 返回结构和计算逻辑保持不变。', 'market-calculator-tools' ); ?></p>
		</details>
	</section>

	<aside class="mct-risk-notice" role="note">
		<h2><?php esc_html_e( '风险提示', 'market-calculator-tools' ); ?></h2>
		<p><?php esc_html_e( '本工具当前使用测试汇率，仅用于功能演示和开发测试，不可用于实际交易、结算、投资或财务决策。后续接入正式数据接口后，请仍以银行、支付机构或交易平台的实际报价为准。', 'market-calculator-tools' ); ?></p>
	</aside>
</article>
