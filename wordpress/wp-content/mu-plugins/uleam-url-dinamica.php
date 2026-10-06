<?php
/**
 * Plugin Name: ULEAM - URLs dinámicas
 * Description: Al abrir el sitio desde otro host (túnel de pruebas, IP de la LAN), reescribe en la salida los enlaces guardados como http://localhost:8000. No modifica la base de datos.
 */

if ( ! isset( $_SERVER['HTTP_HOST'] ) || $_SERVER['HTTP_HOST'] === 'localhost:8000' ) {
	return;
}

// En el admin y la API REST no se reescribe: el editor guardaría la URL temporal en la BD.
$uleam_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
if ( is_admin() || strpos( $uleam_uri, '/wp-json/' ) !== false || strpos( $uleam_uri, 'rest_route=' ) !== false ) {
	return;
}
unset( $uleam_uri );

ob_start(
	function ( $html ) {
		$nueva = WP_HOME;
		return str_replace(
			array( 'http://localhost:8000', 'http:\/\/localhost:8000' ),
			array( $nueva, str_replace( '/', '\/', $nueva ) ),
			$html
		);
	}
);
