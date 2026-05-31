<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'techblog' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '0101' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'D;r>6K)[b[;HwAiYIZ)gR^^W}:BH}BDQ80+`CciawZ].TY]b6gffLI6vbCa^kON2' );
define( 'SECURE_AUTH_KEY',  ',Y8i%ja0%jz$C]Tp ?Aw!LdHa.z=1}9We5!2u*ajxX5B`% R,re5F+o.pf[^t.4k' );
define( 'LOGGED_IN_KEY',    ']D/s/kn^;*5$hG!oU(yN:C9}yJ2efAO?6Zu43b.%X4keZAJ{a:m*}U^~p1^ROQw7' );
define( 'NONCE_KEY',        'lCa?lK/?qvV+X-21yiacf2S6]kDA^v^{53|S0]};^2s>N@8EWg2.|aOPk^]$Jw|j' );
define( 'AUTH_SALT',        's56.l)>@kTbKrWF9I}<+:q{>d6)RQvk_gA3^6pv.x%?H,gNR@n)0E m+24ax~F9X' );
define( 'SECURE_AUTH_SALT', '7q<uvMoF[P)YtJ|6]q&2PKr{)}k~_+yUwm<]8!b:M<0`^qK_<~eRh#%8Flg}r- M' );
define( 'LOGGED_IN_SALT',   'fW+&3_?&~C#3vhL0qL=}9:QV&tPeJRJ8Q7g~!|9/3G/v?~v(3z-8R5t~$`=`b}cN' );
define( 'NONCE_SALT',       'H~I4M7iVsixT_w`@JGV|w(.Qth=iKCbF0h9x7]_,ud-0Hy]*Lq@!nu?clb)-mDtY' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
