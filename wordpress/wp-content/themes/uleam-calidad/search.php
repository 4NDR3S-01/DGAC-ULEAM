<?php
/**
 * Plantilla de resultados de búsqueda
 *
 * @package uleam-calidad
 */

get_header();
?>

<header class="page-header">
	<h1>Resultados para: <?php echo esc_html( get_search_query() ); ?></h1>
</header>

<div class="archive-list">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p class="meta"><?php echo esc_html( get_the_date() ); ?></p>
				<?php the_excerpt(); ?>
			</article>
			<?php
		endwhile;
		the_posts_pagination();
		?>
	<?php else : ?>
		<p>No se encontraron resultados para tu búsqueda.</p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
