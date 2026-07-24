<?php
/**
 * Page template.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main page-template">
	<div class="container container--reading">
		<?php market_pulse_breadcrumbs(); ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'single-article' ); ?>>
				<header class="single-header"><h1><?php the_title(); ?></h1></header>
				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>

