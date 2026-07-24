<?php
/**
 * Posts index.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main archive-page">
	<div class="container">
		<?php market_pulse_breadcrumbs(); ?>
		<header class="page-hero">
			<p class="eyebrow"><?php esc_html_e( 'Market Intelligence', 'market-pulse' ); ?></p>
			<h1><?php echo esc_html( single_post_title( '', false ) ? single_post_title( '', false ) : __( '资讯', 'market-pulse' ) ); ?></h1>
			<p><?php esc_html_e( '获取加密货币、贵金属与全球金融市场的最新动态。', 'market-pulse' ); ?></p>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content-card' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content-none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>

