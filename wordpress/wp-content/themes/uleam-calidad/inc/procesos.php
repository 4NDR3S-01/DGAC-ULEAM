<?php
/**
 * Gestión de Procesos editable desde el administrador (sin código).
 *
 * - Tipo de contenido "Procedimientos": cada procedimiento con sus archivos.
 * - Taxonomía jerárquica "Estructura de procesos": Subsistema > Macroproceso > Proceso.
 * - Shortcode [procesos_universitarios] que pinta pestañas, macroprocesos y archivos.
 * - Importación inicial única desde inc/data/procesos-iniciales.json.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ULEAM_PROC_TAX  = 'estructura_proceso';
const ULEAM_PROC_CPT  = 'procedimiento';
const ULEAM_PROC_META = '_procedimiento_archivos';

/**
 * Registra el tipo de contenido y la taxonomía.
 */
function uleam_procesos_registrar() {
	register_taxonomy(
		ULEAM_PROC_TAX,
		ULEAM_PROC_CPT,
		array(
			'labels'            => array(
				'name'          => 'Estructura de procesos',
				'singular_name' => 'Elemento de la estructura',
				'add_new_item'  => 'Añadir subsistema, macroproceso o proceso',
				'edit_item'     => 'Editar elemento',
				'parent_item'   => 'Pertenece a',
				'all_items'     => 'Estructura de procesos',
				'search_items'  => 'Buscar',
			),
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => false,
		)
	);

	register_post_type(
		ULEAM_PROC_CPT,
		array(
			'labels'       => array(
				'name'          => 'Procedimientos',
				'singular_name' => 'Procedimiento',
				'menu_name'     => 'Gestión de Procesos',
				'add_new'       => 'Añadir procedimiento',
				'add_new_item'  => 'Añadir nuevo procedimiento',
				'edit_item'     => 'Editar procedimiento',
				'search_items'  => 'Buscar procedimientos',
				'not_found'     => 'No se encontraron procedimientos',
			),
			'public'       => true,
			'show_ui'      => true,
			'has_archive'  => false,
			'menu_icon'    => 'dashicons-networking',
			'menu_position' => 21,
			'supports'     => array( 'title', 'page-attributes' ),
			'taxonomies'   => array( ULEAM_PROC_TAX ),
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'procedimiento' ),
		)
	);
}
add_action( 'init', 'uleam_procesos_registrar' );

/* ------------------------------------------------------------------
 * Campos de la estructura (orden, icono, color, etiqueta)
 * ------------------------------------------------------------------ */

/**
 * Colores disponibles para los macroprocesos.
 *
 * @return array
 */
function uleam_procesos_colores() {
	return array(
		'blue'   => 'Azul',
		'green'  => 'Verde',
		'red'    => 'Rojo',
		'orange' => 'Naranja',
		'purple' => 'Morado',
		'dark'   => 'Oscuro',
	);
}

/**
 * Campos extra en el formulario de "añadir" de la estructura.
 */
function uleam_procesos_term_add_fields() {
	wp_nonce_field( 'uleam_proc_term', 'uleam_proc_term_nonce' );
	echo '<div class="form-field"><label for="uleam_orden">Orden</label><input type="number" name="uleam_orden" id="uleam_orden" value="0" /><p>Número para ordenar (1, 2, 3…). Los menores aparecen primero.</p></div>';
	echo '<div class="form-field"><label for="uleam_color">Color (macroprocesos)</label><select name="uleam_color" id="uleam_color">';
	foreach ( uleam_procesos_colores() as $v => $n ) {
		echo '<option value="' . esc_attr( $v ) . '">' . esc_html( $n ) . '</option>';
	}
	echo '</select></div>';
	echo '<div class="form-field"><label for="uleam_icono">Icono (opcional)</label><input type="text" name="uleam_icono" id="uleam_icono" placeholder="fa-solid fa-award" /><p>Clase de <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" rel="noopener">Font Awesome</a>.</p></div>';
	echo '<div class="form-field"><label for="uleam_etiqueta">Etiqueta corta (solo subsistemas)</label><input type="text" name="uleam_etiqueta" id="uleam_etiqueta" placeholder="DOCENCIA" /></div>';
}
add_action( ULEAM_PROC_TAX . '_add_form_fields', 'uleam_procesos_term_add_fields' );

/**
 * Campos extra en el formulario de "editar" de la estructura.
 *
 * @param WP_Term $term Elemento.
 */
function uleam_procesos_term_edit_fields( $term ) {
	$orden = (int) get_term_meta( $term->term_id, 'uleam_orden', true );
	$color = get_term_meta( $term->term_id, 'uleam_color', true );
	$icono = get_term_meta( $term->term_id, 'uleam_icono', true );
	$etiq  = get_term_meta( $term->term_id, 'uleam_etiqueta', true );
	wp_nonce_field( 'uleam_proc_term', 'uleam_proc_term_nonce' );
	echo '<tr class="form-field"><th><label for="uleam_orden">Orden</label></th><td><input type="number" name="uleam_orden" id="uleam_orden" value="' . esc_attr( $orden ) . '" /><p class="description">Los menores aparecen primero.</p></td></tr>';
	echo '<tr class="form-field"><th><label for="uleam_color">Color (macroprocesos)</label></th><td><select name="uleam_color" id="uleam_color">';
	foreach ( uleam_procesos_colores() as $v => $n ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $color, $v, false ) . '>' . esc_html( $n ) . '</option>';
	}
	echo '</select></td></tr>';
	echo '<tr class="form-field"><th><label for="uleam_icono">Icono</label></th><td><input type="text" name="uleam_icono" id="uleam_icono" value="' . esc_attr( $icono ) . '" placeholder="fa-solid fa-award" /><p class="description">Clase de Font Awesome (opcional).</p></td></tr>';
	echo '<tr class="form-field"><th><label for="uleam_etiqueta">Etiqueta corta</label></th><td><input type="text" name="uleam_etiqueta" id="uleam_etiqueta" value="' . esc_attr( $etiq ) . '" /><p class="description">Solo para subsistemas (ej. DOCENCIA).</p></td></tr>';
}
add_action( ULEAM_PROC_TAX . '_edit_form_fields', 'uleam_procesos_term_edit_fields' );

/**
 * Guarda los campos extra de la estructura.
 *
 * @param int $term_id Elemento.
 */
