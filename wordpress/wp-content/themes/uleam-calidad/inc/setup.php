<?php
/**
 * Configuración base del tema y carga de scripts/estilos
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Soporte del tema y registro de menús
 */
function uleam_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 229,
			'width'       => 752,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' )
	);
	register_nav_menus(
		array(
			'primary' => 'Menú principal',
			'footer'  => 'Pie de página: enlaces rápidos',
		)
	);
}
add_action( 'after_setup_theme', 'uleam_setup' );

/**
 * Encolado de fuentes, hojas de estilo e interactividad JavaScript
 */
function uleam_scripts() {
	wp_enqueue_style(
		'uleam-fonts',
		'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'uleam-fontawesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
		array(),
		'6.5.2'
	);
	wp_enqueue_style(
		'uleam-style',
		get_stylesheet_uri(),
		array( 'uleam-fonts', 'uleam-fontawesome' ),
		ULEAM_THEME_VERSION
	);
	wp_enqueue_script(
		'uleam-main',
		get_template_directory_uri() . '/js/main.js',
		array(),
		ULEAM_THEME_VERSION,
		true
	);
	wp_enqueue_script(
		'uleam-interfaz',
		get_template_directory_uri() . '/js/interfaz.js',
		array(),
		ULEAM_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'uleam_scripts' );
