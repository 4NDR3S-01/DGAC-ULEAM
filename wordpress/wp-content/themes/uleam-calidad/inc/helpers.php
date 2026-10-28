<?php
/**
 * Funciones auxiliares y helpers del tema
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Genera el markup de icono Font Awesome a partir de un identificador lógico.
 *
 * @param string $name Nombre lógico del icono.
 * @return string Markup HTML del icono.
 */
function uleam_icon( $name ) {
	$map = array(
		'chart'     => 'fa-solid fa-chart-column',
		'clipboard' => 'fa-solid fa-clipboard-list',
		'refresh'   => 'fa-solid fa-arrows-rotate',
		'folder'    => 'fa-solid fa-folder-open',
		'graduate'  => 'fa-solid fa-graduation-cap',
		'mail'      => 'fa-solid fa-envelope',
		'file'      => 'fa-solid fa-file-lines',
		'search'    => 'fa-solid fa-magnifying-glass',
		'phone'     => 'fa-solid fa-phone',
		'location'  => 'fa-solid fa-location-dot',
		'arrow'     => 'fa-solid fa-arrow-right',
		'download'  => 'fa-solid fa-download',
		'calendar'  => 'fa-regular fa-calendar',
	);

	if ( ! isset( $map[ $name ] ) ) {
		return '';
	}

	return '<i class="' . esc_attr( $map[ $name ] ) . '" aria-hidden="true"></i>';
}

/**
 * Convierte un enlace que empieza con "/" (p. ej. "/poa/" o "/wp-content/uploads/…") en la dirección
 * completa del sitio. Así los enlaces siguen funcionando aunque el sitio viva en una subcarpeta
 * (departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/).
 *
 * @param string $u Enlace.
 * @return string
 */
function uleam_url( $u ) {
	if ( ! is_string( $u ) || ! preg_match( '#^/(?!/)#', $u ) ) {
		return $u;
	}
	if ( 0 === strpos( $u, '/wp-content/uploads/' ) ) {
		return wp_upload_dir()['baseurl'] . substr( $u, strlen( '/wp-content/uploads' ) );
	}
	return home_url( $u );
}

/**
 * Adapta una URL local guardada en el contenido al host usado para esta visita.
 *
 * Los documentos importados pueden conservar enlaces absolutos de la instalación
 * local de desarrollo. En la red, esos enlaces deben apuntar a la IP actual.
 *
 * @param string $url URL.
 * @return string
 */
function uleam_url_local_actual( $url ) {
	if ( ! is_string( $url ) || '' === $url ) {
		return $url;
	}
	$partes = wp_parse_url( $url );
	$host   = isset( $partes['host'] ) ? strtolower( $partes['host'] ) : '';
	if ( ! in_array( $host, array( 'localhost', '127.0.0.1' ), true ) ) {
		return $url;
	}
	$ruta = isset( $partes['path'] ) ? $partes['path'] : '/';
	if ( isset( $partes['query'] ) ) {
		$ruta .= '?' . $partes['query'];
	}
	if ( isset( $partes['fragment'] ) ) {
		$ruta .= '#' . $partes['fragment'];
	}
	return home_url( $ruta );
}

/**
 * Aplica uleam_url() a los enlaces del contenido escrito en el editor.
 *
 * @param string $html Contenido.
 * @return string
 */
function uleam_url_contenido( $html ) {
	if ( false === strpos( $html, '="/' ) ) {
		return $html;
	}
	return preg_replace_callback(
		'#\b(href|src)=(["\'])(/(?!/)[^"\']*)\2#',
		function ( $m ) {
			return $m[1] . '=' . $m[2] . esc_url( uleam_url( html_entity_decode( $m[3] ) ) ) . $m[2];
		},
		$html
	);
}
add_filter( 'the_content', 'uleam_url_contenido', 20 );

/**
 * Enlaces personalizados de los menús escritos como "/ruta/".
 *
 * @param array $atts Atributos del enlace.
 * @return array
 */
function uleam_url_menu( $atts ) {
	if ( isset( $atts['href'] ) ) {
		$atts['href'] = uleam_url( $atts['href'] );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'uleam_url_menu', 5 );

/**
 * Color del borde izquierdo de un banner (promedio de su primera columna), para continuar el
 * fondo de la cabecera sin que se note la unión con la imagen. Se guarda en la imagen.
 *
 * @param int $adjunto_id ID de la imagen.
 * @return string Color hexadecimal o '' si no se puede leer.
 */
function uleam_color_borde_banner( $adjunto_id ) {
	$archivo = get_attached_file( $adjunto_id );
	$version = $archivo && file_exists( $archivo ) ? (string) filemtime( $archivo ) : '';
	$guardado = get_post_meta( $adjunto_id, '_uleam_color_borde', true );
	if ( is_array( $guardado ) && $guardado['version'] === $version ) {
		return $guardado['color'];
	}
	$color = '';
	if ( $version && function_exists( 'imagecreatefromstring' ) ) {
		$img = @imagecreatefromstring( (string) file_get_contents( $archivo ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( $img ) {
			$alto  = imagesy( $img );
			$suma  = array( 0, 0, 0 );
			$pasos = 40;
			for ( $i = 0; $i < $pasos; $i++ ) {
				$rgb      = imagecolorsforindex( $img, imagecolorat( $img, 2, (int) ( ( $i + .5 ) * $alto / $pasos ) ) );
				$suma[0] += $rgb['red'];
				$suma[1] += $rgb['green'];
				$suma[2] += $rgb['blue'];
			}
			imagedestroy( $img );
			$color = vsprintf( '#%02x%02x%02x', array_map( function ( $v ) use ( $pasos ) {
				return (int) round( $v / $pasos );
			}, $suma ) );
		}
	}
	update_post_meta( $adjunto_id, '_uleam_color_borde', array( 'version' => $version, 'color' => $color ) );
	return $color;
}
