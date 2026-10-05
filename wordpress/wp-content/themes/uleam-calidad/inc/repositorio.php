<?php
/**
 * Repositorio documental automático
 *
 * Cada archivo es un "Documento" (CPT `documento`) clasificado por:
 * - Sección (`seccion`): Área › Subsección. Las áreas hijas de la sección elegida
 *   en el bloque se muestran como pestañas.
 * - Año (`anio`): cada año/periodo es un acordeón.
 * - Tipo de documento (`tipo_documento`): agrupa los archivos dentro del año.
 *
 * El bloque "Repositorio documental" arma la página sola: para añadir un archivo
 * basta con crear un Documento y marcar su sección, año y tipo.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra la taxonomía Sección y habilita el orden manual de documentos.
 */
function uleam_repositorio_register() {
	register_taxonomy(
		'seccion',
		'documento',
		array(
			'labels'            => array(
				'name'              => 'Secciones',
				'singular_name'     => 'Sección',
				'menu_name'         => 'Secciones',
				'all_items'         => 'Todas las secciones',
				'edit_item'         => 'Editar sección',
				'add_new_item'      => 'Añadir nueva sección',
				'parent_item'       => 'Sección superior',
				'search_items'      => 'Buscar secciones',
				'not_found'         => 'No se encontraron secciones',
			),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
			'public'            => false,
			'show_ui'           => true,
		)
	);
	add_post_type_support( 'documento', 'page-attributes' );
}
add_action( 'init', 'uleam_repositorio_register', 20 );

/* -------------------------------------------------------------------------
 * Campos extra de la Sección: orden de la pestaña e icono
 * ---------------------------------------------------------------------- */

/**
 * Campos en el formulario "Añadir sección".
 */
function uleam_seccion_add_fields() {
	?>
	<div class="form-field">
		<label for="seccion_orden">Orden</label>
		<input type="number" name="seccion_orden" id="seccion_orden" value="0" />
		<p>Posición de la pestaña (1, 2, 3…). Las de menor número aparecen primero.</p>
	</div>
	<div class="form-field">
		<label for="seccion_icono">Icono</label>
		<input type="text" name="seccion_icono" id="seccion_icono" placeholder="fa-solid fa-folder-open" />
		<p>Opcional. Nombre de un icono de <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" rel="noopener">Font Awesome</a>.</p>
	</div>
	<?php
}
add_action( 'seccion_add_form_fields', 'uleam_seccion_add_fields' );

/**
 * Campos en el formulario "Editar sección".
 *
 * @param WP_Term $term Término actual.
 */
function uleam_seccion_edit_fields( $term ) {
	$orden = (int) get_term_meta( $term->term_id, 'orden', true );
	$icono = get_term_meta( $term->term_id, 'icono', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="seccion_orden">Orden</label></th>
		<td>
			<input type="number" name="seccion_orden" id="seccion_orden" value="<?php echo esc_attr( $orden ); ?>" />
			<p class="description">Posición de la pestaña (1, 2, 3…). Las de menor número aparecen primero.</p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="seccion_icono">Icono</label></th>
		<td>
			<input type="text" name="seccion_icono" id="seccion_icono" value="<?php echo esc_attr( $icono ); ?>" placeholder="fa-solid fa-folder-open" />
			<p class="description">Opcional. Nombre de un icono de <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" rel="noopener">Font Awesome</a>.</p>
		</td>
	</tr>
	<?php
}
add_action( 'seccion_edit_form_fields', 'uleam_seccion_edit_fields' );

/**
 * Guarda orden e icono de la sección.
 *
 * @param int $term_id ID del término.
 */
function uleam_seccion_save_fields( $term_id ) {
	// El nonce del formulario de términos ya lo verifica WordPress.
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['seccion_orden'] ) ) {
		update_term_meta( $term_id, 'orden', (int) $_POST['seccion_orden'] );
	}
	if ( isset( $_POST['seccion_icono'] ) ) {
		update_term_meta( $term_id, 'icono', sanitize_text_field( wp_unslash( $_POST['seccion_icono'] ) ) );
	}
}
add_action( 'created_seccion', 'uleam_seccion_save_fields' );
add_action( 'edited_seccion', 'uleam_seccion_save_fields' );

