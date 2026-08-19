<?php
/**
 * Sokkies — wp-config-sjabloon. Kopieer naar wp-config.php en vul je eigen
 * omgeving in. De tabel-prefix MOET 'sokkies_' blijven (bestaande database).
 */
define( 'DB_NAME', 'sokkies_local' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/* Genereer eigen salts: https://api.wordpress.org/secret-key/1.1/salt/ */
define( 'AUTH_KEY',         'vul-in' );
define( 'SECURE_AUTH_KEY',  'vul-in' );
define( 'LOGGED_IN_KEY',    'vul-in' );
define( 'NONCE_KEY',        'vul-in' );
define( 'AUTH_SALT',        'vul-in' );
define( 'SECURE_AUTH_SALT', 'vul-in' );
define( 'LOGGED_IN_SALT',   'vul-in' );
define( 'NONCE_SALT',       'vul-in' );

$table_prefix = 'sokkies_';

/* Lokaal: aan. Live server: beide op false. */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