function uleam_procesos_term_save( $term_id ) {
	if ( ! isset( $_POST['uleam_proc_term_nonce'] ) || ! wp_verify_nonce( $_POST['uleam_proc_term_nonce'], 'uleam_proc_term' ) || ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	update_term_meta( $term_id, 'uleam_orden', (int) ( $_POST['uleam_orden'] ?? 0 ) );
	$color = sanitize_key( $_POST['uleam_color'] ?? 'blue' );
	update_term_meta( $term_id, 'uleam_color', isset( uleam_procesos_colores()[ $color ] ) ? $color : 'blue' );
	update_term_meta( $term_id, 'uleam_icono', sanitize_text_field( wp_unslash( $_POST['uleam_icono'] ?? '' ) ) );
	update_term_meta( $term_id, 'uleam_etiqueta', sanitize_text_field( wp_unslash( $_POST['uleam_etiqueta'] ?? '' ) ) );
}
add_action( 'created_' . ULEAM_PROC_TAX, 'uleam_procesos_term_save' );
add_action( 'edited_' . ULEAM_PROC_TAX, 'uleam_procesos_term_save' );

/* ------------------------------------------------------------------
 * Metabox: archivos del procedimiento
 * ------------------------------------------------------------------ */

/**
 * Registra el metabox de archivos y aviso de ubicación.
 */
function uleam_procesos_metabox() {
	add_meta_box( 'uleam_proc_archivos', 'Archivos del procedimiento', 'uleam_procesos_metabox_html', ULEAM_PROC_CPT, 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'uleam_procesos_metabox' );

/**
 * Secciones de la página de un procedimiento (clave => nombre).
 *
 * @return array
 */
function uleam_procesos_secciones() {
	return array(
		'manual'      => 'Manual vigente',
		'flujo'       => 'Flujo vigente',
		'formato'     => 'Formatos vigentes',
		'ant_manual'  => 'Manual no vigente',
		'ant_flujo'   => 'Flujo no vigente',
		'ant_formato' => 'Formatos no vigentes',
	);
}

/**
 * Normaliza un archivo guardado (compatibilidad con datos antiguos).
 *
 * @param array $a Archivo.
 * @return array
 */
function uleam_procesos_norm( $a ) {
	$sec = $a['s'] ?? '';
	if ( ! isset( uleam_procesos_secciones()[ $sec ] ) ) {
		$k   = mb_strtolower( $a['k'] ?? '' );
		$sec = false !== strpos( $k, 'flujo' ) ? 'flujo' : ( 'formato' === $k ? 'formato' : 'manual' );
	}
	return array(
		't' => (string) ( $a['t'] ?? '' ),
		's' => $sec,
		'u' => uleam_procesos_url( (string) ( $a['u'] ?? '' ) ),
		'd' => (string) ( $a['d'] ?? '' ),
	);
}

/**
 * Enlace de un archivo: las rutas "/wp-content/uploads/…" se resuelven con la
 * carpeta de subidas real, así funcionan aunque el sitio viva en una subcarpeta.
 *
 * @param string $u Enlace guardado.
 * @return string
 */
function uleam_procesos_url( $u ) {
	if ( 0 === strpos( $u, '/wp-content/uploads/' ) ) {
		return uleam_url( $u );
	}
	return $u;
}

/**
 * Elemento sin archivo que solo informa (p. ej. "Procedimiento sin versiones anteriores").
 * Los formatos sin archivo llevan código (PAT-05-IT-001-F-003…) y sí cuentan como documento.
 *
 * @param array $a Archivo normalizado.
 * @return bool
 */
function uleam_procesos_es_nota( $a ) {
	return '' === $a['u'] && ! preg_match( '/\b[A-Z]{2,4}-\d{2}/', $a['t'] );
}

/**
 * Cuántos documentos hay en una lista (sin contar las notas).
 *
 * @param array $lista Archivos normalizados.
 * @return int
 */
function uleam_procesos_contar( $lista ) {
	return count( array_filter( $lista, fn( $a ) => ! uleam_procesos_es_nota( $a ) ) );
}

/**
 * Archivos de un procedimiento agrupados por sección.
 *
 * @param int $post_id Procedimiento.
 * @return array sección => archivos[]
 */
function uleam_procesos_archivos( $post_id ) {
	$arch = get_post_meta( $post_id, ULEAM_PROC_META, true );
	$out  = array_fill_keys( array_keys( uleam_procesos_secciones() ), array() );
	foreach ( is_array( $arch ) ? $arch : array() as $a ) {
		$n                     = uleam_procesos_norm( $a );
		$out[ $n['s'] ][] = $n;
	}
	return $out;
}

/**
 * Pinta una fila del listado de archivos.
 *
 * @param array $a Datos del archivo (t, s, d, u).
 * @return string
 */
function uleam_procesos_fila( $a ) {
	$sec  = $a['s'] ?? 'manual';
	$html = '<div class="uleam-arch-fila" style="display:flex;gap:6px;margin-bottom:6px;align-items:center;flex-wrap:wrap">';
	$html .= '<input type="text" name="proc_arch_t[]" value="' . esc_attr( $a['t'] ?? '' ) . '" placeholder="Nombre del documento (ej. PAM-02 Homologación… V.9)" style="flex:3;min-width:240px" />';
	$html .= '<select name="proc_arch_s[]" style="flex:1;min-width:150px">';
	foreach ( uleam_procesos_secciones() as $k => $n ) {
		$html .= '<option value="' . esc_attr( $k ) . '"' . selected( $sec, $k, false ) . '>' . esc_html( $n ) . '</option>';
	}
	$html .= '</select>';
	$html .= '<input type="text" name="proc_arch_u[]" value="' . esc_attr( $a['u'] ?? '' ) . '" placeholder="Enlace del archivo" style="flex:3;min-width:240px" />';
	$html .= '<button type="button" class="button uleam-arch-quitar" title="Quitar">&times;</button>';
	$html .= '<input type="text" name="proc_arch_d[]" value="' . esc_attr( $a['d'] ?? '' ) . '" placeholder="Descripción (opcional)" style="flex:1 0 100%" /></div>';
	return $html;
}

/**
 * Contenido del metabox de archivos.
 *
 * @param WP_Post $post Procedimiento.
 */
function uleam_procesos_metabox_html( $post ) {
	wp_nonce_field( 'uleam_proc_archivos', 'uleam_proc_archivos_nonce' );
	echo '<p>Añade los documentos del procedimiento y elige en qué sección va cada uno: <strong>Manual</strong>, <strong>Flujo</strong>, <strong>Formatos</strong> o las versiones no vigentes. Esta información se muestra en la página del procedimiento.</p>';
	echo '<div id="uleam-arch-lista">';
	foreach ( uleam_procesos_archivos( $post->ID ) as $grupo ) {
		foreach ( $grupo as $a ) {
			echo uleam_procesos_fila( $a ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
	echo '</div>';
	echo '<p><button type="button" class="button button-primary" id="uleam-arch-elegir"><span class="dashicons dashicons-upload" style="vertical-align:text-bottom"></span> Elegir o subir archivos</button> ';
	echo '<button type="button" class="button" id="uleam-arch-vacio">Añadir enlace manual</button></p>';
	echo '<p class="description">Puedes elegir varios archivos a la vez de la Biblioteca de medios. Ordénalos moviéndolos con arrastrar y soltar. Si un formato no tiene archivo (por ejemplo, se genera en el sistema) o quieres dejar una aclaración ("Procedimiento sin versiones anteriores"), escribe solo el nombre y deja el enlace vacío: se mostrará como información, sin botón de descarga.</p>';
	echo '<template id="uleam-arch-plantilla">' . uleam_procesos_fila( array() ) . '</template>'; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<p><strong>Ubicación:</strong> marca a la derecha, en <em>Estructura de procesos</em>, el proceso (o macroproceso) donde aparece. Puedes marcar más de uno.</p>';
}

/**
 * Carga la Biblioteca de medios y el comportamiento del repetidor.
 *
 * @param string $hook Pantalla actual.
 */
function uleam_procesos_admin_scripts( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || ULEAM_PROC_CPT !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_add_inline_script(
		'media-editor',
		"jQuery(function($){
			var lista=$('#uleam-arch-lista'), frame;
			function tipo(n){
				n=n.toLowerCase();
				if(/diagrama|flujo/.test(n)) return 'flujo';
				if(/formato|\\-f-\\d/.test(n)) return 'formato';
				return 'manual';
			}
			function agregar(t,u){
				var f=$($('#uleam-arch-plantilla').html());
				f.find('[name=\"proc_arch_t[]\"]').val(t||'');
				f.find('[name=\"proc_arch_u[]\"]').val(u||'');
				f.find('select').val(tipo((t||'')+' '+(u||'')));
				lista.append(f);
			}
			lista.sortable({axis:'y'});
			$(document).on('click','.uleam-arch-quitar',function(){ $(this).closest('.uleam-arch-fila').remove(); });
			$('#uleam-arch-vacio').on('click',function(){ agregar('',''); });
			$('#uleam-arch-elegir').on('click',function(e){
				e.preventDefault();
				if(!frame){
					frame=wp.media({title:'Elegir archivos del procedimiento',button:{text:'Añadir al procedimiento'},multiple:true});
					frame.on('select',function(){
						frame.state().get('selection').each(function(m){ var f=m.toJSON(); agregar(f.title,f.url); });
					});
				}
				frame.open();
			});
		});"
	);
}
add_action( 'admin_enqueue_scripts', 'uleam_procesos_admin_scripts' );

/**
 * Guarda los archivos del procedimiento.
 *
 * @param int $post_id Procedimiento.
 */
function uleam_procesos_guardar( $post_id ) {
	if ( ! isset( $_POST['uleam_proc_archivos_nonce'] ) || ! wp_verify_nonce( $_POST['uleam_proc_archivos_nonce'], 'uleam_proc_archivos' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$t   = (array) ( $_POST['proc_arch_t'] ?? array() );
	$k   = (array) ( $_POST['proc_arch_s'] ?? array() );
	$d   = (array) ( $_POST['proc_arch_d'] ?? array() );
	$u   = (array) ( $_POST['proc_arch_u'] ?? array() );
	$out = array();
	foreach ( $u as $i => $url ) {
		$url    = esc_url_raw( trim( wp_unslash( $url ) ) );
		$titulo = sanitize_text_field( wp_unslash( $t[ $i ] ?? '' ) );
		if ( '' === $url && '' === $titulo ) { // Sin enlace se guarda igual: formato "generado en el sistema" o nota.
			continue;
		}
		$sec   = sanitize_key( wp_unslash( $k[ $i ] ?? 'manual' ) );
		$out[] = array(
			't' => $titulo,
			's' => isset( uleam_procesos_secciones()[ $sec ] ) ? $sec : 'manual',
			'd' => sanitize_text_field( wp_unslash( $d[ $i ] ?? '' ) ),
			'u' => $url,
		);
	}
	update_post_meta( $post_id, ULEAM_PROC_META, $out );
	update_post_meta( $post_id, '_procedimiento_editado', 1 );
}
add_action( 'save_post_' . ULEAM_PROC_CPT, 'uleam_procesos_guardar' );

/* ------------------------------------------------------------------
 * Shortcode [procesos_universitarios]
 * ------------------------------------------------------------------ */

/**
 * Ordena elementos de la estructura por su campo "orden" y luego por ID.
 *
 * @param WP_Term[] $terms Elementos.
 * @return WP_Term[]
 */
function uleam_procesos_ordenar( $terms ) {
	usort(
		$terms,
		function ( $a, $b ) {
			$oa = (int) get_term_meta( $a->term_id, 'uleam_orden', true );
			$ob = (int) get_term_meta( $b->term_id, 'uleam_orden', true );
			return $oa === $ob ? $a->term_id <=> $b->term_id : $oa <=> $ob;
		}
	);
	return $terms;
}

/**
 * Tarjeta de un documento (nombre completo, sección y botón).
 *
 * @param array $a Archivo normalizado (t, s, u, d).
 * @return string
 */
function uleam_procesos_tarjeta( $a ) {
	if ( '' === $a['u'] ) {
		return uleam_procesos_tarjeta_info( $a );
	}
	$ext  = strtolower( pathinfo( wp_parse_url( $a['u'], PHP_URL_PATH ) ?: '', PATHINFO_EXTENSION ) );
	$cls  = 'pdf' === $ext ? 'pdf' : 'docx';
	$icon = 'pdf' === $ext ? 'fa-file-pdf' : ( 0 === strpos( $ext, 'xls' ) ? 'fa-file-excel' : 'fa-file-word' );
	$sec  = uleam_procesos_secciones()[ $a['s'] ] ?? '';
	$meta = trim( $sec . ( $ext ? ' · ' . strtoupper( $ext ) : '' ), ' ·' );
	ob_start();
	?>
<div class="dgac-file-card">
<div class="dgac-file-card__left">
<div class="dgac-file-card__icon dgac-file-card__icon--<?php echo esc_attr( $cls ); ?>"><i class="fa-solid <?php echo esc_attr( $icon ); ?>"></i></div>
<div class="dgac-file-card__info">
<div class="dgac-file-card__name"><?php echo esc_html( '' !== $a['t'] ? $a['t'] : $sec ); ?></div>
	<?php if ( '' !== $a['d'] ) : ?>
<div class="dgac-file-card__meta"><?php echo esc_html( $a['d'] ); ?></div>
	<?php endif; ?>
<div class="dgac-file-card__meta"><?php echo esc_html( $meta ); ?></div>
</div>
</div>
<div class="dgac-file-card__actions"><a class="dgac-file-btn dgac-file-btn--view" href="<?php echo esc_url( $a['u'] ); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-book-open" aria-hidden="true"></i> <?php echo 'pdf' === $ext ? 'Abrir' : 'Descargar'; ?></a></div>
</div>
	<?php
	return ob_get_clean();
}

/**
 * Tarjeta sin archivo: formato que se genera en un sistema o nota aclaratoria.
 *
 * @param array $a Archivo normalizado (t, s, u, d).
 * @return string
 */
function uleam_procesos_tarjeta_info( $a ) {
	$nota = uleam_procesos_es_nota( $a );
	$meta = '' !== $a['d'] ? $a['d'] : ( $nota ? '' : 'Sin archivo descargable' );
	ob_start();
	?>
<div class="dgac-file-card dgac-file-card--info">
<div class="dgac-file-card__left">
<div class="dgac-file-card__icon dgac-file-card__icon--info"><i class="fa-solid <?php echo $nota ? 'fa-circle-info' : 'fa-file-circle-exclamation'; ?>" aria-hidden="true"></i></div>
<div class="dgac-file-card__info">
<div class="dgac-file-card__name"><?php echo esc_html( $a['t'] ); ?></div>
	<?php if ( '' !== $meta ) : ?>
<div class="dgac-file-card__meta"><?php echo esc_html( $meta ); ?></div>
	<?php endif; ?>
</div>
</div>
</div>
	<?php
	return ob_get_clean();
}

/**
 * Pestañas Manual / Flujo / Formatos / Versiones anteriores de un procedimiento.
 *
 * @param array $g Archivos agrupados por sección (uleam_procesos_archivos).
 * @return string
 */
function uleam_procesos_tabs_html( $g ) {
	$ant  = uleam_procesos_contar( $g['ant_manual'] ) + uleam_procesos_contar( $g['ant_flujo'] ) + uleam_procesos_contar( $g['ant_formato'] );
	$tabs = array(
		'manual'  => array( 'Manual vigente', 'fa-book', uleam_procesos_contar( $g['manual'] ) ),
		'flujo'   => array( 'Flujo vigente', 'fa-diagram-project', uleam_procesos_contar( $g['flujo'] ) ),
		'formato' => array( 'Formatos vigentes', 'fa-file-lines', uleam_procesos_contar( $g['formato'] ) ),
		'ant'     => array( 'Versiones Anteriores', 'fa-clock-rotate-left', $ant ),
	);
	$activa = '';
	foreach ( $tabs as $k => $t ) {
		if ( $t[2] ) {
			$activa = $k;
			break;
		}
	}
	if ( ! $activa ) { // Solo hay notas: se abre la primera pestaña que tenga alguna.
		$con    = array_filter( array( 'manual' => $g['manual'], 'flujo' => $g['flujo'], 'formato' => $g['formato'] ) );
		$activa = $con ? array_key_first( $con ) : ( $g['ant_manual'] || $g['ant_flujo'] || $g['ant_formato'] ? 'ant' : 'manual' );
	}
	$grid = function ( $lista ) {
		$h = '<div class="dgac-files-grid">';
		foreach ( $lista as $a ) {
			$h .= uleam_procesos_tarjeta( $a );
		}
		return $h . '</div>';
	};
	$vacio = '<p class="dgac-proc-empty">No hay documentos publicados en esta sección.</p>';
	$h     = '<div class="dgac-proc-tabs"><div class="dgac-proc-page__tabs" role="tablist">';
	foreach ( $tabs as $k => $t ) {
		$h .= '<button type="button" role="tab" class="dgac-proc-page__tab' . ( $k === $activa ? ' is-active' : '' ) . '" data-tab="' . esc_attr( $k ) . '" aria-selected="' . ( $k === $activa ? 'true' : 'false' ) . '"><i class="fa-solid ' . esc_attr( $t[1] ) . '" aria-hidden="true"></i> ' . esc_html( $t[0] ) . ' <span class="dgac-proc-page__count">' . (int) $t[2] . '</span></button>';
	}
	$h .= '</div>';
	foreach ( array( 'manual', 'flujo', 'formato' ) as $k ) {
		$h .= '<div class="dgac-proc-page__panel' . ( $k === $activa ? ' is-active' : '' ) . '" data-panel="' . $k . '" role="tabpanel">' . ( $g[ $k ] ? $grid( $g[ $k ] ) : $vacio ) . '</div>';
	}
	$h .= '<div class="dgac-proc-page__panel' . ( 'ant' === $activa ? ' is-active' : '' ) . '" data-panel="ant" role="tabpanel">';
	foreach ( array( 'ant_manual', 'ant_flujo', 'ant_formato' ) as $sec ) {
		if ( $g[ $sec ] && uleam_procesos_contar( $g[ $sec ] ) ) {
			$h .= '<h3 class="dgac-proc-page__sub"><i class="fa-solid fa-ban" aria-hidden="true"></i> ' . esc_html( uleam_procesos_secciones()[ $sec ] ) . '</h3>' . $grid( $g[ $sec ] );
		}
	}
	if ( ! $ant ) {
		$notas = array_merge( $g['ant_manual'], $g['ant_flujo'], $g['ant_formato'] );
		$h    .= $notas ? $grid( $notas ) : '<p class="dgac-proc-empty">No hay versiones anteriores publicadas.</p>';
	}
	return $h . '</div></div>';
}

/**
 * Script de las pestañas (delegado: sirve para todas las filas y para la página).
 *
 * @return string
 */
function uleam_procesos_tabs_js() {
	return '<script>document.addEventListener("click",function(e){var b=e.target.closest(".dgac-proc-page__tab");if(!b)return;var w=b.closest(".dgac-proc-tabs");if(!w)return;w.querySelectorAll(".dgac-proc-page__tab").forEach(function(x){x.classList.toggle("is-active",x===b);x.setAttribute("aria-selected",x===b?"true":"false");});w.querySelectorAll(".dgac-proc-page__panel").forEach(function(p){p.classList.toggle("is-active",p.getAttribute("data-panel")===b.getAttribute("data-tab"));});});</script>';
}

/**
 * Pinta un procedimiento (fila desplegable con sus documentos vigentes).
 *
 * @param WP_Post $p     Procedimiento.
 * @param string  $ctx   Texto de contexto para el buscador.
 * @return string
 */
function uleam_procesos_render_proc( $p, $ctx ) {
	$g     = uleam_procesos_archivos( $p->ID );
	$vig   = array_merge( $g['manual'], $g['flujo'], $g['formato'] );
	$total = count( $vig ) + count( $g['ant_manual'] ) + count( $g['ant_flujo'] ) + count( $g['ant_formato'] );
	$docs  = uleam_procesos_contar( $vig ) + uleam_procesos_contar( $g['ant_manual'] ) + uleam_procesos_contar( $g['ant_flujo'] ) + uleam_procesos_contar( $g['ant_formato'] );
	$term  = remove_accents( mb_strtolower( $p->post_title . ' ' . $ctx ) );
	$btn   = $docs ? sprintf( _n( '%d archivo', '%d archivos', $docs ), $docs ) : 'Ver Archivos';
	ob_start();
	?>
<details class="dgac-proc-row" data-search-term="<?php echo esc_attr( $term ); ?>">
<summary class="dgac-proc-row-summary">
<div class="dgac-proc-row-left">
<div class="dgac-proc-doc-icon"><i class="fa-solid fa-file-pdf"></i></div>
<div class="dgac-proc-info"><div class="dgac-proc-name"><?php echo esc_html( $p->post_title ); ?></div></div>
</div>
<div class="dgac-proc-row-right"><span class="dgac-proc-open-btn"><i class="fa-solid fa-folder-closed"></i> <?php echo esc_html( $btn ); ?> <i class="fa-solid fa-chevron-down"></i></span></div>
</summary>
<div class="dgac-proc-drawer">
<?php if ( ! $total ) : ?>
<div class="dgac-files-grid">
<div class="dgac-file-card">
<div class="dgac-file-card__left">
<div class="dgac-file-card__icon dgac-file-card__icon--pdf"><i class="fa-solid fa-file-pdf"></i></div>
<div class="dgac-file-card__info">
<div class="dgac-file-card__name">Documentación en el catálogo</div>
<div class="dgac-file-card__meta">Consulte los Catálogos Institucionales Vigentes al inicio de esta página</div>
</div>
</div>
<div class="dgac-file-card__actions"><a class="dgac-file-btn dgac-file-btn--view" href="#dgacCatalogCollapsibleCard"><i class="fa-solid fa-book-open" aria-hidden="true"></i> Ir a catálogos</a></div>
</div>
</div>
	<?php else : ?>
		<?php echo uleam_procesos_tabs_html( $g ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>
</div>
</details>
	<?php
	return ob_get_clean();
}

/**
 * Shortcode que muestra la estructura completa de procesos.
 *
 * @return string
 */
function uleam_procesos_shortcode() {
	$terms = get_terms( array( 'taxonomy' => ULEAM_PROC_TAX, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return '';
	}
	$hijos = array();
	foreach ( $terms as $t ) {
		$hijos[ $t->parent ][] = $t;
	}
	$posts = get_posts(
		array(
			'post_type'      => ULEAM_PROC_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	$por_term = array();
	foreach ( $posts as $p ) {
		$ts = get_the_terms( $p->ID, ULEAM_PROC_TAX );
		foreach ( is_array( $ts ) ? $ts : array() as $t ) {
			$por_term[ $t->term_id ][ $p->ID ] = $p;
		}
	}
	// Procedimientos (distintos) bajo un elemento y todos sus descendientes.
	$contar = function ( $term_id ) use ( &$contar, $hijos, $por_term ) {
		$ids = isset( $por_term[ $term_id ] ) ? array_keys( $por_term[ $term_id ] ) : array();
		foreach ( $hijos[ $term_id ] ?? array() as $h ) {
			$ids = array_merge( $ids, $contar( $h->term_id ) );
		}
		return array_values( array_unique( $ids ) );
	};

	$subs = uleam_procesos_ordenar( $hijos[0] ?? array() );
	if ( ! $subs ) {
		return '';
	}
	$total = count( $posts );

	ob_start();
	?>
<section class="dgac-subsistemas-section" aria-label="Subsistemas de Gestión Universitaria">
<div class="dgac-section-title-wrap">
<h2 class="dgac-section-title"><i class="fa-solid fa-circle-check"></i> Subsistemas de Gestión Universitaria</h2>
<div class="dgac-count-badge" id="dgacProcCounter">Cargando procedimientos...</div>
</div>
<div class="dgac-toolbar">
<div class="dgac-search-box">
<i class="fa-solid fa-magnifying-glass"></i>
<input type="search" id="dgacProcessSearch" class="dgac-search-input" placeholder="Buscar procedimiento o macroproceso (ej. Matrícula, Titulación, Nómina)…" aria-label="Buscar procedimiento">
</div>
<div class="dgac-toolbar-actions">
<button type="button" class="dgac-toggle-all-btn" id="dgacToggleAllBtn" aria-expanded="false">
<i class="fa-solid fa-up-down"></i> <span id="dgacToggleAllText">Expandir todos</span>
</button>
</div>
</div>
<nav class="dgac-subsistema-tabs" role="tablist" aria-label="Subsistemas Universitarios">
	<?php
	foreach ( $subs as $i => $s ) :
		$ids   = $contar( $s->term_id );
		$icono = get_term_meta( $s->term_id, 'uleam_icono', true ) ?: 'fa-solid fa-sitemap';
		$etiq  = get_term_meta( $s->term_id, 'uleam_etiqueta', true ) ?: mb_strtoupper( $s->name );
		?>
<button type="button" class="dgac-subsistema-tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-controls="subsys-<?php echo esc_attr( $s->slug ); ?>" id="subtab-<?php echo esc_attr( $s->slug ); ?>" data-subsys="<?php echo esc_attr( $s->slug ); ?>">
<span class="dgac-subtab-badge"><i class="<?php echo esc_attr( $icono ); ?>"></i> <?php echo esc_html( sprintf( '%02d. %s', $i + 1, $etiq ) ); ?></span>
<strong class="dgac-subtab-title"><?php echo esc_html( $s->name ); ?></strong>
<span class="dgac-subtab-meta"><?php echo (int) count( $hijos[ $s->term_id ] ?? array() ); ?> macroprocesos · <?php echo (int) count( $ids ); ?> procedimientos</span>
</button>
	<?php endforeach; ?>
</nav>
	<?php
	foreach ( $subs as $i => $s ) :
		?>
<div id="subsys-<?php echo esc_attr( $s->slug ); ?>" class="dgac-subsistema-content<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel" aria-labelledby="subtab-<?php echo esc_attr( $s->slug ); ?>">
		<?php
		foreach ( uleam_procesos_ordenar( $hijos[ $s->term_id ] ?? array() ) as $m ) :
			$color = get_term_meta( $m->term_id, 'uleam_color', true ) ?: 'blue';
			$icono = get_term_meta( $m->term_id, 'uleam_icono', true ) ?: 'fa-solid fa-diagram-project';
			$n     = count( $contar( $m->term_id ) );
			?>
<details class="dgac-macro-card dgac-macro-card--<?php echo esc_attr( $color ); ?>">
<summary class="dgac-macro-summary">
<div class="dgac-macro-summary__left"><div class="dgac-macro-icon"><i class="<?php echo esc_attr( $icono ); ?>"></i></div><span class="dgac-macro-title"><?php echo esc_html( $m->name ); ?></span></div>
<div class="dgac-macro-summary__right"><span class="dgac-macro-count"><?php echo esc_html( $n . ( 1 === $n ? ' Procedimiento' : ' Procedimientos' ) ); ?></span><span class="dgac-macro-chevron"><i class="fa-solid fa-chevron-down"></i></span></div>
</summary>
<div class="dgac-macro-body">
			<?php
			$procs = uleam_procesos_ordenar( $hijos[ $m->term_id ] ?? array() );
			$sueltos = $por_term[ $m->term_id ] ?? array();
			if ( ! $procs && ! $sueltos ) {
				echo '<p class="dgac-proc-empty">Sin procedimientos publicados en este macroproceso.</p>';
			}
			if ( $sueltos ) {
				echo '<div class="dgac-subproceso-group"><div class="dgac-proc-list">';
				foreach ( $sueltos as $p ) {
					echo uleam_procesos_render_proc( $p, $m->name ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				echo '</div></div>';
			}
			foreach ( $procs as $pr ) :
				?>
<div class="dgac-subproceso-group"><h4 class="dgac-subproceso-title"><i class="fa-solid fa-folder-open"></i> <?php echo esc_html( $pr->name ); ?></h4><div class="dgac-proc-list">
				<?php
				$items = $por_term[ $pr->term_id ] ?? array();
				if ( ! $items ) {
					echo '<p class="dgac-proc-empty">Sin procedimientos publicados.</p>';
				}
				foreach ( $items as $p ) {
					echo uleam_procesos_render_proc( $p, $pr->name . ' ' . $m->name ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
</div></div>
			<?php endforeach; ?>
</div>
</details>
		<?php endforeach; ?>
</div>
	<?php endforeach; ?>
</section>
	<?php
	echo uleam_procesos_tabs_js(); // phpcs:ignore WordPress.Security.EscapeOutput
	return ob_get_clean();
}
add_shortcode( 'procesos_universitarios', 'uleam_procesos_shortcode' );

/* ------------------------------------------------------------------
 * Sincronización con los datos iniciales (una vez por versión de datos)
 * ------------------------------------------------------------------ */

const ULEAM_PROC_DATOS_VERSION = '13';

/**
 * Crea o actualiza la estructura y los procedimientos desde inc/data/procesos-iniciales.json
 * y deja el shortcode en la página "gestion-de-procesos". Se ejecuta una sola vez por
 * versión de datos. Los procedimientos creados a mano (posteriores a la primera importación)
 * no se tocan.
 */
function uleam_procesos_sincronizar() {
	if ( get_option( 'uleam_procesos_sync' ) === ULEAM_PROC_DATOS_VERSION || get_transient( 'uleam_procesos_importando' ) ) {
		return;
	}
	$archivo = ULEAM_THEME_DIR . '/inc/data/procesos-iniciales.json';
	if ( ! is_readable( $archivo ) || ! taxonomy_exists( ULEAM_PROC_TAX ) ) {
		return;
	}
	$datos = json_decode( (string) file_get_contents( $archivo ), true );
	if ( ! is_array( $datos ) ) {
		return;
	}
	set_transient( 'uleam_procesos_importando', 1, 600 );
	ignore_user_abort( true );
	@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors

	$primera = (int) get_option( 'uleam_procesos_importado', 0 ); // 0 = nunca importado.

	$termino = function ( $nombre, $padre, $orden, $meta = array() ) {
		$ex = term_exists( $nombre, ULEAM_PROC_TAX, $padre );
		$id = $ex ? (int) ( is_array( $ex ) ? $ex['term_id'] : $ex ) : 0;
		if ( ! $id ) {
			$r = wp_insert_term( $nombre, ULEAM_PROC_TAX, array( 'parent' => $padre ) );
			if ( is_wp_error( $r ) ) {
				return 0;
			}
			$id = (int) $r['term_id'];
		}
		update_term_meta( $id, 'uleam_orden', $orden );
		foreach ( $meta as $k => $v ) {
			update_term_meta( $id, $k, $v );
		}
		return $id;
	};

	// Procedimientos existentes por título (los creados a mano no se tocan).
	$existentes = array();
	foreach ( get_posts( array( 'post_type' => ULEAM_PROC_CPT, 'post_status' => 'any', 'posts_per_page' => -1 ) ) as $ya ) {
		$existentes[ $ya->post_title ] = $ya;
	}
	$vistos = array();

	foreach ( $datos as $s ) {
		$sid = $termino( $s['nombre'], 0, $s['orden'], array( 'uleam_icono' => 'fa-solid ' . $s['icono'], 'uleam_etiqueta' => $s['etiqueta'] ) );
		foreach ( $s['macros'] as $m ) {
			$mid = $termino( $m['nombre'], $sid, $m['orden'], array( 'uleam_color' => $m['color'], 'uleam_icono' => $m['icono'] ) );
			foreach ( $m['procesos'] as $pr ) {
				$pid = $pr['nombre'] ? $termino( $pr['nombre'], $mid, $pr['orden'] ) : $mid;
				foreach ( $pr['procs'] as $n => $p ) {
					$arch = array();
					foreach ( $p['archivos'] as $a ) {
						$arch[] = array(
							't' => $a['t'],
							's' => $a['s'] ?? 'manual',
							'd' => $a['d'] ?? '',
							'u' => $a['u'],
						);
					}
					$clave = $p['titulo'];
					if ( isset( $vistos[ $clave ] ) ) { // Mismo procedimiento en otra ubicación.
						wp_set_object_terms( $vistos[ $clave ], array( $pid ), ULEAM_PROC_TAX, true );
						continue;
					}
					$post_id = isset( $existentes[ $clave ] ) ? $existentes[ $clave ]->ID : 0;
					if ( ! $post_id ) {
						$post_id = wp_insert_post(
							array(
								'post_type'   => ULEAM_PROC_CPT,
								'post_status' => 'publish',
								'post_title'  => $clave,
								'menu_order'  => $n + 1,
							)
						);
						if ( is_wp_error( $post_id ) || ! $post_id ) {
							continue;
						}
					} elseif ( get_post_meta( $post_id, '_procedimiento_editado', true ) || ( $primera && strtotime( $existentes[ $clave ]->post_date_gmt ) > $primera ) ) {
						$vistos[ $clave ] = $post_id; // Creado a mano después de la importación: no se toca.
						continue;
					} else {
						wp_update_post( array( 'ID' => $post_id, 'menu_order' => $n + 1, 'post_status' => 'publish' ) );
					}
					$vistos[ $clave ] = $post_id;
					wp_set_object_terms( $post_id, array( $pid ), ULEAM_PROC_TAX );
					update_post_meta( $post_id, ULEAM_PROC_META, $arch );
					if ( ! empty( $p['url'] ) ) {
						update_post_meta( $post_id, '_procedimiento_origen', esc_url_raw( $p['url'] ) );
					}
				}
			}
		}
	}

	// Procedimientos importados antes que ya no existen en el sitio oficial: a la papelera.
	foreach ( $existentes as $titulo => $post ) {
		if ( ! isset( $vistos[ $titulo ] ) && ( ! $primera || strtotime( $post->post_date_gmt ) <= $primera ) ) {
			wp_trash_post( $post->ID );
		}
	}

	// La página queda con dos shortcodes: la información del área y la estructura de procesos.
	$pagina = get_page_by_path( 'gestion-de-procesos' );
	if ( $pagina ) {
		wp_update_post( array( 'ID' => $pagina->ID, 'post_content' => "[procesos_info]\n\n[procesos_universitarios]\n" ) );
	}

	if ( ! $primera ) {
		update_option( 'uleam_procesos_importado', time(), false );
	}
	update_option( 'uleam_procesos_sync', ULEAM_PROC_DATOS_VERSION, false );
	delete_transient( 'uleam_procesos_importando' );
}
add_action( 'init', 'uleam_procesos_sincronizar', 30 );

/* ------------------------------------------------------------------
 * Información del área (catálogos, misión, objetivo y productos) editable
 * ------------------------------------------------------------------ */

const ULEAM_PROC_INFO_OPT = 'uleam_procesos_info';

/**
 * Valores guardados (o los iniciales oficiales).
 *
 * @return array
 */
function uleam_procesos_info() {
	$def = array();
	$f   = ULEAM_THEME_DIR . '/inc/data/procesos-info.json';
	if ( is_readable( $f ) ) {
		$j   = json_decode( (string) file_get_contents( $f ), true );
		$def = is_array( $j ) ? $j : array();
	}
	$cur = get_option( ULEAM_PROC_INFO_OPT, array() );
	return array_merge( array( 'mision' => '', 'objetivo' => '', 'productos' => '', 'cat_proc' => '', 'cat_form' => '' ), $def, is_array( $cur ) ? array_filter( $cur, 'strlen' ) : array() );
}

/**
 * Submenú "Información del área" dentro de Gestión de Procesos.
 */
function uleam_procesos_info_menu() {
	add_submenu_page(
		'edit.php?post_type=' . ULEAM_PROC_CPT,
		'Información del área',
		'Información del área',
		'edit_pages',
		'uleam-procesos-info',
		'uleam_procesos_info_page'
	);
}
add_action( 'admin_menu', 'uleam_procesos_info_menu' );

/**
 * Pantalla de edición de la información del área.
 */
function uleam_procesos_info_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$guardado = false;
	if ( isset( $_POST['uleam_info_nonce'] ) && wp_verify_nonce( $_POST['uleam_info_nonce'], 'uleam_info' ) ) {
		$nuevo = array(
			'mision'    => sanitize_textarea_field( wp_unslash( $_POST['mision'] ?? '' ) ),
			'objetivo'  => sanitize_textarea_field( wp_unslash( $_POST['objetivo'] ?? '' ) ),
			'productos' => sanitize_textarea_field( wp_unslash( $_POST['productos'] ?? '' ) ),
			'cat_proc'  => esc_url_raw( wp_unslash( $_POST['cat_proc'] ?? '' ) ),
			'cat_form'  => esc_url_raw( wp_unslash( $_POST['cat_form'] ?? '' ) ),
		);
		update_option( ULEAM_PROC_INFO_OPT, $nuevo, false );
		$guardado = true;
	}
	$i = uleam_procesos_info();
	wp_enqueue_media();
	?>
	<div class="wrap">
		<h1>Información del área de Gestión de Procesos</h1>
		<?php if ( $guardado ) : ?>
			<div class="notice notice-success is-dismissible"><p>Cambios guardados. Ya se ven en la página Gestión de Procesos.</p></div>
		<?php endif; ?>
		<div class="notice notice-info inline"><p><strong>Cómo se edita esta página:</strong>
			1) Aquí cambias la Misión, el Objetivo, los Productos y los catálogos.
			2) En <em>Procedimientos</em> editas cada procedimiento y sus documentos (manual, flujo, formatos y versiones anteriores).
			3) En <em>Estructura de procesos</em> creas o renombras subsistemas, macroprocesos y procesos.</p></div>
		<form method="post">
			<?php wp_nonce_field( 'uleam_info', 'uleam_info_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tr><th><label for="mision">Misión</label></th><td><textarea name="mision" id="mision" rows="5" class="large-text"><?php echo esc_textarea( $i['mision'] ); ?></textarea></td></tr>
				<tr><th><label for="objetivo">Objetivo</label></th><td><textarea name="objetivo" id="objetivo" rows="5" class="large-text"><?php echo esc_textarea( $i['objetivo'] ); ?></textarea></td></tr>
				<tr><th><label for="productos">Productos</label></th><td>
					<textarea name="productos" id="productos" rows="12" class="large-text"><?php echo esc_textarea( $i['productos'] ); ?></textarea>
					<p class="description">Un producto por línea. Para que una línea sea un enlace, escribe el texto, una barra vertical y el enlace: <code>Plan anual de trabajo | https://…/archivo.pdf</code></p></td></tr>
				<tr><th><label for="cat_proc">Catálogo de Procedimientos (PDF)</label></th><td>
					<input type="url" name="cat_proc" id="cat_proc" value="<?php echo esc_attr( $i['cat_proc'] ); ?>" class="large-text" />
					<button type="button" class="button uleam-info-elegir" data-target="cat_proc">Elegir o subir archivo</button></td></tr>
				<tr><th><label for="cat_form">Catálogo de Formatos (PDF)</label></th><td>
					<input type="url" name="cat_form" id="cat_form" value="<?php echo esc_attr( $i['cat_form'] ); ?>" class="large-text" />
					<button type="button" class="button uleam-info-elegir" data-target="cat_form">Elegir o subir archivo</button></td></tr>
			</table>
			<?php submit_button( 'Guardar cambios' ); ?>
		</form>
	</div>
	<script>
	jQuery(function($){
		$('.uleam-info-elegir').on('click',function(e){
			e.preventDefault();
			var t=$('#'+$(this).data('target'));
			var f=wp.media({title:'Elegir archivo',button:{text:'Usar este archivo'},multiple:false});
			f.on('select',function(){ t.val(f.state().get('selection').first().toJSON().url); });
			f.open();
		});
	});
	</script>
	<?php
}

/**
 * Shortcode [procesos_info]: catálogos y bloque de misión / objetivo / productos.
 *
 * @return string
 */
function uleam_procesos_info_shortcode() {
	$i = uleam_procesos_info();
	ob_start();
	?>
<section class="dgac-proc-combined-section dgac-proc-section--catalogs" aria-label="Catálogos Institucionales">
<details class="dgac-proc-collapse-card" id="dgacCatalogCollapsibleCard" data-open-text="Recoger catálogos" data-close-text="Mostrar catálogos">
<summary class="dgac-proc-collapse-summary">
<div class="dgac-proc-collapse-summary__left">
<div class="dgac-proc-collapse-summary__icon"><i class="fa-solid fa-book-bookmark" aria-hidden="true"></i></div>
<div class="dgac-proc-collapse-summary__text">
<h2 class="dgac-proc-collapse-summary__title">Catálogos Institucionales Vigentes</h2>
<p class="dgac-proc-collapse-summary__desc">Manuales de procedimientos, guías, instructivos y formatos en PDF</p>
</div>
</div>
<div class="dgac-proc-collapse-summary__right">
<span class="dgac-proc-collapse-summary__badge"><i class="fa-solid fa-file-pdf" aria-hidden="true"></i> 2 Catálogos PDF</span>
<span class="dgac-proc-collapse-toggle-btn"><span class="dgac-proc-collapse-toggle-label">Mostrar catálogos</span> <i class="fa-solid fa-chevron-down dgac-proc-collapse-chevron" aria-hidden="true"></i></span>
</div>
</summary>
<div class="dgac-proc-collapse-content">
<div class="dgac-catalog-banner" aria-label="Catálogos Institucionales">
<div class="dgac-catalog-banner__head"><i class="fa-solid fa-thumbtack" aria-hidden="true"></i>
<h3>Consulte los Manuales de Procedimientos, Guías, Instructivos y Formatos vigentes en los siguientes Catálogos:</h3></div>
<div class="dgac-catalog-cards">
<a href="<?php echo esc_url( uleam_url( $i['cat_proc'] ) ); ?>" class="dgac-catalog-card dgac-catalog-card--procedimientos" target="_blank" rel="noopener">
<div class="dgac-catalog-card__info"><div class="dgac-catalog-card__icon"><i class="fa-solid fa-book-bookmark" aria-hidden="true"></i></div>
<div><h4 class="dgac-catalog-card__title">Catálogo de Procedimientos Institucionales</h4><p class="dgac-catalog-card__desc">Compendio unificado de manuales, instructivos y guías vigentes</p></div></div>
<span class="dgac-catalog-card__btn"><i class="fa-solid fa-file-pdf" aria-hidden="true"></i> Consultar <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
</a>
<a href="<?php echo esc_url( uleam_url( $i['cat_form'] ) ); ?>" class="dgac-catalog-card dgac-catalog-card--formatos" target="_blank" rel="noopener">
<div class="dgac-catalog-card__info"><div class="dgac-catalog-card__icon"><i class="fa-solid fa-folder-tree" aria-hidden="true"></i></div>
<div><h4 class="dgac-catalog-card__title">Catálogo de Formatos Institucionales</h4><p class="dgac-catalog-card__desc">Plantillas y formatos oficiales aprobados por la DGAC</p></div></div>
<span class="dgac-catalog-card__btn"><i class="fa-solid fa-file-lines" aria-hidden="true"></i> Consultar <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
</a>
</div>
</div>
<div class="dgac-proc-collapse-footer"><button type="button" class="dgac-proc-collapse-close-btn" aria-label="Recoger sección de catálogos"><i class="fa-solid fa-chevron-up" aria-hidden="true"></i> Recoger catálogos</button></div>
</div>
</details>
</section>

<section class="dgac-proc-combined-section" aria-label="Información General del Área">
<details class="dgac-proc-collapse-card" id="dgacProcCollapsibleCard" data-open-text="Recoger información" data-close-text="Mostrar información">
<summary class="dgac-proc-collapse-summary">
<div class="dgac-proc-collapse-summary__left">
<div class="dgac-proc-collapse-summary__icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></div>
<div class="dgac-proc-collapse-summary__text">
<h2 class="dgac-proc-collapse-summary__title">Información General del Área de Gestión de Procesos</h2>
<p class="dgac-proc-collapse-summary__desc">Misión, objetivo y productos institucionales del área</p>
</div>
</div>
<div class="dgac-proc-collapse-summary__right">
<span class="dgac-proc-collapse-summary__badge"><i class="fa-solid fa-list-check" aria-hidden="true"></i> Misión · Objetivo · Productos</span>
<span class="dgac-proc-collapse-toggle-btn"><span class="dgac-proc-collapse-toggle-label">Mostrar información</span> <i class="fa-solid fa-chevron-down dgac-proc-collapse-chevron" aria-hidden="true"></i></span>
</div>
</summary>
<div class="dgac-proc-collapse-content">
<div class="dgac-proc-intro" aria-label="Información General del Área">
<div class="dgac-pill-tabs" role="tablist" aria-label="Información Institucional">
<button type="button" class="dgac-pill-tab is-active" role="tab" aria-controls="panel-mision" id="tab-mision" data-tab="mision"><span class="dot dot--green"></span> Misión</button>
<button type="button" class="dgac-pill-tab" role="tab" aria-controls="panel-objetivo" id="tab-objetivo" data-tab="objetivo"><span class="dot dot--orange"></span> Objetivo</button>
<button type="button" class="dgac-pill-tab" role="tab" aria-controls="panel-productos" id="tab-productos" data-tab="productos"><span class="dot dot--blue"></span> Productos</button>
</div>
<div id="panel-mision" class="dgac-overview-panel is-active" role="tabpanel" aria-labelledby="tab-mision"><div class="dgac-overview-card"><p><?php echo esc_html( $i['mision'] ); ?></p></div></div>
<div id="panel-objetivo" class="dgac-overview-panel" role="tabpanel" aria-labelledby="tab-objetivo"><div class="dgac-overview-card"><p><?php echo esc_html( $i['objetivo'] ); ?></p></div></div>
<div id="panel-productos" class="dgac-overview-panel" role="tabpanel" aria-labelledby="tab-productos">
<div class="dgac-products-grid">
	<?php
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $i['productos'] ) as $linea ) {
		$linea = trim( $linea );
		if ( '' === $linea ) {
			continue;
		}
		$partes = array_map( 'trim', explode( '|', $linea, 2 ) );
		$texto  = esc_html( $partes[0] );
		if ( ! empty( $partes[1] ) ) {
			$texto = '<a href="' . esc_url( uleam_url( $partes[1] ) ) . '" target="_blank" rel="noopener">' . $texto . '</a>';
		}
		echo '<div class="dgac-product-item"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> <span>' . $texto . '</span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	?>
</div>
</div>
</div>
<div class="dgac-proc-collapse-footer"><button type="button" class="dgac-proc-collapse-close-btn" aria-label="Recoger sección de información"><i class="fa-solid fa-chevron-up" aria-hidden="true"></i> Recoger información</button></div>
</div>
</details>
</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'procesos_info', 'uleam_procesos_info_shortcode' );

/**
 * Regenera los enlaces permanentes una vez para activar las páginas de procedimiento.
 */
function uleam_procesos_flush() {
	if ( '1' !== get_option( 'uleam_procesos_rewrite' ) ) {
		flush_rewrite_rules( false );
		update_option( 'uleam_procesos_rewrite', '1', false );
	}
}
add_action( 'init', 'uleam_procesos_flush', 99 );
