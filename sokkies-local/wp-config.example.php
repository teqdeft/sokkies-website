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

/* ADRESOPZOEKING op het offerte- en sampleformulier: Google Address
   Validation, voor alle landen. Eén sleutel uit een Google Cloud-project
   waarin de Address Validation API is INGESCHAKELD en facturering aanstaat
   (staat de API uit, dan antwoordt Google met 403 SERVICE_DISABLED).

   De aanroep gebeurt SERVERZIJDIG, dus beperk de sleutel op IP-adres en
   niet op verwijzende website: een referrer-beperking laat juist een
   aanroep vanaf de server stuklopen.

   Vul hem PER OMGEVING in — dit bestand deployt niet mee, dus lokaal, dev
   en live hebben elk hun eigen kopie. Leeg = het formulier vult buiten
   Nederland niets automatisch in; Nederland valt dan terug op PDOK
   (gratis en zonder sleutel, maar het raadt bij een onbekend huisnummer). */
define( 'SOKKIES_GOOGLE_ADRES_KEY', '' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
