<?php
/**
 * Market Pulse theme functions.
 *
 * @package Market_Pulse
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/class-market-pulse-okx-api.php';

/**
 * Configure theme supports and menus.
 */
function market_pulse_setup() {
	load_theme_textdomain( 'market-pulse', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( '主导航', 'market-pulse' ),
			'footer'  => __( '页脚导航', 'market-pulse' ),
		)
	);

	add_image_size( 'market-pulse-card', 720, 420, true );
	add_image_size( 'market-pulse-related', 480, 280, true );
}
add_action( 'after_setup_theme', 'market_pulse_setup' );

/**
 * Return a file modification time or the theme version.
 *
 * @param string $relative_path Relative theme path.
 * @return string
 */
function market_pulse_asset_version( $relative_path ) {
	$file = get_template_directory() . $relative_path;

	return file_exists( $file ) ? (string) filemtime( $file ) : (string) wp_get_theme()->get( 'Version' );
}

/**
 * Enqueue local theme assets.
 */
function market_pulse_enqueue_assets() {
	wp_enqueue_style(
		'market-pulse-style',
		get_stylesheet_uri(),
		array(),
		market_pulse_asset_version( '/style.css' )
	);
	wp_enqueue_style(
		'market-pulse-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'market-pulse-style' ),
		market_pulse_asset_version( '/assets/css/main.css' )
	);
	wp_enqueue_script(
		'market-pulse-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		market_pulse_asset_version( '/assets/js/navigation.js' ),
		true
	);

	if ( is_front_page() ) {
		wp_enqueue_script(
			'market-pulse-widgets',
			get_template_directory_uri() . '/assets/js/market-widgets.js',
			array(),
			market_pulse_asset_version( '/assets/js/market-widgets.js' ),
			true
		);
		wp_localize_script(
			'market-pulse-widgets',
			'marketPulseMarkets',
			array(
				'symbols'       => market_pulse_get_market_symbols(),
				'loadingText'   => __( '行情数据加载中', 'market-pulse' ),
				'errorText'     => __( '行情数据暂时无法加载', 'market-pulse' ),
				'viewChartText' => __( '查看图表', 'market-pulse' ),
			)
		);

		wp_enqueue_script(
			'market-pulse-okx-market',
			get_template_directory_uri() . '/assets/js/okx-market-data.js',
			array(),
			market_pulse_asset_version( '/assets/js/okx-market-data.js' ),
			true
		);
		wp_localize_script(
			'market-pulse-okx-market',
			'marketPulseOkx',
			array(
				'restUrl'         => esc_url_raw( rest_url( 'market-pulse/v1/crypto-market' ) ),
				'webSocketUrl'    => 'wss://ws.okx.com:8443/ws/v5/public',
				'currency'        => 'USDT',
				'refreshInterval' => 30000,
				'requestTimeout'  => 10000,
				'loadingMessage'  => __( '行情数据加载中……', 'market-pulse' ),
				'errorMessage'    => __( '行情数据暂时无法加载', 'market-pulse' ),
				'staleMessage'    => __( '当前展示的是最近一次成功更新的数据', 'market-pulse' ),
				'trendError'      => __( '趋势数据暂时不可用', 'market-pulse' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'market_pulse_enqueue_assets' );

/**
 * Add defer to local theme scripts.
 *
 * @param string $tag    Script tag.
 * @param string $handle Script handle.
 * @return string
 */
function market_pulse_defer_scripts( $tag, $handle ) {
	if ( in_array( $handle, array( 'market-pulse-navigation', 'market-pulse-widgets', 'market-pulse-okx-market' ), true ) ) {
		return str_replace( ' src=', ' defer src=', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'market_pulse_defer_scripts', 10, 2 );

/**
 * Central market symbol configuration.
 *
 * @return array
 */
function market_pulse_get_market_symbols() {
	return array(
		'BTC' => array(
			'name'   => __( 'Bitcoin', 'market-pulse' ),
			'code'   => 'BTCUSD',
			'symbol' => 'BITSTAMP:BTCUSD',
		),
		'GOLD' => array(
			'name'   => __( 'Gold', 'market-pulse' ),
			'code'   => 'XAUUSD',
			'symbol' => 'OANDA:XAUUSD',
		),
		'ETH' => array(
			'name'   => __( 'Ethereum', 'market-pulse' ),
			'code'   => 'ETHUSD',
			'symbol' => 'BITSTAMP:ETHUSD',
		),
		'SILVER' => array(
			'name'   => __( 'Silver', 'market-pulse' ),
			'code'   => 'XAGUSD',
			'symbol' => 'OANDA:XAGUSD',
		),
	);
}

/**
 * Return a card image URL.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return string
 */
function market_pulse_get_card_image_url( $post_id, $size = 'market-pulse-card' ) {
	$image = get_the_post_thumbnail_url( $post_id, $size );

	return $image ? $image : get_template_directory_uri() . '/assets/images/placeholder.svg';
}

/**
 * Return a normalized excerpt.
 *
 * @param int $length Word count.
 * @return string
 */
function market_pulse_get_excerpt( $length = 24 ) {
	$text = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( strip_shortcodes( get_the_content() ) );

	return wp_trim_words( $text, $length, '…' );
}

/**
 * Return the posts page URL with a safe fallback.
 *
 * @return string
 */
function market_pulse_get_posts_page_url() {
	$page_id = (int) get_option( 'page_for_posts' );

	return $page_id ? get_permalink( $page_id ) : home_url( '/news/' );
}

/**
 * Render a compact breadcrumb trail.
 */
function market_pulse_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( '面包屑', 'market-pulse' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '首页', 'market-pulse' ) . '</a>';
	echo '<span aria-hidden="true">/</span>';

	if ( is_home() ) {
		echo '<span aria-current="page">' . esc_html__( '资讯', 'market-pulse' ) . '</span>';
	} elseif ( is_category() ) {
		echo '<a href="' . esc_url( market_pulse_get_posts_page_url() ) . '">' . esc_html__( '资讯', 'market-pulse' ) . '</a>';
		echo '<span aria-hidden="true">/</span><span aria-current="page">' . esc_html( single_cat_title( '', false ) ) . '</span>';
	} elseif ( is_single() ) {
		echo '<a href="' . esc_url( market_pulse_get_posts_page_url() ) . '">' . esc_html__( '资讯', 'market-pulse' ) . '</a>';
		$categories = get_the_category();
		if ( $categories ) {
			echo '<span aria-hidden="true">/</span><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
		}
		echo '<span aria-hidden="true">/</span><span aria-current="page">' . esc_html( wp_trim_words( get_the_title(), 8 ) ) . '</span>';
	} elseif ( is_search() ) {
		echo '<span aria-current="page">' . esc_html__( '搜索结果', 'market-pulse' ) . '</span>';
	} elseif ( is_404() ) {
		echo '<span aria-current="page">404</span>';
	} else {
		echo '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
	}

	echo '</nav>';
}

/**
 * Fallback navigation when no menu is assigned.
 */
function market_pulse_primary_menu_fallback() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '首页', 'market-pulse' ) . '</a></li>';
	echo '<li><a href="' . esc_url( market_pulse_get_posts_page_url() ) . '">' . esc_html__( '资讯', 'market-pulse' ) . '</a></li>';
	wp_list_categories(
		array(
			'title_li' => '',
			'number'   => 5,
		)
	);
	echo '</ul>';
}
