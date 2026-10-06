<?php
/**
 * Carrusel de la portada: cada diapositiva se edita en el menú "Carrusel de portada"
 * (imagen destacada, título, palabra destacada, texto, botón, orden y punto de enfoque).
 * El avance automático y su velocidad se ajustan en Apariencia → Personalizar.
 *
 * Accesibilidad (patrón WAI-ARIA "carousel"): botón de pausa, se detiene al pasar el cursor
 * o al enfocar con teclado, no avanza solo si se pidió "reducir movimiento", y las
 * diapositivas ocultas no reciben foco.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tipo de contenido "Diapositiva".
 */
function uleam_carrusel_register() {
	register_post_type(
		'diapositiva',
		array(
			'labels'          => array(
				'name'                  => 'Carrusel de portada',
				'singular_name'         => 'Diapositiva',
				'menu_name'             => 'Carrusel de portada',
				'all_items'             => 'Diapositivas',
				'add_new'               => 'Añadir diapositiva',
				'add_new_item'          => 'Añadir diapositiva',
				'edit_item'             => 'Editar diapositiva',
				'search_items'          => 'Buscar diapositivas',
				'not_found'             => 'Aún no hay diapositivas: la portada muestra la imagen del Personalizador.',
				'featured_image'        => 'Imagen de la diapositiva',
				'set_featured_image'    => 'Elegir imagen',
				'remove_featured_image' => 'Quitar imagen',
				'use_featured_image'    => 'Usar esta imagen',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false, // Pantalla de edición sencilla, con los campos a la vista.
			'menu_icon'       => 'dashicons-images-alt2',
			'menu_position'   => 5,
			'supports'        => array( 'title', 'thumbnail', 'page-attributes' ),
			'capability_type' => 'post',
		)
	);
	// Tamaño para pantallas grandes (se recorta al centro si la imagen original es mayor).
	add_image_size( 'uleam-carrusel', 1920, 900, true );
}
add_action( 'init', 'uleam_carrusel_register' );

/**
 * Campos de cada diapositiva (clave => [ etiqueta, tipo, ayuda ]).
 *
 * @return array
 */
function uleam_carrusel_campos() {
	return array(
		'_slide_etiqueta'  => array( 'Etiqueta superior', 'text', 'Texto corto sobre el título. Ej.: ULEAM · Aseguramiento de la Calidad' ),
		'_slide_destacado' => array( 'Palabra o frase destacada del título', 'text', 'Se mostrará en celeste. Debe estar escrita igual que en el título. Ej.: calidad' ),
		'_slide_texto'     => array( 'Descripción', 'textarea', 'Una o dos frases. Se recomienda no pasar de 160 caracteres.' ),
		'_slide_boton'     => array( 'Texto del botón', 'text', 'Déjalo vacío para no mostrar botón. Ej.: Conócenos' ),
		'_slide_enlace'    => array( 'Enlace del botón', 'url', 'Página a la que lleva el botón. Ej.: /mision-vision/' ),
		'_slide_enfoque'   => array( 'Punto de enfoque de la imagen', 'select', 'Qué parte de la foto debe verse siempre cuando la pantalla la recorta (por ejemplo, en celulares).' ),
		'_slide_franja'    => array( 'Frase en franja de color', 'text', 'Opcional. Aparece bajo el título sobre una franja de color (como «CARRERA ACREDITADA» en los sitios de carreras). Ej.: Repositorio documental' ),
		// Diseño por capas (opcional): fondo + personas recortadas al frente + panel de texto.
		'_slide_recorte'   => array( 'Foto recortada (PNG sin fondo)', 'imagen', 'Si eliges una foto de personas SIN FONDO (PNG transparente), la diapositiva usa el diseño por capas: fondo azul, las personas al frente y el texto en un panel. Ideal: al menos 1200 px de alto.' ),
		'_slide_lado'      => array( 'Lado de las personas', 'lado', 'Dónde se ubican las personas recortadas; el panel de texto va al lado contrario.' ),
		'_slide_fondo'     => array( 'Fondo del diseño por capas', 'fondo', 'La «Imagen de la diapositiva» teñida de azul institucional, o la textura de procesos (sin foto).' ),
		'_slide_insignia'  => array( 'Logo o insignia del panel', 'imagen', 'Opcional. Se muestra a la izquierda del texto con una línea divisoria. Ej.: logo de la ULEAM o un sello de acreditación (PNG con fondo transparente).' ),
		'_slide_insignia_color' => array( 'Color del logo', 'insignia_color', 'Blanco funciona bien con logos de un solo color sobre el panel azul.' ),
	);
}

/**
 * Opciones de los campos de selección (valor => etiqueta).
 *
 * @param string $tipo Tipo de campo.
 * @return array
 */
function uleam_carrusel_opciones( $tipo ) {
	$opciones = array(
		'select'         => uleam_carrusel_enfoques(),
		'lado'           => array( 'derecha' => 'Derecha (texto a la izquierda)', 'izquierda' => 'Izquierda (texto a la derecha)' ),
		'fondo'          => array( 'foto' => 'Imagen de la diapositiva en azul', 'textura' => 'Textura de procesos (sin foto)' ),
		'insignia_color' => array( 'blanco' => 'Blanco', 'original' => 'Colores originales' ),
	);
	return $opciones[ $tipo ] ?? array();
}

/**
 * Opciones del punto de enfoque (valor CSS => etiqueta).
 *
 * @return array
 */
function uleam_carrusel_enfoques() {
	return array(
		'center'        => 'Centro',
		'right center'  => 'Derecha',
		'left center'   => 'Izquierda',
		'center top'    => 'Arriba',
		'center bottom' => 'Abajo',
	);
}

/**
 * Caja de campos en la pantalla de edición.
 */
function uleam_carrusel_metabox() {
	add_meta_box( 'uleam_slide', 'Contenido de la diapositiva', 'uleam_carrusel_metabox_html', 'diapositiva', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'uleam_carrusel_metabox' );

/**
 * Renderiza los campos.
 *
 * @param WP_Post $post Diapositiva.
 */
function uleam_carrusel_metabox_html( $post ) {
	wp_nonce_field( 'uleam_slide', 'uleam_slide_nonce' );
	echo '<p style="background:#f0f6fc;border-left:4px solid #0059A8;padding:10px 12px">'
		. '<strong>Cómo funciona:</strong> el <em>título</em> de arriba es el texto grande. La <em>imagen</em> se elige en la caja «Imagen de la diapositiva» (a la derecha); '
		. 'usa fotos horizontales de al menos <strong>1920 × 900 px</strong> (en el diseño por capas, esa imagen se usa como fondo en azul). En «Atributos → Orden» define la posición (1, 2, 3…). '
		. 'Para ocultar una diapositiva sin borrarla, cámbiala a <em>Borrador</em>.</p>';
	echo '<table class="form-table" role="presentation">';
	foreach ( uleam_carrusel_campos() as $clave => $c ) {
		$valor = get_post_meta( $post->ID, $clave, true );
		$id    = 'campo' . $clave;
		if ( '_slide_recorte' === $clave ) {
			echo '<tr><td colspan="2" style="padding:18px 0 4px"><h3 style="margin:0">Diseño por capas (opcional)</h3>'
				. '<p class="description" style="margin-top:4px">Estilo de los sitios de carreras ULEAM: personas recortadas al frente de un fondo azul y el texto en un panel con logo. Se activa al elegir una foto recortada.</p></td></tr>';
		}
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $c[0] ) . '</label></th><td>';
		if ( 'textarea' === $c[1] ) {
			echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $clave ) . '" rows="3" class="large-text" maxlength="260">' . esc_textarea( $valor ) . '</textarea>';
		} elseif ( 'imagen' === $c[1] ) {
			$vista = $valor ? wp_get_attachment_image( (int) $valor, 'medium', false, array( 'style' => 'max-height:120px;width:auto;background:#003366;padding:6px;border-radius:6px' ) ) : '';
			echo '<div class="uleam-imagen-campo">'
				. '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $clave ) . '" value="' . esc_attr( $valor ) . '" />'
				. '<div class="uleam-imagen-vista" style="margin-bottom:8px">' . $vista . '</div>' // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de wp_get_attachment_image.
				. '<button type="button" class="button uleam-imagen-elegir" data-titulo="' . esc_attr( $c[0] ) . '">Elegir imagen</button> '
				. '<button type="button" class="button-link uleam-imagen-quitar"' . ( $valor ? '' : ' hidden' ) . '>Quitar</button>'
				. '</div>';
		} elseif ( uleam_carrusel_opciones( $c[1] ) ) {
			$opciones = uleam_carrusel_opciones( $c[1] );
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $clave ) . '">';
			foreach ( $opciones as $v => $etiqueta ) {
				echo '<option value="' . esc_attr( $v ) . '"' . selected( $valor ? $valor : array_key_first( $opciones ), $v, false ) . '>' . esc_html( $etiqueta ) . '</option>';
			}
			echo '</select>';
		} else {
			echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $clave ) . '" value="' . esc_attr( $valor ) . '" class="regular-text" />';
		}
		echo '<p class="description">' . esc_html( $c[2] ) . '</p></td></tr>';
	}
	echo '</table>';
}

