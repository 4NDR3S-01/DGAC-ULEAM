<?php
/**
 * Plantilla de entrada individual (noticias, documentos, entradas, evaluaciones)
 *
 * @package uleam-calidad
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<header class="page-header">
		<h1><?php the_title(); ?></h1>
		<?php if ( 'noticia' === get_post_type() ) : ?>
			<p class="meta"><?php echo esc_html( get_the_date() ); ?></p>
		<?php endif; ?>
	</header>
	<div class="entry-content">
		<?php the_content(); ?>

		<?php
		if ( 'documento' === get_post_type() ) :
			$doc_url = get_post_meta( get_the_ID(), '_documento_url', true );
			if ( $doc_url ) :
				?>
				<p style="margin-top:24px">
					<a class="btn" href="<?php echo esc_url( $doc_url ); ?>" target="_blank" rel="noopener">Descargar documento →</a>
				</p>
				<?php
			endif;
		endif;
		?>
	</div>
	<?php
endwhile;
?>

<?php get_footer(); ?>
