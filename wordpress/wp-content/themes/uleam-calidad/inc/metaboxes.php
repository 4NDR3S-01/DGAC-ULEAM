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
	echo '<p>Sube el archivo o elígelo de la Biblioteca de medios (PDF, Word, Excel, PowerPoint):</p>';
	echo '<p><button type="button" class="button button-primary" id="uleam-documento-elegir"><span class="dashicons dashicons-upload" style="vertical-align:text-bottom"></span> Elegir o subir archivo</button></p>';
	echo '<input type="url" name="documento_url" id="uleam-documento-url" value="' . esc_attr( $url ) . '" style="width:100%;padding:8px" placeholder="https://..." />';
	echo '<p class="description">También puedes pegar un enlace externo (por ejemplo, de Google Drive).</p>';
}

/**
 * Carga la Biblioteca de medios y el botón "Elegir o subir archivo" en el editor de Documentos.
 *
 * @param string $hook Pantalla actual del administrador.
 */
function uleam_documento_media_scripts( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'documento' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script(
		'media-editor',
		"jQuery(function($){
			var frame;
			$(document).on('click','#uleam-documento-elegir',function(e){
				e.preventDefault();
				if(!frame){
					frame=wp.media({title:'Elegir archivo del documento',button:{text:'Usar este archivo'},multiple:false});
					frame.on('select',function(){
						var file=frame.state().get('selection').first().toJSON();
						$('#uleam-documento-url').val(file.url);
						var title=$('.editor-post-title__input, #title');
						if(title.length && !title.first().text().trim() && !title.first().val()){
							// Sin título aún: proponer el nombre del archivo.
							if(window.wp && wp.data && wp.data.dispatch('core/editor')){
								wp.data.dispatch('core/editor').editPost({title:file.title});
							}
						}
					});
				}
				frame.open();
			});
		});"
	);
}
add_action( 'admin_enqueue_scripts', 'uleam_documento_media_scripts' );

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

/**
 * Campo "Icono de la cabecera" en las páginas (insignia sobre el título).
 */
function uleam_pagina_icono_metabox() {
	add_meta_box( 'uleam_icono_cabecera', 'Icono de la cabecera', 'uleam_pagina_icono_html', 'page', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'uleam_pagina_icono_metabox' );

/**
 * Renderiza el campo del icono de cabecera.
 *
 * @param WP_Post $post Página actual.
 */
function uleam_pagina_icono_html( $post ) {
	$icono = get_post_meta( $post->ID, '_uleam_icono_cabecera', true );
	wp_nonce_field( 'uleam_icono_cabecera', 'uleam_icono_cabecera_nonce' );
	echo '<input type="text" name="uleam_icono_cabecera" value="' . esc_attr( $icono ) . '" style="width:100%" placeholder="fa-solid fa-award" />';
	echo '<p class="description">Opcional. Icono que acompaña al nombre sobre el título. Búscalo en <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" rel="noopener">Font Awesome</a> y copia su clase.</p>';
}

/**
 * Guarda el icono de cabecera de la página.
 *
 * @param int $post_id ID de la página.
 */
function uleam_save_pagina_icono( $post_id ) {
	if ( ! isset( $_POST['uleam_icono_cabecera_nonce'] ) || ! wp_verify_nonce( $_POST['uleam_icono_cabecera_nonce'], 'uleam_icono_cabecera' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['uleam_icono_cabecera'] ) ) {
		update_post_meta( $post_id, '_uleam_icono_cabecera', sanitize_text_field( wp_unslash( $_POST['uleam_icono_cabecera'] ) ) );
	}
}
add_action( 'save_post_page', 'uleam_save_pagina_icono' );
