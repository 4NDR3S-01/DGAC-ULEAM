<?php
/**
 * Plantilla de resultados de búsqueda
 *
 * @package uleam-calidad
 */

get_header();

$q       = get_search_query( false );
$docs    = uleam_busqueda_filtros_doc();
$filtro  = $docs ? 'documento' : uleam_busqueda_filtro();
$tipos   = uleam_busqueda_tipos();
$hay     = '' !== trim( $q ) || $docs; // Se puede filtrar documentos sin escribir texto.
$conteos = $hay ? uleam_busqueda_conteos( $q ) : array_fill_keys( array_keys( $tipos ), 0 );
$total   = array_sum( $conteos );

// Temas sugeridos cuando no hay búsqueda o no hay resultados.
$temas = array( 'Autoevaluación', 'Plan de mejora', 'Formatos', 'Modelo CACES', 'Evaluación docente', 'POA' );

// Accesos directos a las secciones principales.
$accesos = array(
	array( 'Gestión de Procesos', '/gestion-de-procesos/', 'fa-solid fa-diagram-project', 'Manuales, procedimientos y formatos institucionales.' ),
	array( 'Aseguramiento de la Calidad', '/aseguramiento-de-la-calidad/', 'fa-solid fa-award', 'Autoevaluaciones, planes de mejora y evaluaciones.' ),
	array( 'Auditoría de la Calidad', '/auditoria-de-la-calidad/', 'fa-solid fa-clipboard-check', 'Seguimiento y control a procesos y planes de mejora.' ),
);
?>

<header class="page-header dgac-search-header">
	<div class="page-header__wrap">
		<span class="page-header__badge"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Búsqueda · ULEAM</span>
		<h1 class="page-header__title"><?php echo $hay ? 'Resultados de búsqueda' : 'Buscar en el sitio'; ?></h1>
		<form class="dgac-search-hero" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
			<input type="search" name="s" value="<?php echo esc_attr( $q ); ?>" placeholder="¿Qué estás buscando? Ej. formato de plan de mejora, PAE-01, autoevaluación 2026…" aria-label="Buscar en el sitio" autocomplete="off" />
			<?php if ( $filtro ) : ?>
				<input type="hidden" name="en" value="<?php echo esc_attr( $filtro ); ?>" />
			<?php endif; ?>
			<?php foreach ( $docs as $param => $f ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $param ); ?>" value="<?php echo esc_attr( $f[2]['terms'] ); ?>" />
			<?php endforeach; ?>
			<button type="submit">Buscar</button>
		</form>
	</div>
</header>

