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
//define('WP_CACHE', true); //Added by WP-Cache Manager
//define( 'WPCACHEHOME', '/var/www/html/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'papertask.cn');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'Liuyuhan2016!');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ')`Hr<NbXi]P0g4WY>UjZ)`D0X^<|N;;,<`ErzkcFRG_>03nR+t<@u(gtveD{={XN');
define('SECURE_AUTH_KEY',  '@jn]Jo)(RsvA=8s|9Pdhpju[dl91?9Z$A#u}mUJNGs8=Xzr``VhzZ9OOd;sxQt=r');
define('LOGGED_IN_KEY',    '>!BOT_pM|F@HWI0SL>T`>0/J]}%1S~Lb`d8wRQS~)=l+}h{`l01omP!9yraOdz>S');
define('NONCE_KEY',        'g($9vsKy=[{PJ7AET>cbacqFmv|qRGX|uEszmvqN9CP}`~[G>l:hKA<:1d8JG7E)');
define('AUTH_SALT',        'mty}}JeK+fOnjzwK-oZ*DygVsdOa?Vos8|eJn|Eu_aY67!w7awE+(/-NcM(RysHp');
define('SECURE_AUTH_SALT', '?6<pQpYFnAvhYWlrRvi2pWNt^9[uhjNVg$_VKHHkm(;c2kqR.I4EF:~6#evgf?Gt');
define('LOGGED_IN_SALT',   '^*o2[DU#}c8j$o,s!1P:lBn;#rePvsV#PE@KTU~|>es L]:DHwPsHLbYapviYqlP');
define('NONCE_SALT',       'LBv|V6`479G1R#oDKM2]0Csb?|y=8HUFMktOh{6z!os-_;t2ooY;0U-vDqzP{>tt');

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
define('WP_DEBUG', false);

define('DISABLE_WP_CRON', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
