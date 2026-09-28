<?php
/**
 * Registro de tipos de contenido personalizados (CPT)
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra CPTs: noticia, documento, evaluacion
 */
function uleam_register_post_types() {
	// Tipo de contenido: Noticias
	register_post_type(
		'noticia',
		array(
			'labels'       => array(
				'name'          => 'Noticias',
				'singular_name' => 'Noticia',
				'add_new'       => 'Añadir noticia',
				'add_new_item'  => 'Añadir nueva noticia',
				'edit_item'     => 'Editar noticia',
				'search_items'  => 'Buscar noticias',
				'not_found'     => 'No se encontraron noticias',
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-megaphone',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'rewrite'      => array( 'slug' => 'noticias' ),
			'show_in_rest' => true,
		)
	);

	// Tipo de contenido: Documentos
	register_post_type(
		'documento',
		array(
			'labels'       => array(
				'name'          => 'Documentos',
				'singular_name' => 'Documento',
				'add_new'       => 'Añadir documento',
				'add_new_item'  => 'Añadir nuevo documento',
				'edit_item'     => 'Editar documento',
				'search_items'  => 'Buscar documentos',
				'not_found'     => 'No se encontraron documentos',
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-media-document',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'      => array( 'slug' => 'documentos' ),
			'show_in_rest' => true,
		)
	);

	// Tipo de contenido: Evaluaciones
	register_post_type(
		'evaluacion',
		array(
			'labels'       => array(
				'name'          => 'Evaluaciones',
				'singular_name' => 'Evaluación',
				'add_new'       => 'Añadir evaluación',
				'add_new_item'  => 'Añadir nueva evaluación',
				'edit_item'     => 'Editar evaluación',
				'search_items'  => 'Buscar evaluaciones',
				'not_found'     => 'No se encontraron evaluaciones',
			),
			'public'       => true,
			'has_archive'  => false,
			'menu_icon'    => 'dashicons-chart-bar',
			'supports'     => array( 'title', 'editor' ),
			'rewrite'      => array( 'slug' => 'evaluacion' ),
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'uleam_register_post_types' );
