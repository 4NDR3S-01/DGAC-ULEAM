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
	wp_enqueue_script(
		'uleam-equipo',
		get_template_directory_uri() . '/js/equipo.js',
		array(),
		ULEAM_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'uleam_scripts' );

/**
 * Seguridad: los visitantes no pueden listar usuarios (REST ni ?author=N).
 */
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( ! current_user_can( 'list_users' ) ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);
add_action(
	'template_redirect',
	function () {
		if ( ! is_user_logged_in() && ( isset( $_GET['author'] ) || is_author() ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( home_url( '/' ), 302 );
			exit;
		}
	},
	0 // antes de redirect_canonical, que convertiría ?author=N en /author/<usuario>/
);
// El mapa del sitio también lista usuarios (user_nicename).
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);
