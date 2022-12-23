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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** // mysql://b0805b8308d061:2ff13d56 @ us-cdbr-east-06.cleardb.net /heroku_502bd52cca87322?reconnect=true
/** The name of the database for WordPress */
define( 'DB_NAME', 'heroku_502bd52cca87322' );
/** Database username */
define( 'DB_USER', 'b0805b8308d061' );
/** Database password */
define( 'DB_PASSWORD', '8a7ff03d2f5491e' );
/** Database hostname */
define( 'DB_HOST', 'us-cdbr-east-06.cleardb.net' );
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
define( 'AUTH_KEY',         'X nuIn:c<w2O}hti7EFkvxw![)]a;5KK<%,XPuwYCRHb<  dmbhB2e{4SqU7T-l=' );
define( 'SECURE_AUTH_KEY',  'uc[!g=q^g~?|J+1rG#X(ku~MQ_lV,i]Xo.^v-=xlFiB<MzQyrsk?[=70pX@=mLSt' );
define( 'LOGGED_IN_KEY',    'x.^gQX%^YOAxi^LUR:4$u)(`i`pWaivNxCBzKEgH$!_k4fQ<-^RE+3OVF*+?Jv-7' );
define( 'NONCE_KEY',        '>RhY* Wvv!]_p0W01cl{i3*6*nbojjHHmE !$/o<:SHBk%7C+;~k?G:%}h]:Y tM' );
define( 'AUTH_SALT',        '+;O:3R>(n`!ht+`~nF_xjXkV5e9%^B.fW+yF05uA~R_zOQ8Q{|Ouy1b4CL,T@/vv' );
define( 'SECURE_AUTH_SALT', 'tE7#Moy`<WufMH|IRlGl;2ja,^lhZf~]!PO$C39?^~8>]DW_d:Tm:[(S ezFonGG' );
define( 'LOGGED_IN_SALT',   'v27wtpF!?*O/AusUAPF2@7&WdsUqY^WhiDLpVV?-i:[:G%wY_DNJ]N{/csYD3<4U' );
define( 'NONCE_SALT',       ',QpI$>w-&Bm7iL:=f;[B.(Tb_F>B|AY&Rx!rdq2#)S/u}t@0TE3(E^^qsNmneu.u' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/ *
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

// see also https://wordpress.org/support/article/administration-over-ssl/#using-a-reverse-proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
	$_SERVER['HTTPS'] = 'on';
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
/**
 * Disable pingback.ping xmlrpc method to prevent WordPress from participating in DDoS attacks
 * More info at: https://docs.bitnami.com/general/apps/wordpress/troubleshooting/xmlrpc-and-pingback/
 */
if ( !defined( 'WP_CLI' ) ) {
	// remove x-pingback HTTP header
	add_filter("wp_headers", function($headers) {
		unset($headers["X-Pingback"]);
		return $headers;
	});
	// disable pingbacks
	add_filter( "xmlrpc_methods", function( $methods ) {
		unset( $methods["pingback.ping"] );
		return $methods;
	});
}