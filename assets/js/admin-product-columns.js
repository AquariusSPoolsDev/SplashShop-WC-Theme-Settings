/**
 * Task 4: expandable variant rows on the WooCommerce admin product list.
 */
( function () {
	'use strict';

	function stockBadge( status ) {
		var map = {
			instock: { label: 'In stock', className: 'instock' },
			onbackorder: { label: 'Low stock', className: 'onbackorder' },
			outofstock: { label: 'Out of stock', className: 'outofstock' },
			pre_order: { label: 'Pre-Order Item', className: 'pre-order' },
			coming_soon: { label: 'Coming Soon', className: 'coming-soon' },
		};
		var info = map[ status ] || map.outofstock;
		return '<mark class="' + info.className + '">' + info.label + '</mark>';
	}

	function variantName( variation ) {
		if ( ! variation.attributes || ! variation.attributes.length ) {
			return '#' + variation.id;
		}
		// Label each value with its attribute (e.g. "Color: Blue, Logo: Yes") —
		// bare option values alone ("Blue, Yes") don't say which attribute each
		// value belongs to once a product has more than one attribute.
		return variation.attributes.map( function ( attr ) {
			return attr.name + ': ' + attr.option;
		} ).join( ', ' );
	}

	function formatPrice( amount ) {
		var symbol = ( window.shopchopProductColumns && window.shopchopProductColumns.currencySymbol ) || '';
		return symbol + parseFloat( amount || 0 ).toFixed( 2 );
	}

	/**
	 * Mirrors the simple-product Price column markup/classes (already styled
	 * by admin-product-columns.php's admin_head CSS: .discount-price, del.regular,
	 * .discount, .sale) so a discounted variant looks identical to a discounted
	 * simple product in that column.
	 */
	function priceCellHtml( variation ) {
		var regular = parseFloat( variation.regular_price || variation.price || 0 );
		var hasSale = variation.sale_price !== '' && variation.sale_price !== null && variation.sale_price !== undefined;
		var sale = hasSale ? parseFloat( variation.sale_price ) : null;

		if ( sale !== null && sale < regular ) {
			var discountPct = Math.round( ( ( regular - sale ) / regular ) * 100 );
			return '<span class="discount-price">' +
				'<del class="regular">' + formatPrice( regular ) + '</del>' +
				'<span class="discount">-' + discountPct + '%</span>' +
				'<span class="sale">' + formatPrice( sale ) + '</span>' +
				'</span>';
		}

		return formatPrice( regular );
	}

	/**
	 * Build one <td> per real header cell, copying its column-* class (and
	 * "hidden" state) so the row lines up under the exact same columns as
	 * the parent product row — including any columns hidden via Screen
	 * Options or CSS (e.g. Cost) that still exist in the DOM.
	 */
	function cellFor( th, variation ) {
		var td = document.createElement( 'td' );
		td.className = th.className
			.replace( /\bmanage-column\b/g, '' )
			.replace( /\bsortable\b/g, '' )
			.replace( /\basc\b|\bdesc\b/g, '' )
			.trim();

		var columnId = th.id;

		switch ( columnId ) {
			case 'thumb':
				if ( variation.image && variation.image.src ) {
					td.innerHTML = '<img class="shopchop-variant-row__thumb" src="' + variation.image.src + '" alt="" width="40" height="40" />';
				}
				break;

			case 'name':
				td.classList.add( 'shopchop-variant-row__name' );
				td.textContent = variantName( variation );
				break;

			case 'sku':
				td.classList.add( 'shopchop-variant-row__sku' );
				td.textContent = variation.sku || '–';
				break;

			case 'is_in_stock':
				td.innerHTML = stockBadge( variation.stock_status ) +
					'<br><span class="shopchop-variant-row__stock-line">Stock: ' + ( variation.stock_quantity ?? 0 ) + '</span>';
				break;

			case 'price':
				td.innerHTML = priceCellHtml( variation );
				break;

			default:
				break;
		}

		return td;
	}

	function buildVariantRow( variation, headerCells ) {
		var tr = document.createElement( 'tr' );
		tr.className = 'shopchop-variant-row';
		tr.style.backgroundColor = '#f6f7f7';

		headerCells.forEach( function ( th ) {
			tr.appendChild( cellFor( th, variation ) );
		} );

		return tr;
	}

	function buildStatusRow( colspan, className, message ) {
		var tr = document.createElement( 'tr' );
		tr.className = 'shopchop-variant-row ' + className;
		var td = document.createElement( 'td' );
		td.colSpan = colspan;
		td.textContent = message;
		tr.appendChild( td );
		return tr;
	}

	/**
	 * Fade+slide rows in/out via a CSS transition on insert/remove, rather
	 * than popping them in with no transition at all.
	 */
	function animateIn( row ) {
		row.classList.add( 'shopchop-variant-row--enter' );
		requestAnimationFrame( function () {
			requestAnimationFrame( function () {
				row.classList.remove( 'shopchop-variant-row--enter' );
			} );
		} );
	}

	function animateOutAndRemove( row ) {
		row.classList.add( 'shopchop-variant-row--leave' );
		row.addEventListener( 'transitionend', function () {
			row.remove();
		}, { once: true } );
		// Fallback in case the transition never fires (e.g. reduced motion).
		setTimeout( function () {
			row.remove();
		}, 250 );
	}

	function collapse( button, parentRow ) {
		var next = parentRow.nextElementSibling;
		while ( next && next.classList.contains( 'shopchop-variant-row' ) ) {
			var toRemove = next;
			next = next.nextElementSibling;
			animateOutAndRemove( toRemove );
		}
		button.setAttribute( 'aria-expanded', 'false' );
		button.dataset.expanded = '';
	}

	function expand( button, parentRow, headerCells ) {
		var productId = button.dataset.productId;
		var colspan = headerCells.length;
		var loadingRow = buildStatusRow( colspan, 'shopchop-variant-row--loading', 'Loading variants…' );
		parentRow.parentNode.insertBefore( loadingRow, parentRow.nextSibling );
		animateIn( loadingRow );

		var url = window.shopchopProductColumns.restUrl + '/' + productId + '/variations'
			+ '?per_page=100&orderby=menu_order&order=asc';

		fetch( url, {
			headers: { 'X-WP-Nonce': window.shopchopProductColumns.nonce },
			credentials: 'same-origin',
		} )
			.then( function ( response ) {
				if ( ! response.ok ) {
					throw new Error( 'Request failed' );
				}
				return response.json();
			} )
			.then( function ( variations ) {
				loadingRow.remove();

				if ( ! variations.length ) {
					var emptyRow = buildStatusRow( colspan, 'shopchop-variant-row--error', 'No variants found.' );
					parentRow.parentNode.insertBefore( emptyRow, parentRow.nextSibling );
					animateIn( emptyRow );
					return;
				}

				var anchor = parentRow;
				variations.forEach( function ( variation ) {
					var row = buildVariantRow( variation, headerCells );
					anchor.parentNode.insertBefore( row, anchor.nextSibling );
					animateIn( row );
					anchor = row;
				} );
			} )
			.catch( function () {
				loadingRow.remove();
				var errorRow = buildStatusRow( colspan, 'shopchop-variant-row--error', 'Could not load variants.' );
				parentRow.parentNode.insertBefore( errorRow, parentRow.nextSibling );
				animateIn( errorRow );
			} );

		button.setAttribute( 'aria-expanded', 'true' );
		button.dataset.expanded = '1';
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.shopchop-variant-toggle' );
		if ( ! button ) {
			return;
		}

		var parentRow = button.closest( 'tr' );
		var table = button.closest( 'table' );
		var headerCells = Array.prototype.slice.call( table.querySelectorAll( 'thead th, thead td' ) );

		if ( button.dataset.expanded === '1' ) {
			collapse( button, parentRow );
			return;
		}

		expand( button, parentRow, headerCells );
	} );

	/**
	 * WP core stripes rows via ".striped > tbody > :nth-child(odd)" (pure
	 * position parity). Inserting variant rows shifts that parity for every
	 * product row after them, so two consecutive top-level products can end
	 * up landing on the same odd/even slot and render the same color —
	 * breaking the alternating pattern staff use to scan rows.
	 *
	 * Pin each top-level row's background once, up front, by its real
	 * product order (not DOM position), via inline style — inline style
	 * always beats a stylesheet rule, so it can't be knocked off later by
	 * variant rows shifting nth-child parity.
	 */
	function pinParentRowStripes() {
		var table = document.querySelector( '.wp-list-table.posts' );
		if ( ! table || ! table.tBodies.length ) {
			return;
		}

		Array.prototype.forEach.call( table.tBodies[ 0 ].rows, function ( row, index ) {
			row.style.backgroundColor = ( index % 2 === 1 ) ? '#f6f7f7' : '#ffffff';
		} );
	}

	pinParentRowStripes();
} )();
