<?php
/**
 * Funciones del tema ULEAM Calidad
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Configuración base del tema
 * ---------------------------------------------------------------------- */
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
		)
	);
}
add_action( 'after_setup_theme', 'uleam_setup' );

/* -------------------------------------------------------------------------
 * Estilos y scripts
 * ---------------------------------------------------------------------- */
function uleam_scripts() {
	wp_enqueue_style( 'uleam-style', get_stylesheet_uri(), array(), '1.0.0' );
	wp_enqueue_script( 'uleam-main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'uleam_scripts' );

/* -------------------------------------------------------------------------
 * Tipos de contenido personalizados
 * ---------------------------------------------------------------------- */
function uleam_register_post_types() {
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

/* -------------------------------------------------------------------------
 * Taxonomías
 * ---------------------------------------------------------------------- */
function uleam_register_taxonomies() {
	register_taxonomy(
		'tipo_documento',
		'documento',
		array(
			'label'        => 'Tipo de documento',
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);
	register_taxonomy(
		'proceso',
		'documento',
		array(
			'label'        => 'Proceso',
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);
	register_taxonomy(
		'anio',
		'documento',
		array(
			'label'        => 'Año',
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);
	register_taxonomy(
		'periodo',
		'evaluacion',
		array(
			'label'        => 'Período',
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'uleam_register_taxonomies' );

/* -------------------------------------------------------------------------
 * Metabox: URL del archivo del documento
 * ---------------------------------------------------------------------- */
function uleam_documento_metabox() {
	add_meta_box( 'uleam_documento_url', 'Archivo del documento', 'uleam_documento_url_html', 'documento', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'uleam_documento_metabox' );

function uleam_documento_url_html( $post ) {
	$url = get_post_meta( $post->ID, '_documento_url', true );
	wp_nonce_field( 'uleam_documento_url', 'uleam_documento_url_nonce' );
	echo '<p>URL del archivo (PDF, XLSX, etc.):</p>';
	echo '<input type="url" name="documento_url" value="' . esc_attr( $url ) . '" style="width:100%;padding:8px" placeholder="https://..." />';
}

function uleam_save_documento_url( $post_id ) {
	if ( ! isset( $_POST['uleam_documento_url_nonce'] ) || ! wp_verify_nonce( $_POST['uleam_documento_url_nonce'], 'uleam_documento_url' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['documento_url'] ) ) {
		update_post_meta( $post_id, '_documento_url', esc_url_raw( $_POST['documento_url'] ) );
	}
}
add_action( 'save_post', 'uleam_save_documento_url' );

/* -------------------------------------------------------------------------
 * Textos editables (Personalizador)
 * ---------------------------------------------------------------------- */
function uleam_defaults() {
	return array(
		'uleam_hero_titulo'    => 'Trabajamos por una<br>cultura de <span>calidad</span>',
		'uleam_hero_subtitulo' => 'Promovemos la gestión, evaluación y mejora continua de los procesos académicos y administrativos de la Universidad.',
		'uleam_telefono'       => '05 2623 740 ext. 181 / 182',
		'uleam_email'          => 'calidad@uleam.edu.ec',
		'uleam_direccion'      => 'Av. Circunvalación - Vía a San Mateo, Manta - Manabí - Ecuador',
		'uleam_sobre'          => 'Somos responsables de promover y asegurar la calidad institucional a través de la gestión, evaluación y mejora continua de los procesos.',
		'uleam_hero_imagen'    => '',
	);
}

function uleam_opt( $id ) {
	$defaults = uleam_defaults();
	$default  = isset( $defaults[ $id ] ) ? $defaults[ $id ] : '';
	return get_theme_mod( $id, $default );
}

function uleam_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'uleam_contenido',
		array(
			'title'    => 'ULEAM - Contenido del sitio',
			'priority' => 30,
		)
	);

	$campos = array(
		'uleam_hero_titulo'    => array( 'label' => 'Título del hero (admite HTML)', 'type' => 'textarea' ),
		'uleam_hero_subtitulo' => array( 'label' => 'Subtítulo del hero', 'type' => 'textarea' ),
		'uleam_telefono'       => array( 'label' => 'Teléfono', 'type' => 'text' ),
		'uleam_email'          => array( 'label' => 'Correo', 'type' => 'text' ),
		'uleam_direccion'      => array( 'label' => 'Dirección', 'type' => 'text' ),
		'uleam_sobre'          => array( 'label' => 'Texto "Sobre la Dirección" (footer)', 'type' => 'textarea' ),
		'uleam_hero_imagen'    => array( 'label' => 'Imagen del hero (portada)', 'type' => 'image' ),
	);

	$defaults = uleam_defaults();

	foreach ( $campos as $id => $cfg ) {
		$sanitize = 'sanitize_text_field';
		if ( 'textarea' === $cfg['type'] ) {
			$sanitize = 'wp_kses_post';
		} elseif ( 'image' === $cfg['type'] ) {
			$sanitize = 'absint';
		}
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ],
				'sanitize_callback' => $sanitize,
			)
		);
		if ( 'image' === $cfg['type'] ) {
			$wp_customize->add_control(
				new WP_Customize_Cropped_Image_Control(
					$wp_customize,
					$id,
					array(
						'label'       => $cfg['label'],
						'section'     => 'uleam_contenido',
						'width'       => 1600,
						'height'      => 600,
						'flex_width'  => true,
						'flex_height' => true,
					)
				)
			);
		} else {
			$wp_customize->add_control(
				$id,
				array(
					'label'   => $cfg['label'],
					'section' => 'uleam_contenido',
					'type'    => $cfg['type'],
				)
			);
		}
	}
}
add_action( 'customize_register', 'uleam_customize_register' );

/* -------------------------------------------------------------------------
 * Menú por defecto (anclas de la portada)
 * ---------------------------------------------------------------------- */
function uleam_menu_fallback() {
	echo '<div class="nav-inner">';
	echo '<a class="active" href="' . esc_url( home_url( '/#inicio' ) ) . '">Inicio</a>';
	echo '<a href="' . esc_url( home_url( '/#direccion' ) ) . '">La Dirección</a>';
	echo '<a href="' . esc_url( home_url( '/#procesos' ) ) . '">Procesos</a>';
	echo '<a href="' . esc_url( home_url( '/#calidad' ) ) . '">Aseguramiento de la Calidad</a>';
	echo '<a href="' . esc_url( home_url( '/#desempeno' ) ) . '">Evaluación de Desempeño</a>';
	echo '<a href="' . esc_url( home_url( '/#documentos' ) ) . '">Documentos</a>';
	echo '<a href="' . esc_url( home_url( '/#noticias' ) ) . '">Noticias</a>';
	echo '<a href="' . esc_url( home_url( '/#contacto' ) ) . '">Contacto</a>';
	echo '</div>';
}
