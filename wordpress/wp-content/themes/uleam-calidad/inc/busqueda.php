<?php
/**
 * Búsqueda del sitio: filtros por tipo, resaltado, enlaces a la sección del
 * repositorio y sugerencias mientras se escribe (REST: /wp-json/uleam/v1/buscar).
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tipos de contenido que aparecen en la búsqueda, en orden de importancia.
 *
 * @return array slug => [ etiqueta plural, etiqueta singular, icono ]
 */
function uleam_busqueda_tipos() {
	return array(
		'documento' => array( 'Documentos', 'Documento', 'fa-solid fa-file-lines' ),
		'page'      => array( 'Páginas', 'Página', 'fa-solid fa-window-maximize' ),
		'noticia'   => array( 'Noticias', 'Noticia', 'fa-solid fa-newspaper' ),
	);
}

/**
 * Tipo elegido en el filtro (?en=documento), o vacío para "Todos".
 *
 * @return string
 */
function uleam_busqueda_filtro() {
	$en = isset( $_GET['en'] ) ? sanitize_key( wp_unslash( $_GET['en'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return array_key_exists( $en, uleam_busqueda_tipos() ) ? $en : '';
}

/**
 * Filtros de documentos activos en la URL (área, tipo y año), con su nombre visible.
 *
 * @return array param => [ etiqueta, nombre del término, args de taxonomía ]
 */
function uleam_busqueda_filtros_doc() {
	$mapa    = array(
		'area'           => array( 'Área', 'seccion' ),
		'tipo_documento' => array( 'Tipo', 'tipo_documento' ),
		'anio'           => array( 'Año', 'anio' ),
	);
	$activos = array();
	foreach ( $mapa as $param => $cfg ) {
		$slug = isset( $_GET[ $param ] ) ? sanitize_title( wp_unslash( $_GET[ $param ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$term = $slug ? get_term_by( 'slug', $slug, $cfg[1] ) : null;
		if ( $term ) {
			$activos[ $param ] = array(
				$cfg[0],
				$term->name,
				array(
					'taxonomy'         => $cfg[1],
					'field'            => 'slug',
					'terms'            => $slug,
					'include_children' => true,
				),
			);
		}
	}
	return $activos;
}

/**
 * Limita la búsqueda principal a documentos, páginas y noticias, y aplica el filtro.
 *
 * @param WP_Query $query Consulta.
 */
function uleam_busqueda_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}
	// La búsqueda dentro de /noticias/ la maneja su propio listado (ver inc/noticias.php).
	if ( $query->is_post_type_archive( 'noticia' ) || $query->is_tax( 'categoria_noticia' ) ) {
		return;
	}
	$filtro = uleam_busqueda_filtro();
	$docs   = uleam_busqueda_filtros_doc();
	if ( $docs ) {
		// Filtrar por área, tipo o año solo tiene sentido para documentos.
		$filtro = 'documento';
		$query->set( 'tax_query', array_values( wp_list_pluck( $docs, 2 ) ) );
		$query->set( 'tipo_documento', '' );
		$query->set( 'anio', '' );
	}
	$query->set( 'post_type', $filtro ? $filtro : array_keys( uleam_busqueda_tipos() ) );
	$query->set( 'posts_per_page', 12 );
}
add_action( 'pre_get_posts', 'uleam_busqueda_query' );

/**
 * Cantidad de resultados por tipo para los filtros.
 *
 * @param string $q Términos buscados.
 * @return array slug => cantidad
 */
function uleam_busqueda_conteos( $q ) {
	$conteos = array();
	$docs    = uleam_busqueda_filtros_doc();
	foreach ( array_keys( uleam_busqueda_tipos() ) as $tipo ) {
		if ( $docs && 'documento' !== $tipo ) {
			$conteos[ $tipo ] = 0;
			continue;
		}
		$args = array(
			's'              => $q,
			'post_type'      => $tipo,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		);
		if ( $docs ) {
			$args['tax_query'] = array_values( wp_list_pluck( $docs, 2 ) );
		}
		$r                = new WP_Query( $args );
		$conteos[ $tipo ] = (int) $r->found_posts;
	}
	return $conteos;
}

/**
 * Escapa un texto y resalta con <mark> las palabras buscadas, sin importar tildes ni mayúsculas.
 *
 * @param string $texto Texto plano.
 * @param string $q     Términos buscados.
 * @return string HTML seguro.
 */
function uleam_resaltar( $texto, $q ) {
	$html     = esc_html( $texto );
	// Palabras vacías ("de", "del", "la"…) no se resaltan: aparecen dentro de casi cualquier palabra.
	$vacias   = array( 'de', 'del', 'la', 'las', 'el', 'los', 'lo', 'y', 'e', 'o', 'u', 'en', 'a', 'al', 'un', 'una', 'para', 'por', 'con', 'sin', 'que', 'se' );
	$palabras = array_filter(
		preg_split( '/\s+/u', remove_accents( mb_strtolower( trim( $q ) ) ) ),
		function ( $p ) use ( $vacias ) {
			return mb_strlen( $p ) >= 2 && ! in_array( $p, $vacias, true );
		}
	);
	if ( ! $palabras ) {
		return $html;
	}
	$variantes = array(
		'a' => '[aáàäâ]',
		'e' => '[eéèëê]',
		'i' => '[iíìïî]',
		'o' => '[oóòöô]',
		'u' => '[uúùüû]',
		'n' => '[nñ]',
	);
	$partes = array();
	foreach ( $palabras as $p ) {
		$rx = '';
		foreach ( preg_split( '//u', $p, -1, PREG_SPLIT_NO_EMPTY ) as $c ) {
			$rx .= isset( $variantes[ $c ] ) ? $variantes[ $c ] : preg_quote( $c, '/' );
		}
		$partes[] = $rx;
	}
	return preg_replace( '/(' . implode( '|', $partes ) . ')/iu', '<mark>$1</mark>', $html );
}

/**
 * Extracto limpio de una página o noticia (sin HTML, iconos ni bloques), centrado en la palabra buscada.
 *
 * @param WP_Post $post Contenido.
 * @param string  $q    Términos buscados.
 * @return string Texto plano.
 */
function uleam_busqueda_extracto( $post, $q ) {
	$texto = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	$texto = uleam_extracto_limpio( $texto, 400 );
	if ( '' === $texto ) {
		return '';
	}
	// Empieza cerca de la primera coincidencia para que se vea por qué apareció.
	$base = remove_accents( mb_strtolower( $texto ) );
	$pos  = mb_strpos( $base, remove_accents( mb_strtolower( strtok( trim( $q ), ' ' ) ) ) );
	$ini  = ( false !== $pos && $pos > 80 ) ? $pos - 60 : 0;
	$frag = mb_substr( $texto, $ini, 220 );
	return ( $ini ? '… ' : '' ) . $frag . ( mb_strlen( $texto ) > $ini + 220 ? ' …' : '' );
}

/**
 * Enlace a la pestaña del repositorio donde aparece un documento
 * (p. ej. /aseguramiento-de-la-calidad/#planes-de-mejora).
 *
 * @param int $doc_id ID del documento.
 * @return array|null { url, area } o null si no está en un repositorio publicado.
 */
function uleam_documento_ubicacion( $doc_id ) {
	static $paginas = null;
	if ( null === $paginas ) {
		// Páginas que contienen el bloque, por sección raíz.
		$paginas = array();
		$ids     = get_posts(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => 'ids',
				's'           => 'wp:uleam/repositorio-documental',
			)
		);
		foreach ( $ids as $pid ) {
			if ( preg_match_all( '/wp:uleam\/repositorio-documental\s+\{"seccion":(\d+)/', get_post_field( 'post_content', $pid ), $m ) ) {
				foreach ( $m[1] as $raiz ) {
					$paginas[ (int) $raiz ] = $pid;
				}
			}
		}
	}
	$secciones = get_the_terms( $doc_id, 'seccion' );
	if ( ! $secciones || is_wp_error( $secciones ) ) {
		return null;
	}
	// Recorre hacia arriba: sección → área (hija de la raíz) → raíz con página.
	$cadena   = array_reverse( get_ancestors( $secciones[0]->term_id, 'seccion', 'taxonomy' ) );
	$cadena[] = $secciones[0]->term_id;
	foreach ( $cadena as $i => $tid ) {
		if ( isset( $paginas[ $tid ] ) ) {
			$area = isset( $cadena[ $i + 1 ] ) ? get_term( $cadena[ $i + 1 ], 'seccion' ) : null;
			return array(
				'url'  => get_permalink( $paginas[ $tid ] ) . ( $area ? '#' . $area->slug : '' ),
				'area' => $area ? $area->name : get_the_title( $paginas[ $tid ] ),
			);
		}
	}
	return null;
}

/**
 * Datos de un resultado listos para mostrar (los usa la página de resultados y las sugerencias).
 *
 * @param WP_Post $post Resultado.
 * @return array
 */
function uleam_busqueda_item( $post ) {
	$tipos = uleam_busqueda_tipos();
	$tipo  = isset( $tipos[ $post->post_type ] ) ? $post->post_type : 'page';
	$item  = array(
		'id'       => $post->ID,
		'tipo'     => $tipo,
		'etiqueta' => $tipos[ $tipo ][1],
		'icono'    => $tipos[ $tipo ][2],
		'titulo'   => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
		'url'      => get_permalink( $post ),
		'archivo'  => '',
		'ext'      => '',
		'datos'    => array(),
	);
	if ( 'documento' === $tipo ) {
		$archivo = get_post_meta( $post->ID, '_documento_url', true );
		if ( $archivo ) {
			$item['archivo'] = $archivo;
			$item['url']     = $archivo;
			$item['ext']     = strtoupper( pathinfo( wp_parse_url( $archivo, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
			$item['icono']   = uleam_doc_icono( $archivo );
		}
		foreach ( array( 'seccion', 'tipo_documento', 'anio' ) as $tax ) {
			$t = get_the_terms( $post, $tax );
			if ( $t && ! is_wp_error( $t ) ) {
				$item['datos'][] = $t[0]->name;
			}
		}
	} elseif ( 'noticia' === $tipo ) {
		$item['datos'][] = get_the_date( '', $post );
	} else {
		$padre = $post->post_parent ? get_the_title( $post->post_parent ) : '';
		if ( $padre ) {
			$item['datos'][] = $padre;
		}
	}
	return $item;
}

/**
 * Endpoint de sugerencias: GET /wp-json/uleam/v1/buscar?q=texto
 */
function uleam_busqueda_rest() {
	register_rest_route(
		'uleam/v1',
		'/buscar',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
			'callback'            => function ( WP_REST_Request $req ) {
				$q = trim( $req->get_param( 'q' ) );
				if ( mb_strlen( $q ) < 2 ) {
					return array( 'total' => 0, 'items' => array() );
				}
				$r     = new WP_Query(
					array(
						's'              => $q,
						'post_type'      => array_keys( uleam_busqueda_tipos() ),
						'post_status'    => 'publish',
						'posts_per_page' => 7,
					)
				);
				$items = array();
				foreach ( $r->posts as $p ) {
					$i                = uleam_busqueda_item( $p );
					$i['titulo_html'] = uleam_resaltar( $i['titulo'], $q );
					$items[]          = $i;
				}
				return array(
					'total' => (int) $r->found_posts,
					'items' => $items,
					'todos' => add_query_arg( 's', rawurlencode( $q ), home_url( '/' ) ),
				);
			},
		)
	);
}
add_action( 'rest_api_init', 'uleam_busqueda_rest' );

/**
 * Pasa la URL del endpoint al JavaScript del tema.
 */
function uleam_busqueda_script_data() {
	wp_localize_script(
		'uleam-main',
		'uleamBusqueda',
		array( 'endpoint' => esc_url_raw( rest_url( 'uleam/v1/buscar' ) ) )
	);
}
add_action( 'wp_enqueue_scripts', 'uleam_busqueda_script_data', 20 );