// El mismo campo "Orden" sirve para ordenar los grupos de Tipo de documento.
add_action( 'tipo_documento_add_form_fields', 'uleam_seccion_add_fields' );
add_action( 'tipo_documento_edit_form_fields', 'uleam_seccion_edit_fields' );
add_action( 'created_tipo_documento', 'uleam_seccion_save_fields' );
add_action( 'edited_tipo_documento', 'uleam_seccion_save_fields' );

/* -------------------------------------------------------------------------
 * Tipos de documento consistentes
 * ---------------------------------------------------------------------- */

/**
 * Tipo de documento según palabras del título. Se usa cuando un documento se guarda sin tipo.
 * El orden de las reglas importa: "Formato de plan…" es un Formato, no un Plan.
 *
 * @param string $titulo Título del documento.
 * @return string Nombre del tipo.
 */
function uleam_tipo_por_titulo( $titulo ) {
	// Guiones bajos como espacios: "Plan_de_acción" debe reconocer "plan".
	$t      = ' ' . preg_replace( '/[_\s]+/', ' ', strtolower( remove_accents( $titulo ) ) ) . ' ';
	$reglas = array(
		'Modelo de evaluación'       => '/\bmodelo\b/',
		'Normativa'                  => '/reglamento|resolucion|politica|lineamiento|directri|\bley\b|acuerdo|normativ|codigo|estatuto|sistema de gestion de la calidad/',
		// Códigos institucionales: -F- = formato, -G- = guía.
		'Formatos'                   => '/\bformato|-f-\d|\bf-\d/',
		'Informes'                   => '/informe|resultado/',
		'Manuales y procedimientos'  => '/manual|procedimiento|instructivo|guia(?! de preguntas)|diagrama|protocolo|-g-\d/',
		'Instrumentos y matrices'    => '/matriz|matrices|instrumento|ficha|encuesta|cuestionario|guia de preguntas|lista|formula|rubrica|evidencia|valoracion|calculo/',
		'Talleres y capacitación'    => '/taller|capacitacion/',
		'Socialización y difusión'   => '/presentacion|socializ|difusion|jornada|convocatoria|debate|video/',
		'Planificación y cronograma' => '/cronograma|planificacion|\bplan\b|agenda|calendario|programacion/',
	);
	foreach ( $reglas as $tipo => $rx ) {
		if ( preg_match( $rx, $t ) ) {
			return $tipo;
		}
	}
	return 'Otros documentos';
}

/**
 * Si un documento queda sin tipo, se le asigna uno según su título.
 * Corre después de guardar términos, tanto en el editor de bloques como en el clásico.
 *
 * @param int     $post_id ID del documento.
 * @param WP_Post $post    Documento.
 */
function uleam_documento_tipo_por_defecto( $post_id, $post ) {
	if ( 'documento' !== $post->post_type || wp_is_post_revision( $post_id ) || 'auto-draft' === $post->post_status ) {
		return;
	}
	$tipos = wp_get_object_terms( $post_id, 'tipo_documento', array( 'fields' => 'ids' ) );
	if ( $tipos || is_wp_error( $tipos ) ) {
		return;
	}
	$nombre = uleam_tipo_por_titulo( $post->post_title );
	$term   = get_term_by( 'name', $nombre, 'tipo_documento' );
	$id     = $term ? $term->term_id : wp_insert_term( $nombre, 'tipo_documento' );
	if ( is_array( $id ) ) {
		$id = $id['term_id'];
	}
	if ( $id && ! is_wp_error( $id ) ) {
		wp_set_object_terms( $post_id, array( (int) $id ), 'tipo_documento' );
	}
}
add_action( 'wp_after_insert_post', 'uleam_documento_tipo_por_defecto', 10, 2 );

