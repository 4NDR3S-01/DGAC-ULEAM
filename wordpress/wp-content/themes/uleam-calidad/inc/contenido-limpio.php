<?php
/**
 * Contenido limpio: repara al guardar lo que el editor visual desordena
 *
 * Las páginas usan HTML a medida dentro del bloque "Clásico". Al editarlas es fácil
 * dejar restos que descuadran el diseño sin que quien edita lo note: párrafos vacíos,
 * "&nbsp;" sueltos, filas de contacto partidas por un Enter, una nota partida en dos,
 * o enlaces cuyo correo visible ya no coincide con el destino real. Este módulo lo
 * corrige al guardar, de modo que solo haga falta cambiar los textos.
 *
 * Al guardar (páginas):
 * - quita párrafos vacíos, "&nbsp;" y saltos <br> sueltos entre bloques;
 * - quita los comentarios HTML de desarrollo (en el editor ocupan una celda de las
 *   rejillas y descuadran las tarjetas), salvo los de WordPress como <!--more-->;
 * - quita viñetas vacías y completa la clase de las filas nuevas de las listas del tema;
 * - reconstruye las listas de correos de "Canales por Área Técnica" a partir de los
 *   correos escritos (también si se escribieron sueltos dentro de la tarjeta);
 * - hace que cada enlace de correo/teléfono apunte a lo que se ve escrito;
 * - mantiene el texto de las notas en un solo bloque, aunque se pulse Enter;
 * - corrige la proporción de una imagen reemplazada (si no, se ve estirada) y
 *   hace que las imágenes propias usen la dirección actual del sitio.
 *
 * Al mostrar:
 * - los archivos propios se sirven desde la dirección actual del sitio aunque se
 *   insertaran desde otra (IP de la red local, túnel, localhost);
 * - los correos largos se cortan después de la @ y no a mitad de palabra.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Expresión de un correo electrónico. */
const ULEAM_RE_CORREO = '/[A-Z0-9._%+\-]+@[A-Z0-9\-]+(?:\.[A-Z0-9\-]+)*\.[A-Z]{2,}/i';

/**
 * Limpia el contenido de una página antes de guardarlo.
 *
 * @param array $data    Datos del post (con barras).
 * @param array $postarr Datos recibidos.
 * @return array
 */
function uleam_limpiar_al_guardar( $data, $postarr ) {
	$tipo = $data['post_type'];
	if ( 'revision' === $tipo && ! empty( $data['post_parent'] ) ) {
		$tipo = get_post_type( (int) $data['post_parent'] );
	}
	if ( 'page' !== $tipo || '' === trim( (string) $data['post_content'] ) ) {
		return $data;
	}

	$original = wp_unslash( $data['post_content'] );
	$limpio   = uleam_limpiar_contenido( $original );
	if ( $limpio !== $original ) {
		$data['post_content'] = wp_slash( $limpio );
	}
	return $data;
}
add_filter( 'wp_insert_post_data', 'uleam_limpiar_al_guardar', 20, 2 );

/**
 * Limpia un contenido completo (bloques y HTML del bloque Clásico).
 *
 * Si no hay nada que corregir devuelve el texto original sin tocar.
 *
 * @param string $contenido Contenido del post.
 * @return string
 */
function uleam_limpiar_contenido( $contenido ) {
	$bloques = parse_blocks( $contenido );
	$cambio  = false;
	foreach ( $bloques as &$bloque ) {
		$cambio = uleam_limpiar_bloque( $bloque ) || $cambio;
	}
	unset( $bloque );
	return $cambio ? serialize_blocks( $bloques ) : $contenido;
}

/**
 * Limpia un bloque y sus bloques internos.
 *
 * @param array $bloque Bloque de parse_blocks() (se modifica).
 * @return bool Si hubo cambios.
 */
