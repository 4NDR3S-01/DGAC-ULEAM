<?php
/**
 * Redirecciones del sitio anterior
 *
 * Este sitio reemplaza a departamentos.uleam.edu.ec/gestion-aseguramiento-calidad/. Para que los enlaces
 * antiguos (Google, otras páginas de la ULEAM, correos) sigan funcionando, cuando una dirección no existe
 * se busca su equivalente, en este orden:
 *   1. La tabla editable en Herramientas → Redirecciones (ruta antigua => ruta nueva).
 *   2. Archivos /files/AAAA/MM/… → la misma ruta en wp-content/uploads (aunque cambien tildes o mayúsculas).
 *   3. Procedimientos: la dirección original guardada en cada procedimiento (_procedimiento_origen).
 *   4. Noticias con fecha /AAAA/MM/DD/nombre/ (o la dirección corta /nombre/) → la noticia con ese nombre.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ULEAM_REDIR_OPT = 'uleam_redirecciones';

/**
 * Texto de la tabla (lo guardado o, si nunca se guardó, la lista inicial del tema).
 *
 * @return string
 */
function uleam_redir_texto() {
	$t = get_option( ULEAM_REDIR_OPT, null );
	if ( null === $t ) {
		$f = ULEAM_THEME_DIR . '/inc/data/redirecciones.txt';
		$t = is_readable( $f ) ? (string) file_get_contents( $f ) : '';
	}
	return (string) $t;
}

/**
 * Normaliza una ruta para comparar: sin dominio ni consulta, decodificada, en minúsculas y con barras.
 *
 * @param string $ruta Ruta o URL.
 * @return string
 */
function uleam_redir_norm( $ruta ) {
	$ruta = rawurldecode( (string) wp_parse_url( trim( $ruta ), PHP_URL_PATH ) );
	$ruta = '/' . trim( mb_strtolower( $ruta ), '/' ) . '/';
	return '//' === $ruta ? '/' : $ruta;
}

/**
 * Tabla ruta antigua => destino.
 *
 * @return array
 */
function uleam_redir_tabla() {
	static $tabla = null;
	if ( null !== $tabla ) {
		return $tabla;
	}
	$tabla = array();
	foreach ( preg_split( '/\R/', uleam_redir_texto() ) as $linea ) {
		$linea = trim( $linea );
		if ( '' === $linea || '#' === $linea[0] || false === strpos( $linea, '=>' ) ) {
			continue;
		}
		list( $de, $a ) = array_map( 'trim', explode( '=>', $linea, 2 ) );
		if ( '' !== $de && '' !== $a ) {
			$tabla[ uleam_redir_norm( $de ) ] = $a;
		}
	}
	return $tabla;
}

/**
 * Quita tildes y mayúsculas de un nombre de archivo para compararlo.
 *
 * @param string $s Nombre.
 * @return string
 */
function uleam_redir_clave_archivo( $s ) {
	return mb_strtolower( remove_accents( $s ) );
}

/**
 * Ruta relativa (AAAA/MM/archivo) de un archivo subido que corresponde a $rel, o '' si no existe.
 *
 * @param string $rel Ruta relativa a uploads, decodificada.
 * @return string
 */
function uleam_redir_archivo( $rel ) {
	$base = wp_upload_dir()['basedir'];
	$rel  = ltrim( str_replace( array( '..', '\\' ), '', $rel ), '/' );
	if ( '' === $rel ) {
		return '';
	}
	if ( is_file( $base . '/' . $rel ) ) {
		return $rel;
	}
	if ( class_exists( 'Normalizer' ) ) {
		foreach ( array( Normalizer::FORM_C, Normalizer::FORM_D ) as $forma ) {
			$alt = Normalizer::normalize( $rel, $forma );
			if ( $alt && is_file( $base . '/' . $alt ) ) {
				return $alt;
			}
		}
	}
	// Misma carpeta, mismo nombre sin tildes ni mayúsculas (el sitio anterior guardó algunos con "Ó", aquí con "O").
	$dir = dirname( $rel );
	if ( '.' === $dir || ! is_dir( $base . '/' . $dir ) ) {
		return '';
	}
	$buscado = uleam_redir_clave_archivo( basename( $rel ) );
	foreach ( (array) scandir( $base . '/' . $dir ) as $f ) {
		if ( uleam_redir_clave_archivo( $f ) === $buscado ) {
			return $dir . '/' . $f;
		}
	}
	return '';
}

/**
 * Destino de una ruta antigua (ruta relativa al sitio o URL completa), o '' si no hay equivalente.
 *
 * @param string $ruta Ruta pedida, relativa al inicio del sitio (p. ej. "/historia/").
 * @return string
 */
