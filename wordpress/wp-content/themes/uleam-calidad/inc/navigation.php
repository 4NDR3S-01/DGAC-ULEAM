<?php
/**
 * Navegación, menús, reglas de reescritura y filtros de enlaces activos
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menú de respaldo en caso de que no haya un menú asignado a la ubicación principal.
 */
function uleam_menu_fallback() {
	$is_home = is_front_page() || is_home();
	echo '<ul id="primaryMenu" class="nav-inner">';
	echo '<li class="menu-item ' . ( $is_home ? 'current-menu-item active' : '' ) . '"><a class="' . ( $is_home ? 'active' : '' ) . '" href="' . esc_url( home_url( '/' ) ) . '">Inicio</a></li>';
	echo '<li class="menu-item menu-item-has-children">';
	echo '<a href="#">Quiénes Somos</a>';
	echo '<ul class="sub-menu">';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/resena-historica/' ) ) . '">Reseña Histórica</a></li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/mision-vision/' ) ) . '">Misión y Visión</a></li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/estructura-organica/' ) ) . '">Estructura Orgánica</a></li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/recursos-humanos/' ) ) . '">Recursos Humanos</a></li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/poa/' ) ) . '">POA</a></li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/horario-atencion/' ) ) . '">Horario de Atención</a></li>';
	echo '</ul>';
	echo '</li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/gestion-de-procesos/' ) ) . '">Gestión de Procesos</a></li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/aseguramiento-de-la-calidad/' ) ) . '">Aseguramiento de la Calidad</a></li>';
	echo '<li class="menu-item menu-item-has-children">';
	echo '<a href="#">Auditoría de la Calidad</a>';
	echo '<ul class="sub-menu">';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/seguimiento-y-control-a-procesos-del-sistema-de-gestion-de-la-calidad/' ) ) . '">Seguimiento y Control a Procesos del Sistema de Gestión de la Calidad</a></li>';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/seguimiento-y-control-a-planes-de-mejora/' ) ) . '">Seguimiento y Control a Planes de Mejora</a></li>';
	echo '</ul>';
	echo '</li>';
	echo '</ul>';
}

/**
 * Reglas de reescritura personalizadas para URLs con caracteres especiales
 */
function uleam_custom_rewrite_rules() {
	add_rewrite_rule( '^(reseña-historica|resena-historica)/?$', 'index.php?pagename=resena-historica', 'top' );
}
add_action( 'init', 'uleam_custom_rewrite_rules' );

/**
 * Redirección canónica amigable para rutas con tildes o eñes, o secciones padre que redirigen al primer subelemento
 */
