<?php
/**
 * Plantilla de entrada individual (noticias, documentos, entradas, evaluaciones)
 *
 * @package uleam-calidad
 */

get_header();

$is_noticia = ( 'noticia' === get_post_type() );
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'reading' ); ?>>
		<header class="reading__header">
			<?php if ( $is_noticia ) : ?>
				<p class="reading__kicker">Noticia</p>
			<?php endif; ?>
			<h1 class="reading__title"><?php the_title(); ?></h1>
			<?php if ( $is_noticia ) : ?>
				<p class="reading__meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<?php if ( get_the_author() ) : ?>
						<span class="reading__dot" aria-hidden="true"></span>
						<span><?php echo esc_html( get_the_author() ); ?></span>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="reading__figure">
				<?php
				the_post_thumbnail(
					'large',
					array(
						'class'   => 'reading__image',
						'loading' => 'eager',
						'alt'     => the_title_attribute( array( 'echo' => false ) ),
					)
				);
				?>
			</figure>
		<?php endif; ?>

		<div class="reading__body entry-content">
			<?php the_content(); ?>

			<?php
			if ( 'documento' === get_post_type() ) :
				$doc_url = get_post_meta( get_the_ID(), '_documento_url', true );
				if ( $doc_url ) :
					?>
					<p class="reading__cta">
						<a class="btn" href="<?php echo esc_url( $doc_url ); ?>" target="_blank" rel="noopener"><?php echo uleam_icon( 'download' ); ?> Descargar documento</a>
					</p>
					<?php
				endif;
			endif;
			?>
		</div>
	</article>
	<?php
endwhile;
?>

<?php get_footer(); ?>
