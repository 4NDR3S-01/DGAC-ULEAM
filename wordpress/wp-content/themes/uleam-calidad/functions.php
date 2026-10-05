<?php
/**
 * Funciones del tema ULEAM Calidad
 *
 * Arquitectura modular:
 * /inc/setup.php       -> Configuración base, soporte del tema y encolado de scripts/estilos.
 * /inc/helpers.php     -> Funciones auxiliares (iconos Font Awesome, etc.).
 * /inc/post-types.php  -> Registro de Custom Post Types (Noticias, Documentos, Evaluaciones).
 * /inc/taxonomies.php  -> Registro de Taxonomías y filtrado de consultas de archivo.
 * /inc/metaboxes.php   -> Metaboxes para documentos y enlaces de descarga.
 * /inc/customizer.php  -> Opciones editables del personalizador de WordPress.
 * /inc/navigation.php  -> Menú principal, fallback, reglas de reescritura y estado activo.
 * /inc/editor.php      -> Ajustes del editor (TinyMCE) para editar páginas sin perder iconos ni estructura.
 * /inc/repositorio.php -> Repositorio documental automático (secciones, bloque y listado por año).
 * /inc/busqueda.php    -> Búsqueda del sitio: filtros por tipo, resaltado y sugerencias en vivo.
 * /inc/noticias.php    -> Imágenes de noticias: relacionada automática, predeterminada y respaldo.
 * /inc/asistente.php   -> Asistente virtual (chat): documentos, preguntas frecuentes e IA opcional.
 *
 * @package uleam-calidad
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constantes globales del tema
define( 'ULEAM_THEME_DIR', get_template_directory() );
define( 'ULEAM_THEME_URI', get_template_directory_uri() );
define( 'ULEAM_THEME_VERSION', '1.21.0' );

// 1. Configuración básica y encolado de estilos/scripts
require_once ULEAM_THEME_DIR . '/inc/setup.php';

// 2. Funciones auxiliares y helpers
require_once ULEAM_THEME_DIR . '/inc/helpers.php';

// 3. Tipos de contenido personalizados (CPT)
require_once ULEAM_THEME_DIR . '/inc/post-types.php';

// 4. Taxonomías y filtros de consultas
require_once ULEAM_THEME_DIR . '/inc/taxonomies.php';

// 5. Campos personalizados y metaboxes
require_once ULEAM_THEME_DIR . '/inc/metaboxes.php';

// 6. Opciones del personalizador (Theme Customizer)
require_once ULEAM_THEME_DIR . '/inc/customizer.php';

// 7. Navegación, menús, reglas de reescritura y enlaces activos
require_once ULEAM_THEME_DIR . '/inc/navigation.php';

// 8. Editor: conservar iconos, acordeones y enlaces al editar páginas
require_once ULEAM_THEME_DIR . '/inc/editor.php';

// 9. Repositorio documental: Documentos por sección/año y bloque que los lista
require_once ULEAM_THEME_DIR . '/inc/repositorio.php';

// 10. Búsqueda: filtros, resaltado y sugerencias mientras se escribe
require_once ULEAM_THEME_DIR . '/inc/busqueda.php';

// 11. Noticias: imagen relacionada automática e imagen predeterminada
require_once ULEAM_THEME_DIR . '/inc/noticias.php';

// 12. Asistente virtual: chat para encontrar documentos y resolver dudas frecuentes
require_once ULEAM_THEME_DIR . '/inc/asistente.php';
