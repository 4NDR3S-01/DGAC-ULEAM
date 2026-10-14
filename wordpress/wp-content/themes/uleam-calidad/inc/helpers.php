<?php
/**
 * Funciones auxiliares y helpers del tema
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Genera el markup de icono Font Awesome a partir de un identificador lógico.
 *
 * @param string $name Nombre lógico del icono.
 * @return string Markup HTML del icono.
 */
function uleam_icon( $name ) {
	$map = array(
		'chart'     => 'fa-solid fa-chart-column',
		'clipboard' => 'fa-solid fa-clipboard-list',
		'refresh'   => 'fa-solid fa-arrows-rotate',
		'folder'    => 'fa-solid fa-folder-open',
		'graduate'  => 'fa-solid fa-graduation-cap',
		'mail'      => 'fa-solid fa-envelope',
		'file'      => 'fa-solid fa-file-lines',
		'search'    => 'fa-solid fa-magnifying-glass',
		'phone'     => 'fa-solid fa-phone',
		'location'  => 'fa-solid fa-location-dot',
		'arrow'     => 'fa-solid fa-arrow-right',
		'download'  => 'fa-solid fa-download',
		'calendar'  => 'fa-regular fa-calendar',
	);

	if ( ! isset( $map[ $name ] ) ) {
		return '';
	}

	return '<i class="' . esc_attr( $map[ $name ] ) . '" aria-hidden="true"></i>';
}

/**
 * Convierte un enlace que empieza con "/" (p. ej. "/poa/" o "/wp-content/uploads/…") en la dirección
 * completa del sitio. Así los enlaces siguen funcionando aunque el sitio viva en una subcarpeta
 * (departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/).
 *
 * @param string $u Enlace.
 * @return string
 */
function uleam_url( $u ) {
	if ( ! is_string( $u ) || ! preg_match( '#^/(?!/)#', $u ) ) {
		return $u;
	}
	if ( 0 === strpos( $u, '/wp-content/uploads/' ) ) {
		return wp_upload_dir()['baseurl'] . substr( $u, strlen( '/wp-content/uploads' ) );
	}
	return home_url( $u );
}

/**
 * Aplica uleam_url() a los enlaces del contenido escrito en el editor.
 *
 * @param string $html Contenido.
 * @return string
 */
function uleam_url_contenido( $html ) {
	if ( false === strpos( $html, '="/' ) ) {
		return $html;
	}
	return preg_replace_callback(
		'#\b(href|src)=(["\'])(/(?!/)[^"\']*)\2#',
		function ( $m ) {
			return $m[1] . '=' . $m[2] . esc_url( uleam_url( html_entity_decode( $m[3] ) ) ) . $m[2];
		},
		$html
	);
}
add_filter( 'the_content', 'uleam_url_contenido', 20 );

/**
 * Enlaces personalizados de los menús escritos como "/ruta/".
 *
 * @param array $atts Atributos del enlace.
 * @return array
 */
function uleam_url_menu( $atts ) {
	if ( isset( $atts['href'] ) ) {
		$atts['href'] = uleam_url( $atts['href'] );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'uleam_url_menu', 5 );
