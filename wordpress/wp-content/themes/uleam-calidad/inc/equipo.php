<?php
/**
 * Personal y carrusel de Recursos Humanos.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		register_post_type(
			'persona',
			array(
				'labels'       => array(
					'name'               => 'Personal',
					'singular_name'      => 'Persona',
					'add_new_item'       => 'Añadir persona',
					'edit_item'          => 'Editar persona',
					'featured_image'     => 'Foto',
					'set_featured_image' => 'Elegir foto',
					'remove_featured_image' => 'Quitar foto',
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-groups',
				'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			)
		);
	}
);

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'uleam_persona_extra',
			'Cargo y destacado',
			function ( $post ) {
				wp_nonce_field( 'uleam_persona_extra', 'uleam_persona_nonce' );
				echo '<p><label for="uleam_persona_cargo">Cargo</label><input id="uleam_persona_cargo" type="text" name="uleam_persona_cargo" class="widefat" value="' . esc_attr( get_post_meta( $post->ID, '_uleam_persona_cargo', true ) ) . '" placeholder="Ej. Analista de talento humano"></p>';
				echo '<p><label><input type="checkbox" name="uleam_persona_principal" value="1"' . checked( get_post_meta( $post->ID, '_uleam_persona_principal', true ), '1', false ) . '> Autoridad principal</label></p>';
			},
			'persona',
			'side'
		);
	}
);

add_action(
	'save_post_persona',
	function ( $post_id ) {
		if (
			! isset( $_POST['uleam_persona_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['uleam_persona_nonce'] ) ), 'uleam_persona_extra' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			|| ! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		update_post_meta( $post_id, '_uleam_persona_cargo', sanitize_text_field( wp_unslash( $_POST['uleam_persona_cargo'] ?? '' ) ) );
		update_post_meta( $post_id, '_uleam_persona_principal', empty( $_POST['uleam_persona_principal'] ) ? '' : '1' );
	}
);

function uleam_equipo_tarjeta( $persona, $principal = false ) {
	$foto = get_the_post_thumbnail_url( $persona->ID, 'medium_large' );
	$clase = $principal ? ' perfil-destacado' : ' perfil';
	$imagen = $foto
		? '<div class="perfil-foto"><img src="' . esc_url( $foto ) . '" alt="' . esc_attr( $persona->post_title ) . '" loading="lazy"></div>'
		: '<div class="perfil-foto perfil-foto--inicial" aria-hidden="true">' . esc_html( strtoupper( substr( $persona->post_title, 0, 1 ) ) ) . '</div>';
	$cargo = get_post_meta( $persona->ID, '_uleam_persona_cargo', true );
	return '<div class="' . esc_attr( $clase ) . '"><div class="wp-block-group__inner-container">' . $imagen . '<h3>' . esc_html( $persona->post_title ) . '</h3>' . ( $cargo ? '<p class="perfil-cargo">' . esc_html( $cargo ) . '</p>' : '' ) . '</div></div>';
}

add_shortcode(
	'equipo',
	function () {
		$personas = get_posts(
			array(
				'post_type'      => 'persona',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			)
		);
		if ( ! $personas ) {
			return current_user_can( 'edit_posts' ) ? '<p class="doc-vacio">Añade personas en WordPress → Personal para mostrar el carrusel.</p>' : '';
		}

		$principal = null;
		$resto = array();
		foreach ( $personas as $persona ) {
			if ( ! $principal && '1' === get_post_meta( $persona->ID, '_uleam_persona_principal', true ) ) {
				$principal = $persona;
			} else {
				$resto[] = $persona;
			}
		}

		$html = '<section class="equipo-recursos" aria-labelledby="equipo-recursos-titulo">';
		if ( $principal ) {
			$html .= '<div class="equipo-recursos__principal">' . uleam_equipo_tarjeta( $principal, true ) . '</div>';
		}
		if ( $resto ) {
			$html .= '<div class="equipo-recursos__carrusel">';
			$html .= '<div class="equipo-recursos__titulo"><span class="equipo-recursos__kicker">PERSONAL DGAC</span><h2 id="equipo-recursos-titulo">Equipo de Recursos Humanos</h2><p>Conoce a las personas que forman parte de nuestra dirección.</p></div>';
			$html .= '<div class="carrusel"><button type="button" class="carrusel-btn carrusel-prev" aria-label="Personal anterior"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>';
			$html .= '<ul class="carrusel-pista" tabindex="0" aria-label="Personal de Recursos Humanos">';
			foreach ( $resto as $persona ) {
				$html .= '<li>' . uleam_equipo_tarjeta( $persona ) . '</li>';
			}
			$html .= '</ul><button type="button" class="carrusel-btn carrusel-next" aria-label="Personal siguiente"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button><button type="button" class="carrusel-pausa" aria-label="Pausar carrusel" aria-pressed="false"><i class="fa-solid fa-pause" aria-hidden="true"></i></button></div></div>';
		}
		$html .= '</section>';
		return $html;
	}
);