function uleam_redir_destino( $ruta ) {
	global $wpdb;
	$clave = uleam_redir_norm( $ruta );

	$tabla = uleam_redir_tabla();
	if ( isset( $tabla[ $clave ] ) ) {
		return $tabla[ $clave ];
	}

	// Archivos del sitio anterior.
	if ( preg_match( '#^/?(?:files|wp-content/uploads)/(.+)$#', rawurldecode( (string) wp_parse_url( $ruta, PHP_URL_PATH ) ), $m ) ) {
		$rel = uleam_redir_archivo( $m[1] );
		return $rel ? wp_upload_dir()['baseurl'] . '/' . implode( '/', array_map( 'rawurlencode', explode( '/', $rel ) ) ) : '';
	}

	// Procedimientos: dirección que tenían en el sitio anterior.
	$id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT p.ID FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
			WHERE m.meta_key = '_procedimiento_origen' AND p.post_status = 'publish' AND LOWER(m.meta_value) LIKE %s LIMIT 1",
			'%/gestion-aseguramiento-calidad' . $wpdb->esc_like( $clave )
		)
	);
	if ( $id ) {
		return get_permalink( $id );
	}

	// Noticias con fecha (/AAAA/MM/DD/nombre/) o con la dirección corta antigua (/nombre/).
	if ( preg_match( '#^(?:/\d{4}/\d{2}/\d{2})?/([^/]+)/$#', $clave, $m ) ) {
		$slug = sanitize_title( $m[1] );
		$q    = get_posts( array( 'post_type' => 'noticia', 'post_status' => 'publish', 'name' => $slug, 'posts_per_page' => 1, 'fields' => 'ids' ) );
		if ( ! $q ) {
			$q = get_posts( array( 'post_type' => 'noticia', 'post_status' => 'publish', 'meta_key' => '_wp_old_slug', 'meta_value' => $slug, 'posts_per_page' => 1, 'fields' => 'ids' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		if ( $q ) {
			return get_permalink( $q[0] );
		}
	}
	return '';
}

/**
 * Convierte un destino de la tabla ("/poa/", "/x/#ancla" o una URL completa) en URL absoluta.
 *
 * @param string $destino Destino.
 * @return string
 */
function uleam_redir_url( $destino ) {
	return preg_match( '#^https?://#i', $destino ) ? $destino : home_url( '/' . ltrim( $destino, '/' ) );
}

/**
 * Redirige (301) las direcciones antiguas que hoy darían "página no encontrada".
 */
function uleam_redir_404() {
	if ( ! is_404() ) {
		return;
	}
	$pedida = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$base   = rtrim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $base && 0 === strpos( $pedida, $base . '/' ) ) {
		$pedida = substr( $pedida, strlen( $base ) );
	}
	$destino = uleam_redir_destino( $pedida );
	if ( '' === $destino ) {
		return;
	}
	$url = uleam_redir_url( $destino );
	if ( uleam_redir_norm( $url ) === uleam_redir_norm( home_url( $pedida ) ) ) {
		return; // Evita bucles si el destino es la misma página.
	}
	wp_redirect( $url, 301, 'ULEAM' ); // phpcs:ignore WordPress.Security.SafeRedirect -- la tabla la mantiene el personal y puede apuntar a otros dominios.
	exit;
}
add_action( 'template_redirect', 'uleam_redir_404', 1 ); // Antes de que WordPress intente adivinar.

/**
 * Pantalla Herramientas → Redirecciones.
 */
function uleam_redir_menu() {
	add_management_page( 'Redirecciones del sitio anterior', 'Redirecciones', 'edit_pages', 'uleam-redirecciones', 'uleam_redir_pantalla' );
}
add_action( 'admin_menu', 'uleam_redir_menu' );

/**
 * Contenido de la pantalla de redirecciones.
 */
function uleam_redir_pantalla() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$guardado = false;
	if ( isset( $_POST['uleam_redir_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['uleam_redir_nonce'] ), 'uleam_redir' ) ) {
		$texto = sanitize_textarea_field( wp_unslash( $_POST['uleam_redir_texto'] ?? '' ) );
		update_option( ULEAM_REDIR_OPT, $texto, false );
		$guardado = true;
	}
	$prueba  = isset( $_GET['probar'] ) ? sanitize_text_field( wp_unslash( $_GET['probar'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$destino = '' !== $prueba ? uleam_redir_destino( $prueba ) : '';
	?>
	<div class="wrap">
		<h1>Redirecciones del sitio anterior</h1>
		<?php if ( $guardado ) : ?>
			<div class="notice notice-success is-dismissible"><p>Redirecciones guardadas.</p></div>
		<?php endif; ?>
		<div class="notice notice-info inline"><p>
			Cuando alguien entra con una dirección del sitio anterior que ya no existe, se le lleva a su página nueva.
			Escribe una por línea: <code>/ruta-antigua/  =&gt;  /ruta-nueva/</code>. El destino puede llevar una pestaña
			(<code>/aseguramiento-de-la-calidad/#evaluacion-institucional</code>) o ser una dirección completa.
			Los procedimientos, las noticias con fecha y los archivos <code>/files/…</code> se resuelven solos.
		</p></div>

		<form method="get" style="margin:1em 0">
			<input type="hidden" name="page" value="uleam-redirecciones" />
			<label for="probar"><strong>Probar una dirección antigua:</strong></label>
			<input type="text" id="probar" name="probar" value="<?php echo esc_attr( $prueba ); ?>" class="regular-text" placeholder="/historia/" />
			<?php submit_button( 'Probar', 'secondary', '', false ); ?>
			<?php if ( '' !== $prueba ) : ?>
				<p><?php echo $destino ? 'Lleva a: <a href="' . esc_url( uleam_redir_url( $destino ) ) . '" target="_blank">' . esc_html( uleam_redir_url( $destino ) ) . '</a>' : 'No tiene equivalente: mostrará "página no encontrada".'; ?></p>
			<?php endif; ?>
		</form>

		<form method="post">
			<?php wp_nonce_field( 'uleam_redir', 'uleam_redir_nonce' ); ?>
			<textarea name="uleam_redir_texto" rows="24" class="large-text code"><?php echo esc_textarea( uleam_redir_texto() ); ?></textarea>
			<?php submit_button( 'Guardar redirecciones' ); ?>
		</form>
	</div>
	<?php
}
