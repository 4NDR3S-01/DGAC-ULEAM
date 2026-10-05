<?php
/**
 * Opciones editables del personalizador (Theme Customizer)
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valores predeterminados de las opciones institucionales
 *
 * @return array Opciones por defecto.
 */
function uleam_defaults() {
	return array(
		'uleam_hero_titulo'    => 'Trabajamos por una<br>cultura de <span>calidad</span>',
		'uleam_hero_subtitulo' => 'Promovemos la gestión, evaluación y mejora continua de los procesos académicos y administrativos de la Universidad.',
		'uleam_telefono'       => '05 2623 740 ext. 181 / 182',
		'uleam_email'          => 'calidad@uleam.edu.ec',
		'uleam_direccion'      => 'Av. Circunvalación - Vía a San Mateo, Manta - Manabí - Ecuador',
		'uleam_sobre'          => 'Somos responsables de promover y asegurar la calidad institucional a través de la gestión, evaluación y mejora continua de los procesos.',
		'uleam_hero_imagen'    => '',
		'uleam_hero_boton'     => 'Conócenos',
		'uleam_hero_boton_url' => '/mision-vision/',
		'uleam_facebook'       => 'https://www.facebook.com/UleamEc',
		'uleam_instagram'      => 'https://www.instagram.com/uleam_ecuador_oficial/',
		'uleam_tiktok'         => 'https://www.tiktok.com/@uleamecuador',
		'uleam_noticia_imagen' => '',
	);
}

/**
 * Obtiene el valor de una opción del tema
 *
 * @param string $id Identificador del campo.
 * @return mixed Valor de la opción.
 */
function uleam_opt( $id ) {
	$defaults = uleam_defaults();
	$default  = isset( $defaults[ $id ] ) ? $defaults[ $id ] : '';
	return get_theme_mod( $id, $default );
}

/**
 * Redes sociales configuradas en el personalizador (solo las que tienen enlace).
 *
 * @return array Lista de [ url, etiqueta, clase del icono ].
 */
function uleam_redes() {
	$redes = array(
		array( uleam_opt( 'uleam_facebook' ), 'Facebook', 'fa-brands fa-facebook-f' ),
		array( uleam_opt( 'uleam_instagram' ), 'Instagram', 'fa-brands fa-instagram' ),
		array( uleam_opt( 'uleam_tiktok' ), 'TikTok', 'fa-brands fa-tiktok' ),
	);
	return array_filter(
		$redes,
		function ( $r ) {
			return ! empty( $r[0] );
		}
	);
}

/**
 * Registro de controles y secciones en el personalizador
 *
 * @param WP_Customize_Manager $wp_customize Gestor del personalizador.
 */
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
		'uleam_hero_boton'     => array( 'label' => 'Texto del botón del hero', 'type' => 'text' ),
		'uleam_hero_boton_url' => array( 'label' => 'Enlace del botón del hero', 'type' => 'url' ),
		'uleam_facebook'       => array( 'label' => 'Facebook (dejar vacío para ocultar)', 'type' => 'url' ),
		'uleam_instagram'      => array( 'label' => 'Instagram (dejar vacío para ocultar)', 'type' => 'url' ),
		'uleam_tiktok'         => array( 'label' => 'TikTok (dejar vacío para ocultar)', 'type' => 'url' ),
		'uleam_noticia_imagen' => array( 'label' => 'Imagen predeterminada de noticias (para las que no tienen imagen)', 'type' => 'image' ),
	);

	$defaults = uleam_defaults();

	foreach ( $campos as $id => $cfg ) {
		$sanitize = 'sanitize_text_field';
		if ( 'textarea' === $cfg['type'] ) {
			$sanitize = 'wp_kses_post';
		} elseif ( 'image' === $cfg['type'] ) {
			$sanitize = 'absint';
		} elseif ( 'url' === $cfg['type'] ) {
			$sanitize = 'esc_url_raw';
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
