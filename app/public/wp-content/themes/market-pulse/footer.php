<?php
/**
 * Site footer.
 *
 * @package Market_Pulse
 */
?>
<footer class="site-footer">
	<div class="container site-footer__grid">
		<div>
			<p class="site-footer__title"><?php bloginfo( 'name' ); ?></p>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( '追踪全球加密货币、贵金属与金融市场动态。', 'market-pulse' ) ); ?></p>
		</div>
		<nav aria-label="<?php esc_attr_e( '页脚导航', 'market-pulse' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>
	</div>
	<div class="container risk-notice">
		<?php esc_html_e( '本站行情及资讯仅供参考，不构成任何投资建议。加密货币及贵金属市场存在较高风险，请根据自身情况谨慎决策。', 'market-pulse' ); ?>
	</div>
	<div class="container site-footer__bottom">
		<span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
		<?php the_privacy_policy_link(); ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

