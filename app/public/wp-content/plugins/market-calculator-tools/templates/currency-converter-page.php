<?php
/**
 * Full page wrapper for the generated converter page.
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main mct-page-main">
	<div class="mct-page-shell">
		<?php
		if ( function_exists( 'market_pulse_breadcrumbs' ) ) {
			market_pulse_breadcrumbs();
		} else {
			?>
			<nav class="mct-breadcrumbs" aria-label="<?php esc_attr_e( '面包屑', 'market-calculator-tools' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '首页', 'market-calculator-tools' ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php esc_html_e( '汇率换算器', 'market-calculator-tools' ); ?></span>
			</nav>
			<?php
		}

		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</div>
</main>
<?php
get_footer();

