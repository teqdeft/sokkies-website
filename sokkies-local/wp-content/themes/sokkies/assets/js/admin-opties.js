/**
 * Website-instellingen > Aanvullende opties: een optie die al in een rij
 * staat verdwijnt uit de keuzelijst van de andere rijen.
 *
 * Elke rij hoort over ÉÉN optie te gaan - twee rijen met "Labels" is geen
 * geldige situatie maar levert wel stilzwijgend een dubbele kaart op het
 * offerteformulier op. De lijst zelf komt uit Gravity Forms en wordt
 * serverzijdig gezet; welke rij wat gekozen heeft weet alleen het scherm,
 * vandaar dat dit hier gebeurt en niet in PHP.
 *
 * De eigen keuze van een rij blijft uiteraard staan, en een optie komt
 * meteen weer beschikbaar zodra de rij die hem bezette wordt leeggemaakt of
 * verwijderd.
 */
( function () {
	'use strict';

	var SLEUTEL = 'field_si_extra_naam';

	function lijsten() {
		return Array.prototype.slice.call(
			document.querySelectorAll( '[data-key="' + SLEUTEL + '"] select' )
		);
	}

	function bijwerken() {
		var alle = lijsten();
		if ( ! alle.length ) {
			return;
		}

		var bezet = alle
			.map( function ( select ) { return select.value; } )
			.filter( function ( waarde ) { return '' !== waarde; } );

		alle.forEach( function ( select ) {
			Array.prototype.forEach.call( select.options, function ( optie ) {
				// De lege "Select"-regel blijft altijd staan: daarmee maakt de
				// redacteur een rij weer leeg.
				if ( '' === optie.value ) {
					return;
				}
				var elders = optie.value !== select.value &&
					bezet.indexOf( optie.value ) !== -1;

				// hidden haalt hem uit de lijst, disabled vangt de browsers af
				// die hidden op een <option> negeren.
				optie.hidden   = elders;
				optie.disabled = elders;
			} );
		} );
	}

	document.addEventListener( 'change', function ( e ) {
		if ( e.target && e.target.closest( '[data-key="' + SLEUTEL + '"]' ) ) {
			bijwerken();
		}
	} );

	// Meteen draaien én op DOMContentLoaded: het script staat in de voet, dus
	// de velden staan er al - maar bij een andere laadvolgorde (of als ACF de
	// rijen later opbouwt) vangt de tweede aanroep dat op. bijwerken() is
	// idempotent, dus twee keer draaien is onschadelijk.
	bijwerken();
	document.addEventListener( 'DOMContentLoaded', bijwerken );

	// ACF voegt rijen toe en verwijdert ze zonder paginalading; zonder deze
	// haken zou een nieuwe rij de volledige lijst tonen.
	if ( window.acf && 'function' === typeof window.acf.addAction ) {
		window.acf.addAction( 'append', bijwerken );
		window.acf.addAction( 'remove', bijwerken );
		window.acf.addAction( 'ready', bijwerken );
	}
} )();
