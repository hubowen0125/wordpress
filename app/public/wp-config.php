<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '501/-bdc#udfE<?n_vkPp6akeR#0 _kxG#--?v0!g~g4LWi!sYP:WV3wc(yV<zQ:' );
define( 'SECURE_AUTH_KEY',   '6!h`XMNg9u{/-MV|gIcn?4rS`IJOwI4^w(QxUu5-1SWOX&-6tGptJVEv@UFWKg1u' );
define( 'LOGGED_IN_KEY',     'TmSNc!:VRD5_ga@6Z=>=jp>n@cKEK f?-%WPB~$7ssk-q+A%rbBUSP>K1T`B9.i7' );
define( 'NONCE_KEY',         'mABmy,^UY&Gp!mB]0pQICN<S(TJRXR6oG^m)N.8b!%LT-{E_I=5w6>L`Qs!.Vco8' );
define( 'AUTH_SALT',         ')T7}taeUa@D{Sfono8~VJN/tCW3_LB2&PMc1uNQkP LP+W5$5KkiU ((9;S>GHS.' );
define( 'SECURE_AUTH_SALT',  ':+r8z3g`XiB2An)LT&*Ql_/Z?06y]P;3LHg(,) C1`QdY4Dg,7x~QwPtQKJfz^=Q' );
define( 'LOGGED_IN_SALT',    'j@EC+rj8#y3@<+k7P)%f<<?uSIURcJbww>6l93QsBj! T%:=0tf5CnjSk(nG4tuf' );
define( 'NONCE_SALT',        'nX(5(zkik~?U$UahALFIXeX^ BStUv][fMR1oiIAJF`5ak|qFbiqAp|^8(bN@$I$' );
define( 'WP_CACHE_KEY_SALT', 'Gi{Gm7WaISU:QB?4yaZ52:/Qs6p%LL3u~!NeesM@X1a06!`}:uk$N+ZAptYN/1vD' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