/**
 * Clave para comparar nombres: sin tildes, mayúsculas, espacios ni plurales.
 * "Formato", "formatos" y "FORMATOS " dan la misma clave.
 *
 * @param string $name Nombre del término.
 * @return string
 */
function uleam_term_clave( $name ) {
	$name     = strtolower( remove_accents( trim( $name ) ) );
	$palabras = preg_split( '/[^a-z0-9]+/', $name, -1, PREG_SPLIT_NO_EMPTY );
	$palabras = array_map(
		function ( $p ) {
			return preg_replace( '/(es|s)$/', '', $p );
		},
		$palabras
	);
	return implode( '', $palabras );
}

/**
 * Impide crear un tipo, sección o año casi igual a uno que ya existe.
 *
 * @param string|WP_Error $term     Nombre del término nuevo.
 * @param string          $taxonomy Taxonomía.
 * @param array|string    $args     Argumentos (incluye el padre).
 * @return string|WP_Error
 */
function uleam_evitar_terminos_duplicados( $term, $taxonomy, $args = array() ) {
	if ( is_wp_error( $term ) || ! in_array( $taxonomy, array( 'tipo_documento', 'seccion', 'anio' ), true ) ) {
		return $term;
	}
	$args      = wp_parse_args( $args, array( 'parent' => 0 ) );
	$clave     = uleam_term_clave( $term );
	$existentes = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			// En Secciones, la misma subsección puede repetirse bajo áreas distintas.
			'parent'     => 'seccion' === $taxonomy ? (int) $args['parent'] : '',
		)
	);
	foreach ( (array) $existentes as $existente ) {
		if ( $existente instanceof WP_Term && uleam_term_clave( $existente->name ) === $clave ) {
			return new WP_Error(
				'uleam_termino_duplicado',
				sprintf( 'Ya existe «%s». Selecciónalo de la lista en lugar de crear uno nuevo.', $existente->name )
			);
		}
	}
	return $term;
}
add_filter( 'pre_insert_term', 'uleam_evitar_terminos_duplicados', 10, 3 );

/* -------------------------------------------------------------------------
 * Consultas y utilidades
 * ---------------------------------------------------------------------- */

/**
 * Secciones hijas ordenadas por el campo "orden" y luego por nombre.
 *
 * @param int $parent ID de la sección padre.
 * @return WP_Term[]
 */
function uleam_seccion_hijas( $parent ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'seccion',
			'parent'     => $parent,
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	usort(
		$terms,
		function ( $a, $b ) {
			$oa = (int) get_term_meta( $a->term_id, 'orden', true );
			$ob = (int) get_term_meta( $b->term_id, 'orden', true );
			return $oa === $ob ? strnatcasecmp( $a->name, $b->name ) : $oa - $ob;
		}
	);
	return $terms;
}

/**
 * Ordena periodos: años más recientes primero; etiquetas sin año (p. ej. "Histórico") al final.
 *
 * @param string $a Periodo A.
 * @param string $b Periodo B.
 * @return int
 */
function uleam_periodo_cmp( $a, $b ) {
	$ya = preg_match( '/^\d{4}/', $a );
	$yb = preg_match( '/^\d{4}/', $b );
	if ( $ya !== $yb ) {
		return $ya ? -1 : 1;
	}
	return $ya ? strnatcasecmp( $b, $a ) : strnatcasecmp( $a, $b );
}

/**
 * Icono Font Awesome según la extensión del archivo.
 *
 * @param string $url URL del archivo.
 * @return string Clase del icono.
 */