/**
 * Guarda los campos.
 *
 * @param int $post_id ID de la diapositiva.
 */
function uleam_carrusel_guardar( $post_id ) {
	if ( ! isset( $_POST['uleam_slide_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['uleam_slide_nonce'] ), 'uleam_slide' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( uleam_carrusel_campos() as $clave => $c ) {
		if ( ! isset( $_POST[ $clave ] ) ) {
			continue;
		}
		$valor = wp_unslash( $_POST[ $clave ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- se sanea abajo según el tipo.
		if ( 'textarea' === $c[1] ) {
			$valor = sanitize_textarea_field( $valor );
		} elseif ( 'url' === $c[1] ) {
			$valor = esc_url_raw( trim( $valor ) );
		} elseif ( 'imagen' === $c[1] ) {
			$valor = absint( $valor ) && wp_attachment_is_image( absint( $valor ) ) ? absint( $valor ) : '';
		} elseif ( uleam_carrusel_opciones( $c[1] ) ) {
			$opciones = uleam_carrusel_opciones( $c[1] );
			$valor    = array_key_exists( $valor, $opciones ) ? $valor : array_key_first( $opciones );
		} else {
			$valor = sanitize_text_field( $valor );
		}
		update_post_meta( $post_id, $clave, $valor );
	}
}
add_action( 'save_post_diapositiva', 'uleam_carrusel_guardar' );

/**
 * Botones "Elegir imagen" / "Quitar" de los campos de imagen (Biblioteca de medios).
 *
 * @param string $hook Pantalla del administrador.
 */
function uleam_carrusel_media_scripts( $hook ) {
	$screen = get_current_screen();
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'diapositiva' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script(
		'media-editor',
		"jQuery(function($){
			$(document).on('click','.uleam-imagen-elegir',function(e){
				e.preventDefault();
				var caja=$(this).closest('.uleam-imagen-campo');
				var marco=wp.media({title:$(this).data('titulo'),button:{text:'Usar esta imagen'},library:{type:'image'},multiple:false});
				marco.on('select',function(){
					var img=marco.state().get('selection').first().toJSON();
					var url=(img.sizes&&img.sizes.medium?img.sizes.medium.url:img.url);
					caja.find('input').val(img.id);
					caja.find('.uleam-imagen-vista').html($('<img>',{src:url,alt:'',style:'max-height:120px;width:auto;background:#003366;padding:6px;border-radius:6px'}));
					caja.find('.uleam-imagen-quitar').prop('hidden',false);
				});
				marco.open();
			});
			$(document).on('click','.uleam-imagen-quitar',function(e){
				e.preventDefault();
				var caja=$(this).closest('.uleam-imagen-campo');
				caja.find('input').val('');
				caja.find('.uleam-imagen-vista').empty();
				$(this).prop('hidden',true);
			});
		});"
	);
}
add_action( 'admin_enqueue_scripts', 'uleam_carrusel_media_scripts' );

/**
 * Columnas del listado: miniatura y orden, para ver el carrusel de un vistazo.
 *
 * @param array $cols Columnas.
 * @return array
 */
function uleam_carrusel_columnas( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'miniatura' => 'Imagen',
		'title'     => 'Título',
		'orden'     => 'Orden',
		'date'      => $cols['date'],
	);
}
add_filter( 'manage_diapositiva_posts_columns', 'uleam_carrusel_columnas' );

/**
 * Contenido de las columnas.
 *
 * @param string $col     Columna.
 * @param int    $post_id Diapositiva.
 */
function uleam_carrusel_columna( $col, $post_id ) {
	if ( 'miniatura' === $col ) {
		echo has_post_thumbnail( $post_id )
			? get_the_post_thumbnail( $post_id, array( 120, 60 ), array( 'style' => 'width:120px;height:60px;object-fit:cover;border-radius:6px' ) )
			: '<span style="color:#b11719">Sin imagen</span>';
	} elseif ( 'orden' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_diapositiva_posts_custom_column', 'uleam_carrusel_columna', 10, 2 );

/**
 * El listado del administrador se ordena como en la portada.
 *
 * @param WP_Query $q Consulta.
 */
function uleam_carrusel_orden_admin( $q ) {
	if ( is_admin() && $q->is_main_query() && 'diapositiva' === $q->get( 'post_type' ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}
add_action( 'pre_get_posts', 'uleam_carrusel_orden_admin' );

/**
 * Versión desenfocada de la foto, generada una sola vez en el servidor y guardada junto al
 * original (…-desenfocada-v2.jpg). Así el navegador solo muestra una imagen: el desenfoque
 * con CSS era costoso en celulares y hacía que el cambio de diapositiva se trabara.
 *
 * @param int $attachment_id Imagen.
 * @return string URL, o vacío si no se pudo generar (entonces se desenfoca con CSS).
 */
function uleam_carrusel_desenfocada( $attachment_id ) {
	$origen = wp_get_attachment_image_src( $attachment_id, 'medium_large' );
	$ruta   = $origen ? wp_get_original_image_path( $attachment_id ) : '';
	if ( ! $origen || ! $ruta || ! function_exists( 'imagecreatefromstring' ) ) {
		return '';
	}
	// La versión depende del archivo usado: si cambian la imagen, se genera otra.
	$fuente  = path_join( dirname( get_attached_file( $attachment_id ) ), wp_basename( $origen[0] ) );
	$fuente  = file_exists( $fuente ) ? $fuente : $ruta;
	$destino = preg_replace( '/\.[^.]+$/', '', $fuente ) . '-desenfocada-v2.jpg';
	$url     = str_replace( wp_basename( $origen[0] ), wp_basename( $destino ), $origen[0] );
	if ( file_exists( $destino ) && filemtime( $destino ) >= filemtime( $fuente ) ) {
		return $url;
	}
	$img = @imagecreatefromstring( (string) file_get_contents( $fuente ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors,WordPress.WP.AlternativeFunctions
	if ( ! $img ) {
		return '';
	}
	$w = imagesx( $img );
	$h = imagesy( $img );
	// Desenfoque suave sin "cuadritos": reducir mucho y volver a ampliar POR ETAPAS,
	// desenfocando en cada una (ampliar de golpe deja bloques visibles).
	$actual = imagecreatetruecolor( 48, max( 1, (int) round( 48 * $h / $w ) ) );
	imagecopyresampled( $actual, $img, 0, 0, 0, 0, imagesx( $actual ), imagesy( $actual ), $w, $h );
	$ancho_final = min( 768, $w );
	foreach ( array( 96, 192, 384, $ancho_final ) as $ancho ) {
		$alto      = max( 1, (int) round( $ancho * $h / $w ) );
		$siguiente = imagecreatetruecolor( $ancho, $alto );
		imagecopyresampled( $siguiente, $actual, 0, 0, 0, 0, $ancho, $alto, imagesx( $actual ), imagesy( $actual ) );
		imagedestroy( $actual );
		for ( $i = 0; $i < 4; $i++ ) {
			imagefilter( $siguiente, IMG_FILTER_GAUSSIAN_BLUR );
		}
		$actual = $siguiente;
	}
	$final = $actual;
	$chica = null;
	imagefilter( $final, IMG_FILTER_CONTRAST, -6 ); // un poco más vivo, como el saturate() que se usaba
	$ok = imagejpeg( $final, $destino, 72 );
	imagedestroy( $img );
	imagedestroy( $final );
	return $ok ? $url : '';
}

/**
 * Versión "duotono" de la foto en azul institucional (sombras azul profundo, luces azul grisáceo),
 * para el fondo del diseño por capas. Se genera una vez (…-azul-v1.jpg) junto al original.
 *
 * @param int $attachment_id Imagen.
 * @return string URL o vacío si no se pudo generar.
 */
function uleam_carrusel_duotono( $attachment_id ) {
	$ruta = wp_get_original_image_path( $attachment_id );
	$url  = wp_get_original_image_url( $attachment_id );
	if ( ! $ruta || ! $url || ! file_exists( $ruta ) || ! function_exists( 'imagecreatefromstring' ) ) {
		return '';
	}
	$destino = preg_replace( '/\.[^.]+$/', '', $ruta ) . '-azul-v1.jpg';
	$salida  = str_replace( wp_basename( $ruta ), wp_basename( $destino ), $url );
	if ( file_exists( $destino ) && filemtime( $destino ) >= filemtime( $ruta ) ) {
		return $salida;
	}
	$img = @imagecreatefromstring( (string) file_get_contents( $ruta ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors,WordPress.WP.AlternativeFunctions
	if ( ! $img ) {
		return '';
	}
	$w      = imagesx( $img );
	$h      = imagesy( $img );
	$ancho  = min( 1600, $w );
	$alto   = (int) round( $ancho * $h / $w );
	$lienzo = imagecreatetruecolor( $ancho, $alto );
	imagecopyresampled( $lienzo, $img, 0, 0, 0, 0, $ancho, $alto, $w, $h );
	imagefilter( $lienzo, IMG_FILTER_GRAYSCALE );
	imagefilter( $lienzo, IMG_FILTER_CONTRAST, -10 );
	// Duotono rápido: escala de grises → paleta de 256 tonos → cada tono se reemplaza por su azul.
	imagetruecolortopalette( $lienzo, false, 256 );
	$oscuro = array( 0, 22, 58 );
	$claro  = array( 118, 152, 196 );
	for ( $i = 0, $n = imagecolorstotal( $lienzo ); $i < $n; $i++ ) {
		$c = imagecolorsforindex( $lienzo, $i );
		$t = $c['red'] / 255;
		imagecolorset( $lienzo, $i, (int) ( $oscuro[0] + ( $claro[0] - $oscuro[0] ) * $t ), (int) ( $oscuro[1] + ( $claro[1] - $oscuro[1] ) * $t ), (int) ( $oscuro[2] + ( $claro[2] - $oscuro[2] ) * $t ) );
	}
	imagepalettetotruecolor( $lienzo );
	$ok = imagejpeg( $lienzo, $destino, 78 );
	imagedestroy( $img );
	imagedestroy( $lienzo );
	return $ok ? $salida : '';
}

/**
 * Diapositivas publicadas con imagen, en orden.
 *
 * @return array Lista de arrays listos para pintar.
 */
function uleam_carrusel_diapositivas() {
	$posts = get_posts(
		array(
			'post_type'   => 'diapositiva',
			'post_status' => 'publish',
			'numberposts' => 8,
			'orderby'     => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'meta_key'    => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery -- pocas filas.
		)
	);
	$slides = array();
	foreach ( $posts as $p ) {
		$slides[] = array(
			'id'        => $p->ID,
			'imagen'    => (int) get_post_thumbnail_id( $p ),
			'titulo'    => get_the_title( $p ),
			'etiqueta'  => get_post_meta( $p->ID, '_slide_etiqueta', true ),
			'destacado' => get_post_meta( $p->ID, '_slide_destacado', true ),
			'texto'     => get_post_meta( $p->ID, '_slide_texto', true ),
			'boton'     => get_post_meta( $p->ID, '_slide_boton', true ),
			'enlace'    => get_post_meta( $p->ID, '_slide_enlace', true ),
			'enfoque'   => get_post_meta( $p->ID, '_slide_enfoque', true ) ?: 'center',
			'franja'    => get_post_meta( $p->ID, '_slide_franja', true ),
			'recorte'   => (int) get_post_meta( $p->ID, '_slide_recorte', true ),
			'lado'      => get_post_meta( $p->ID, '_slide_lado', true ) ?: 'derecha',
			'fondo'     => get_post_meta( $p->ID, '_slide_fondo', true ) ?: 'foto',
			'insignia'  => (int) get_post_meta( $p->ID, '_slide_insignia', true ),
			'insignia_color' => get_post_meta( $p->ID, '_slide_insignia_color', true ) ?: 'blanco',
		);
	}
	return $slides;
}

/**
 * Título con la palabra destacada envuelta en <span> (escapado).
 *
 * @param string $titulo    Título.
 * @param string $destacado Palabra destacada.
 * @return string HTML.
 */
function uleam_carrusel_titulo( $titulo, $destacado ) {
	$html = esc_html( $titulo );
	$d    = esc_html( trim( (string) $destacado ) );
	if ( '' !== $d && false !== mb_stripos( $html, $d ) ) {
		$html = preg_replace( '/' . preg_quote( $d, '/' ) . '/iu', '<span>$0</span>', $html, 1 );
	}
	return $html;
}

/**
 * Pinta el carrusel (o nada si no hay diapositivas: la portada usa entonces el hero clásico).
 *
 * @return bool Si se pintó.
 */
function uleam_carrusel_render() {
	$slides = uleam_carrusel_diapositivas();
	if ( ! $slides ) {
		return false;
	}
	$total     = count( $slides );
	$auto      = $total > 1 && uleam_opt( 'uleam_carrusel_auto' );
	$intervalo = max( 4, min( 20, (int) uleam_opt( 'uleam_carrusel_intervalo' ) ) );
	?>
	<section class="carrusel<?php echo $total > 1 ? ' carrusel--multiple' : ''; ?>" id="inicio" aria-roledescription="carrusel" aria-label="Destacados" data-auto="<?php echo $auto ? '1' : '0'; ?>" data-intervalo="<?php echo esc_attr( $intervalo ); ?>" style="--carrusel-intervalo:<?php echo esc_attr( $intervalo ); ?>s">
		<div class="carrusel__pista" aria-live="<?php echo $auto ? 'off' : 'polite'; ?>">
			<?php foreach ( $slides as $i => $s ) : ?>
				<?php
				$capas  = $s['recorte'] && wp_attachment_is_image( $s['recorte'] );
				$clases = 'carrusel__slide' . ( 0 === $i ? ' is-activa' : '' );
				$vars   = '--slide-enfoque:' . $s['enfoque'] . ';';
				if ( $capas ) {
					// Diseño por capas: fondo azul (foto en duotono o textura) + personas recortadas + panel.
					$clases .= ' carrusel__slide--capas carrusel__slide--persona-' . ( 'izquierda' === $s['lado'] ? 'izq' : 'der' );
					$duotono = 'foto' === $s['fondo'] ? uleam_carrusel_duotono( $s['imagen'] ) : '';
					$clases .= $duotono ? '' : ' carrusel__slide--textura';
				} else {
					// Dos columnas: copia desenfocada de la foto (mismo encuadre que la nítida: se funden sin líneas).
					$horneada = uleam_carrusel_desenfocada( $s['imagen'] );
					$fondo    = $horneada ? $horneada : wp_get_attachment_image_url( $s['imagen'], 'medium_large' );
					$vars    .= $fondo ? '--slide-fondo:url(\'' . esc_url( $fondo ) . '\');' : '';
					$clases  .= $horneada ? ' carrusel__slide--horneada' : '';
				}
				$carga = array(
					// La primera diapositiva es lo más importante de la portada: se carga de inmediato.
					'loading'       => 0 === $i ? 'eager' : 'lazy',
					'fetchpriority' => 0 === $i ? 'high' : 'low',
					'decoding'      => 0 === $i ? 'sync' : 'async',
				);
				?>
				<div class="<?php echo esc_attr( $clases ); ?>" style="<?php echo esc_attr( $vars ); ?>" role="group" aria-roledescription="diapositiva" aria-label="<?php echo esc_attr( ( $i + 1 ) . ' de ' . $total ); ?>"<?php echo 0 === $i ? '' : ' aria-hidden="true" inert'; ?>>
					<?php if ( $capas ) : ?>
						<div class="carrusel__fondo" aria-hidden="true">
							<?php if ( $duotono ) : ?>
								<img class="carrusel__fondo-img" src="<?php echo esc_url( $duotono ); ?>" alt="" style="object-position:<?php echo esc_attr( $s['enfoque'] ); ?>" loading="<?php echo esc_attr( $carga['loading'] ); ?>" decoding="async" />
							<?php endif; ?>
						</div>
						<?php
						// Personas recortadas: nunca se amplían más de 1,4× su tamaño real (se verían borrosas).
						$meta  = wp_get_attachment_metadata( $s['recorte'] );
						$max_h = ! empty( $meta['height'] ) ? (int) round( $meta['height'] * 1.4 ) : 900;
						echo wp_get_attachment_image(
							$s['recorte'],
							'full',
							false,
							$carga + array(
								'class' => 'carrusel__persona',
								'sizes' => '(max-width: 900px) 90vw, 50vw',
								'style' => '--persona-max:' . $max_h . 'px',
							)
						);
						?>
					<?php else : ?>
						<div class="carrusel__media">
							<?php
							echo wp_get_attachment_image(
								$s['imagen'],
								'uleam-carrusel',
								false,
								$carga + array(
									'class' => 'carrusel__img',
									'sizes' => '100vw',
									'style' => 'object-position:' . esc_attr( $s['enfoque'] ),
								)
							);
							?>
						</div>
					<?php endif; ?>
					<div class="carrusel__contenido">
						<?php if ( $capas && $s['insignia'] ) : ?>
							<div class="carrusel__insignia<?php echo 'blanco' === $s['insignia_color'] ? ' carrusel__insignia--blanca' : ''; ?>">
								<?php echo wp_get_attachment_image( $s['insignia'], 'medium', false, array( 'loading' => $carga['loading'] ) ); ?>
							</div>
						<?php endif; ?>
						<div class="carrusel__textos">
							<?php if ( $s['etiqueta'] ) : ?>
								<p class="carrusel__etiqueta"><?php echo esc_html( $s['etiqueta'] ); ?></p>
							<?php endif; ?>
							<h2 class="carrusel__titulo"><?php echo uleam_carrusel_titulo( $s['titulo'], $s['destacado'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado en la función. ?></h2>
							<?php if ( $s['franja'] ) : ?>
								<p class="carrusel__franja"><span><?php echo esc_html( $s['franja'] ); ?></span></p>
							<?php endif; ?>
							<?php if ( $s['texto'] ) : ?>
								<p class="carrusel__texto"><?php echo esc_html( $s['texto'] ); ?></p>
							<?php endif; ?>
							<?php if ( $s['boton'] && $s['enlace'] ) : ?>
								<a class="btn carrusel__boton" href="<?php echo esc_url( $s['enlace'] ); ?>"><?php echo esc_html( $s['boton'] ); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $total > 1 ) : ?>
			<div class="carrusel__controles">
				<?php if ( $auto ) : ?>
					<button type="button" class="carrusel__btn carrusel__pausa" aria-label="Pausar el carrusel" data-texto-pausar="Pausar el carrusel" data-texto-reanudar="Reanudar el carrusel">
						<i class="fa-solid fa-pause" aria-hidden="true"></i>
					</button>
				<?php endif; ?>
				<button type="button" class="carrusel__btn carrusel__anterior" aria-label="Diapositiva anterior"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
				<div class="carrusel__puntos" role="group" aria-label="Elegir diapositiva">
					<?php foreach ( $slides as $i => $s ) : ?>
						<button type="button" class="carrusel__punto" aria-label="<?php echo esc_attr( 'Ir a la diapositiva ' . ( $i + 1 ) . ': ' . $s['titulo'] ); ?>"<?php echo 0 === $i ? ' aria-current="true"' : ''; ?>><span class="carrusel__progreso"></span></button>
					<?php endforeach; ?>
				</div>
				<button type="button" class="carrusel__btn carrusel__siguiente" aria-label="Diapositiva siguiente"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
			</div>
		<?php endif; ?>
	</section>
	<?php
	return true;
}

/**
 * Carga el JavaScript del carrusel solo en la portada.
 */
function uleam_carrusel_script() {
	if ( is_front_page() ) {
		wp_enqueue_script( 'uleam-carrusel', ULEAM_THEME_URI . '/js/carrusel.js', array(), ULEAM_THEME_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'uleam_carrusel_script' );
