<?php
/**
 * Archivo de noticias — listado en cuadrícula
 *
 * @package uleam-calidad
 */

get_header();

$archive_url = get_post_type_archive_link( 'noticia' );
$current_cat = isset( $_GET['categoria_noticia'] ) ? sanitize_title( wp_unslash( $_GET['categoria_noticia'] ) ) : '';
$current_s   = get_search_query();
$categorias  = get_terms(
	array(
		'taxonomy'   => 'categoria_noticia',
		'hide_empty' => false,
	)
);
?>

<section class="news-archive">
	<div class="news-archive__wrap">
		<header class="news-archive__header">
			<div class="news-archive__intro">
				<h1>Noticias y Actualidad</h1>
				<p>Comunicados, avances y novedades de la Dirección de Gestión y Aseguramiento de la Calidad.</p>
			</div>

			<form class="news-archive__filters" method="get" action="<?php echo esc_url( $archive_url ); ?>" role="search">
				<label class="screen-reader-text" for="news-search">Buscar noticias</label>
				<div class="news-archive__search">
					<?php echo uleam_icon( 'search' ); ?>
					<input id="news-search" type="search" name="s" placeholder="Buscar noticias…" value="<?php echo esc_attr( $current_s ); ?>" />
				</div>

				<label class="screen-reader-text" for="news-cat">Categoría</label>
				<select id="news-cat" name="categoria_noticia">
					<option value="">Todas las categorías</option>
					<?php
					if ( ! empty( $categorias ) && ! is_wp_error( $categorias ) ) :
						foreach ( $categorias as $cat ) :
							?>
							<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $current_cat, $cat->slug ); ?>>
								<?php echo esc_html( $cat->name ); ?>
							</option>
							<?php
						endforeach;
					endif;
					?>
				</select>

				<button type="submit" class="btn btn--primary btn--sm">Filtrar</button>
			</form>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="news-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$cats     = get_the_terms( get_the_ID(), 'categoria_noticia' );
					$cat_name = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : 'Noticia';
					$excerpt  = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 28 );
					?>
					<article <?php post_class( 'news-card' ); ?>>
						<a class="news-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php
								the_post_thumbnail(
									'medium_large',
									array(
										'class'   => 'news-card__image',
										'loading' => 'lazy',
										'alt'     => the_title_attribute( array( 'echo' => false ) ),
									)
								);
								?>
							<?php else : ?>
								<span class="news-card__placeholder" aria-hidden="true"></span>
							<?php endif; ?>
						</a>

						<div class="news-card__body">
							<span class="news-card__badge"><?php echo esc_html( $cat_name ); ?></span>
							<h2 class="news-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="news-card__meta">
								<?php echo uleam_icon( 'calendar' ); ?>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							</p>
							<p class="news-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
							<a class="news-card__link" href="<?php the_permalink(); ?>">
								Leer noticia <?php echo uleam_icon( 'arrow' ); ?>
							</a>
						</div>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<nav class="news-pagination" aria-label="Paginación de noticias">
				<?php
				echo paginate_links(
					array(
						'total'     => $GLOBALS['wp_query']->max_num_pages,
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'prev_text' => 'Anterior',
						'next_text' => 'Siguiente',
						'type'      => 'list',
						'mid_size'  => 1,
						'end_size'  => 1,
					)
				);
				?>
			</nav>
		<?php else : ?>
			<p class="news-archive__empty">No hay noticias publicadas con esos criterios.</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
