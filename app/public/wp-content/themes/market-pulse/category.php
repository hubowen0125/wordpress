<?php
/**
 * Category archive template.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main archive-page">
	<div class="container">
		<?php market_pulse_breadcrumbs(); ?>
		<header class="page-hero">
			<p class="eyebrow"><?php esc_html_e( '分类资讯', 'market-pulse' ); ?></p>
			<h1><?php single_cat_title(); ?></h1>
			<?php if ( category_description() ) : ?>
				<div class="archive-description"><?php echo wp_kses_post( category_description() ); ?></div>
			<?php endif; ?>
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

