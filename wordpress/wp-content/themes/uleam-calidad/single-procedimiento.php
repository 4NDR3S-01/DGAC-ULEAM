<?php
/**
 * Página de un procedimiento: Manual, Flujo, Formatos y Versiones anteriores.
 *
 * @package uleam-calidad
 */

get_header();

while ( have_posts() ) :
	the_post();
	$g     = uleam_procesos_archivos( get_the_ID() );
	$ruta  = array();
	$terms = get_the_terms( get_the_ID(), ULEAM_PROC_TAX );
	if ( is_array( $terms ) && $terms ) {
		$t = $terms[0];
		while ( $t && ! is_wp_error( $t ) ) {
			array_unshift( $ruta, $t->name );
			$t = $t->parent ? get_term( $t->parent, ULEAM_PROC_TAX ) : null;
		}
	}
	?>
	<header class="page-header">
		<div class="page-header__wrap">
			<span class="page-header__badge"><?php echo esc_html( ( $ruta ? $ruta[0] : 'Gestión de Procesos' ) . ' · ULEAM' ); ?></span>
			<h1 class="page-header__title"><?php the_title(); ?></h1>
		</div>
	</header>
	<div class="entry-content">
		<section class="dgac-proc-page">
			<p class="dgac-proc-page__ruta">
				<a href="<?php echo esc_url( home_url( '/gestion-de-procesos/' ) ); ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Gestión de Procesos</a>
				<?php if ( count( $ruta ) > 1 ) : ?>
					· <?php echo esc_html( implode( ' › ', $ruta ) ); ?>
				<?php endif; ?>
			</p>

			<?php echo uleam_procesos_tabs_html( $g ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</section>
	</div>
	<?php echo uleam_procesos_tabs_js(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php
endwhile;

get_footer();
