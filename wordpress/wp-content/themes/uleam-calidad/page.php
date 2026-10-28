<?php
/**
 * Plantilla de página estática
 *
 * @package uleam-calidad
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();

	// Determinar dinámicamente el badge / sección padre según el menú principal.
	// Una página de primer nivel muestra su propio nombre, como Auditoría o Gestión de Procesos.
	$badge_text = get_the_title() . ' · ULEAM';
	$badge_icon = get_post_meta( get_the_ID(), '_uleam_icono_cabecera', true );
	$current_id = get_the_ID();
	$locations  = get_nav_menu_locations();
	if ( isset( $locations['primary'] ) ) {
		$menu_items = wp_get_nav_menu_items( $locations['primary'] );
		if ( $menu_items ) {
			$parent_map   = array();
			$my_parent_id = 0;
			foreach ( $menu_items as $item ) {
				$parent_map[ $item->ID ] = $item->title;
				if ( (int) $item->object_id === $current_id ) {
					$my_parent_id = (int) $item->menu_item_parent;
				}
			}
			if ( $my_parent_id && isset( $parent_map[ $my_parent_id ] ) ) {
				$badge_text = $parent_map[ $my_parent_id ] . ' · ULEAM';
			}
		}
	}
	// La "Imagen destacada" de la página se usa como banner de la cabecera (textos en "Cabecera de la página").
	$banner    = has_post_thumbnail() ? wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' ) : '';
	$estilo    = '';
	if ( $banner ) {
		$fondo  = uleam_color_borde_banner( get_post_thumbnail_id() );
		$estilo = '--page-header-banner:url(' . esc_url( $banner ) . ');' . ( $fondo ? '--page-header-fondo:' . $fondo . ';' : '' );
	}
	$subtitulo = $banner ? trim( (string) get_post_meta( get_the_ID(), '_uleam_cabecera_subtitulo', true ) ) : '';
	$puntos    = array();
	if ( $banner ) {
		foreach ( preg_split( '/\R/', (string) get_post_meta( get_the_ID(), '_uleam_cabecera_puntos', true ) ) as $linea ) {
			$partes = array_map( 'trim', explode( '|', $linea, 2 ) );
			if ( isset( $partes[1] ) && '' !== $partes[1] ) {
				$puntos[] = array( 'icono' => $partes[0], 'texto' => $partes[1] );
			}
		}
	}
	// En el banner, la última palabra del título va en su propia línea, más grande y resaltada.
	$titulo   = get_the_title();
	$palabras = preg_split( '/\s+/u', trim( wp_strip_all_tags( $titulo ) ) );
	?>
	<header class="page-header<?php echo $banner ? ' page-header--banner' : ''; ?>"<?php echo $estilo ? ' style="' . esc_attr( $estilo ) . '"' : ''; ?>>
		<div class="page-header__wrap">
			<span class="page-header__badge">
				<?php if ( $badge_icon ) : ?>
					<i class="<?php echo esc_attr( $badge_icon ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
				<?php echo esc_html( $badge_text ); ?>
			</span>
			<?php if ( $banner && count( $palabras ) > 1 ) : ?>
				<h1 class="page-header__title"><?php echo esc_html( implode( ' ', array_slice( $palabras, 0, -1 ) ) ); ?> <span class="page-header__resalte"><?php echo esc_html( end( $palabras ) ); ?></span></h1>
			<?php else : ?>
				<h1 class="page-header__title"><?php the_title(); ?></h1>
			<?php endif; ?>
			<?php if ( $subtitulo || $puntos ) : ?>
				<div class="page-header__extras">
			<?php endif; ?>
			<?php if ( $subtitulo ) : ?>
				<p class="page-header__subtitulo"><?php echo nl2br( esc_html( $subtitulo ) ); ?></p>
			<?php endif; ?>
			<?php if ( $puntos ) : ?>
				<ul class="page-header__puntos">
					<?php foreach ( $puntos as $punto ) : ?>
						<li class="page-header__punto">
							<?php if ( $punto['icono'] ) : ?>
								<i class="<?php echo esc_attr( $punto['icono'] ); ?>" aria-hidden="true"></i>
							<?php endif; ?>
							<?php echo esc_html( $punto['texto'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $subtitulo || $puntos ) : ?>
				</div>
			<?php endif; ?>
		</div>
	</header>
	<div class="entry-content">
		<?php
		$es_recursos_humanos = 'recursos-humanos' === get_post_field( 'post_name', get_the_ID() );
		if ( ! $es_recursos_humanos ) {
			remove_filter( 'the_content', 'wpautop' );
			the_content();
		}
		if ( $es_recursos_humanos && ! has_shortcode( get_the_content(), 'equipo' ) ) {
			echo do_shortcode( '[equipo]' );
		}
		?>
	</div>
	<?php
endwhile;
?>

<?php get_footer(); ?>
