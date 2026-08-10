/**
 * Product Video player — dual gallery adapter (Swiper / generic fallback).
 *
 * PHP only ever emits a JSON payload (#shopchop-product-video-data) via a
 * generic, always-fired hook — it never assumes a particular gallery
 * library. This file is the only place that has to know what's actually on
 * the page.
 */
( function () {
	'use strict';

	function readPayload() {
		var el = document.getElementById( 'shopchop-product-video-data' );
		if ( ! el ) {
			return null;
		}
		try {
			return JSON.parse( el.textContent );
		} catch ( e ) {
			return null;
		}
	}

	var isMobile = window.matchMedia( '(hover: none) and (pointer: coarse)' ).matches;

	function playIconSvg() {
		return '<svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>';
	}

	function buildMainSlide( data ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'swiper-slide shopchop-video-slide';
		wrap.dataset.videoState = 'placeholder';

		var bg = data.videoThumbnail || data.featuredImageUrl || '';

		wrap.innerHTML =
			'<div class="shopchop-video-placeholder" style="background-image:url(' + bg + ')">' +
				'<button type="button" class="shopchop-video-play-btn" aria-label="Play video">' + playIconSvg() + '</button>' +
			'</div>' +
			'<div class="shopchop-video-player" hidden></div>' +
			'<button type="button" class="shopchop-video-mute-btn" aria-label="Toggle mute" hidden>🔇</button>';

		return wrap;
	}

	function buildThumbSlide( data, thumbsEl ) {
		// Prefer the theme's own already-rendered thumb <img> over the
		// PHP-computed featuredThumbUrl — it's the exact same image proven
		// to load correctly right next to this one, sidestepping any
		// attachment-size edge case in the server-side URL lookup.
		var existingThumb = thumbsEl && thumbsEl.querySelector( '.swiper-slide img' );
		var bg = ( existingThumb && existingThumb.currentSrc ) || ( existingThumb && existingThumb.src ) || data.featuredThumbUrl;

		var wrap = document.createElement( 'div' );
		wrap.className = 'swiper-slide';
		wrap.innerHTML =
			'<div class="shopchop-video-thumb" style="background-image:url(' + bg + ')">' +
				'<span class="shopchop-video-thumb-play">' + playIconSvg() + '</span>' +
			'</div>';
		return wrap;
	}

	function buildPlayerMarkup( data, autoplay ) {
		var mute = 1; // always start muted, per spec.

		if ( 'youtube' === data.type ) {
			var src = data.embedUrl + '?enablejsapi=1&playsinline=1&rel=0&modestbranding=1'
				+ '&autoplay=' + ( autoplay ? 1 : 0 ) + '&mute=' + mute;
			return '<iframe src="' + src + '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
		}

		if ( 'vimeo' === data.type ) {
			var vsrc = data.embedUrl + '?api=1&background=0&muted=1&autoplay=' + ( autoplay ? 1 : 0 );
			return '<iframe src="' + vsrc + '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
		}

		// mp4
		return '<video src="' + data.videoUrl + '" playsinline muted' + ( autoplay ? ' autoplay' : '' ) + '></video>';
	}

	function postToIframe( iframe, message ) {
		if ( iframe && iframe.contentWindow ) {
			iframe.contentWindow.postMessage( JSON.stringify( message ), '*' );
		}
	}

	/**
	 * Controls one video slide: activating/deactivating the player, mute
	 * toggling, and play/pause toggling, regardless of which gallery library
	 * put the slide on screen.
	 */
	function VideoSlideController( slideEl, data ) {
		var placeholder = slideEl.querySelector( '.shopchop-video-placeholder' );
		var playerEl = slideEl.querySelector( '.shopchop-video-player' );
		var muteBtn = slideEl.querySelector( '.shopchop-video-mute-btn' );
		var playBtn = slideEl.querySelector( '.shopchop-video-play-btn' );
		var muted = true;
		var manuallyPaused = false;
		var observer = null;

		function currentMedia() {
			return playerEl.querySelector( 'iframe, video' );
		}

		function setMuted( next ) {
			muted = next;
			muteBtn.textContent = muted ? '🔇' : '🔊';

			var media = currentMedia();
			if ( ! media ) {
				return;
			}

			if ( 'VIDEO' === media.tagName ) {
				media.muted = muted;
				return;
			}

			if ( 'youtube' === data.type ) {
				postToIframe( media, { event: 'command', func: muted ? 'mute' : 'unMute', args: [] } );
			} else if ( 'vimeo' === data.type ) {
				postToIframe( media, { method: 'setVolume', value: muted ? 0 : 1 } );
			}
		}

		function play() {
			var media = currentMedia();
			if ( ! media ) {
				return;
			}
			manuallyPaused = false;
			if ( 'VIDEO' === media.tagName ) {
				media.play().catch( function () {} );
			} else if ( 'youtube' === data.type ) {
				postToIframe( media, { event: 'command', func: 'playVideo', args: [] } );
			} else if ( 'vimeo' === data.type ) {
				postToIframe( media, { method: 'play' } );
			}
		}

		function pause( isManual ) {
			var media = currentMedia();
			manuallyPaused = !! isManual;
			if ( ! media ) {
				return;
			}
			if ( 'VIDEO' === media.tagName ) {
				media.pause();
			} else if ( 'youtube' === data.type ) {
				postToIframe( media, { event: 'command', func: 'pauseVideo', args: [] } );
			} else if ( 'vimeo' === data.type ) {
				postToIframe( media, { method: 'pause' } );
			}
		}

		function activate( userInitiated ) {
			if ( 'active' === slideEl.dataset.videoState ) {
				return;
			}

			slideEl.dataset.videoState = 'active';
			placeholder.hidden = true;
			playerEl.hidden = false;
			muteBtn.hidden = false;
			muted = true;
			muteBtn.textContent = '🔇';

			var autoplay = ! isMobile || userInitiated;
			playerEl.innerHTML = autoplay ? buildPlayerMarkup( data, true ) : '';

			if ( ! autoplay ) {
				// Mobile, not yet tapped: stay on the placeholder/play button.
				slideEl.dataset.videoState = 'placeholder';
				placeholder.hidden = false;
				playerEl.hidden = true;
				muteBtn.hidden = true;
				return;
			}

			if ( ! observer && ! isMobile ) {
				observer = new IntersectionObserver( function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( 'active' !== slideEl.dataset.videoState ) {
							return;
						}
						if ( entry.isIntersecting && ! manuallyPaused ) {
							play();
						} else if ( ! entry.isIntersecting ) {
							pause( false );
						}
					} );
				}, { threshold: 0.4 } );
				observer.observe( slideEl );
			}
		}

		function deactivate() {
			if ( observer ) {
				observer.disconnect();
				observer = null;
			}
			slideEl.dataset.videoState = 'placeholder';
			placeholder.hidden = false;
			playerEl.hidden = true;
			playerEl.innerHTML = '';
			muteBtn.hidden = true;
			manuallyPaused = false;
		}

		playBtn.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			activate( true );
		} );

		muteBtn.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			setMuted( ! muted );
		} );

		playerEl.addEventListener( 'click', function () {
			var media = currentMedia();
			if ( media && 'VIDEO' === media.tagName ) {
				media.paused ? play() : pause( true );
			}
			// Iframes: platform's own controls handle click inside the embed;
			// we don't have synchronous play-state feedback via postMessage
			// alone without loading each platform's full player SDK.
		} );

		this.activate = activate;
		this.deactivate = deactivate;
	}

	function initSwiperAdapter( galleryEl, data ) {
		var mainEl = galleryEl.querySelector( '.splashshop-gallery-main' );
		var thumbsEl = galleryEl.querySelector( '.splashshop-gallery-thumbs' );

		if ( ! mainEl ) {
			return false;
		}

		var attempts = 0;
		function whenReady( el, cb ) {
			if ( el && el.swiper ) {
				cb( el.swiper );
			} else if ( attempts++ < 40 ) {
				setTimeout( function () { whenReady( el, cb ); }, 100 );
			}
		}

		whenReady( mainEl, function ( mainSwiper ) {
			// Wait for both swipers (if a thumb strip exists) before touching
			// either — prepending into thumbsSwiper *after* mainSwiper's
			// slideTo(0) races Swiper's thumbs-sync: it highlights whatever
			// is sitting at index 0 in the thumb strip at that exact moment,
			// which was still the photo, and the active-border class doesn't
			// follow the photo when it then gets shifted to index 1.
			function proceed( thumbsSwiper ) {
				var slideEl = buildMainSlide( data );
				var controller = new VideoSlideController( slideEl, data );

				mainSwiper.on( 'slideChange', function () {
					var activeEl = mainSwiper.slides[ mainSwiper.activeIndex ];
					if ( activeEl === slideEl ) {
						controller.activate( false );
					} else {
						controller.deactivate();
					}
				} );

				if ( thumbsSwiper ) {
					thumbsSwiper.prependSlide( buildThumbSlide( data, thumbsEl ) );
				}

				// Video goes first (slide 0), product photo becomes slide 1 —
				// prependSlide() keeps Swiper's *visual* position on the photo
				// by shifting activeIndex to match, so slideTo(0) is needed to
				// actually land on the video as the default view. By now the
				// thumb strip already has its matching slide 0, so the
				// thumbs-sync highlights the right one.
				mainSwiper.prependSlide( slideEl );
				mainSwiper.slideTo( 0, 0 );

				if ( thumbsSwiper ) {
					thumbsSwiper.slideTo( 0, 0 );
				}
			}

			if ( thumbsEl ) {
				whenReady( thumbsEl, proceed );
			} else {
				proceed( null );
			}
		} );

		return true;
	}

	/**
	 * Fallback for galleries that aren't this theme's Swiper setup (e.g. a
	 * default WooCommerce/Flexslider gallery on another theme). Appends the
	 * slide as a plain list item and activates purely on click, since we
	 * can't rely on a specific slider's change event existing.
	 */
	function initFallbackAdapter( galleryEl, data ) {
		var list = galleryEl.querySelector( 'ol, ul, .flex-viewport, .woocommerce-product-gallery__wrapper' ) || galleryEl;
		var slideEl = buildMainSlide( data );
		slideEl.classList.remove( 'swiper-slide' );
		slideEl.classList.add( 'shopchop-video-fallback-slide' );
		list.insertBefore( slideEl, list.firstChild );

		var controller = new VideoSlideController( slideEl, data );
		slideEl.querySelector( '.shopchop-video-play-btn' ).addEventListener( 'click', function () {
			controller.activate( true );
		}, { once: false } );
	}

	function init() {
		var data = readPayload();
		if ( ! data ) {
			return;
		}

		var galleryEl = document.querySelector( '.woocommerce-product-gallery' );
		if ( ! galleryEl ) {
			return;
		}

		var isSwiperGallery = !! galleryEl.querySelector( '.splashshop-gallery-main' );

		if ( isSwiperGallery ) {
			initSwiperAdapter( galleryEl, data );
		} else {
			initFallbackAdapter( galleryEl, data );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
