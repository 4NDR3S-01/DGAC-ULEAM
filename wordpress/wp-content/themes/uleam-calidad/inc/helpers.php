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
