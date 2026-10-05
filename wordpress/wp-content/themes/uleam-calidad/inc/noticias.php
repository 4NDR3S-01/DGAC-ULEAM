<?php
/**
 * Imágenes de las noticias
 *
 * Orden de preferencia: imagen destacada → (se asigna sola al guardar) primera imagen del texto
 * o portada del PDF que publica la noticia → imagen predeterminada del Personalizador →
 * fondo institucional con icono. Todo se cambia desde el panel "Imagen destacada" o el Personalizador.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ID del adjunto al que apunta una URL de uploads (acepta tamaños "-300x200" y el dominio antiguo /files/).
 *
 * @param string $url URL del archivo.
 * @return int
 */
function uleam_adjunto_por_url( $url ) {
	global $wpdb;
	if ( ! preg_match( '#/(?:files|uploads)/(.+)$#', $url, $m ) ) {
		return 0;
	}
	$rel = preg_replace( '/-\d+x\d+(\.\w+)$/', '$1', rawurldecode( $m[1] ) );
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1", $rel ) );
}

/**
 * Imagen real relacionada con la noticia: una imagen de su texto o la portada del PDF que publica.
 *
 * @param int $post_id ID de la noticia.
 * @return int ID del adjunto, o 0 si no hay ninguna.
 */
function uleam_noticia_imagen_relacionada( $post_id ) {
	$contenido = get_post_field( 'post_content', $post_id );
	if ( preg_match_all( '/<img[^>]+src="([^"]+)"/i', $contenido, $m ) ) {
		foreach ( $m[1] as $url ) {
			$id = uleam_adjunto_por_url( $url );
			if ( $id && wp_attachment_is_image( $id ) && file_exists( get_attached_file( $id ) ) ) {
				return $id;
			}
		}
	}
	// WordPress genera una vista previa de la primera página de cada PDF subido.
	if ( preg_match_all( '/href="([^"]+\.pdf)"/i', $contenido, $m ) ) {
		foreach ( $m[1] as $url ) {
			$id   = uleam_adjunto_por_url( $url );
			$meta = $id ? wp_get_attachment_metadata( $id ) : array();
			if ( ! empty( $meta['sizes'] ) ) {
				return $id;
			}
		}
	}
	return 0;
}

/**
 * Al guardar una noticia sin imagen destacada, se le asigna la imagen relacionada (si existe).
 *
 * @param int     $post_id ID de la noticia.
 * @param WP_Post $post    Noticia.
 */
function uleam_noticia_imagen_al_guardar( $post_id, $post ) {
	if ( 'noticia' !== $post->post_type || wp_is_post_revision( $post_id ) || has_post_thumbnail( $post_id ) ) {
		return;
	}
	$id = uleam_noticia_imagen_relacionada( $post_id );
	if ( $id ) {
		set_post_thumbnail( $post_id, $id );
	}
}
add_action( 'wp_after_insert_post', 'uleam_noticia_imagen_al_guardar', 10, 2 );

/**
 * HTML de la imagen de una noticia para tarjetas y listados (siempre devuelve algo visible).
 *
 * @param int    $post_id ID de la noticia.
 * @param string $size    Tamaño de imagen.
 * @param string $class   Clase CSS de la imagen.
 * @return string
 */
function uleam_noticia_media( $post_id, $size = 'medium_large', $class = 'news-card__image' ) {
	$id = get_post_thumbnail_id( $post_id );
	if ( ! $id ) {
		$id = (int) uleam_opt( 'uleam_noticia_imagen' );
	}
	if ( $id ) {
		// Las portadas de PDF son verticales: se muestra la parte superior (título del documento).
		$es_pdf = 'application/pdf' === get_post_mime_type( $id );
		$html   = wp_get_attachment_image(
			$id,
			$size,
			false,
			array(
				'class'   => $class . ( $es_pdf ? ' is-documento' : '' ),
				'loading' => 'lazy',
				'alt'     => '',
			)
		);
		if ( $html ) {
			return $html;
		}
	}
	return '<span class="news-placeholder" aria-hidden="true"><i class="fa-solid fa-newspaper"></i><span>DGAC · ULEAM</span></span>';
}

/**
 * Extracto legible: quita HTML y los símbolos decorativos del texto importado (☑ ⚠ ➤ ⇨ ●…),
 * que dejaban palabras pegadas en los resúmenes.
 *
 * @param string $texto   Texto o HTML.
 * @param int    $palabras Cantidad máxima de palabras.
 * @return string Texto plano.
 */
function uleam_extracto_limpio( $texto, $palabras = 28 ) {
	// Sin excerpt_remove_blocks(): descartaría el texto de las pestañas (gutena-tabs) de las noticias importadas.
	// Un espacio por cada etiqueta: así "<div>Manual</div><div>Flujo</div>" no queda como "ManualFlujo".
	$texto = wp_strip_all_tags( str_replace( "<", " <", strip_shortcodes( $texto ) ) );
	$texto = preg_replace( '/[\p{So}\p{Sk}\x{2190}-\x{21FF}\x{25A0}-\x{25FF}\x{2700}-\x{27BF}\x{2B00}-\x{2BFF}\x{FE0F}]+/u', ' · ', $texto );
	$texto = preg_replace( '/\s*·\s*(·\s*)*/u', ' · ', $texto );
	$texto = preg_replace( '/^[\s·]+|[\s·]+$/u', '', preg_replace( '/\s+/u', ' ', $texto ) );
	return wp_trim_words( $texto, $palabras, '…' );
}

/**
 * Listado de noticias: 9 por página (3 × 3 en computadora). En tableta (2 columnas) la última
 * tarjeta suelta se centra con CSS (css/news.css).
 *
 * @param WP_Query $query Consulta.
 */
function uleam_noticias_por_pagina( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_post_type_archive( 'noticia' ) || $query->is_tax( 'categoria_noticia' ) ) ) {
		$query->set( 'posts_per_page', 9 );
	}
}
add_action( 'pre_get_posts', 'uleam_noticias_por_pagina' );

/**
 * Buscar dentro de /noticias/ debe quedarse en el listado de noticias, no ir a la búsqueda general.
 *
 * @param string $template Plantilla elegida por WordPress.
 * @return string
 */
function uleam_noticias_plantilla_busqueda( $template ) {
	if ( is_search() && ( is_post_type_archive( 'noticia' ) || is_tax( 'categoria_noticia' ) ) ) {
		$listado = locate_template( 'archive-noticia.php' );
		if ( $listado ) {
			return $listado;
		}
	}
	return $template;
}
add_filter( 'template_include', 'uleam_noticias_plantilla_busqueda' );