function uleam_limpiar_bloque( &$bloque ) {
	$cambio = false;

	if ( null === $bloque['blockName'] ) {
		// HTML del bloque Clásico.
		if ( '' !== trim( $bloque['innerHTML'] ) ) {
			$html = uleam_urls_propias( uleam_limpiar_html( $bloque['innerHTML'] ) );
			if ( $html !== $bloque['innerHTML'] ) {
				$bloque['innerHTML']    = $html;
				$bloque['innerContent'] = array( $html );
				$cambio                 = true;
			}
		}
	} elseif ( 'core/image' === $bloque['blockName'] ) {
		$cambio = uleam_limpiar_imagen( $bloque );
	}

	if ( ! empty( $bloque['innerBlocks'] ) ) {
		foreach ( $bloque['innerBlocks'] as &$interno ) {
			$cambio = uleam_limpiar_bloque( $interno ) || $cambio;
		}
		unset( $interno );
	}
	return $cambio;
}

/**
 * Bloque Imagen: proporción real tras reemplazar la foto y direcciones propias.
 *
 * Al reemplazar una imagen el editor conserva la proporción de la anterior; si la
 * nueva tiene otra forma, se muestra estirada. Solo se corrige cuando no se eligió
 * un recorte ("scale"), es decir, cuando la imagen se deformaría.
 *
 * @param array $bloque Bloque core/image (se modifica).
 * @return bool
 */
function uleam_limpiar_imagen( &$bloque ) {
	$cambio = false;
	$attrs  = $bloque['attrs'];

	if ( ! empty( $attrs['id'] ) && ! empty( $attrs['aspectRatio'] ) && empty( $attrs['scale'] ) ) {
		$meta = wp_get_attachment_metadata( (int) $attrs['id'] );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && is_numeric( $attrs['aspectRatio'] ) ) {
			$real = $meta['width'] / $meta['height'];
			if ( abs( $real - (float) $attrs['aspectRatio'] ) / $real > 0.01 ) {
				$nueva = (string) round( $real, 10 );
				$viejo = $attrs['aspectRatio'];
				$bloque['attrs']['aspectRatio'] = $nueva;
				foreach ( $bloque['innerContent'] as $i => $parte ) {
					if ( is_string( $parte ) ) {
						$bloque['innerContent'][ $i ] = str_replace( 'aspect-ratio:' . $viejo, 'aspect-ratio:' . $nueva, $parte );
					}
				}
				$cambio = true;
			}
		}
	}

	foreach ( $bloque['innerContent'] as $i => $parte ) {
		if ( is_string( $parte ) ) {
			$nueva_parte = uleam_urls_propias( $parte );
			if ( $nueva_parte !== $parte ) {
				$bloque['innerContent'][ $i ] = $nueva_parte;
				$cambio                       = true;
			}
		}
	}

	if ( $cambio ) {
		$bloque['innerHTML'] = implode( '', array_filter( $bloque['innerContent'], 'is_string' ) );
	}
	return $cambio;
}

/**
 * Cambia las direcciones de archivos propios (wp-content/uploads) escritas con otro
 * dominio por la dirección actual de la biblioteca de medios.
 *
 * Solo se cambian las que corresponden a un archivo que existe en este sitio, para
 * no tocar enlaces a archivos de otras webs.
 *
 * @param string $html HTML.
 * @return string
 */
function uleam_urls_propias( $html ) {
	if ( false === strpos( $html, '/wp-content/uploads/' ) ) {
		return $html;
	}
	$subidas = wp_upload_dir( null, false );
	$base    = $subidas['baseurl'];

	return preg_replace_callback(
		'#https?://[^\s"\'<>/]+(?:/[^\s"\'<>]*?)?/wp-content/uploads/([^\s"\'<>?\#,]+)#i',
		function ( $m ) use ( $subidas, $base ) {
			static $existe = array();
			$ruta = $m[1];
			if ( 0 === strpos( $m[0], $base . '/' ) ) {
				return $m[0];
			}
			if ( ! isset( $existe[ $ruta ] ) ) {
				$existe[ $ruta ] = false === strpos( $ruta, '..' ) && file_exists( $subidas['basedir'] . '/' . rawurldecode( $ruta ) );
			}
			return $existe[ $ruta ] ? $base . '/' . $ruta : $m[0];
		},
		$html
	);
}

/**
 * Al mostrar: archivos propios desde la dirección actual y correos cortados en la @.
 *
 * @param string $html Contenido.
 * @return string
 */
