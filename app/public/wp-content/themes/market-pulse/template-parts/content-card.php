<?php
/**
 * Reusable article card.
 *
 * @package Market_Pulse
 */

$categories = get_the_category();
?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card__image" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( '阅读：%s', 'market-pulse' ), get_the_title() ) ); ?>">
		<img src="<?php echo esc_url( market_pulse_get_card_image_url( get_the_ID() ) ); ?>" alt="<?php echo esc_attr( has_post_thumbnail() ? get_the_title() : __( '金融市场资讯占位图', 'market-pulse' ) ); ?>" loading="lazy" width="720" height="420">
	</a>
	<div class="post-card__body">
		<?php if ( $categories ) : ?>
			<a class="post-category" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a>
		<?php endif; ?>
		<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="post-card__excerpt"><?php echo esc_html( market_pulse_get_excerpt() ); ?></p>
		<div class="post-card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<span><?php echo esc_html( get_the_author() ); ?></span>
		</div>
		<a class="post-card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( '阅读全文', 'market-pulse' ); ?><span aria-hidden="true"> →</span></a>
	</div>
</article>

