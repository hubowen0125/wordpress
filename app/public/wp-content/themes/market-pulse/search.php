<?php
/**
 * Search results.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main archive-page">
	<div class="container">
		<?php market_pulse_breadcrumbs(); ?>
		<header class="page-hero">
			<p class="eyebrow"><?php esc_html_e( '站内搜索', 'market-pulse' ); ?></p>
			<h1><?php echo esc_html( sprintf( __( '“%s”的搜索结果', 'market-pulse' ), get_search_query() ) ); ?></h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content-card' ); endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content-none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>

