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
define( 'DB_PASSWORD', 'admin786' );

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
define( 'AUTH_KEY',         'ZRHFJP<U(Tf);gQPtI]HE!Jvx3#e]Nf@!QLj(&?sEqt?*7N+_s0d*H8aZ0($bQB$' );
define( 'SECURE_AUTH_KEY',  'On?6S#=X8:$=qrp:%bUM:l.c54r9fC6/Wf?UgI%KD:+# z|Wx639M$VvVd}`BHGf' );
define( 'LOGGED_IN_KEY',    '!r4B}.fUj8+^fq_j$#r,o=c?/=?Zy?a/GdwMv;~,eN4=^*2,L.s:b>&YB[ aFoqr' );
define( 'NONCE_KEY',        '-,#UdE%~< YC317|Gtv<-Ee?(,m$PgVc3Vow{IiaC$00DA]~av{7*LXxzae3ABG=' );
define( 'AUTH_SALT',        'kL~VCp2L|@<(AM9.;0>2KJ=2soiy,T&rW6ApxbHJ?NW{@-<i?}#^3`@xU-}D&S5Q' );
define( 'SECURE_AUTH_SALT', 'N&jh/@D]o):9@%IkYmEKg^o@5{?fX7+w|iPS1?INVAjfkG^_MQf&nqj2TS5dNC>]' );
define( 'LOGGED_IN_SALT',   'jZ*NxT)1/GfG#+(biT$zR(&D8n` dlwO(Ao.dDNrKoCit5e2vZA=;vt`^>7i?+LR' );
define( 'NONCE_SALT',       '.GK:exfv>t1r}p<NKokha8mpKt+CU+d*<VAsuUnb5UOMj&Oj.nxFrr6ylE=^f|#j' );
define('FS_METHOD', 'direct');

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
