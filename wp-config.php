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
define( 'DB_NAME', 'daycare_welfare' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '<~|)}vaix!hjbFvI-3/}-rZ;d6*zo*N-,-dJ!w.A(RWT2OViZ`0gzSs^kS_b!GOj' );
define( 'SECURE_AUTH_KEY',  'etc8bBE5F2f$=73j=tcz(S}>Q@Btp7Q]b;({mhjEV;a<;RH3Tn3]{+r3o;ouwVfW' );
define( 'LOGGED_IN_KEY',    'SXi!f~Q_m(=NO~=<hDqelAv.%~yB,s-yj}(HzTAy;vLNR0lcoF>/GzS(FTB9)#$P' );
define( 'NONCE_KEY',        ',j-aevelv|JUxux[F==L?l9~{5$nydHP)V9;/I?8_kz@oarqcY3.3}&(l%f}hGWF' );
define( 'AUTH_SALT',        '$|SSJu9:m=9TG`9JBzY.q; oCKw%A]BPQd1Hz`.:2%mQd<W@Oqf/`JvL *TuA_fU' );
define( 'SECURE_AUTH_SALT', '5wY=fwz8^yJsC2$K9:`x2,g_oNKI<1.>aXrZ`()vb4j bcHc2^+;m=_Gb-KP^aEZ' );
define( 'LOGGED_IN_SALT',   'c}`$U8MkVkvBHuOY<Vg)HkSKMoV=M)@6IH)~b6v;oX~K);N8fAP -&H{?.kQKwAP' );
define( 'NONCE_SALT',       '*HUi~i8PnG-(0Mzza/I e$|N7mht#>v-jSA;HqnSa7^4~S?z8,c1Z~HgQ5&tdtjl' );

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
