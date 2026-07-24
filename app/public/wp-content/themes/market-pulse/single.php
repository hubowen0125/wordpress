<?php
/**
 * Single post template.
 *
 * @package Market_Pulse
 */

get_header();
?>
<main id="primary" class="site-main single-page">
	<div class="container">
		<?php market_pulse_breadcrumbs(); ?>
		<?php
		while ( have_posts() ) :
			the_post();
			$categories = get_the_category();
			?>
			<article <?php post_class( 'single-article' ); ?>>
				<header class="single-header">
					<?php if ( $categories ) : ?>
						<a class="post-category" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a>
					<?php endif; ?>
					<h1><?php the_title(); ?></h1>
					<div class="single-meta">
						<span><?php esc_html_e( '发布：', 'market-pulse' ); ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
						<span><?php esc_html_e( '更新：', 'market-pulse' ); ?><time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time></span>
						<span><?php esc_html_e( '作者：', 'market-pulse' ); ?><?php echo esc_html( get_the_author() ); ?></span>
					</div>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="single-featured-image"><?php the_post_thumbnail( 'full', array( 'alt' => get_the_title() ) ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="page-links" aria-label="' . esc_attr__( '文章分页', 'market-pulse' ) . '">' . esc_html__( '页面：', 'market-pulse' ),
							'after'  => '</nav>',
						)
					);
					?>
				</div>
				<?php if ( get_the_tags() ) : ?>
					<div class="post-tags"><span><?php esc_html_e( '标签：', 'market-pulse' ); ?></span><?php the_tags( '', '', '' ); ?></div>
				<?php endif; ?>
				<nav class="post-navigation-links" aria-label="<?php esc_attr_e( '相邻文章', 'market-pulse' ); ?>">
					<div><?php previous_post_link( '%link', '<span>' . esc_html__( '上一篇', 'market-pulse' ) . '</span>%title' ); ?></div>
					<div><?php next_post_link( '%link', '<span>' . esc_html__( '下一篇', 'market-pulse' ) . '</span>%title' ); ?></div>
				</nav>
			</article>

			<section class="related-posts" aria-labelledby="related-posts-title">
				<div class="section-heading">
					<h2 id="related-posts-title"><?php esc_html_e( '相关文章', 'market-pulse' ); ?></h2>
				</div>
				<div class="post-grid post-grid--related">
					<?php
					$related_args = array(
						'post_type'           => 'post',
						'post_status'         => 'publish',
						'posts_per_page'      => 3,
						'post__not_in'        => array( get_the_ID() ),
						'ignore_sticky_posts' => true,
					);
					if ( $categories ) {
						$related_args['category__in'] = wp_list_pluck( $categories, 'term_id' );
					}
					$related_posts = new WP_Query( $related_args );
					if ( $related_posts->have_posts() ) :
						while ( $related_posts->have_posts() ) :
							$related_posts->the_post();
							get_template_part( 'template-parts/content-card' );
						endwhile;
					else :
						get_template_part( 'template-parts/content-none' );
					endif;
					wp_reset_postdata();
					?>
				</div>
			</section>
			<div class="section-action"><a class="button button--secondary" href="<?php echo esc_url( market_pulse_get_posts_page_url() ); ?>"><?php esc_html_e( '返回资讯列表', 'market-pulse' ); ?></a></div>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>

