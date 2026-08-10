/**
 * Admin: Product Video tab — source select toggling + WP Media picker.
 */
( function ( $ ) {
	'use strict';

	function toggleFields() {
		var source = $( '#_product_video_source' ).val();
		$( '.shopchop-video-field' ).hide();
		$( '.shopchop-video-field--' + source ).show();
	}

	$( function () {
		var $panel = $( '#shopchop_video_product_data' );
		if ( ! $panel.length ) {
			return;
		}

		toggleFields();
		$( '#_product_video_source' ).on( 'change', toggleFields );

		var mediaFrame = null;

		$panel.on( 'click', '.shopchop-video-select-media', function ( e ) {
			e.preventDefault();

			if ( mediaFrame ) {
				mediaFrame.open();
				return;
			}

			mediaFrame = wp.media( {
				title: 'Select Video',
				library: { type: 'video' },
				button: { text: 'Use this video' },
				multiple: false,
			} );

			mediaFrame.on( 'select', function () {
				var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();
				$panel.find( '.shopchop-video-attachment-id' ).val( attachment.id );
				$panel.find( '.shopchop-video-media-filename' ).text( attachment.filename || attachment.url );
				$panel.find( '.shopchop-video-remove-media' ).show();
			} );

			mediaFrame.open();
		} );

		$panel.on( 'click', '.shopchop-video-remove-media', function ( e ) {
			e.preventDefault();
			$panel.find( '.shopchop-video-attachment-id' ).val( '' );
			$panel.find( '.shopchop-video-media-filename' ).text( '' );
			$( this ).hide();
		} );
	} );
} )( jQuery );
