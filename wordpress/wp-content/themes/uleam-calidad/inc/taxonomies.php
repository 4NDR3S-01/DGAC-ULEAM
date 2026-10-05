<?php
/**
 * Registro de taxonomías y consultas de archivos
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra taxonomías para documentos, evaluaciones y noticias
 */
function uleam_register_taxonomies() {
	// Taxonomía: Tipo de documento
	register_taxonomy(
		'tipo_documento',
		'documento',
		array(
			'label'             => 'Tipo de documento',
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
		)
	);

	// Taxonomía: Proceso
	register_taxonomy(
		'proceso',
		'documento',
		array(
			'label'        => 'Proceso',
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Taxonomía: Año
	register_taxonomy(
		'anio',
		'documento',
		array(
			'label'             => 'Año',
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
		)
	);

	// Taxonomía: Período (para evaluación)
	register_taxonomy(
		'periodo',
		'evaluacion',
		array(
			'label'        => 'Período',
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Taxonomía: Categoría de noticia
	register_taxonomy(
		'categoria_noticia',
		'noticia',
		array(
			'labels'       => array(
				'name'          => 'Categorías de noticias',
				'singular_name' => 'Categoría',
				'search_items'  => 'Buscar categorías',
				'all_items'     => 'Todas las categorías',
				'edit_item'     => 'Editar categoría',
				'add_new_item'  => 'Añadir categoría',
			),
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'categoria-noticia' ),
		)
	);
}
add_action( 'init', 'uleam_register_taxonomies' );

/**
 * Filtra el archivo de noticias por categoría vía query string.
 *
 * @param WP_Query $query Consulta principal de WordPress.
 */
function uleam_noticia_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_post_type_archive( 'noticia' ) ) {
		return;
	}
	if ( empty( $_GET['categoria_noticia'] ) ) {
		return;
	}
	$slug = sanitize_title( wp_unslash( $_GET['categoria_noticia'] ) );
	if ( '' === $slug ) {
		return;
	}
	$query->set(
		'tax_query',
		array(
			array(
				'taxonomy' => 'categoria_noticia',
				'field'    => 'slug',
				'terms'    => $slug,
			),
		)
	);
}
add_action( 'pre_get_posts', 'uleam_noticia_archive_query' );
