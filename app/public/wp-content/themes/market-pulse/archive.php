<?php
/**
 * Archive template.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main archive-page">
	<div class="container">
		<?php market_pulse_breadcrumbs(); ?>
		<header class="page-hero">
			<p class="eyebrow"><?php esc_html_e( '资讯归档', 'market-pulse' ); ?></p>
			<h1><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
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

