<?php
/**
 * Site header.
 *
 * @package Market_Pulse
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( '跳至内容', 'market-pulse' ); ?></a>
<header class="site-header">
	<div class="site-header__inner container">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-branding__mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-hidden="true">MP</a>
			<?php endif; ?>
			<div>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="site-description"><?php bloginfo( 'description' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
			<span class="screen-reader-text"><?php esc_html_e( '切换导航菜单', 'market-pulse' ); ?></span>
			<span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
		</button>
		<nav id="primary-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( '主导航', 'market-pulse' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'market_pulse_primary_menu_fallback',
				)
			);
			?>
		</nav>
		<button class="search-toggle" type="button" aria-expanded="false" aria-controls="header-search">
			<span aria-hidden="true">⌕</span>
			<span class="screen-reader-text"><?php esc_html_e( '打开搜索', 'market-pulse' ); ?></span>
		</button>
	</div>
	<div id="header-search" class="header-search container" hidden>
		<?php get_search_form(); ?>
	</div>
</header>