function uleam_contenido_al_mostrar( $html ) {
	$html = uleam_urls_propias( $html );
	if ( false !== strpos( $html, 'dgac-contact-email-text' ) ) {
		$html = preg_replace_callback(
			'#(<span class="dgac-contact-email-text">)([^<@]+)@#',
			function ( $m ) {
				return $m[1] . $m[2] . '<wbr>@';
			},
			$html
		);
	}
	return $html;
}
add_filter( 'the_content', 'uleam_contenido_al_mostrar', 9 );

/* -------------------------------------------------------------------------
 * Limpieza del HTML del bloque Clásico
 * ---------------------------------------------------------------------- */

/**
 * Limpia el HTML a medida de una página.
 *
 * @param string $html HTML.
 * @return string El HTML corregido, o el mismo texto si no había nada que corregir.
 */
function uleam_limpiar_html( $html ) {
	if ( ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}

	$dom     = new DOMDocument( '1.0', 'UTF-8' );
	$errores = libxml_use_internal_errors( true );
	$dom->loadHTML(
		'<?xml encoding="UTF-8"?><html><body><div id="uleam-raiz">' . $html . '</div></body></html>',
		LIBXML_HTML_NODEFDTD | LIBXML_NONET
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $errores );

	$xp   = new DOMXPath( $dom );
	$raiz = $xp->query( '//div[@id="uleam-raiz"]' )->item( 0 );
	if ( ! $raiz ) {
		return $html;
	}

	$cambios = 0;
	$cambios += uleam_limpiar_contactos( $dom, $xp );
	$cambios += uleam_limpiar_notas( $dom, $xp );
	$cambios += uleam_limpiar_enlaces( $xp );
	$cambios += uleam_limpiar_listas( $xp );
	$cambios += uleam_limpiar_vacios( $xp );
	$cambios += uleam_limpiar_comentarios( $xp );

	if ( ! $cambios ) {
		return $html;
	}

	$salida = '';
	foreach ( $raiz->childNodes as $nodo ) {
		$salida .= $dom->saveHTML( $nodo );
	}
	return $salida;
}

/**
 * Busca elementos por clase.
 *
 * @param DOMXPath $xp     Consulta.
 * @param string   $clase  Clase CSS.
 * @param string   $tag    Etiqueta (o *).
 * @param DOMNode  $dentro Nodo en el que buscar.
 * @return DOMNodeList
 */
function uleam_por_clase( $xp, $clase, $tag = '*', $dentro = null ) {
	$q = ( $dentro ? './/' : '//' ) . $tag . '[contains(concat(" ", normalize-space(@class), " "), " ' . $clase . ' ")]';
	return $dentro ? $xp->query( $q, $dentro ) : $xp->query( $q );
}

/**
 * Indica si un elemento tiene una clase.
 *
 * @param DOMNode $el    Elemento.
 * @param string  $clase Clase CSS.
 * @return bool
 */
function uleam_tiene_clase( $el, $clase ) {
	return $el instanceof DOMElement && in_array( $clase, preg_split( '/\s+/', $el->getAttribute( 'class' ) ), true );
}

/**
 * Texto visible de un nodo, sin espacios duros.
 *
 * @param DOMNode $nodo Nodo.
 * @return string
 */
function uleam_texto( $nodo ) {
	return trim( preg_replace( '/[\s\x{00A0}\x{200B}\x{FEFF}]+/u', ' ', $nodo->textContent ) );
}

/**
 * Indica si un nodo es otro o lo contiene.
 *
 * @param DOMNode $padre Nodo contenedor.
 * @param DOMNode $nodo  Nodo buscado.
 * @return bool
 */
function uleam_contiene( $padre, $nodo ) {
	for ( ; $nodo; $nodo = $nodo->parentNode ) {
		if ( $nodo === $padre ) {
			return true;
		}
	}
	return false;
}

/**
 * Indica si una etiqueta es de bloque.
 *
 * @param DOMNode $nodo Nodo.
 * @return bool
 */
