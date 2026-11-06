( function () {
	'use strict';

	function formatPrice( cents ) {
		return '$' + ( cents / 100 ).toLocaleString( 'en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		} );
	}

	function updateTotal( calculator ) {
		var cents = Number( calculator.dataset.basePrice );

		calculator.querySelectorAll( 'input:checked' ).forEach( function ( addon ) {
			cents += Number( addon.dataset.price );
		} );

		calculator.querySelector( '[data-total]' ).textContent = formatPrice( cents );
	}

	document.querySelectorAll( '[data-service-calculator]' ).forEach( function ( calculator ) {
		var addons = calculator.querySelector( 'fieldset' );

		if ( addons ) {
			addons.hidden = false;
		}

		calculator.addEventListener( 'change', function () {
			updateTotal( calculator );
		} );

		// Browsers can restore ticked boxes after a refresh.
		updateTotal( calculator );
	} );
} )();
