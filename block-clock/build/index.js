/**
 * Editor script for the "Bangla Clock" block.
 *
 * Ticks a local preview in the editor using the browser clock; the actual
 * front-end tick is handled by view.js. Server-rendered on save (this block
 * has no static markup of its own).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var useEffect = wp.element.useEffect;
	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var components = wp.components;

	var BN_DIGITS = [ '০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯' ];

	function toBanglaDigits( str ) {
		return String( str ).replace( /[0-9]/g, function ( d ) {
			return BN_DIGITS[ d ];
		} );
	}

	function pad( n ) {
		return n < 10 ? '0' + n : '' + n;
	}

	function formatNow( showSeconds ) {
		var now = new Date();
		var parts = [ pad( now.getHours() ), pad( now.getMinutes() ) ];
		if ( showSeconds ) {
			parts.push( pad( now.getSeconds() ) );
		}
		return toBanglaDigits( parts.join( ':' ) );
	}

	function Edit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;
		var blockProps = useBlockProps();

		var timeState = useState( formatNow( attributes.showSeconds ) );
		var time = timeState[ 0 ];
		var setTime = timeState[ 1 ];

		useEffect(
			function () {
				var id = setInterval( function () {
					setTime( formatNow( attributes.showSeconds ) );
				}, 1000 );
				return function () {
					clearInterval( id );
				};
			},
			[ attributes.showSeconds ]
		);

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				{ key: 'inspector' },
				el(
					components.PanelBody,
					{ title: __( 'Clock settings', 'number-to-bangla' ), initialOpen: true },
					el( components.ToggleControl, {
						label: __( 'Show seconds', 'number-to-bangla' ),
						checked: attributes.showSeconds,
						onChange: function ( v ) {
							setAttributes( { showSeconds: v } );
						},
					} )
				)
			),
			el( 'span', { className: 'ntb-clock' }, time )
		);
	}

	registerBlockType( 'ntb/clock', {
		edit: Edit,
		save: function () {
			return null; // Server-rendered.
		},
	} );
} )( window.wp );
