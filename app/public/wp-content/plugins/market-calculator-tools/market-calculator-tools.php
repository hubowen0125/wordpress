<?php
/**
 * Plugin Name: Market Calculator Tools
 * Description: 提供测试版汇率换算器，并预留可替换的汇率数据提供器架构。
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: Market Pulse
 * Text Domain: market-calculator-tools
 *
 * @package Market_Calculator_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MCT_VERSION', '1.0.0' );
define( 'MCT_PLUGIN_FILE', __FILE__ );
define( 'MCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MCT_PLUGIN_DIR . 'includes/interface-mct-rate-provider.php';
require_once MCT_PLUGIN_DIR . 'includes/class-mct-mock-rate-provider.php';
require_once MCT_PLUGIN_DIR . 'includes/class-mct-pages.php';
require_once MCT_PLUGIN_DIR . 'includes/class-mct-rest-api.php';
require_once MCT_PLUGIN_DIR . 'includes/class-mct-shortcodes.php';
require_once MCT_PLUGIN_DIR . 'includes/class-mct-plugin.php';

register_activation_hook( __FILE__, array( 'MCT_Pages', 'activate' ) );
add_action( 'plugins_loaded', array( MCT_Plugin::instance(), 'init' ) );
