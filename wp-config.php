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
define( 'DB_NAME', 'devmesreflexes' );

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
define( 'AUTH_KEY',          'gReuK>BG8V~zwp|s~wI/ODNPyn6L%}5Nr/hTppCwOR!:>5d?-T3-=<EbE9.XoR}6' );
define( 'SECURE_AUTH_KEY',   'P$t6vp25c4XGt-^H/fyHYrB/!5c%VkTYc5+`rpbqI*v VXX:YbyJz<^I;6lk^9K>' );
define( 'LOGGED_IN_KEY',     ']B|i@=4:nq4@._S<mCkj0~]_>pLJoSD$2jB`kXF0pN6yaM<OQgCnU0V2Xbl.. >N' );
define( 'NONCE_KEY',         'iXs-$U]cEA?0:SQ`;/c3f!oDBRV-~7wU*_n=m:3A..c8GXu4#42wtLoMly.hrHbX' );
define( 'AUTH_SALT',         'MUV%|k^X}}{4`NAg0.yKwvXM?X]2r*2Wknw:>2 ,(-;$O03!2Yv0a fraRCh6cj-' );
define( 'SECURE_AUTH_SALT',  'TNCR60y.~*xs0l,#lR]?EmyXLQo),HdM4OOjuNnqTxx%8Tl$:U8X,49}lt:_:c 1' );
define( 'LOGGED_IN_SALT',    'rXBOq~r5qAO<#,uUw%$gLk5LF+H0O&vS9V kRCEHPLyS7fiAt1]=w@42Y;9,0+AR' );
define( 'NONCE_SALT',        '=>6+lK#!gRo9A>LbCkVDpg[54G]>?eSZ$v4|6!^Tkt|~[lEe,/4S!eRHN|d^;S9*' );
define( 'WP_CACHE_KEY_SALT', '9Q<EB-1E%+olT^ToG(wFRV=g&!nT:G7}3x5HQV}Po/+%nK=+z&2KwR-5c{y!6QWc' );


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
	define( 'WP_DEBUG', true );
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('WP_MEMORY_LIMIT', '1024');
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
