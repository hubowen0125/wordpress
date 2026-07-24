<?php
/**
 * Not found template.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main error-page">
	<div class="container">
		<?php market_pulse_breadcrumbs(); ?>
		<section class="error-card">
			<p class="error-code">404</p>
			<h1><?php esc_html_e( '页面未找到', 'market-pulse' ); ?></h1>
			<p><?php esc_html_e( '您访问的页面可能已移动或不存在。可以搜索站内资讯，或返回首页。', 'market-pulse' ); ?></p>
			<?php get_search_form(); ?>
			<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '返回首页', 'market-pulse' ); ?></a>
		</section>
	</div>
</main>
<?php get_footer(); ?>

