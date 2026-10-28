<?php
/**
 * wp-config.php del servidor: departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/
 *
 * Completar las 4 líneas marcadas con «COMPLETAR» (los datos los da TI) y las claves secretas.
 * Este archivo NO se sube a git.
 */

// ** Base de datos ** //
define( 'DB_NAME', 'COMPLETAR_nombre_bd' );
define( 'DB_USER', 'COMPLETAR_usuario_bd' );
define( 'DB_PASSWORD', 'COMPLETAR_contrasena_bd' );
define( 'DB_HOST', 'localhost' ); // COMPLETAR si TI indica otro servidor (p. ej. 10.0.0.5:3306).
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/*
 * Claves secretas: generar unas NUEVAS en https://api.wordpress.org/secret-key/1.1/salt/
 * y pegarlas aquí en lugar de estas 8 líneas (no reutilizar las de la copia local).
 */
define( 'AUTH_KEY',         'COMPLETAR' );
define( 'SECURE_AUTH_KEY',  'COMPLETAR' );
define( 'LOGGED_IN_KEY',    'COMPLETAR' );
define( 'NONCE_KEY',        'COMPLETAR' );
define( 'AUTH_SALT',        'COMPLETAR' );
define( 'SECURE_AUTH_SALT', 'COMPLETAR' );
define( 'LOGGED_IN_SALT',   'COMPLETAR' );
define( 'NONCE_SALT',       'COMPLETAR' );

$table_prefix = 'wp_';

// Dirección fija del sitio (con la subcarpeta). En local se arma con el host; aquí no.
define( 'WP_HOME', 'https://departamentos.uleam.edu.ec/gestion-aseguramiento-calidad' );
define( 'WP_SITEURL', 'https://departamentos.uleam.edu.ec/gestion-aseguramiento-calidad' );

// Detrás de un proxy/balanceador HTTPS la petición puede llegar como http.
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}
define( 'FORCE_SSL_ADMIN', true );

// Seguridad: el sitio anterior fue hackeado. Nadie edita PHP desde el panel.
// (Plugins y actualizaciones siguen funcionando; sólo se oculta el editor de archivos.)
define( 'DISALLOW_FILE_EDIT', true );

// El personal no necesita cientos de revisiones por página.
define( 'WP_POST_REVISIONS', 15 );

define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );

/* That's all, stop editing! Happy publishing. */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
