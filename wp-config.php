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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'M[BO(tKxo;w|Gy]4{,sj#2IWQ=r;fEx6tE(R!eedJgUWzD.nyZU_/OD$SR9Oe_Ti' );
define( 'SECURE_AUTH_KEY',  '$bjL8?xR1Htnz.$!B.u4~v5t<dL<nK?Rl>94e[v,iUWXvC1%p ZHN?7f2[;u!.2Y' );
define( 'LOGGED_IN_KEY',    '~$]6S?21(qI^ DvHhM`,JCMX7~X,_ :y =7_F[MMT=_YpmNSa~pJ^PpdEV&1jhx@' );
define( 'NONCE_KEY',        ' $s4*{,s>KfJ8%i-DX%nP^.yL~j!gXT5!+1^`!eJL}H<q=E?;HZlKmjkt^vO1vHx' );
define( 'AUTH_SALT',        '_Q3F)*j3n&^EW/nd~!7MncT0Sib}^*.*.^,YHd-1bA|zW=/QSWeAtE`lwj!E{uNe' );
define( 'SECURE_AUTH_SALT', '&zr6D=?d_bl%>,(GVxFDFrFws=J1u_Z:YC~@,l7-}A_x:PNi a=st(!QAjq~x!sJ' );
define( 'LOGGED_IN_SALT',   '&{OU|Qai[28Za3%Ew,{e&4r^as3?Jm?6l903N~xCCR32d|;qgM Fx,9=NQoVPRzR' );
define( 'NONCE_SALT',       '0 JTeK&2p.q:, N,e+pT Y&kXrqz4c=SW?hQQDz3 =u)W1R!6d(s]]Cj?$a@.:^`' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
