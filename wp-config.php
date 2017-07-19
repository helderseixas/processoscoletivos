<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'PucMG!2016');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('FS_METHOD', 'direct');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '8Vs2kar}C?(k?*ur2_1qj+:nfu#nubH:_oVIoQuZ+-JWQec&yQUy[3n,z~@X-2Ep');
define('SECURE_AUTH_KEY',  '@1aU!]Goijpej6u-L^0p& 53YM+A3o6q?RM/`JPGHWZB% cs-aLbvKL1#|6Ik5uV');
define('LOGGED_IN_KEY',    'TR){|(Aa{0Zip|Cl6(.l/;c=;<UZ8^+Yzh_.189llI16P++vO6%Os%+ kBh)F}Z@');
define('NONCE_KEY',        '@Y:*b9V8HPk(h[e2-DD]>J{X>&`{9699!Z,gak8chJ>@~+y6,3W-P2R9Z*8DxaMu');
define('AUTH_SALT',        '9g#-!*@`]1LpFr#lo0#5S668-|F]+gV:fKSB.OXhJ4eEQ 6 {r]%W6f}|3wY0cyw');
define('SECURE_AUTH_SALT', '2n!TbAhMtl#vC5.+<-8kZOABL</Bg#4V.uLpMA0+-_7!AkBsO%|w0VJhcr:,n6|e');
define('LOGGED_IN_SALT',   'i@=,,4P$Q(tr~|SNU,i|2<tv8SRp;jM+Yp:RvgO7#~En%||;RNTm~P#s-%mg@q-!');
define('NONCE_SALT',       'fX)|TsG(.CRytC6>mW<?q@P,<hA~?GHBE=Jdo:KC#@zDhU+5*db{d57SgDhmHt3V');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', true );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
