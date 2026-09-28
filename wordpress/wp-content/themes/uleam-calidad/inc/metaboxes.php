<?php
/**
 * Metaboxes para tipos de contenido personalizados
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra metabox para la URL del documento
 */
function uleam_documento_metabox() {
	add_meta_box(
		'uleam_documento_url',
		'Archivo del documento',
		'uleam_documento_url_html',
		'documento',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'uleam_documento_metabox' );

/**
 * Renderiza el HTML del metabox
 *
 * @param WP_Post $post Objeto del post actual.
 */
function uleam_documento_url_html( $post ) {
	$url = get_post_meta( $post->ID, '_documento_url', true );
	wp_nonce_field( 'uleam_documento_url', 'uleam_documento_url_nonce' );
	echo '<p>URL del archivo (PDF, XLSX, etc.):</p>';
	echo '<input type="url" name="documento_url" value="' . esc_attr( $url ) . '" style="width:100%;padding:8px" placeholder="https://..." />';
}

/**
 * Guarda el valor de la URL del documento
 *
 * @param int $post_id ID del post que se guarda.
 */
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
