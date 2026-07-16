/**
 * Editor script for the "Number to Bangla" block.
 *
 * Written in plain JS against the WordPress script globals so the plugin needs
 * no build step. The block is server-rendered; the editor shows a live preview
 * fetched from the REST endpoint.
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
	var apiFetch = wp.apiFetch;
	var addQueryArgs = wp.url.addQueryArgs;

	var FORMATS = [
		{ label: __( 'Bangla number', 'number-to-bangla' ), value: 'number' },
		{ label: __( 'Bangla word', 'number-to-bangla' ), value: 'word' },
		{ label: __( 'Money (Taka)', 'number-to-bangla' ), value: 'money' },
		{ label: __( 'Comma (lakh)', 'number-to-bangla' ), value: 'comma' },
		{ label: __( 'Percentage', 'number-to-bangla' ), value: 'percentage' },
		{ label: __( 'Gregorian month', 'number-to-bangla' ), value: 'month' },
		{ label: __( 'Bengali month', 'number-to-bangla' ), value: 'bengali-month' },
		{ label: __( 'Season', 'number-to-bangla' ), value: 'season' },
		{ label: __( 'Weekday', 'number-to-bangla' ), value: 'day' },
		{ label: __( 'Date', 'number-to-bangla' ), value: 'date' },
		{ label: __( 'Bengali calendar date', 'number-to-bangla' ), value: 'bengali-date' },
		{ label: __( 'Week number', 'number-to-bangla' ), value: 'week' },
		{ label: __( 'Time', 'number-to-bangla' ), value: 'time' },
		{ label: __( 'Duration (seconds)', 'number-to-bangla' ), value: 'duration' },
		{ label: __( 'Age (birth date)', 'number-to-bangla' ), value: 'age' },
		{ label: __( 'Ordinal', 'number-to-bangla' ), value: 'ordinal' },
		{ label: __( 'Parse (Bangla → English)', 'number-to-bangla' ), value: 'parse' },
	];

	var DATE_FORMATS = [ 'date', 'bengali-date' ];

	var WORD_FORMATS = [ 'percentage', 'time' ];

	function Edit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;
		var blockProps = useBlockProps();

		var previewState = useState( '' );
		var preview = previewState[ 0 ];
		var setPreview = previewState[ 1 ];

		useEffect(
			function () {
				if ( attributes.value === '' ) {
					setPreview( '' );
					return;
				}

				var args = {
					value: attributes.value,
					format: attributes.format,
					prefix: attributes.prefix,
					suffix: attributes.suffix,
					words: attributes.words,
					date_format: attributes.dateFormat,
				};

				apiFetch( { path: addQueryArgs( '/ntb/v1/convert', args ) } )
					.then( function ( res ) {
						setPreview( res.output );
					} )
					.catch( function () {
						setPreview( __( '— invalid input —', 'number-to-bangla' ) );
					} );
			},
			[
				attributes.value,
				attributes.format,
				attributes.prefix,
				attributes.suffix,
				attributes.words,
				attributes.dateFormat,
			]
		);

		var controls = [
			el( components.TextControl, {
				key: 'value',
				label: __( 'Value', 'number-to-bangla' ),
				value: attributes.value,
				onChange: function ( v ) {
					setAttributes( { value: v } );
				},
			} ),
			el( components.SelectControl, {
				key: 'format',
				label: __( 'Format', 'number-to-bangla' ),
				value: attributes.format,
				options: FORMATS,
				onChange: function ( v ) {
					setAttributes( { format: v } );
				},
			} ),
			el( components.TextControl, {
				key: 'prefix',
				label: __( 'Prefix', 'number-to-bangla' ),
				value: attributes.prefix,
				onChange: function ( v ) {
					setAttributes( { prefix: v } );
				},
			} ),
			el( components.TextControl, {
				key: 'suffix',
				label: __( 'Suffix', 'number-to-bangla' ),
				value: attributes.suffix,
				onChange: function ( v ) {
					setAttributes( { suffix: v } );
				},
			} ),
		];

		if ( WORD_FORMATS.indexOf( attributes.format ) !== -1 ) {
			controls.push(
				el( components.ToggleControl, {
					key: 'words',
					label: __( 'Spell out in words', 'number-to-bangla' ),
					checked: attributes.words,
					onChange: function ( v ) {
						setAttributes( { words: v } );
					},
				} )
			);
		}

		if ( DATE_FORMATS.indexOf( attributes.format ) !== -1 ) {
			controls.push(
				el( components.TextControl, {
					key: 'dateFormat',
					label: __( 'Date format', 'number-to-bangla' ),
					help: __( 'PHP date tokens: j F, Y', 'number-to-bangla' ),
					value: attributes.dateFormat,
					onChange: function ( v ) {
						setAttributes( { dateFormat: v } );
					},
				} )
			);
		}

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				{ key: 'inspector' },
				el(
					components.PanelBody,
					{ title: __( 'Conversion settings', 'number-to-bangla' ), initialOpen: true },
					controls
				)
			),
			el(
				'span',
				{ className: 'ntb-block-preview' },
				preview !== ''
					? preview
					: __( 'Enter a value in the block settings…', 'number-to-bangla' )
			)
		);
	}

	registerBlockType( 'ntb/converter', {
		edit: Edit,
		save: function () {
			return null; // Server-rendered.
		},
	} );
} )( window.wp );