function uleam_es_bloque( $nodo ) {
	static $bloques = array( 'address', 'article', 'aside', 'blockquote', 'details', 'div', 'dl', 'dd', 'dt', 'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'li', 'main', 'nav', 'ol', 'p', 'pre', 'section', 'summary', 'table', 'ul' );
	return $nodo instanceof DOMElement && in_array( strtolower( $nodo->nodeName ), $bloques, true );
}

/**
 * Correos que aparecen en un texto (en minúsculas y sin repetir).
 *
 * @param string $texto Texto.
 * @return string[]
 */
function uleam_correos( $texto ) {
	preg_match_all( ULEAM_RE_CORREO, $texto, $m );
	return array_values( array_unique( array_map( 'strtolower', $m[0] ) ) );
}

/**
 * Crea un elemento con clase y contenido.
 *
 * @param DOMDocument $dom   Documento.
 * @param string      $tag   Etiqueta.
 * @param array       $attrs Atributos.
 * @param array       $hijos Nodos o textos hijos.
 * @return DOMElement
 */
function uleam_el( $dom, $tag, $attrs = array(), $hijos = array() ) {
	$el = $dom->createElement( $tag );
	foreach ( $attrs as $k => $v ) {
		$el->setAttribute( $k, $v );
	}
	foreach ( $hijos as $hijo ) {
		$el->appendChild( is_string( $hijo ) ? $dom->createTextNode( $hijo ) : $hijo );
	}
	return $el;
}

/**
 * "Canales por Área Técnica": deja cada tarjeta con una fila correcta por correo.
 *
 * Toma los correos escritos en la lista (aunque el Enter haya partido la fila, o el
 * enlace siga apuntando al correo anterior) y también los escritos sueltos dentro de
 * la tarjeta, por ejemplo después de borrar la lista entera. Una fila con texto que no
 * es un correo (p. ej. un nombre) se conserva como línea de texto, sin enlace.
 *
 * @param DOMDocument $dom Documento.
 * @param DOMXPath    $xp  Consulta.
 * @return int Cambios.
 */
function uleam_limpiar_contactos( $dom, $xp ) {
	$cambios = 0;
	foreach ( iterator_to_array( uleam_por_clase( $xp, 'dgac-area-card', 'div' ) ) as $tarjeta ) {
		// Al borrar, el editor puede partir la lista en varias (incluso una dentro de otra):
		// se leen todas y se dejan en una sola.
		$listas  = iterator_to_array( uleam_por_clase( $xp, 'dgac-area-contacts', 'ul', $tarjeta ) );
		$lista   = $listas ? $listas[0] : null;
		$cabeza  = uleam_por_clase( $xp, 'dgac-area-head', 'div', $tarjeta )->item( 0 );
		$correos = array(); // Filas en orden: un correo (texto) o array( 'nota' => texto ).
		$varias  = false;

		foreach ( $listas as $otra ) {
			if ( $otra !== $lista && ! uleam_contiene( $lista, $otra ) ) {
				$varias = true;
			}
		}

		foreach ( $listas as $otra ) {
			foreach ( $otra->childNodes as $li ) {
				if ( XML_TEXT_NODE === $li->nodeType ) {
					// Correo escrito directamente en la lista, fuera de una fila.
					$correos = array_merge( $correos, uleam_correos( $li->nodeValue ) );
					continue;
				}
				if ( 'li' !== strtolower( $li->nodeName ) ) {
					continue; // Las listas metidas dentro se leen en su propia vuelta.
				}
				$en_texto = uleam_correos( $li->textContent );
				if ( ! $en_texto ) {
					// Fila sin correo visible: se usa el del enlace, si lo tiene.
					foreach ( $xp->query( './/a[starts-with(@href, "mailto:")]', $li ) as $a ) {
						$en_texto = array_merge( $en_texto, uleam_correos( rawurldecode( $a->getAttribute( 'href' ) ) ) );
					}
				}
				if ( ! $en_texto && '' !== uleam_texto( $li ) ) {
					$en_texto = array( array( 'nota' => uleam_texto( $li ) ) );
				}
				$correos = array_merge( $correos, $en_texto );
			}
		}

		foreach ( $listas as $otra ) {
			if ( $otra !== $lista && $otra->parentNode && ! uleam_contiene( $lista, $otra ) ) {
				$otra->parentNode->removeChild( $otra );
				++$cambios;
			}
		}

		// Correos escritos sueltos en la tarjeta (fuera de la cabecera y de la lista).
		foreach ( iterator_to_array( $tarjeta->childNodes ) as $hijo ) {
			if ( $hijo === $cabeza || ( $lista && uleam_contiene( $hijo, $lista ) ) ) {
				continue;
			}
			$sueltos = uleam_correos( $hijo->textContent );
			if ( ! $sueltos ) {
				continue;
			}
			$resto = trim( preg_replace( ULEAM_RE_CORREO, '', uleam_texto( $hijo ) ), " \t\n\r\0\x0B,;:.-·" );
			if ( '' === $resto ) {
				$correos = array_merge( $correos, $sueltos );
				$tarjeta->removeChild( $hijo );
				++$cambios;
			}
		}

		$vistos = array();
		foreach ( $correos as $i => $fila ) {
			$clave = is_array( $fila ) ? 'nota:' . $fila['nota'] : $fila;
			if ( isset( $vistos[ $clave ] ) ) {
				unset( $correos[ $i ] );
			}
			$vistos[ $clave ] = true;
		}
		$correos = array_values( $correos );

		if ( ! $correos ) {
			if ( $lista ) {
				$lista->parentNode->removeChild( $lista );
				++$cambios;
			}
			continue;
		}

		if ( $lista && ! $varias && uleam_contactos_correctos( $xp, $lista, $correos ) ) {
			continue;
		}

		$nueva = uleam_el( $dom, 'ul', array( 'class' => 'dgac-area-contacts' ) );
		foreach ( $correos as $fila ) {
			$nueva->appendChild( $dom->createTextNode( "\n" ) );
			$nueva->appendChild(
				is_array( $fila )
					? uleam_el( $dom, 'li', array( 'class' => 'dgac-area-contact-item dgac-area-contact-note' ), array( $fila['nota'] ) )
					: uleam_fila_contacto( $dom, $fila )
			);
		}
		$nueva->appendChild( $dom->createTextNode( "\n" ) );

		if ( $lista ) {
			$lista->parentNode->replaceChild( $nueva, $lista );
		} elseif ( $cabeza && $cabeza->parentNode === $tarjeta ) {
			$tarjeta->insertBefore( $nueva, $cabeza->nextSibling );
		} else {
			$tarjeta->appendChild( $nueva );
		}
		++$cambios;
	}
	return $cambios;
}

/**
 * Fila de contacto con la estructura del diseño.
 *
 * @param DOMDocument $dom    Documento.
 * @param string      $correo Correo.
 * @return DOMElement
 */
function uleam_fila_contacto( $dom, $correo ) {
	$icono = function ( $clase ) use ( $dom ) {
		return uleam_el( $dom, 'i', array( 'class' => $clase, 'aria-hidden' => 'true' ) );
	};
	return uleam_el(
		$dom,
		'li',
		array( 'class' => 'dgac-area-contact-item' ),
		array(
			uleam_el(
				$dom,
				'a',
				array(
					'class' => 'dgac-area-contact-link',
					'title' => 'Escribir a ' . $correo,
					'href'  => 'mailto:' . $correo,
				),
				array(
					uleam_el(
						$dom,
						'div',
						array( 'class' => 'dgac-contact-link-left' ),
						array(
							uleam_el( $dom, 'span', array( 'class' => 'dgac-contact-mail-icon' ), array( $icono( 'fa-solid fa-envelope' ) ) ),
							uleam_el( $dom, 'span', array( 'class' => 'dgac-contact-email-text' ), array( $correo ) ),
						)
					),
					uleam_el( $dom, 'span', array( 'class' => 'dgac-contact-arrow-btn', 'aria-hidden' => 'true' ), array( $icono( 'fa-solid fa-arrow-up-right-from-square' ) ) ),
				)
			),
		)
	);
}

/**
 * Indica si la lista ya tiene exactamente las filas correctas para esos correos.
 *
 * @param DOMXPath   $xp      Consulta.
 * @param DOMElement $lista   Lista.
 * @param array      $correos Filas esperadas (correos o array( 'nota' => texto )).
 * @return bool
 */
function uleam_contactos_correctos( $xp, $lista, $correos ) {
	$filas = $xp->query( './li', $lista );
	if ( $filas->length !== count( $correos ) || uleam_texto_suelto( $lista ) ) {
		return false;
	}
	foreach ( $filas as $i => $li ) {
		$correo = $correos[ $i ];
		if ( is_array( $correo ) ) {
			if ( ! uleam_tiene_clase( $li, 'dgac-area-contact-note' ) || $xp->query( './*', $li )->length || uleam_texto( $li ) !== $correo['nota'] ) {
				return false;
			}
			continue;
		}
		$a      = $xp->query( './a', $li )->item( 0 );
		$texto  = $a ? uleam_por_clase( $xp, 'dgac-contact-email-text', 'span', $a )->item( 0 ) : null;
		if (
			! uleam_tiene_clase( $li, 'dgac-area-contact-item' ) ||
			! $a || ! uleam_tiene_clase( $a, 'dgac-area-contact-link' ) ||
			strtolower( $a->getAttribute( 'href' ) ) !== 'mailto:' . $correo ||
			$a->getAttribute( 'title' ) !== 'Escribir a ' . $correo ||
			! $texto || strtolower( uleam_texto( $texto ) ) !== $correo ||
			uleam_texto( $li ) !== uleam_texto( $texto ) ||
			! uleam_por_clase( $xp, 'dgac-contact-mail-icon', 'span', $a )->length ||
			! uleam_por_clase( $xp, 'dgac-contact-arrow-btn', 'span', $a )->length
		) {
			return false;
		}
	}
	return true;
}

/**
 * Indica si un elemento tiene texto (o &nbsp;) directamente dentro, fuera de sus hijos.
 *
 * @param DOMElement $el Elemento.
 * @return bool
 */
function uleam_texto_suelto( $el ) {
	foreach ( $el->childNodes as $hijo ) {
		if ( XML_TEXT_NODE === $hijo->nodeType && preg_match( '/[^\s]/u', $hijo->nodeValue ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Notas (recuadro con icono): todo el texto en un solo bloque al lado del icono.
 *
 * Un Enter al final de la nota la parte en dos recuadros, y los párrafos sueltos se
 * colocan uno al lado del otro. Se juntan en un único contenido.
 *
 * @param DOMDocument $dom Documento.
 * @param DOMXPath    $xp  Consulta.
 * @return int Cambios.
 */
function uleam_limpiar_notas( $dom, $xp ) {
	$cambios = 0;
	foreach ( iterator_to_array( uleam_por_clase( $xp, 'dgac-note-box', 'div' ) ) as $nota ) {
		if ( ! $nota->parentNode ) {
			continue; // Ya se juntó con la anterior.
		}
		$icono = null;
		foreach ( $nota->childNodes as $hijo ) {
			if ( $hijo instanceof DOMElement ) {
				$icono = 'i' === strtolower( $hijo->nodeName ) ? $hijo : null;
				break;
			}
		}

		// Recuadros siguientes sin icono = trozos de esta nota partida por un Enter.
		$extra = array();
		$sig   = $nota->nextSibling;
		while ( $sig ) {
			if ( XML_TEXT_NODE === $sig->nodeType && '' === uleam_texto( $sig ) ) {
				$sig = $sig->nextSibling;
				continue;
			}
			if ( ! uleam_tiene_clase( $sig, 'dgac-note-box' ) || $xp->query( './i', $sig )->length ) {
				break;
			}
			$extra[] = $sig;
			$sig     = $sig->nextSibling;
		}

		$partes = array();
		foreach ( $nota->childNodes as $hijo ) {
			if ( $hijo !== $icono ) {
				$partes[] = $hijo;
			}
		}
		$bloques = array_filter(
			$partes,
			function ( $n ) {
				return ! ( XML_TEXT_NODE === $n->nodeType && '' === uleam_texto( $n ) );
			}
		);
		$unico = 1 === count( $bloques ) ? reset( $bloques ) : null;
		if ( ! $extra && $unico && uleam_es_bloque( $unico ) ) {
			continue; // Icono + un solo bloque de contenido: correcto.
		}

		$contenido = uleam_el( $dom, 'div', array( 'class' => 'dgac-note-content' ) );
		$fuentes   = array( $partes );
		foreach ( $extra as $trozo ) {
			$fuentes[] = iterator_to_array( $trozo->childNodes );
		}
		foreach ( $fuentes as $nodos ) {
			// Si es un único div de contenido, se usan sus hijos.
			$llenos = array_values(
				array_filter(
					$nodos,
					function ( $n ) {
						return ! ( XML_TEXT_NODE === $n->nodeType && '' === uleam_texto( $n ) );
					}
				)
			);
			if ( 1 === count( $llenos ) && 'div' === strtolower( $llenos[0]->nodeName ) ) {
				$nodos = iterator_to_array( $llenos[0]->childNodes );
			}
			uleam_agrupar_en_parrafos( $dom, $contenido, $nodos );
		}

		foreach ( $partes as $n ) {
			if ( $n->parentNode === $nota ) {
				$nota->removeChild( $n );
			}
		}
		foreach ( $extra as $trozo ) {
			$trozo->parentNode->removeChild( $trozo );
		}
		$nota->appendChild( $contenido );
		++$cambios;
	}
	return $cambios;
}

/**
 * Mueve nodos a un contenedor; los textos y elementos en línea seguidos van en un <p>.
 *
 * @param DOMDocument $dom        Documento.
 * @param DOMElement  $contenedor Destino.
 * @param DOMNode[]   $nodos      Nodos a mover.
 */
function uleam_agrupar_en_parrafos( $dom, $contenedor, $nodos ) {
	$p = null;
	foreach ( $nodos as $n ) {
		if ( uleam_es_bloque( $n ) ) {
			$p = null;
			$contenedor->appendChild( $n );
			continue;
		}
		if ( XML_TEXT_NODE === $n->nodeType && '' === uleam_texto( $n ) && ! $p ) {
			continue;
		}
		if ( 'br' === strtolower( $n->nodeName ) && ! $p ) {
			continue;
		}
		if ( ! $p ) {
			$p = $dom->createElement( 'p' );
			$contenedor->appendChild( $p );
		}
		$p->appendChild( $n );
	}
}

/**
 * Enlaces de correo y teléfono: el destino y la descripción siguen al texto visible.
 *
 * En el editor visual se cambia el texto que se ve, pero el destino del enlace queda
 * con el dato anterior; el visitante vería un correo y escribiría a otro.
 *
 * @param DOMXPath $xp Consulta.
 * @return int Cambios.
 */
function uleam_limpiar_enlaces( $xp ) {
	$cambios = 0;
	foreach ( $xp->query( '//a[starts-with(@href, "mailto:")]' ) as $a ) {
		$visibles = uleam_correos( $a->textContent );
		if ( 1 !== count( $visibles ) ) {
			continue;
		}
		$correo   = $visibles[0];
		$href     = $a->getAttribute( 'href' );
		$consulta = strpos( $href, '?' ) !== false ? substr( $href, strpos( $href, '?' ) ) : '';
		if ( strtolower( $href ) !== 'mailto:' . $correo . strtolower( $consulta ) ) {
			$a->setAttribute( 'href', 'mailto:' . $correo . $consulta );
			++$cambios;
		}
		if ( $a->hasAttribute( 'title' ) ) {
			$titulo = $a->getAttribute( 'title' );
			$nuevo  = preg_match( ULEAM_RE_CORREO, $titulo ) ? preg_replace( ULEAM_RE_CORREO, $correo, $titulo ) : $titulo;
			if ( $nuevo !== $titulo ) {
				$a->setAttribute( 'title', $nuevo );
				++$cambios;
			}
		}
	}

	// Enlace de correo o teléfono sin dato (p. ej. una fila nueva que no se llenó).
	foreach ( iterator_to_array( $xp->query( '//a[@href="mailto:" or @href="tel:"]' ) ) as $a ) {
		if ( '' === uleam_texto( $a ) ) {
			$a->parentNode->removeChild( $a );
			++$cambios;
		}
	}

	$digitos = function ( $texto ) {
		$sin_ext = preg_split( '/\bext\b|extensi/i', $texto )[0];
		return preg_replace( '/\D+/', '', $sin_ext );
	};
	foreach ( $xp->query( '//a[starts-with(@href, "tel:")]' ) as $a ) {
		$visible = uleam_texto( $a );
		$numero  = $digitos( $visible );
		if ( strlen( $numero ) < 7 ) {
			continue;
		}
		if ( $digitos( substr( $a->getAttribute( 'href' ), 4 ) ) !== $numero ) {
			$a->setAttribute( 'href', 'tel:' . $numero );
			++$cambios;
		}
		if ( $a->hasAttribute( 'title' ) && $digitos( $a->getAttribute( 'title' ) ) !== $numero ) {
			$a->setAttribute( 'title', 'Llamar al ' . $visible );
			++$cambios;
		}
	}
	return $cambios;
}

/**
 * Listas: quita viñetas vacías y da a las filas nuevas la clase de sus hermanas.
 *
 * @param DOMXPath $xp Consulta.
 * @return int Cambios.
 */
function uleam_limpiar_listas( $xp ) {
	$cambios = 0;
	foreach ( iterator_to_array( $xp->query( '//li' ) ) as $li ) {
		if ( '' === uleam_texto( $li ) && ! $xp->query( './/img|.//iframe|.//video', $li )->length ) {
			$li->parentNode->removeChild( $li );
			++$cambios;
		}
	}
	foreach ( $xp->query( '//ul[@class]|//ol[@class]' ) as $lista ) {
		$clase = '';
		foreach ( $xp->query( './li[@class]', $lista ) as $li ) {
			$clase = $li->getAttribute( 'class' );
			break;
		}
		if ( '' === $clase ) {
			continue;
		}
		foreach ( $xp->query( './li[not(@class) or @class=""]', $lista ) as $li ) {
			$li->setAttribute( 'class', $clase );
			++$cambios;
		}
	}
	return $cambios;
}

/**
 * Quita párrafos vacíos, "&nbsp;" sueltos y saltos <br> entre bloques.
 *
 * Son los restos que deja el editor al borrar o al pulsar Enter, y en las rejillas del
 * diseño ocupan una celda o empujan las tarjetas.
 *
 * @param DOMXPath $xp Consulta.
 * @return int Cambios.
 */
function uleam_limpiar_vacios( $xp ) {
	$cambios = 0;

	foreach ( iterator_to_array( $xp->query( '//p' ) ) as $p ) {
		if ( '' === uleam_texto( $p ) && ! $xp->query( './/*[not(self::br)]', $p )->length ) {
			$p->parentNode->removeChild( $p );
			++$cambios;
		}
	}

	$contenedor = function ( $n ) {
		return $n instanceof DOMElement && uleam_es_bloque( $n ) && ! in_array( strtolower( $n->nodeName ), array( 'p', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'dt', 'dd', 'figcaption', 'blockquote', 'summary' ), true );
	};

	foreach ( iterator_to_array( $xp->query( '//text()' ) ) as $t ) {
		if ( $contenedor( $t->parentNode ) && false !== strpos( $t->nodeValue, "\xC2\xA0" ) && '' === uleam_texto( $t ) ) {
			$t->nodeValue = "\n";
			++$cambios;
		}
	}

	$vecino = function ( $n, $dir ) {
		do {
			$n = 'prev' === $dir ? $n->previousSibling : $n->nextSibling;
		} while ( $n && ( XML_COMMENT_NODE === $n->nodeType || ( XML_TEXT_NODE === $n->nodeType && '' === uleam_texto( $n ) ) ) );
		return $n;
	};
	foreach ( iterator_to_array( $xp->query( '//br' ) ) as $br ) {
		if ( ! $contenedor( $br->parentNode ) ) {
			continue;
		}
		$antes   = $vecino( $br, 'prev' );
		$despues = $vecino( $br, 'next' );
		if ( ( ! $antes || uleam_es_bloque( $antes ) ) && ( ! $despues || uleam_es_bloque( $despues ) ) ) {
			$br->parentNode->removeChild( $br );
			++$cambios;
		}
	}

	return $cambios;
}

/**
 * Quita los comentarios HTML, salvo los que WordPress usa (<!--more-->, <!--nextpage-->).
 *
 * @param DOMXPath $xp Consulta.
 * @return int Cambios.
 */
function uleam_limpiar_comentarios( $xp ) {
	$cambios = 0;
	foreach ( iterator_to_array( $xp->query( '//comment()' ) ) as $c ) {
		if ( ! preg_match( '/^\s*(more|noteaser|nextpage)\b/', $c->nodeValue ) ) {
			$c->parentNode->removeChild( $c );
			++$cambios;
		}
	}
	return $cambios;
}
