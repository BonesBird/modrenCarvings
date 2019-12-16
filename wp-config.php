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
define( 'DB_NAME', 'mysite' );

/** MySQL database username */
define( 'DB_USER', 'ronaldgaskin' );

/** MySQL database password */
define( 'DB_PASSWORD', 'ronaldgaskin123' );

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
define( 'AUTH_KEY',         'V$lLJ#X)Gof(au_3W*np X(<u]?,owH;sk~%|<S]xQ[i)>~Wx2+LI&e1_]wJ8vV4' );
define( 'SECURE_AUTH_KEY',  '+w*|-tUcC#d((xz[3cBAz.c|3S#tb|nb][6!,GJsI}mrjiEO5O736w-xOq*_c~0^' );
define( 'LOGGED_IN_KEY',    'Dn[%!x#xu(DrwWjuERUXb_@o@rW^gOCNWwqUX(0/@),zwLL1pjwt*g!N~(m:R!vx' );
define( 'NONCE_KEY',        ';}occ 4U!2B<9pmA,tw)2>ND8C.(D9/IjN?D@w-5gHarG{XMTLvo]1onNGPEWqo|' );
define( 'AUTH_SALT',        '>4QCVf#cyh<J#1DlWlLd)1YZfz*U^M?F!>9}#&bzy;/X*BJU^=r232u}y|}u$;W]' );
define( 'SECURE_AUTH_SALT', 'BIjy7WW2FU`Do,mz<RJ.Qc{g]]!F^(<L4KcBsyC;Rw3{9:70Ol<9#g)lP%f.8dX&' );
define( 'LOGGED_IN_SALT',   'Jr/Q&G~;=IA|<RT!Oe>,/bjxVI5L=~6Vi1mR-U?Y37TMlIdkUCb%JK%`#jLE<HV&' );
define( 'NONCE_SALT',       ':mA&9IzI|g s.|<mAn1qr H2UC2U=TVUBw:C0o0[m[n}53MLWmX)u[Gc85^_q}Qu' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
