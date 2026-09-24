<?php
/**
 * Plantilla de archivo (listados de noticias, documentos, etc.)
 *
 * @package uleam-calidad
 */

get_header();
?>

<header class="page-header">
	<h1><?php the_archive_title(); ?></h1>
</header>

<div class="archive-list">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p class="meta">
					<?php if ( 'noticia' === get_post_type() ) : ?>
						<?php echo esc_html( get_the_date() ); ?>
					<?php else : ?>
						<?php echo esc_html( get_the_date() ); ?>
						<?php
						$tipos = get_the_terms( get_the_ID(), 'tipo_documento' );
						if ( $tipos && ! is_wp_error( $tipos ) ) {
							echo ' · ' . esc_html( $tipos[0]->name );
						}
						?>
					<?php endif; ?>
				</p>
				<?php the_excerpt(); ?>
			</article>
			<?php
		endwhile;
		the_posts_pagination();
		?>
	<?php else : ?>
		<p>No hay contenido publicado todavía.</p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
