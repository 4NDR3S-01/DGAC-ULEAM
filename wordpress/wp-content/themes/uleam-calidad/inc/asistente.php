<?php
/**
 * Asistente virtual (chat) para encontrar documentos y resolver consultas frecuentes.
 *
 * Funciona sin IA: busca en Documentos y Páginas y responde con las "Preguntas del asistente"
 * que el personal escribe en el administrador. Si en Asistente virtual → Configuración se pega
 * una clave de API de Claude (o se define ULEAM_CLAUDE_API_KEY en wp-config.php), además redacta
 * la respuesta en lenguaje natural usando solo ese contenido del sitio.
 *
 * Endpoint: POST /wp-json/uleam/v1/asistente  { mensaje, historial: [ {rol, texto} ] }
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Preguntas frecuentes (editables) y configuración
 * ---------------------------------------------------------------------- */

/**
 * Tipo de contenido "Preguntas del asistente": título = pregunta, contenido = respuesta.
 */
function uleam_asistente_register() {
	register_post_type(
		'pregunta_frecuente',
		array(
			'labels'        => array(
				'name'          => 'Preguntas del asistente',
				'singular_name' => 'Pregunta del asistente',
				'menu_name'     => 'Asistente virtual',
				'all_items'     => 'Preguntas y respuestas',
				'add_new'       => 'Añadir pregunta',
				'add_new_item'  => 'Añadir pregunta y respuesta',
				'edit_item'     => 'Editar pregunta',
				'search_items'  => 'Buscar preguntas',
				'not_found'     => 'Aún no hay preguntas',
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-format-chat',
			'menu_position' => 26,
			'supports'      => array( 'title', 'editor' ),
		)
	);
}
add_action( 'init', 'uleam_asistente_register' );

/**
 * Configuración guardada, con valores predeterminados.
 *
 * @return array
 */
function uleam_asistente_config() {
	$cfg = get_option( 'uleam_asistente', array() );
	return wp_parse_args(
		is_array( $cfg ) ? $cfg : array(),
		array(
			'activo'      => 1,
			'nombre'      => 'Asistente DGAC',
			'bienvenida'  => "¡Hola! Soy el asistente virtual de la Dirección de Gestión y Aseguramiento de la Calidad.\nPuedo ayudarte a encontrar documentos (formatos, informes, manuales, POA…) y resolver dudas frecuentes. ¿Qué necesitas?",
			'sugerencias' => "POA 2025\nFormatos de autoevaluación de carreras\nHorario de atención\n¿Cómo contacto a la Dirección?",
			'modelo'      => 'claude-opus-5-5',
			'api_key'     => '',
		)
	);
}

/**
 * Clave de API de Claude: la constante de wp-config.php tiene prioridad sobre la guardada.
 *
 * @return string
 */
function uleam_asistente_api_key() {
	if ( defined( 'ULEAM_CLAUDE_API_KEY' ) && ULEAM_CLAUDE_API_KEY ) {
		return ULEAM_CLAUDE_API_KEY;
	}
	return (string) uleam_asistente_config()['api_key'];
}

/**
 * Modelos que se pueden elegir (id => etiqueta).
 *
 * @return array
 */
function uleam_asistente_modelos() {
	return array(
		'claude-opus-5-5'   => 'Claude Opus 5.5 — mejores respuestas (recomendado)',
		'claude-sonnet-5-5' => 'Claude Sonnet 5.5 — más económico',
		'claude-haiku-4-5'  => 'Claude Haiku 4.5 — el más económico y rápido',
	);
}

/**
 * Campo "Otras formas de preguntarlo" en cada pregunta.
 */
function uleam_asistente_metabox() {
	add_meta_box( 'uleam_asistente_variantes', 'Otras formas de preguntarlo', 'uleam_asistente_metabox_html', 'pregunta_frecuente', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'uleam_asistente_metabox' );

/**
 * Renderiza el campo de variantes.
 *
 * @param WP_Post $post Pregunta.
 */
function uleam_asistente_metabox_html( $post ) {
	wp_nonce_field( 'uleam_asistente_variantes', 'uleam_asistente_variantes_nonce' );
	echo '<textarea name="uleam_asistente_variantes" rows="5" style="width:100%" placeholder="¿A qué hora atienden?&#10;horario de oficina">' . esc_textarea( get_post_meta( $post->ID, '_asistente_variantes', true ) ) . '</textarea>';
	echo '<p class="description">Una por línea. Escribe cómo lo preguntaría la gente (palabras clave o frases). El título y estas frases se usan para reconocer la pregunta; el contenido es la respuesta que verá la persona.</p>';
}

/**
 * Guarda las variantes.
 *
 * @param int $post_id ID de la pregunta.
 */
function uleam_asistente_metabox_save( $post_id ) {
	if ( ! isset( $_POST['uleam_asistente_variantes_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['uleam_asistente_variantes_nonce'] ), 'uleam_asistente_variantes' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['uleam_asistente_variantes'] ) ) {
		update_post_meta( $post_id, '_asistente_variantes', sanitize_textarea_field( wp_unslash( $_POST['uleam_asistente_variantes'] ) ) );
	}
}
add_action( 'save_post_pregunta_frecuente', 'uleam_asistente_metabox_save' );

/**
 * Página Asistente virtual → Configuración.
 */
function uleam_asistente_menu() {
	add_submenu_page( 'edit.php?post_type=pregunta_frecuente', 'Configuración del asistente', 'Configuración', 'manage_options', 'uleam-asistente', 'uleam_asistente_pagina' );
}
add_action( 'admin_menu', 'uleam_asistente_menu' );

/**
 * Guarda la configuración y vacía el registro de preguntas sin respuesta.
 */
function uleam_asistente_guardar() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Sin permisos.' );
	}
	check_admin_referer( 'uleam_asistente_config' );
	$cfg = uleam_asistente_config();
	if ( isset( $_POST['vaciar_registro'] ) ) {
		delete_option( 'uleam_asistente_sin_respuesta' );
	} else {
		$cfg['activo']      = empty( $_POST['activo'] ) ? 0 : 1;
		$cfg['nombre']      = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
		$cfg['bienvenida']  = sanitize_textarea_field( wp_unslash( $_POST['bienvenida'] ?? '' ) );
		$cfg['sugerencias'] = sanitize_textarea_field( wp_unslash( $_POST['sugerencias'] ?? '' ) );
		$modelo             = sanitize_text_field( wp_unslash( $_POST['modelo'] ?? '' ) );
		$cfg['modelo']      = isset( uleam_asistente_modelos()[ $modelo ] ) ? $modelo : 'claude-opus-5-5';
		$clave              = trim( sanitize_text_field( wp_unslash( $_POST['api_key'] ?? '' ) ) );
		if ( ! empty( $_POST['borrar_clave'] ) ) {
			$cfg['api_key'] = '';
		} elseif ( '' !== $clave ) {
			$cfg['api_key'] = $clave;
		}
	}
	// Sin autoload: la clave no se carga en cada visita.
	update_option( 'uleam_asistente', $cfg, false );
	wp_safe_redirect( admin_url( 'edit.php?post_type=pregunta_frecuente&page=uleam-asistente&guardado=1' ) );
	exit;
}
add_action( 'admin_post_uleam_asistente_guardar', 'uleam_asistente_guardar' );

/**
 * Renderiza la página de configuración.
 */
function uleam_asistente_pagina() {
	$cfg       = uleam_asistente_config();
	$constante = defined( 'ULEAM_CLAUDE_API_KEY' ) && ULEAM_CLAUDE_API_KEY;
	$registro  = get_option( 'uleam_asistente_sin_respuesta', array() );
	?>
	<div class="wrap">
		<h1>Configuración del asistente virtual</h1>
		<?php if ( isset( $_GET['guardado'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p>Cambios guardados.</p></div>
		<?php endif; ?>
		<p>El asistente aparece como un botón de chat en la esquina inferior de todas las páginas. Busca en los <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=documento' ) ); ?>">Documentos</a> y páginas del sitio, y responde con las <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pregunta_frecuente' ) ); ?>">Preguntas y respuestas</a> que escribas.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="uleam_asistente_guardar" />
			<?php wp_nonce_field( 'uleam_asistente_config' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">Mostrar el asistente</th>
					<td><label><input type="checkbox" name="activo" value="1" <?php checked( $cfg['activo'] ); ?> /> Activado en el sitio</label></td>
				</tr>
				<tr>
					<th scope="row"><label for="ua-nombre">Nombre</label></th>
					<td><input type="text" id="ua-nombre" name="nombre" class="regular-text" value="<?php echo esc_attr( $cfg['nombre'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="ua-bienvenida">Mensaje de bienvenida</label></th>
					<td><textarea id="ua-bienvenida" name="bienvenida" rows="4" class="large-text"><?php echo esc_textarea( $cfg['bienvenida'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="ua-sugerencias">Sugerencias rápidas</label></th>
					<td>
						<textarea id="ua-sugerencias" name="sugerencias" rows="4" class="large-text"><?php echo esc_textarea( $cfg['sugerencias'] ); ?></textarea>
						<p class="description">Una por línea. Aparecen como botones al abrir el chat.</p>
					</td>
				</tr>
			</table>

			<h2>Respuestas con inteligencia artificial (opcional)</h2>
			<p>Sin clave, el asistente responde con las preguntas frecuentes y muestra los documentos encontrados. Con una clave de <a href="https://console.anthropic.com/" target="_blank" rel="noopener">Claude (Anthropic)</a>, además redacta la respuesta en lenguaje natural usando <strong>solo</strong> el contenido de este sitio. Cada consulta tiene un costo según el uso, y el texto de la consulta se envía a Anthropic para procesarlo.</p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="ua-clave">Clave de API</label></th>
					<td>
						<?php if ( $constante ) : ?>
							<p><span class="dashicons dashicons-lock"></span> Definida en <code>wp-config.php</code> (<code>ULEAM_CLAUDE_API_KEY</code>).</p>
						<?php else : ?>
							<input type="password" id="ua-clave" name="api_key" class="regular-text" autocomplete="off" placeholder="<?php echo $cfg['api_key'] ? esc_attr( 'Guardada (termina en …' . substr( $cfg['api_key'], -4 ) . ')' ) : 'sk-ant-…'; ?>" />
							<p class="description">Déjala vacía para conservar la actual. Más seguro: definir <code>define( 'ULEAM_CLAUDE_API_KEY', '…' );</code> en <code>wp-config.php</code>.</p>
							<?php if ( $cfg['api_key'] ) : ?>
								<label><input type="checkbox" name="borrar_clave" value="1" /> Quitar la clave (volver a respuestas sin IA)</label>
							<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ua-modelo">Modelo</label></th>
					<td>
						<select id="ua-modelo" name="modelo">
							<?php foreach ( uleam_asistente_modelos() as $id => $etiqueta ) : ?>
								<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $cfg['modelo'], $id ); ?>><?php echo esc_html( $etiqueta ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>
			<?php submit_button( 'Guardar cambios' ); ?>
		</form>

		<h2>Preguntas que el asistente no supo responder</h2>
		<p>Consultas recientes sin pregunta frecuente ni documentos relacionados (solo el texto y la fecha, sin datos de la persona). Úsalas para crear nuevas respuestas.</p>
		<?php if ( $registro ) : ?>
			<table class="widefat striped" style="max-width:900px">
				<thead><tr><th>Consulta</th><th style="width:150px">Fecha</th><th style="width:170px"></th></tr></thead>
				<tbody>
					<?php foreach ( array_reverse( $registro ) as $fila ) : ?>
						<tr>
							<td><?php echo esc_html( $fila['q'] ); ?></td>
							<td><?php echo esc_html( wp_date( 'd/m/Y H:i', $fila['t'] ) ); ?></td>
							<td><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=pregunta_frecuente&post_title=' . rawurlencode( $fila['q'] ) ) ); ?>">Crear respuesta</a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:10px">
				<input type="hidden" name="action" value="uleam_asistente_guardar" />
				<input type="hidden" name="vaciar_registro" value="1" />
				<?php wp_nonce_field( 'uleam_asistente_config' ); ?>
				<?php submit_button( 'Vaciar la lista', 'secondary', 'submit', false ); ?>
			</form>
		<?php else : ?>
			<p><em>Nada por ahora.</em></p>
		<?php endif; ?>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 * Comprensión de la consulta
 * ---------------------------------------------------------------------- */

/**
 * Palabras que no ayudan a buscar (artículos, saludos y "quiero/dónde/documento"...).
 *
 * @return array
 */
function uleam_asistente_vacias() {
	return array_flip(
		array(
			'de', 'del', 'la', 'las', 'el', 'los', 'lo', 'le', 'les', 'y', 'e', 'o', 'u', 'en', 'a', 'al', 'un', 'una', 'unos', 'unas',
			'para', 'por', 'con', 'sin', 'que', 'se', 'su', 'sus', 'mi', 'mis', 'me', 'te', 'tu', 'yo', 'es', 'son', 'esta', 'este', 'esto',
			'hola', 'buenas', 'buenos', 'dias', 'tardes', 'noches', 'saludos', 'gracias', 'favor', 'porfa', 'porfavor', 'ok',
			'donde', 'como', 'cual', 'cuales', 'cuando', 'quien', 'puedo', 'puede', 'puedes', 'podria', 'podrias', 'quiero', 'quisiera',
			'necesito', 'busco', 'buscar', 'buscando', 'encuentro', 'encontrar', 'hallar', 'ver', 'descargar', 'bajar', 'obtener', 'conseguir',
			'tienen', 'tiene', 'hay', 'existe', 'sobre', 'acerca', 'informacion', 'info', 'ayuda', 'ayudar', 'ayudame', 'dame', 'muestrame',
			'documento', 'documentos', 'archivo', 'archivos', 'pdf', 'saber', 'conocer', 'algun', 'alguna', 'todo', 'todos', 'mas',
		)
	);
}

/**
 * Pasa a minúsculas sin tildes y separa en palabras.
 *
 * @param string $texto Texto.
 * @return array
 */
function uleam_asistente_palabras( $texto ) {
	$texto = remove_accents( mb_strtolower( wp_strip_all_tags( $texto ) ) );
	$texto = preg_replace( '/\bplan(es)? operativos? anual(es)?\b/u', 'poa', $texto );
	return preg_split( '/[^a-z0-9ñ]+/u', $texto, -1, PREG_SPLIT_NO_EMPTY );
}

/**
 * Raíz simple para comparar singular/plural ("informes" → "inform", "carreras" → "carrera").
 *
 * @param string $p Palabra sin tildes.
 * @return string
 */
function uleam_asistente_raiz( $p ) {
	if ( mb_strlen( $p ) > 4 && 's' === substr( $p, -1 ) ) {
		$antes = substr( $p, -3, 1 );
		return ( 'e' === substr( $p, -2, 1 ) && ! in_array( $antes, array( 'a', 'e', 'i', 'o', 'u' ), true ) ) ? substr( $p, 0, -2 ) : substr( $p, 0, -1 );
	}
	return $p;
}

/**
 * Palabras clave útiles de un texto (raíces, sin palabras vacías).
 *
 * @param string $texto Texto.
 * @return array
 */
function uleam_asistente_claves( $texto ) {
	$vacias = uleam_asistente_vacias();
	$claves = array();
	foreach ( uleam_asistente_palabras( $texto ) as $p ) {
		if ( ( mb_strlen( $p ) >= 3 || ctype_digit( $p ) ) && ! isset( $vacias[ $p ] ) ) {
			$claves[] = uleam_asistente_raiz( $p );
		}
	}
	return array_values( array_unique( $claves ) );
}

/**
 * Detecta año y tipo de documento en la consulta ("informes 2023") y deja el resto como palabras clave.
 *
 * @param string $mensaje Consulta.
 * @return array { claves, tax_query, filtros (para el enlace al buscador) }
 */
function uleam_asistente_interpretar( $mensaje ) {
	$claves = uleam_asistente_claves( $mensaje );
	$tax    = array();
	$filtro = array();

	// Año: los términos de "anio" pueden ser "2023" o "2013 (1)".
	if ( preg_match( '/\b(19|20)\d{2}\b/', $mensaje, $m ) ) {
		$anios = get_terms( array( 'taxonomy' => 'anio', 'hide_empty' => true, 'name__like' => $m[0] ) );
		if ( $anios && ! is_wp_error( $anios ) ) {
			$tax[]          = array( 'taxonomy' => 'anio', 'field' => 'term_id', 'terms' => wp_list_pluck( $anios, 'term_id' ) );
			$filtro['anio'] = $anios[0]->slug;
			$claves         = array_values( array_diff( $claves, array( $m[0] ) ) );
		}
	}
	$tax_anio = $tax;
	$sin_tipo = $claves;

	// Tipo de documento: alguna palabra del nombre del tipo ("Informes", "Instrumentos y matrices").
	$tipos = get_terms( array( 'taxonomy' => 'tipo_documento', 'hide_empty' => true ) );
	if ( $tipos && ! is_wp_error( $tipos ) ) {
		foreach ( $tipos as $tipo ) {
			$raices = array_filter(
				uleam_asistente_claves( $tipo->name ),
				function ( $r ) {
					return mb_strlen( $r ) >= 5;
				}
			);
			$comunes = array_intersect( $claves, $raices );
			if ( $comunes ) {
				$tax[]                    = array( 'taxonomy' => 'tipo_documento', 'field' => 'term_id', 'terms' => $tipo->term_id );
				$filtro['tipo_documento'] = $tipo->slug;
				$claves                   = array_values( array_diff( $claves, $comunes ) );
				break;
			}
		}
	}

	return array(
		'claves'    => $claves,
		'tax_query' => $tax,
		'filtros'   => $filtro,
		'sin_tipo'  => array( $sin_tipo, $tax_anio ),
	);
}

/* -------------------------------------------------------------------------
 * Búsqueda de respuestas
 * ---------------------------------------------------------------------- */

/**
 * Preguntas frecuentes más parecidas a la consulta.
 *
 * @param string $mensaje Consulta.
 * @return array Lista de [ puntaje 0–1, WP_Post ], de mayor a menor.
 */
function uleam_asistente_faq( $mensaje ) {
	$q = uleam_asistente_claves( $mensaje );
	if ( ! $q ) {
		return array();
	}
	$faqs = get_posts( array( 'post_type' => 'pregunta_frecuente', 'post_status' => 'publish', 'numberposts' => 200 ) );
	$res  = array();
	foreach ( $faqs as $faq ) {
		$frases = array_merge( array( $faq->post_title ), explode( "\n", (string) get_post_meta( $faq->ID, '_asistente_variantes', true ) ) );
		$mejor  = 0;
		foreach ( $frases as $frase ) {
			$f = uleam_asistente_claves( $frase );
			if ( ! $f ) {
				continue;
			}
			$comunes = count( array_intersect( $q, $f ) );
			// F1: cuánto de la consulta y de la frase coinciden a la vez.
			$mejor = max( $mejor, 2 * $comunes / ( count( $q ) + count( $f ) ) );
		}
		if ( $mejor > 0 ) {
			$res[] = array( $mejor, $faq );
		}
	}
	usort(
		$res,
		function ( $a, $b ) {
			return $b[0] <=> $a[0];
		}
	);
	return array_slice( $res, 0, 3 );
}

/**
 * Variantes que se escriben de dos formas en los títulos.
 *
 * @return array raíz => otras raíces
 */
function uleam_asistente_sinonimos() {
	return array(
		'posgrado'  => array( 'postgrado' ),
		'postgrado' => array( 'posgrado' ),
		'maestria'  => array( 'posgrado', 'postgrado' ),
		'docente'   => array( 'profesor' ),
		'profesor'  => array( 'docente' ),
	);
}

/**
 * Busca contenido de un tipo y lo ordena por cuántas palabras de la consulta coinciden
 * (en el título o el contenido y, para documentos, también en el nombre de su sección).
 *
 * @param string $tipo   documento | page.
 * @param array  $claves Palabras clave (raíces).
 * @param array  $tax    tax_query (año, tipo).
 * @param int    $max    Máximo de resultados.
 * @return array { total, posts }
 */
function uleam_asistente_buscar( $tipo, $claves, $tax, $max ) {
	$base = array(
		'post_type'   => $tipo,
		'post_status' => 'publish',
		'fields'      => 'ids',
		'numberposts' => 400,
	);
	if ( $tax ) {
		$base['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax );
	}
	if ( ! $claves ) {
		// Solo año/tipo ("formatos 2023"): los más recientes de ese filtro.
		if ( ! $tax ) {
			return array( 'total' => 0, 'posts' => array() );
		}
		$ids = get_posts( $base );
		return array( 'total' => count( $ids ), 'posts' => array_map( 'get_post', array_slice( $ids, 0, $max ) ) );
	}

	$sinonimos = uleam_asistente_sinonimos();
	$puntos    = array(); // id => [ palabras que coinciden, puntaje ].
	foreach ( $claves as $c ) {
		$variantes = array_merge( array( $c ), $sinonimos[ $c ] ?? array() );
		$grupo     = array();
		foreach ( $variantes as $v ) {
			foreach ( get_posts( $base + array( 's' => $v ) ) as $id ) {
				$grupo[ $id ] = 2;
			}
			if ( 'documento' === $tipo && mb_strlen( $v ) >= 4 ) {
				// "carreras", "desempeño", "sedes": documentos de la sección con ese nombre.
				$secciones = get_terms( array( 'taxonomy' => 'seccion', 'name__like' => $v, 'fields' => 'ids', 'hide_empty' => false ) );
				if ( $secciones && ! is_wp_error( $secciones ) ) {
					$en_seccion = array( 'taxonomy' => 'seccion', 'field' => 'term_id', 'terms' => $secciones, 'include_children' => true );
					$args       = array( 'tax_query' => array_merge( array( 'relation' => 'AND' ), $tax, array( $en_seccion ) ) ) + $base;
					foreach ( get_posts( $args ) as $id ) {
						$grupo[ $id ] = max( $grupo[ $id ] ?? 0, 1 );
					}
				}
			}
		}
		foreach ( $grupo as $id => $p ) {
			$puntos[ $id ] = array( ( $puntos[ $id ][0] ?? 0 ) + 1, ( $puntos[ $id ][1] ?? 0 ) + $p );
		}
	}
	if ( ! $puntos ) {
		return array( 'total' => 0, 'posts' => array() );
	}

	// Si algún resultado tiene todas las palabras, solo esos; si no, los que tengan al menos la mitad.
	$n      = count( $claves );
	$mejor  = max( array_column( $puntos, 0 ) );
	$minimo = $mejor >= $n ? $n : max( 1, (int) ceil( $n / 2 ) );
	$puntos = array_filter(
		$puntos,
		function ( $p ) use ( $minimo ) {
			return $p[0] >= $minimo;
		}
	);
	// Desempate: las palabras aparecen en el título.
	$candidatos = array_slice( array_keys( $puntos ), 0, 80, true );
	foreach ( $candidatos as $id ) {
		$titulo = implode( ' ', array_map( 'uleam_asistente_raiz', uleam_asistente_palabras( get_the_title( $id ) ) ) );
		foreach ( $claves as $c ) {
			if ( false !== strpos( $titulo, $c ) ) {
				$puntos[ $id ][1] += 1;
			}
		}
	}
	uasort(
		$puntos,
		function ( $a, $b ) {
			return array( $b[0], $b[1] ) <=> array( $a[0], $a[1] );
		}
	);
	return array(
		'total' => count( $puntos ),
		'posts' => array_map( 'get_post', array_slice( array_keys( $puntos ), 0, $max ) ),
	);
}

/**
 * Datos de contacto del Personalizador.
 *
 * @return array
 */
function uleam_asistente_contacto() {
	return array(
		'email'     => (string) uleam_opt( 'uleam_email' ),
		'telefono'  => (string) uleam_opt( 'uleam_telefono' ),
		'direccion' => (string) uleam_opt( 'uleam_direccion' ),
	);
}

/**
 * Guarda una consulta sin respuesta (solo texto y fecha; máximo 100).
 *
 * @param string $mensaje Consulta.
 */
function uleam_asistente_registrar_sin_respuesta( $mensaje ) {
	$registro   = get_option( 'uleam_asistente_sin_respuesta', array() );
	$registro[] = array( 'q' => mb_substr( $mensaje, 0, 200 ), 't' => time() );
	update_option( 'uleam_asistente_sin_respuesta', array_slice( $registro, -100 ), false );
}

/**
 * Texto plano de la IA → HTML seguro (párrafos, viñetas y **negritas**).
 *
 * @param string $texto Texto.
 * @return string
 */
function uleam_asistente_texto_html( $texto ) {
	$html = '';
	foreach ( preg_split( '/\n\s*\n/u', trim( $texto ) ) as $bloque ) {
		$parrafo = array();
		$lista   = array();
		foreach ( explode( "\n", $bloque ) as $linea ) {
			if ( preg_match( '/^\s*[-*•]\s+(.*)$/u', $linea, $m ) ) {
				if ( $parrafo ) {
					$html   .= '<p>' . implode( '<br>', $parrafo ) . '</p>';
					$parrafo = array();
				}
				$lista[] = '<li>' . esc_html( $m[1] ) . '</li>';
			} elseif ( '' !== trim( $linea ) ) {
				if ( $lista ) {
					$html .= '<ul>' . implode( '', $lista ) . '</ul>';
					$lista = array();
				}
				$parrafo[] = esc_html( trim( $linea ) );
			}
		}
		$html .= ( $parrafo ? '<p>' . implode( '<br>', $parrafo ) . '</p>' : '' ) . ( $lista ? '<ul>' . implode( '', $lista ) . '</ul>' : '' );
	}
	return preg_replace( '/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $html );
}

/**
 * Pide a Claude que redacte la respuesta con el contenido encontrado.
 *
 * @param string $mensaje   Consulta.
 * @param array  $historial Turnos anteriores [ {rol, texto} ].
 * @param array  $faqs      Preguntas frecuentes candidatas.
 * @param array  $fuentes   Documentos y páginas candidatos (uleam_busqueda_item + extracto).
 * @return array|null { texto, fuentes: [índices] } o null si falla (se usa la respuesta sin IA).
 */
function uleam_asistente_ia( $mensaje, $historial, $faqs, $fuentes ) {
	$clave = uleam_asistente_api_key();
	if ( ! $clave ) {
		return null;
	}
	// Tope diario de consultas con IA para controlar el costo; después se responde sin IA.
	$dia  = 'uleam_asis_ia_' . wp_date( 'Ymd' );
	$hoy  = (int) get_transient( $dia );
	$tope = (int) apply_filters( 'uleam_asistente_tope_diario', 1000 );
	if ( $hoy >= $tope ) {
		return null;
	}
	set_transient( $dia, $hoy + 1, DAY_IN_SECONDS );
	$modelo   = uleam_asistente_config()['modelo'];
	$contacto = uleam_asistente_contacto();

	// Instrucciones fijas (se cachean); el contenido variable va en el mensaje del usuario.
	$sistema = 'Eres el asistente virtual de la Dirección de Gestión y Aseguramiento de la Calidad (DGAC) de la Universidad Laica Eloy Alfaro de Manabí (ULEAM). '
		. 'Atiendes a estudiantes, docentes, personal y público que buscan documentos institucionales (formatos, informes, manuales, normativa, POA, autoevaluación de carreras y posgrados, etc.) o tienen dudas sobre la Dirección. '
		. 'Responde en español, con tono cordial y profesional, en 1 a 4 frases cortas; usa viñetas con "- " solo si enumeras varias cosas. '
		. 'Usa EXCLUSIVAMENTE la información de <contexto>. No inventes documentos, códigos, fechas, nombres, enlaces ni trámites. No escribas URL: los documentos que elijas se mostrarán como tarjetas con su enlace debajo de tu respuesta. '
		. 'Si el contexto no responde la consulta, dilo con honestidad y sugiere escribir al correo o llamar a la Dirección (datos en <contacto>). '
		. 'En "fuentes" incluye solo los números de los documentos o páginas del contexto que realmente sirven para la consulta (ninguno si no aplica), en orden de utilidad.';

	$ctx = "<contacto>\nCorreo: {$contacto['email']}\nTeléfono: {$contacto['telefono']}\nDirección: {$contacto['direccion']}\n</contacto>\n<contexto>\n";
	foreach ( $faqs as $f ) {
		$ctx .= "<pregunta_frecuente>\nPregunta: " . $f[1]->post_title . "\nRespuesta: " . uleam_extracto_limpio( $f[1]->post_content, 250 ) . "\n</pregunta_frecuente>\n";
	}
	foreach ( $fuentes as $i => $s ) {
		$ctx .= '<fuente numero="' . ( $i + 1 ) . '" tipo="' . $s['etiqueta'] . '">' . $s['titulo'];
		if ( $s['datos'] ) {
			$ctx .= ' (' . implode( ' · ', $s['datos'] ) . ')';
		}
		if ( ! empty( $s['extracto'] ) ) {
			$ctx .= "\n" . $s['extracto'];
		}
		$ctx .= "</fuente>\n";
	}
	$ctx .= '</contexto>';

	$mensajes = array();
	foreach ( $historial as $turno ) {
		$mensajes[] = array( 'role' => 'bot' === $turno['rol'] ? 'assistant' : 'user', 'content' => $turno['texto'] );
	}
	// La API exige empezar con "user" y alternar roles.
	while ( $mensajes && 'user' !== $mensajes[0]['role'] ) {
		array_shift( $mensajes );
	}
	$limpio = array();
	foreach ( $mensajes as $m ) {
		if ( $limpio && end( $limpio )['role'] === $m['role'] ) {
			$limpio[ count( $limpio ) - 1 ]['content'] .= "\n" . $m['content'];
		} else {
			$limpio[] = $m;
		}
	}
	$consulta = $ctx . "\n\nConsulta: " . $mensaje;
	if ( $limpio && 'user' === end( $limpio )['role'] ) {
		// Una consulta anterior quedó sin respuesta (p. ej. error de red): se envían juntas.
		$limpio[ count( $limpio ) - 1 ]['content'] .= "\n\n" . $consulta;
	} else {
		$limpio[] = array( 'role' => 'user', 'content' => $consulta );
	}

	$cuerpo = array(
		'model'         => $modelo,
		'max_tokens'    => 4000,
		'system'        => array( array( 'type' => 'text', 'text' => $sistema, 'cache_control' => array( 'type' => 'ephemeral' ) ) ),
		'messages'      => $limpio,
		'output_config' => array(
			'format' => array(
				'type'   => 'json_schema',
				'schema' => array(
					'type'                 => 'object',
					'properties'           => array(
						'respuesta' => array( 'type' => 'string' ),
						'fuentes'   => array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
					),
					'required'             => array( 'respuesta', 'fuentes' ),
					'additionalProperties' => false,
				),
			),
		),
	);
	$cabeceras = array(
		'x-api-key'         => $clave,
		'anthropic-version' => '2023-06-01',
		'content-type'      => 'application/json',
	);
	if ( 'claude-haiku-4-5' !== $modelo ) {
		// Chat sencillo: poco razonamiento = respuesta rápida y económica (Haiku 4.5 no admite "effort").
		$cuerpo['output_config']['effort'] = 'low';
	}
	if ( in_array( $modelo, array( 'claude-opus-5-5', 'claude-sonnet-5-5' ), true ) ) {
		// Si un filtro de seguridad rechaza la consulta, la API la reintenta con otro modelo.
		$cuerpo['fallbacks']           = 'default';
		$cabeceras['anthropic-beta']   = 'server-side-fallback-2026-07-01';
	}

	$resp = wp_remote_post(
		'https://api.anthropic.com/v1/messages',
		array(
			'timeout' => 45,
			'headers' => $cabeceras,
			'body'    => wp_json_encode( $cuerpo ),
		)
	);
	if ( is_wp_error( $resp ) ) {
		error_log( 'Asistente ULEAM: ' . $resp->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return null;
	}
	$codigo = wp_remote_retrieve_response_code( $resp );
	$datos  = json_decode( wp_remote_retrieve_body( $resp ), true );
	if ( 200 !== $codigo || ! is_array( $datos ) ) {
		error_log( 'Asistente ULEAM: HTTP ' . $codigo . ' ' . substr( wp_remote_retrieve_body( $resp ), 0, 300 ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return null;
	}
	if ( in_array( $datos['stop_reason'] ?? '', array( 'refusal', 'max_tokens' ), true ) ) {
		return null;
	}
	foreach ( $datos['content'] ?? array() as $bloque ) {
		if ( 'text' === ( $bloque['type'] ?? '' ) ) {
			$json = json_decode( $bloque['text'], true );
			if ( is_array( $json ) && isset( $json['respuesta'] ) ) {
				return array(
					'texto'   => (string) $json['respuesta'],
					'fuentes' => array_map( 'intval', (array) ( $json['fuentes'] ?? array() ) ),
				);
			}
		}
	}
	return null;
}

/**
 * Responde una consulta.
 *
 * @param string $mensaje   Consulta.
 * @param array  $historial Turnos anteriores.
 * @return array { html, fuentes, ver_todos, contacto, ia }
 */
function uleam_asistente_responder( $mensaje, $historial ) {
	$contacto = uleam_asistente_contacto();
	$claves   = uleam_asistente_claves( $mensaje );
	$palabras = uleam_asistente_palabras( $mensaje );

	// Saludos y agradecimientos sin consulta.
	if ( ! $claves ) {
		$gracias = array_intersect( $palabras, array( 'gracias', 'agradezco', 'genial', 'perfecto', 'excelente' ) );
		return array(
			'html'     => $gracias
				? '<p>¡Con gusto! Si necesitas algo más, aquí estoy.</p>'
				: '<p>¡Hola! Cuéntame qué documento o información buscas. Por ejemplo: <em>«informes de autoevaluación 2023»</em> o <em>«horario de atención»</em>.</p>',
			'fuentes'  => array(),
			'contacto' => false,
		);
	}

	$faqs     = uleam_asistente_faq( $mensaje );
	$interp   = uleam_asistente_interpretar( $mensaje );
	$docs     = uleam_asistente_buscar( 'documento', $interp['claves'], $interp['tax_query'], 6 );
	if ( ! $docs['posts'] && isset( $interp['filtros']['tipo_documento'] ) ) {
		// "manual de posgrado": quizá está clasificado con otro tipo; buscar la palabra en el título.
		$docs = uleam_asistente_buscar( 'documento', $interp['sin_tipo'][0], $interp['sin_tipo'][1], 6 );
		unset( $interp['filtros']['tipo_documento'] );
		$interp['claves'] = $interp['sin_tipo'][0];
	}
	$paginas = array( 'total' => 0, 'posts' => array() );
	if ( ! $interp['tax_query'] ) {
		$paginas = uleam_asistente_buscar( 'page', $claves, array(), 3 );
		// Páginas con la palabra en el título primero; si hay alguna, solo esas (evita "Inicio" en todo).
		$con_titulo = array_filter(
			$paginas['posts'],
			function ( $p ) use ( $claves ) {
				$titulo = implode( ' ', array_map( 'uleam_asistente_raiz', uleam_asistente_palabras( $p->post_title ) ) );
				foreach ( $claves as $c ) {
					if ( false !== strpos( $titulo, $c ) ) {
						return true;
					}
				}
				return false;
			}
		);
		if ( $con_titulo ) {
			$paginas['posts'] = array_values( $con_titulo );
		}
	}
	$ver_mas  = add_query_arg( array_merge( array( 's' => rawurlencode( implode( ' ', $interp['claves'] ) ) ), $interp['filtros'], $interp['tax_query'] ? array( 'en' => 'documento' ) : array() ), home_url( '/' ) );
	$fuentes  = array();
	foreach ( array_merge( $docs['posts'], $paginas['posts'] ) as $p ) {
		$item = uleam_busqueda_item( $p );
		if ( 'page' === $item['tipo'] ) {
			$item['extracto'] = uleam_extracto_limpio( $p->post_content, 120 );
		} else {
			$ubic = uleam_documento_ubicacion( $p->ID );
			if ( $ubic ) {
				$item['ubicacion'] = $ubic;
			}
		}
		$fuentes[] = $item;
	}
	$hay_faq = $faqs && $faqs[0][0] >= 0.5;

	// 1) Con IA: redacta y elige las fuentes.
	$ia = uleam_asistente_ia( $mensaje, $historial, array_filter( $faqs, function ( $f ) { return $f[0] >= 0.25; } ), $fuentes );
	if ( $ia ) {
		$elegidas = array();
		foreach ( $ia['fuentes'] as $n ) {
			if ( isset( $fuentes[ $n - 1 ] ) ) {
				$elegidas[] = $fuentes[ $n - 1 ];
			}
		}
		if ( ! $elegidas && ! $hay_faq ) {
			uleam_asistente_registrar_sin_respuesta( $mensaje );
		}
		return array(
			'html'      => uleam_asistente_texto_html( $ia['texto'] ),
			'fuentes'   => uleam_asistente_publicas( $elegidas ),
			'ver_todos' => $docs['total'] > count( $elegidas ) ? array( 'url' => $ver_mas, 'total' => $docs['total'] ) : null,
			'contacto'  => ! $elegidas && ! $hay_faq,
			'ia'        => true,
		);
	}

	// 2) Sin IA: pregunta frecuente, documentos encontrados o derivación a la Dirección.
	if ( $hay_faq ) {
		// La respuesta escrita por el personal y, debajo, los documentos que coinciden ("POA 2024").
		$faq      = $faqs[0][1];
		$con_docs  = array_slice( $fuentes, 0, count( $docs['posts'] ) );
		return array(
			'html'      => wp_kses_post( wpautop( do_shortcode( $faq->post_content ) ) ) . ( $con_docs ? '<p><strong>Documentos relacionados:</strong></p>' : '' ),
			'fuentes'   => uleam_asistente_publicas( $con_docs ),
			'ver_todos' => $docs['total'] > count( $con_docs ) ? array( 'url' => $ver_mas, 'total' => $docs['total'] ) : null,
			'contacto'  => false,
		);
	}
	if ( $fuentes ) {
		$n     = $docs['total'];
		$texto = $n
			? sprintf( '<p>Encontré %s relacionado%s con tu consulta%s:</p>', 1 === $n ? 'un documento' : $n . ' documentos', 1 === $n ? '' : 's', $n > count( $docs['posts'] ) ? '. Estos son los más relevantes' : '' )
			: '<p>No encontré documentos con esas palabras, pero esta información del sitio puede ayudarte:</p>';
		return array(
			'html'      => $texto,
			'fuentes'   => uleam_asistente_publicas( $fuentes ),
			'ver_todos' => $n > count( $docs['posts'] ) ? array( 'url' => $ver_mas, 'total' => $n ) : null,
			'contacto'  => false,
		);
	}

	uleam_asistente_registrar_sin_respuesta( $mensaje );
	return array(
		'html'     => '<p>No encontré información sobre eso en el sitio. Prueba con otras palabras (por ejemplo, el nombre del formato, la carrera o el año) o comunícate con la Dirección y te ayudarán personalmente.</p>',
		'fuentes'  => array(),
		'contacto' => true,
	);
}

/**
 * Deja solo los campos que necesita el chat.
 *
 * @param array $fuentes Ítems de uleam_busqueda_item.
 * @return array
 */
function uleam_asistente_publicas( $fuentes ) {
	return array_map(
		function ( $s ) {
			return array(
				'titulo'    => $s['titulo'],
				'url'       => $s['url'],
				'icono'     => $s['icono'],
				'etiqueta'  => $s['ext'] ? $s['ext'] : $s['etiqueta'],
				'datos'     => implode( ' · ', $s['datos'] ),
				'archivo'   => (bool) $s['archivo'],
				'ubicacion' => $s['ubicacion'] ?? null,
			);
		},
		array_values( $fuentes )
	);
}

/**
 * Endpoint del chat.
 */
function uleam_asistente_rest() {
	register_rest_route(
		'uleam/v1',
		'/asistente',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'callback'            => function ( WP_REST_Request $req ) {
				$cfg = uleam_asistente_config();
				if ( ! $cfg['activo'] ) {
					return new WP_Error( 'asistente_inactivo', 'El asistente está desactivado.', array( 'status' => 403 ) );
				}
				// Límite por conexión: 100 consultas cada 10 minutos (holgado: en el campus muchos comparten IP).
				$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
				$llave = 'uleam_asis_' . md5( $ip . wp_salt() );
				$usos  = (int) get_transient( $llave );
				if ( $usos >= 100 ) {
					return new WP_Error( 'asistente_limite', 'Has hecho muchas consultas seguidas. Espera unos minutos e inténtalo de nuevo.', array( 'status' => 429 ) );
				}
				set_transient( $llave, $usos + 1, 10 * MINUTE_IN_SECONDS );

				$mensaje = trim( mb_substr( sanitize_textarea_field( (string) $req->get_param( 'mensaje' ) ), 0, 500 ) );
				if ( '' === $mensaje ) {
					return new WP_Error( 'asistente_vacio', 'Escribe tu consulta.', array( 'status' => 400 ) );
				}
				$historial = array();
				foreach ( array_slice( (array) $req->get_param( 'historial' ), -6 ) as $turno ) {
					if ( is_array( $turno ) && isset( $turno['rol'], $turno['texto'] ) ) {
						$historial[] = array(
							'rol'   => 'bot' === $turno['rol'] ? 'bot' : 'usuario',
							'texto' => mb_substr( sanitize_textarea_field( (string) $turno['texto'] ), 0, 1000 ),
						);
					}
				}
				return uleam_asistente_responder( $mensaje, $historial );
			},
		)
	);
}
add_action( 'rest_api_init', 'uleam_asistente_rest' );

/* -------------------------------------------------------------------------
 * Ventana de chat en el sitio
 * ---------------------------------------------------------------------- */

/**
 * Carga el JavaScript del chat y sus datos.
 */
function uleam_asistente_scripts() {
	$cfg = uleam_asistente_config();
	if ( ! $cfg['activo'] || is_admin() ) {
		return;
	}
	wp_enqueue_script( 'uleam-asistente', ULEAM_THEME_URI . '/js/asistente.js', array(), ULEAM_THEME_VERSION, true );
	$contacto = uleam_asistente_contacto();
	wp_localize_script(
		'uleam-asistente',
		'uleamAsistente',
		array(
			'endpoint'    => esc_url_raw( rest_url( 'uleam/v1/asistente' ) ),
			'nombre'      => $cfg['nombre'],
			'bienvenida'  => $cfg['bienvenida'],
			'sugerencias' => array_values( array_filter( array_map( 'trim', explode( "\n", $cfg['sugerencias'] ) ) ) ),
			'email'       => $contacto['email'],
			'telefono'    => $contacto['telefono'],
			'ia'          => (bool) uleam_asistente_api_key(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'uleam_asistente_scripts' );

/**
 * Botón flotante y ventana del chat (los mensajes los arma js/asistente.js).
 */
function uleam_asistente_html() {
	$cfg = uleam_asistente_config();
	if ( ! $cfg['activo'] ) {
		return;
	}
	?>
	<div class="asistente" id="asistente">
		<section class="asistente__panel" id="asistente-panel" role="dialog" aria-labelledby="asistente-titulo" hidden>
			<div class="asistente__cabecera">
				<span class="asistente__avatar" aria-hidden="true"><i class="fa-solid fa-robot"></i></span>
				<div class="asistente__titulos">
					<h2 class="asistente__titulo" id="asistente-titulo"><?php echo esc_html( $cfg['nombre'] ); ?></h2>
					<p class="asistente__estado">Respuestas automáticas · disponible 24/7</p>
				</div>
				<button type="button" class="asistente__icono-btn" data-asistente-reiniciar title="Nueva conversación" aria-label="Nueva conversación"><i class="fa-solid fa-rotate-right" aria-hidden="true"></i></button>
				<button type="button" class="asistente__icono-btn" data-asistente-cerrar title="Cerrar" aria-label="Cerrar el chat"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
			</div>
			<div class="asistente__log" role="log" aria-live="polite" aria-label="Conversación"></div>
			<form class="asistente__form" novalidate>
				<label class="screen-reader-text" for="asistente-entrada">Escribe tu consulta</label>
				<textarea id="asistente-entrada" class="asistente__entrada" rows="1" maxlength="500" placeholder="Escribe tu consulta…" autocomplete="off"></textarea>
				<button type="submit" class="asistente__enviar" aria-label="Enviar"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
			</form>
			<p class="asistente__aviso">
				<?php echo uleam_asistente_api_key() ? 'Respuestas generadas con IA a partir del contenido de este sitio; confirma en el documento oficial.' : 'Respuestas automáticas a partir del contenido de este sitio.'; ?>
			</p>
		</section>
		<button type="button" class="asistente__fab" aria-expanded="false" aria-controls="asistente-panel" aria-label="<?php echo esc_attr( $cfg['nombre'] . ': chat de ayuda' ); ?>">
			<i class="fa-solid fa-comments" aria-hidden="true"></i>
			<span class="asistente__fab-texto">¿Te ayudo?</span>
		</button>
	</div>
	<?php
}
add_action( 'wp_footer', 'uleam_asistente_html', 5 );
