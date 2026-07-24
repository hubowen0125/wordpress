<?php
/**
 * Fallback template.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main archive-page">
	<div class="container">
		<?php market_pulse_breadcrumbs(); ?>
		<header class="page-hero"><h1><?php esc_html_e( '最新内容', 'market-pulse' ); ?></h1></header>
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