function uleam_handle_accented_slugs() {
	$raw_uri = isset( $_SERVER['REQUEST_URI'] ) ? rawurldecode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	if ( empty( $raw_uri ) ) {
		return;
	}

	$map = array(
		'reseña-historica'        => 'resena-historica',
		'misión-visión'           => 'mision-vision',
		'estructura-orgánica'     => 'estructura-organica',
		'horario-atención'        => 'horario-atencion',
		'gestión-de-procesos'     => 'gestion-de-procesos',
		'quienes-somos'           => 'resena-historica',
		'auditoria-de-la-calidad' => 'seguimiento-y-control-a-procesos-del-sistema-de-gestion-de-la-calidad',
		'auditoría-de-la-calidad' => 'seguimiento-y-control-a-procesos-del-sistema-de-gestion-de-la-calidad',
	);

	foreach ( $map as $accented => $canonical ) {
		if ( preg_match( '#/' . preg_quote( $accented, '#' ) . '(/|\?.*)?$#iu', $raw_uri ) ) {
			wp_safe_redirect( home_url( '/' . $canonical . '/' ), 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'uleam_handle_accented_slugs', 1 );

/**
 * Filtro para marcar activo el enlace actual en el menú de WordPress
 *
 * @param array    $classes Clases CSS del elemento de menú.
 * @param WP_Post  $item    Objeto del elemento de menú.
 * @return array Clases modificadas.
 */
function uleam_nav_menu_active_classes( $classes, $item ) {
	if ( is_front_page() || is_home() ) {
		$clean_item_url = untrailingslashit( strtok( $item->url, '#' ) );
		$clean_home_url = untrailingslashit( home_url( '/' ) );
		$has_hash       = ( false !== strpos( $item->url, '#' ) && false === strpos( $item->url, '#inicio' ) );
		$is_inicio      = ( ! $has_hash && ( $clean_item_url === $clean_home_url || 'Inicio' === $item->title || false !== strpos( $item->url, '#inicio' ) ) );

		if ( $is_inicio ) {
			if ( ! in_array( 'current-menu-item', $classes, true ) ) {
				$classes[] = 'current-menu-item';
			}
			if ( ! in_array( 'active', $classes, true ) ) {
				$classes[] = 'active';
			}
		} else {
			$to_remove = array(
				'current-menu-item',
				'active',
				'current_page_item',
				'current-menu-ancestor',
				'current-menu-parent',
				'current_page_parent',
				'current_page_ancestor',
				'menu-item-home',
			);
			$classes = array_diff( $classes, $to_remove );
		}
	} else {
		$clean_item_url = untrailingslashit( strtok( $item->url, '#' ) );
		$clean_home_url = untrailingslashit( home_url( '/' ) );
		if ( $clean_item_url === $clean_home_url || 'Inicio' === $item->title ) {
			$classes = array_diff( $classes, array( 'current-menu-item', 'active', 'current_page_item', 'menu-item-home' ) );
		}
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'uleam_nav_menu_active_classes', 10, 2 );

/**
 * Filtro de atributos de enlaces para el menú principal
 *
 * @param array   $atts Atributos HTML del enlace (href, class, etc.).
 * @param WP_Post $item Objeto del elemento de menú.
 * @return array Atributos modificados.
 */
function uleam_nav_menu_link_attributes( $atts, $item ) {
	if ( is_front_page() || is_home() ) {
		$has_hash       = ( false !== strpos( $item->url, '#' ) && false === strpos( $item->url, '#inicio' ) );
		$clean_item_url = untrailingslashit( strtok( $item->url, '#' ) );
		$clean_home_url = untrailingslashit( home_url( '/' ) );
		$is_inicio      = ( ! $has_hash && ( $clean_item_url === $clean_home_url || 'Inicio' === $item->title || false !== strpos( $item->url, '#inicio' ) ) );

		if ( $is_inicio ) {
			$classes = isset( $atts['class'] ) ? explode( ' ', $atts['class'] ) : array();
			if ( ! in_array( 'active', $classes, true ) ) {
				$classes[] = 'active';
			}
			$atts['class']        = trim( implode( ' ', $classes ) );
			$atts['aria-current'] = 'page';
		} else {
			unset( $atts['aria-current'] );
			if ( isset( $atts['class'] ) ) {
				$cls = explode( ' ', $atts['class'] );
				$cls = array_diff( $cls, array( 'active' ) );
				$atts['class'] = trim( implode( ' ', $cls ) );
				if ( empty( $atts['class'] ) ) {
					unset( $atts['class'] );
				}
			}
		}
	} else {
		$clean_item_url = untrailingslashit( strtok( $item->url, '#' ) );
		$clean_home_url = untrailingslashit( home_url( '/' ) );
		if ( $clean_item_url === $clean_home_url || 'Inicio' === $item->title ) {
			unset( $atts['aria-current'] );
			if ( isset( $atts['class'] ) ) {
				$cls = explode( ' ', $atts['class'] );
				$cls = array_diff( $cls, array( 'active' ) );
				$atts['class'] = trim( implode( ' ', $cls ) );
				if ( empty( $atts['class'] ) ) {
					unset( $atts['class'] );
				}
			}
		}
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'uleam_nav_menu_link_attributes', 10, 2 );