<div class="dgac-search-page">

	<?php if ( $hay && $total > 0 ) : ?>

		<div class="dgac-search-summary">
			<p class="dgac-search-summary__text">
				<strong><?php echo esc_html( number_format_i18n( $filtro ? $conteos[ $filtro ] : $total ) ); ?></strong>
				<?php echo esc_html( 1 === ( $filtro ? $conteos[ $filtro ] : $total ) ? 'resultado' : 'resultados' ); ?>
				<?php if ( '' !== trim( $q ) ) : ?>
					para <span class="dgac-search-summary__q">«<?php echo esc_html( $q ); ?>»</span>
				<?php endif; ?>
			</p>
			<?php if ( $docs ) : ?>
				<ul class="dgac-search-active" aria-label="Filtros aplicados">
					<?php foreach ( $docs as $param => $f ) : ?>
						<li>
							<a href="<?php echo esc_url( remove_query_arg( array( $param, 'paged' ) ) ); ?>" aria-label="<?php echo esc_attr( 'Quitar filtro ' . $f[0] . ': ' . $f[1] ); ?>">
								<?php echo esc_html( $f[0] . ': ' . $f[1] ); ?> <i class="fa-solid fa-xmark" aria-hidden="true"></i>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<nav class="dgac-pill-tabs dgac-search-filters" aria-label="Filtrar resultados por tipo">
				<a class="dgac-pill-tab<?php echo '' === $filtro ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 's', rawurlencode( $q ), home_url( '/' ) ) ); ?>"<?php echo '' === $filtro ? ' aria-current="page"' : ''; ?>>
					Todos <span class="dgac-search-filters__n"><?php echo esc_html( $total ); ?></span>
				</a>
				<?php foreach ( $tipos as $slug => $t ) : ?>
					<?php if ( $conteos[ $slug ] ) : ?>
						<a class="dgac-pill-tab<?php echo $filtro === $slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 's' => rawurlencode( $q ), 'en' => $slug ), home_url( '/' ) ) ); ?>"<?php echo $filtro === $slug ? ' aria-current="page"' : ''; ?>>
							<i class="<?php echo esc_attr( $t[2] ); ?>" aria-hidden="true"></i> <?php echo esc_html( $t[0] ); ?>
							<span class="dgac-search-filters__n"><?php echo esc_html( $conteos[ $slug ] ); ?></span>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>
		</div>

		<ol class="dgac-search-results">
			<?php
			while ( have_posts() ) :
				the_post();
				$item = uleam_busqueda_item( get_post() );
				?>
				<li class="dgac-result dgac-result--<?php echo esc_attr( $item['tipo'] ); ?>">
					<?php if ( 'noticia' === $item['tipo'] && has_post_thumbnail() ) : ?>
						<a class="dgac-result__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
					<?php else : ?>
						<span class="dgac-result__icon"><i class="<?php echo esc_attr( $item['icono'] ); ?>" aria-hidden="true"></i></span>
					<?php endif; ?>

					<div class="dgac-result__body">
						<span class="dgac-result__kicker">
							<?php echo esc_html( $item['etiqueta'] . ( $item['ext'] ? ' · ' . $item['ext'] : '' ) ); ?>
						</span>
						<h2 class="dgac-result__title">
							<a href="<?php echo esc_url( 'documento' === $item['tipo'] && $item['archivo'] ? $item['archivo'] : get_permalink() ); ?>"<?php echo 'documento' === $item['tipo'] && $item['archivo'] ? ' target="_blank" rel="noopener"' : ''; ?>>
								<?php echo uleam_resaltar( $item['titulo'], $q ); // phpcs:ignore WordPress.Security.EscapeOutput -- uleam_resaltar escapa el texto. ?>
							</a>
						</h2>

						<?php if ( $item['datos'] ) : ?>
							<ul class="dgac-result__chips">
								<?php foreach ( $item['datos'] as $dato ) : ?>
									<li><?php echo esc_html( $dato ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( 'documento' !== $item['tipo'] ) : ?>
							<?php $extracto = uleam_busqueda_extracto( get_post(), $q ); ?>
							<?php if ( $extracto ) : ?>
								<p class="dgac-result__excerpt"><?php echo uleam_resaltar( $extracto, $q ); // phpcs:ignore WordPress.Security.EscapeOutput -- uleam_resaltar escapa el texto. ?></p>
							<?php endif; ?>
						<?php endif; ?>

						<div class="dgac-result__actions">
							<?php if ( 'documento' === $item['tipo'] ) : ?>
								<?php if ( $item['archivo'] ) : ?>
									<a class="dgac-doc-mini-btn dgac-doc-mini-btn--view" href="<?php echo esc_url( $item['archivo'] ); ?>" target="_blank" rel="noopener"><i class="fa-regular fa-eye" aria-hidden="true"></i> Ver</a>
									<a class="dgac-doc-mini-btn dgac-doc-mini-btn--download" href="<?php echo esc_url( $item['archivo'] ); ?>" download><i class="fa-solid fa-download" aria-hidden="true"></i> Descargar</a>
								<?php endif; ?>
								<?php $ubicacion = uleam_documento_ubicacion( get_the_ID() ); ?>
								<?php if ( $ubicacion ) : ?>
									<a class="dgac-result__link" href="<?php echo esc_url( $ubicacion['url'] ); ?>">Ver en <?php echo esc_html( $ubicacion['area'] ); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
								<?php endif; ?>
							<?php elseif ( 'noticia' === $item['tipo'] ) : ?>
								<a class="dgac-result__link" href="<?php the_permalink(); ?>">Leer noticia <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
							<?php else : ?>
								<a class="dgac-result__link" href="<?php the_permalink(); ?>">Ir a la página <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endwhile; ?>
		</ol>

		<?php
		the_posts_pagination(
			array(
				'mid_size'           => 1,
				'prev_text'          => '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i><span class="screen-reader-text">Anterior</span>',
				'next_text'          => '<span class="screen-reader-text">Siguiente</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i>',
				'screen_reader_text' => 'Más resultados',
				'class'              => 'dgac-pagination',
			)
		);
		?>

	<?php else : ?>

		<div class="dgac-search-empty">
			<div class="dgac-search-empty__icon"><i class="fa-solid <?php echo $hay ? 'fa-magnifying-glass-minus' : 'fa-magnifying-glass'; ?>" aria-hidden="true"></i></div>
			<?php if ( $hay ) : ?>
				<h2>No encontramos resultados<?php echo '' !== trim( $q ) ? ' para «' . esc_html( $q ) . '»' : ' con esos filtros'; ?></h2>
				<?php if ( $docs ) : ?>
					<p><a href="<?php echo esc_url( add_query_arg( 's', rawurlencode( $q ), home_url( '/' ) ) ); ?>">Quitar los filtros de área, tipo y año</a></p>
				<?php endif; ?>
				<ul class="dgac-search-empty__tips">
					<li>Revisa la ortografía o usa menos palabras.</li>
					<li>Busca por el código del documento, por ejemplo <strong>PAE-01</strong> o <strong>PCS-01</strong>.</li>
					<li>Prueba con un término más general, como <strong>formato</strong> o <strong>informe</strong>.</li>
				</ul>
			<?php else : ?>
				<h2>Escribe un tema para empezar</h2>
				<p>Puedes buscar documentos, páginas y noticias de la Dirección.</p>
			<?php endif; ?>

			<div class="dgac-search-topics">
				<span class="dgac-search-topics__label">Temas frecuentes:</span>
				<?php foreach ( $temas as $tema ) : ?>
					<a class="dgac-pill-tab" href="<?php echo esc_url( add_query_arg( 's', rawurlencode( $tema ), home_url( '/' ) ) ); ?>"><?php echo esc_html( $tema ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="dgac-section-header">
			<span class="dgac-section-kicker">Explora por área</span>
			<h2 class="dgac-section-title"><i class="fa-solid fa-compass" aria-hidden="true"></i> Secciones principales</h2>
		</div>
		<div class="dgac-search-areas">
			<?php foreach ( $accesos as $a ) : ?>
				<a class="dgac-audit-card dgac-search-area" href="<?php echo esc_url( home_url( $a[1] ) ); ?>">
					<div>
						<div class="dgac-audit-card__icon"><i class="<?php echo esc_attr( $a[2] ); ?>" aria-hidden="true"></i></div>
						<h3 class="dgac-audit-card__title"><?php echo esc_html( $a[0] ); ?></h3>
						<p class="dgac-audit-card__desc"><?php echo esc_html( $a[3] ); ?></p>
					</div>
					<span class="dgac-audit-card__btn">Ir a la sección <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
				</a>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>
</div>

<?php get_footer(); ?>
