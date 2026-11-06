( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;

	wp.blocks.registerBlockType( 'ropstam-services/service-calculator', {
		edit: function () {
			return el(
				'div',
				wp.blockEditor.useBlockProps(),
				el( wp.serverSideRender, { block: 'ropstam-services/service-calculator' } )
			);
		},
		// Rendered by PHP, so nothing is saved in the post content.
		save: function () {
			return null;
		},
	} );
} )( window.wp );
