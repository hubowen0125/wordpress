<?php
/**
 * Front page template.
 *
 * @package Market_Pulse
 */

get_header();
$symbols     = market_pulse_get_market_symbols();
$chart_order = array( 'BTC', 'ETH', 'GOLD', 'SILVER' );
?>
<main id="primary" class="site-main front-page">
	<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) . ' — ' . __( '市场行情与最新资讯', 'market-pulse' ) ); ?></h1>
	<div class="container">
		<section class="market-section ticker-section" aria-label="<?php esc_attr_e( '实时行情滚动条', 'market-pulse' ); ?>">
			<div id="market-ticker-tape" class="tradingview-frame ticker-frame">
				<p class="widget-status"><?php esc_html_e( '行情数据加载中', 'market-pulse' ); ?></p>
			</div>
		</section>

		<section class="market-section" aria-labelledby="market-cards-title">
			<div class="section-heading">
				<div>
					<p class="eyebrow"><?php esc_html_e( '实时市场', 'market-pulse' ); ?></p>
					<h2 id="market-cards-title"><?php esc_html_e( '核心资产行情', 'market-pulse' ); ?></h2>
				</div>
				<p><?php esc_html_e( '加密资产数据由 OKX 提供；贵金属数据由 TradingView 提供', 'market-pulse' ); ?></p>
			</div>
			<div class="market-card-grid">
				<?php foreach ( $symbols as $key => $market ) : ?>
					<?php
					get_template_part(
						'template-parts/market-price-card',
						null,
						array(
							'key'    => $key,
							'market' => $market,
						)
					);
					?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="market-section chart-section" aria-labelledby="advanced-chart-title">
			<div class="section-heading chart-heading">
				<div>
					<p class="eyebrow"><?php esc_html_e( '专业图表', 'market-pulse' ); ?></p>
					<h2 id="advanced-chart-title"><?php esc_html_e( '市场走势', 'market-pulse' ); ?></h2>
				</div>
				<div class="chart-symbols" role="group" aria-label="<?php esc_attr_e( '切换图表品种', 'market-pulse' ); ?>">
					<?php foreach ( $chart_order as $key ) : ?>
						<button type="button" class="chart-symbol-button<?php echo 'BTC' === $key ? ' is-active' : ''; ?>" data-market-symbol="<?php echo esc_attr( $key ); ?>" aria-pressed="<?php echo 'BTC' === $key ? 'true' : 'false'; ?>">
							<?php echo esc_html( $key ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="advanced-chart-shell">
				<div id="market-advanced-chart" class="advanced-chart">
					<p class="widget-status"><?php esc_html_e( '行情数据加载中', 'market-pulse' ); ?></p>
				</div>
			</div>
		</section>

		<section class="latest-news" aria-labelledby="latest-news-title">
			<div class="section-heading">
				<div>
					<p class="eyebrow"><?php esc_html_e( '市场脉搏', 'market-pulse' ); ?></p>
					<h2 id="latest-news-title"><?php esc_html_e( '最新资讯', 'market-pulse' ); ?></h2>
				</div>
			</div>
			<div class="post-grid">
				<?php
				$latest_posts = new WP_Query(
					array(
						'post_type'           => 'post',
						'post_status'         => 'publish',
						'posts_per_page'      => 6,
						'ignore_sticky_posts' => true,
					)
				);
				if ( $latest_posts->have_posts() ) :
					while ( $latest_posts->have_posts() ) :
						$latest_posts->the_post();
						get_template_part( 'template-parts/content-card' );
					endwhile;
				else :
					get_template_part( 'template-parts/content-none' );
				endif;
				wp_reset_postdata();
				?>
			</div>
			<div class="section-action">
				<a class="button" href="<?php echo esc_url( market_pulse_get_posts_page_url() ); ?>"><?php esc_html_e( '查看更多资讯', 'market-pulse' ); ?></a>
			</div>
		</section>
	</div>
</main>
<?php get_footer(); ?>
