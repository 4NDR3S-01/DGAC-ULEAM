<?php
/**
 * Ajustes del editor para que las páginas se puedan editar sin código
 *
 * Las páginas del tema usan HTML a medida (iconos Font Awesome, acordeones
 * <details>/<summary>, tarjetas enlazadas) que se edita desde el bloque
 * "Clásico" (TinyMCE). Por defecto TinyMCE "limpia" ese HTML al guardar:
 * - borra las etiquetas <i> y <span> vacías (los iconos Font Awesome),
 * - borra los <summary> de los acordeones,
 * - quita los <a> que envuelven bloques (<div>), p. ej. tarjetas de contacto.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Añade valores a una opción de TinyMCE separada por comas sin perder lo existente.
 *
 * @param array  $init   Configuración de TinyMCE.
 * @param string $key    Clave de la opción.
 * @param string $values Valores a añadir.
 * @return array
 */
function uleam_mce_append( $init, $key, $values ) {
	$init[ $key ] = empty( $init[ $key ] ) ? $values : $init[ $key ] . ',' . $values;
	return $init;
}

/**
 * Evita que TinyMCE elimine iconos, acordeones y enlaces de bloque al guardar.
 *
 * Aplica tanto al bloque Clásico de Gutenberg ('classic-block') como al editor clásico.
 *
 * @param array $init Configuración de TinyMCE.
 * @return array
 */
function uleam_mce_preservar_html( $init ) {
	// Redefinir i/span quita la regla "eliminar si está vacío" que traen por defecto.
	$init = uleam_mce_append( $init, 'extended_valid_elements', 'i[*],span[*],summary[*],details[*]' );

	// Hijos permitidos que el esquema de TinyMCE 4 no contempla.
	$init = uleam_mce_append(
		$init,
		'valid_children',
		'+a[div|span|i|p|h2|h3|h4|img|strong|em],+summary[div|span|i|h2|h3|h4|strong],+details[summary|div|p|ul]'
	);

	// Un contenedor que solo tiene un icono (<div><i class="fa-..."></i></div>) no debe
	// considerarse vacío; si no, TinyMCE reemplaza el icono por un espacio.
	if ( empty( $init['non_empty_elements'] ) ) {
		$init['non_empty_elements'] = 'td,th,iframe,video,audio,object,script,pre,code,area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed,source,wbr,track';
	}
	$init = uleam_mce_append( $init, 'non_empty_elements', 'i' );

	return $init;
}
add_filter( 'tiny_mce_before_init', 'uleam_mce_preservar_html' );

/**
 * Muestra el contenido en el editor con los estilos e iconos reales del sitio,
 * para que quien edite vea lo mismo que el visitante.
 */
function uleam_editor_styles() {
	add_theme_support( 'editor-styles' );
	add_editor_style(
		array(
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
			'css/variables.css',
			'css/pages.css',
			'css/institutional.css',
			'css/documents.css',
			'css/contacts.css',
		)
	);
}
add_action( 'after_setup_theme', 'uleam_editor_styles' );
