<?php
/**
 * Market price card.
 *
 * @package Market_Pulse
 */

$key    = isset( $args['key'] ) ? $args['key'] : '';
$market = isset( $args['market'] ) ? $args['market'] : array();
if ( ! $key || ! $market ) {
	return;
}
$is_okx = in_array( strtolower( $key ), array_keys( Market_Pulse_OKX_API::get_markets() ), true );
?>
<article class="market-card<?php echo $is_okx ? ' market-card--okx' : ''; ?>" id="market-card-<?php echo esc_attr( strtolower( $key ) ); ?>"<?php echo $is_okx ? ' data-okx-market="' . esc_attr( strtolower( $key ) ) . '"' : ''; ?>>
	<header class="market-card__header">
		<div>
			<p class="market-card__name"><?php echo esc_html( $market['name'] ); ?></p>
			<p class="market-card__code"><?php echo esc_html( $is_okx ? $key . '/USDT' : $market['code'] ); ?></p>
		</div>
		<?php if ( $is_okx ) : ?>
			<span class="market-card__live market-card__live--stream" data-okx-live-state="connecting">
				<span class="market-card__live-dot" aria-hidden="true"></span>
				<span data-okx-live-label><?php esc_html_e( '连接中', 'market-pulse' ); ?></span>
			</span>
		<?php else : ?>
			<span class="market-card__live"><?php esc_html_e( '实时', 'market-pulse' ); ?></span>
		<?php endif; ?>
	</header>
	<?php if ( $is_okx ) : ?>
		<div class="okx-market" aria-live="polite">
			<p class="okx-market__status" data-okx-status><?php esc_html_e( '行情数据加载中……', 'market-pulse' ); ?></p>
			<noscript><p class="okx-market__noscript"><?php esc_html_e( '行情数据暂时无法加载，请启用 JavaScript。', 'market-pulse' ); ?></p></noscript>
			<div class="okx-market__content" data-okx-content hidden>
				<div class="okx-market__price-row">
					<p class="okx-market__price" data-okx-price>—</p>
					<div class="okx-market__change" data-okx-change>
						<span data-okx-change-amount>—</span>
						<span data-okx-change-percent>—</span>
					</div>
				</div>
				<div class="okx-market__trend" data-okx-trend></div>
				<dl class="okx-market__stats">
					<div><dt><?php esc_html_e( '24H 高', 'market-pulse' ); ?></dt><dd data-okx-high>—</dd></div>
					<div><dt><?php esc_html_e( '24H 低', 'market-pulse' ); ?></dt><dd data-okx-low>—</dd></div>
					<div><dt><?php esc_html_e( '24H 量', 'market-pulse' ); ?></dt><dd data-okx-volume>—</dd></div>
				</dl>
				<p class="okx-market__stale" data-okx-stale hidden></p>
			</div>
		</div>
	<?php else : ?>
		<div id="market-widget-<?php echo esc_attr( strtolower( $key ) ); ?>" class="tradingview-frame mini-chart" data-market-card="<?php echo esc_attr( $key ); ?>">
			<p class="widget-status"><?php esc_html_e( '行情数据加载中', 'market-pulse' ); ?></p>
		</div>
	<?php endif; ?>
	<footer class="market-card__footer">
		<?php if ( $is_okx ) : ?>
			<span><span><?php esc_html_e( '数据来源：OKX', 'market-pulse' ); ?></span> · <span data-okx-updated><?php esc_html_e( '更新时间：—', 'market-pulse' ); ?></span></span>
		<?php else : ?>
			<span><?php esc_html_e( '价格、涨跌与更新时间由 TradingView 显示', 'market-pulse' ); ?></span>
		<?php endif; ?>
		<button type="button" class="text-button market-card-link" data-chart-target="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( '查看图表', 'market-pulse' ); ?></button>
	</footer>
</article>
