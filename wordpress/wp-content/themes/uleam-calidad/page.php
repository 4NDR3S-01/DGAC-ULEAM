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
	?>
	<header class="page-header">
		<div class="page-header__wrap">
			<span class="page-header__badge">
				<?php if ( $badge_icon ) : ?>
					<i class="<?php echo esc_attr( $badge_icon ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
				<?php echo esc_html( $badge_text ); ?>
			</span>
			<h1 class="page-header__title"><?php the_title(); ?></h1>
		</div>
	</header>
	<div class="entry-content">
		<?php
		remove_filter( 'the_content', 'wpautop' );
		the_content();
		?>
	</div>
	<?php
endwhile;
?>

<?php get_footer(); ?>
