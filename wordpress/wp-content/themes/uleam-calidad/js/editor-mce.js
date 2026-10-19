/**
 * Editor visual (TinyMCE): que la tecla Enter no rompa el diseño de las páginas
 *
 * Por defecto, Enter parte el elemento en el que está el cursor. En el HTML a medida
 * del tema eso deja filas de contacto sin icono, tarjetas partidas o notas duplicadas.
 * - En una fila de una lista del tema (p. ej. un correo de "Canales por Área Técnica")
 *   Enter crea otra fila igual y vacía, lista para escribir.
 * - Dentro de una caja de diseño que no es un párrafo (título de tarjeta, fila de
 *   documento, etiqueta...) Enter hace un salto de línea en lugar de partir la caja.
 * En párrafos, títulos y listas normales Enter funciona como siempre.
 */
/* global tinymce */
( function () {
	tinymce.PluginManager.add( 'uleam', function ( editor ) {
		// Caja con diseño propio: cualquier clase que no sea de las estándar de WordPress
		// (alineación, imágenes, colores) ni del propio editor.
		function esDelTema( el ) {
			if ( ! el || 1 !== el.nodeType || el === editor.getBody() ) {
				return false;
			}
			return ( el.className || '' ).split( /\s+/ ).some( function ( c ) {
				return c && ! /^(align|wp-|size-|has-|is-|mce-|attachment)/.test( c );
			} );
		}

		function tieneTexto( el ) {
			return /\S/.test( ( el.textContent || '' ).replace( /[\u00a0\u200b\ufeff]/g, ' ' ) );
		}

		/** Copia la fila sin sus textos y deja el cursor donde iba el primero. */
		function nuevaFila( li ) {
			var copia = li.cloneNode( true );
			var destino = null;
			var textos = [];
			var paseo = editor.getDoc().createTreeWalker( copia, NodeFilter.SHOW_TEXT, null, false );
			while ( paseo.nextNode() ) {
				textos.push( paseo.currentNode );
			}
			textos.forEach( function ( t ) {
				if ( ! destino && /\S/.test( t.nodeValue.replace( /\u00a0/g, ' ' ) ) ) {
					destino = t.parentNode;
				}
				t.parentNode.removeChild( t );
			} );
			// Los enlaces de la copia aún apuntan al dato de la fila original.
			Array.prototype.forEach.call( copia.querySelectorAll( 'a[href^="mailto:"], a[href^="tel:"]' ), function ( a ) {
				a.setAttribute( 'href', a.getAttribute( 'href' ).split( ':' )[ 0 ] + ':' );
				a.setAttribute( 'data-mce-href', a.getAttribute( 'href' ) );
				a.removeAttribute( 'title' );
			} );
			destino = destino || copia;
			destino.innerHTML = '<br data-mce-bogus="1">';
			editor.dom.insertAfter( copia, li );
			editor.selection.setCursorLocation( destino, 0 );
			editor.nodeChanged();
		}

		/** Salto de línea (<br>) en el lugar del cursor. */
		function saltoDeLinea( caja ) {
			var rng = editor.selection.getRng();
			var br = editor.dom.create( 'br' );
			rng.deleteContents();
			rng.insertNode( br );
			// Al final de la caja hace falta un segundo <br> para que la línea nueva se vea.
			var sig = br.nextSibling;
			while ( sig && 3 === sig.nodeType && ! sig.nodeValue.length ) {
				sig = sig.nextSibling;
			}
			if ( ! sig || ( caja.lastChild === br ) ) {
				editor.dom.insertAfter( editor.dom.create( 'br', { 'data-mce-bogus': '1' } ), br );
			}
			editor.selection.setCursorLocation( br.parentNode, editor.dom.nodeIndex( br ) + 1 );
			editor.nodeChanged();
		}

		editor.on( 'keydown', function ( e ) {
			if ( 13 !== e.keyCode || e.shiftKey || e.ctrlKey || e.metaKey || e.altKey || e.isDefaultPrevented() ) {
				return;
			}
			var nodo = editor.selection.getNode();

			var li = editor.dom.getParent( nodo, 'li' );
			if ( li && esDelTema( li.parentNode ) ) {
				e.preventDefault();
				if ( tieneTexto( li ) ) {
					editor.undoManager.transact( function () {
						nuevaFila( li );
					} );
				}
				return;
			}

			var bloque = editor.dom.getParent( nodo, editor.dom.isBlock );
			if ( bloque && /^(DIV|SECTION|ARTICLE|SUMMARY|DETAILS|FIGURE)$/.test( bloque.nodeName ) && ( esDelTema( bloque ) || editor.dom.getParent( bloque, esDelTema ) ) ) {
				e.preventDefault();
				editor.undoManager.transact( function () {
					saltoDeLinea( bloque );
				} );
			}
		}, true );
	} );
}() );
