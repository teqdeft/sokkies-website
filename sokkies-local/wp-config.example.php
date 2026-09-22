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

/* ADRESOPZOEKING op het offerte- en sampleformulier. Twee diensten, elk
   voor een eigen deel van de wereld. Vul ze PER OMGEVING in: dit bestand
   deployt niet mee, dus lokaal, dev en live hebben elk hun eigen kopie.

   Postcode.eu — alleen Nederland, maar dan wel exact: het bevraagt het
   officiële register en zegt hard "bestaat niet" als een combinatie er
   niet is. Basic auth, dus je hebt BEIDE waarden nodig; alleen de sleutel
   geeft 401 "Password not correct". Laat je ze leeg, dan valt Nederland
   terug op PDOK: gratis en zonder sleutel, maar die raadt bij een onbekend
   huisnummer een adres in de buurt bij elkaar. */
define( 'SOKKIES_POSTCODE_EU_KEY',    '' );
define( 'SOKKIES_POSTCODE_EU_SECRET', '' );

/* Google Address Validation — alle overige landen. Eén sleutel uit een
   Google Cloud-project waarin de Address Validation API is ingeschakeld en
   facturering aanstaat. De aanroep gebeurt SERVERZIJDIG, dus beperk de
   sleutel op IP-adres en niet op verwijzende website: een
   referrer-beperking laat een aanroep vanaf de server juist stuklopen.
   Leeg = buiten Nederland vult het formulier niets automatisch in en
   vraagt het de bezoeker de velden zelf te vullen. */
define( 'SOKKIES_GOOGLE_ADRES_KEY', '' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