function uleam_doc_icono( $url ) {
	$ext = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$map = array(
		'pdf'  => 'fa-file-pdf',
		'doc'  => 'fa-file-word',
		'docx' => 'fa-file-word',
		'xls'  => 'fa-file-excel',
		'xlsx' => 'fa-file-excel',
		'ppt'  => 'fa-file-powerpoint',
		'pptx' => 'fa-file-powerpoint',
	);
	return 'fa-solid ' . ( isset( $map[ $ext ] ) ? $map[ $ext ] : 'fa-file-lines' );
}

/* -------------------------------------------------------------------------
 * Renderizado
 * ---------------------------------------------------------------------- */

/**
 * Fila de un documento con botones Ver / Descargar.
 *
 * @param WP_Post $doc Documento.
 * @return string
 */
function uleam_repositorio_fila( $doc ) {
	$url = get_post_meta( $doc->ID, '_documento_url', true );
	if ( ! $url ) {
		return '';
	}
	$ext  = strtoupper( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$file = wp_basename( wp_parse_url( $url, PHP_URL_PATH ) );
	$edit = current_user_can( 'edit_post', $doc->ID ) ? get_edit_post_link( $doc->ID ) : '';

	ob_start();
	?>
	<div class="dgac-doc-row" data-buscar="<?php echo esc_attr( wp_strip_all_tags( $doc->post_title ) ); ?>">
		<div class="dgac-doc-row-left">
			<div class="dgac-doc-row-icon"><i class="<?php echo esc_attr( uleam_doc_icono( $url ) ); ?>" aria-hidden="true"></i></div>
			<div class="dgac-doc-row-info">
				<div class="dgac-doc-row-title"><?php echo esc_html( get_the_title( $doc ) ); ?></div>
				<?php if ( $ext ) : ?>
					<div class="dgac-doc-row-meta"><?php echo esc_html( $ext ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="dgac-doc-row-btns">
			<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="dgac-doc-mini-btn dgac-doc-mini-btn--view"><i class="fa-regular fa-eye" aria-hidden="true"></i> Ver</a>
			<a href="<?php echo esc_url( $url ); ?>" download="<?php echo esc_attr( $file ); ?>" class="dgac-doc-mini-btn dgac-doc-mini-btn--download"><i class="fa-solid fa-download" aria-hidden="true"></i> Descargar</a>
			<?php if ( $edit ) : ?>
				<a href="<?php echo esc_url( $edit ); ?>" class="dgac-repo-edit" title="Editar este documento"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="screen-reader-text">Editar</span></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Lista de documentos agrupados por tipo de documento.
 *
 * @param WP_Post[] $docs Documentos ya ordenados.
 * @return string
 */
function uleam_repositorio_grupos( $docs ) {
	$grupos = array();
	$orden  = array();
	foreach ( $docs as $doc ) {
		$tipos = get_the_terms( $doc, 'tipo_documento' );
		$tipo  = ( $tipos && ! is_wp_error( $tipos ) ) ? $tipos[0]->name : 'Otros documentos';
		$grupos[ $tipo ][] = $doc;
		if ( ! isset( $orden[ $tipo ] ) ) {
			$o              = ( $tipos && ! is_wp_error( $tipos ) ) ? (int) get_term_meta( $tipos[0]->term_id, 'orden', true ) : 0;
			$orden[ $tipo ] = $o ? $o : 999;
		}
	}
	// Mismo orden de grupos en todos los años (campo "Orden" del tipo de documento).
	uksort(
		$grupos,
		function ( $a, $b ) use ( $orden ) {
			return $orden[ $a ] === $orden[ $b ] ? strnatcasecmp( $a, $b ) : $orden[ $a ] - $orden[ $b ];
		}
	);

	$html = '';
	foreach ( $grupos as $tipo => $lista ) {
		$filas = implode( '', array_map( 'uleam_repositorio_fila', $lista ) );
		if ( count( $grupos ) > 1 ) {
			$html .= '<div class="dgac-doc-group"><div class="dgac-doc-group-title"><i class="fa-solid fa-folder-open" aria-hidden="true"></i> '
				. esc_html( $tipo ) . '</div><div class="dgac-doc-list">' . $filas . '</div></div>';
		} else {
			$html .= '<div class="dgac-doc-list">' . $filas . '</div>';
		}
	}
	return $html;
}

/**
 * Documentos publicados de una sección (sin incluir subsecciones), en el orden manual.
 *
 * @param WP_Term $seccion Sección.
 * @return WP_Post[]
 */
function uleam_repositorio_docs( $seccion ) {
	static $cache = array();
	if ( ! isset( $cache[ $seccion->term_id ] ) ) {
		$cache[ $seccion->term_id ] = get_posts(
			array(
				'post_type'      => 'documento',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'ID'         => 'ASC',
				),
				'tax_query'      => array(
					array(
						'taxonomy'         => 'seccion',
						'terms'            => $seccion->term_id,
						'include_children' => false,
					),
				),
			)
		);
	}
	return $cache[ $seccion->term_id ];
}

/**
 * Total de documentos y rango de años de un área (incluye sus subsecciones).
 *
 * @param WP_Term $area Área.
 * @return array { total: int, rango: string }
 */
function uleam_repositorio_resumen( $area ) {
	$secciones = array_merge( array( $area ), uleam_seccion_hijas( $area->term_id ) );
	$total     = 0;
	$anios     = array();
	foreach ( $secciones as $s ) {
		foreach ( uleam_repositorio_docs( $s ) as $doc ) {
			$total++;
			foreach ( (array) get_the_terms( $doc, 'anio' ) as $t ) {
				if ( $t instanceof WP_Term && preg_match( '/^(\d{4})/', $t->name, $m ) ) {
					$anios[] = (int) $m[1];
				}
			}
		}
	}
	$rango = '';
	if ( $anios ) {
		$min   = min( $anios );
		$max   = max( $anios );
		$rango = $min === $max ? (string) $min : $min . '–' . $max;
	}
	return array(
		'total' => $total,
		'rango' => $rango,
	);
}

/**
 * Texto "N documento(s)".
 *
 * @param int $n Cantidad.
 * @return string
 */
function uleam_n_documentos( $n ) {
	return sprintf( _n( '%d documento', '%d documentos', $n, 'uleam-calidad' ), $n );
}

/**
 * Documentos de una sección: sin año en lista directa, con año en acordeones.
 *
 * @param WP_Term $seccion Sección.
 * @return string
 */
function uleam_repositorio_seccion( $seccion ) {
	$docs = uleam_repositorio_docs( $seccion );
	if ( ! $docs ) {
		return '<div class="dgac-doc-empty-state"><i class="fa-solid fa-folder-open" aria-hidden="true"></i><span>Aún no hay documentos publicados en esta sección.</span></div>';
	}

	$sin_anio = array();
	$por_anio = array();
	foreach ( $docs as $doc ) {
		$anios = get_the_terms( $doc, 'anio' );
		if ( $anios && ! is_wp_error( $anios ) ) {
			$por_anio[ $anios[0]->name ][] = $doc;
		} else {
			$sin_anio[] = $doc;
		}
	}
	uksort( $por_anio, 'uleam_periodo_cmp' );

	// Un solo documento por año (p. ej. el POA): lista directa, del más reciente al más antiguo,
	// en lugar de un acordeón por cada archivo.
	if ( ! $sin_anio && count( $por_anio ) > 1 && 1 === max( array_map( 'count', $por_anio ) ) ) {
		return '<div class="dgac-doc-list">' . implode( '', array_map( 'uleam_repositorio_fila', array_merge( ...array_values( $por_anio ) ) ) ) . '</div>';
	}

	$html = $sin_anio ? uleam_repositorio_grupos( $sin_anio ) : '';
	if ( $por_anio ) {
		$html .= '<div class="dgac-archive-section">';
		$first = ! $sin_anio;
		foreach ( $por_anio as $anio => $lista ) {
			$pill  = preg_match( '/^\d{4}/', $anio ) ? 'green' : 'gray';
			$icon  = 'green' === $pill ? 'fa-calendar-days' : 'fa-box-archive';
			$html .= '<details class="dgac-year-accordion"' . ( $first ? ' open' : '' ) . '>'
				. '<summary class="dgac-year-summary"><div class="dgac-year-summary-left">'
				. '<span class="dgac-year-pill dgac-year-pill--' . $pill . '"><i class="fa-solid ' . $icon . '" aria-hidden="true"></i> ' . esc_html( $anio ) . '</span>'
				. '<span class="dgac-year-count" data-total="' . esc_attr( uleam_n_documentos( count( $lista ) ) ) . '">' . esc_html( uleam_n_documentos( count( $lista ) ) ) . '</span>'
				. '</div><span class="dgac-year-chevron"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span></summary>'
				. '<div class="dgac-year-content">' . uleam_repositorio_grupos( $lista ) . '</div></details>';
			$first = false;
		}
		$html .= '</div>';
	}
	return $html;
}

/**
 * Render del bloque: buscador, tarjetas de área (como los subsistemas de Gestión de Procesos)
 * y un panel por área con sus documentos por año.
 *
 * @param array $attrs Atributos del bloque.
 * @return string
 */
function uleam_repositorio_render( $attrs ) {
	$raiz = isset( $attrs['seccion'] ) ? get_term( (int) $attrs['seccion'], 'seccion' ) : null;
	if ( ! $raiz || is_wp_error( $raiz ) ) {
		return current_user_can( 'edit_posts' )
			? '<p class="dgac-repo-aviso">Repositorio documental: elige una sección en el panel lateral del bloque.</p>'
			: '';
	}

	$areas = uleam_seccion_hijas( $raiz->term_id );
	if ( ! $areas ) {
		$areas = array( $raiz );
	}
	$uid      = 'repo-' . $raiz->term_id;
	$resumen  = array();
	$total    = 0;
	foreach ( $areas as $area ) {
		$resumen[ $area->term_id ] = uleam_repositorio_resumen( $area );
		$total                    += $resumen[ $area->term_id ]['total'];
	}

	ob_start();
	?>
	<section class="dgac-repo" id="<?php echo esc_attr( $uid ); ?>" data-total="<?php echo esc_attr( uleam_n_documentos( $total ) ); ?>">

		<div class="dgac-toolbar dgac-repo-toolbar">
			<div class="dgac-search-box">
				<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
				<input type="search" class="dgac-search-input dgac-repo-search__input" placeholder="Buscar por nombre o código (ej. PAE-01, Formato, Informe)…" aria-label="Buscar documento en todas las áreas" />
			</div>
			<div class="dgac-toolbar-actions">
				<span class="dgac-count-badge dgac-repo-count" aria-live="polite"><?php echo esc_html( uleam_n_documentos( $total ) ); ?></span>
				<button type="button" class="dgac-toggle-all-btn dgac-repo-toggle" aria-expanded="false">
					<i class="fa-solid fa-up-down" aria-hidden="true"></i> <span>Expandir todos</span>
				</button>
			</div>
		</div>

		<?php if ( count( $areas ) > 1 ) : ?>
			<nav class="dgac-subsistema-tabs dgac-repo-areas" role="tablist" aria-label="Áreas del repositorio">
				<?php foreach ( $areas as $i => $area ) : ?>
					<?php
					$icono = get_term_meta( $area->term_id, 'icono', true );
					$r     = $resumen[ $area->term_id ];
					$meta  = uleam_n_documentos( $r['total'] ) . ( $r['rango'] ? ' · ' . $r['rango'] : '' );
					?>
					<button type="button" class="dgac-subsistema-tab dgac-repo-tab<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tab"
						id="<?php echo esc_attr( $uid . '-tab-' . $area->slug ); ?>"
						aria-controls="<?php echo esc_attr( $area->slug ); ?>"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>">
						<span class="dgac-subtab-badge">
							<i class="<?php echo esc_attr( $icono ? $icono : 'fa-solid fa-folder-open' ); ?>" aria-hidden="true"></i>
							<?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>
						</span>
						<strong class="dgac-subtab-title"><?php echo esc_html( $area->name ); ?></strong>
						<span class="dgac-subtab-meta" data-total="<?php echo esc_attr( $meta ); ?>"><?php echo esc_html( $meta ); ?></span>
					</button>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<p class="dgac-repo-noresults" hidden>
			<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
			No encontramos documentos con ese nombre. Prueba con otra palabra o con el código del documento.
		</p>

		<?php foreach ( $areas as $i => $area ) : ?>
			<?php $r = $resumen[ $area->term_id ]; ?>
			<div class="dgac-repo-panel<?php echo 0 === $i ? ' is-active' : ''; ?>" role="tabpanel"
				id="<?php echo esc_attr( $area->slug ); ?>"
				aria-labelledby="<?php echo esc_attr( $uid . '-tab-' . $area->slug ); ?>">
				<div class="dgac-section-title-wrap dgac-repo-panel__head">
					<h3 class="dgac-repo-panel__title">
						<?php $icono = get_term_meta( $area->term_id, 'icono', true ); ?>
						<?php if ( $icono ) : ?>
							<i class="<?php echo esc_attr( $icono ); ?>" aria-hidden="true"></i>
						<?php endif; ?>
						<?php echo esc_html( $area->name ); ?>
					</h3>
					<span class="dgac-count-badge"><?php echo esc_html( uleam_n_documentos( $r['total'] ) ); ?></span>
				</div>
				<?php if ( $area->description ) : ?>
					<div class="dgac-repo-panel__desc"><?php echo wp_kses_post( wpautop( $area->description ) ); ?></div>
				<?php endif; ?>
				<?php
				$subs = uleam_seccion_hijas( $area->term_id );
				if ( $subs ) {
					foreach ( $subs as $sub ) {
						$n = count( uleam_repositorio_docs( $sub ) );
						echo '<div class="dgac-repo-sub"><h4 class="dgac-repo-sub__title"><i class="fa-solid fa-angles-right" aria-hidden="true"></i> '
							. esc_html( $sub->name ) . ' <span class="dgac-repo-sub__count">' . esc_html( uleam_n_documentos( $n ) ) . '</span></h4>';
						if ( $sub->description ) {
							echo '<div class="dgac-repo-panel__desc">' . wp_kses_post( wpautop( $sub->description ) ) . '</div>';
						}
						echo uleam_repositorio_seccion( $sub ); // phpcs:ignore WordPress.Security.EscapeOutput -- HTML escapado al construirlo.
						echo '</div>';
					}
				} else {
					echo uleam_repositorio_seccion( $area ); // phpcs:ignore WordPress.Security.EscapeOutput -- HTML escapado al construirlo.
				}
				?>
			</div>
		<?php endforeach; ?>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Registra el bloque "Repositorio documental" (sin paso de compilación).
 */
function uleam_repositorio_block() {
	wp_register_script(
		'uleam-repositorio-block',
		ULEAM_THEME_URI . '/js/repositorio-block.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'wp-data', 'wp-core-data' ),
		ULEAM_THEME_VERSION,
		true
	);
	register_block_type(
		'uleam/repositorio-documental',
		array(
			'api_version'     => 3,
			'title'           => 'Repositorio documental',
			'category'        => 'widgets',
			'icon'            => 'portfolio',
			'description'     => 'Muestra automáticamente los Documentos de una sección, en pestañas y por año.',
			'editor_script'   => 'uleam-repositorio-block',
			'attributes'      => array(
				'seccion' => array(
					'type'    => 'number',
					'default' => 0,
				),
			),
			'supports'        => array( 'html' => false ),
			'render_callback' => 'uleam_repositorio_render',
		)
	);
}
add_action( 'init', 'uleam_repositorio_block', 25 );
