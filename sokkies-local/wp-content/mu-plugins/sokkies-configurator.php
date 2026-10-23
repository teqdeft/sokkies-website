<?php
/**
 * Sokkies-configurator — lader
 *
 * WordPress laadt uit mu-plugins alleen PHP-bestanden die er LOS in staan, niet
 * wat in een submap zit. Vandaar dit bestandje: het haalt de echte plugin uit
 * de map ernaast. Code toevoegen doe je daar, niet hier.
 *
 * WAAROM EEN MU-PLUGIN EN GEEN GEWONE PLUGIN (besluit 2026-10-23, Kulwant):
 * een gewone plugin moet per omgeving AANGEZET worden, en dat "actief"-vlaggetje
 * staat in de database. Op dit project deployt code wel en de database niet -
 * dezelfde reden waarom formulier-ID's, ACF-waarden en menu's telkens apart
 * nagelopen moeten worden. Een mu-plugin draait zodra het bestand er staat, dus
 * die hele stap vervalt.
 * DE KEERZIJDE: hij is niet uit te zetten via Weergave > Plugins. Moet dat toch,
 * dan is het dit bestand hernoemen of weghalen.
 *
 * @package Sokkies
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/sokkies-configurator/configurator.php';
