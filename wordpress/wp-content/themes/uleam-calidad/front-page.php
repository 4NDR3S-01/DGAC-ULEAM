<?php
/**
 * Portada del tema ULEAM Calidad
 *
 * @package uleam-calidad
 */

get_header();
?>

<h1 class="screen-reader-text"><?php bloginfo( 'name' ); ?></h1>

<?php
// Carrusel editable en "Carrusel de portada"; si aún no hay diapositivas, el hero del Personalizador.
if ( ! uleam_carrusel_render() ) :
  ?>
<section class="hero" id="inicio">
  <div class="hero-copy">
    <p class="hero-kicker">ULEAM · Aseguramiento de la Calidad</p>
    <h2 class="hero-titulo"><?php echo wp_kses_post( uleam_opt( 'uleam_hero_titulo' ) ); ?></h2>
    <p><?php echo esc_html( uleam_opt( 'uleam_hero_subtitulo' ) ); ?></p>
    <?php if ( uleam_opt( 'uleam_hero_boton' ) ) : ?>
      <a class="btn" href="<?php echo esc_url( uleam_url( uleam_opt( 'uleam_hero_boton_url' ) ) ); ?>"><?php echo esc_html( uleam_opt( 'uleam_hero_boton' ) ); ?></a>
    <?php endif; ?>
  </div>
  <?php $uleam_hero_url = wp_get_attachment_image_url( (int) uleam_opt( 'uleam_hero_imagen' ), 'full' ); ?>
  <div class="hero-image" role="img" aria-label="Campus universitario"<?php echo $uleam_hero_url ? ' style="background-image:url(' . esc_url( $uleam_hero_url ) . ');"' : ''; ?>></div>
</section>
<?php endif; ?>

<section class="section" id="direccion">
  <?php
  // Contenido editable: Páginas → "Inicio" (asignada como portada en Ajustes → Lectura).
  $portada = (int) get_option( 'page_on_front' );
  if ( $portada && '' !== trim( get_post_field( 'post_content', $portada ) ) ) {
    remove_filter( 'the_content', 'wpautop' );
    echo apply_filters( 'the_content', get_post_field( 'post_content', $portada ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- contenido de la página filtrado por WordPress.
    add_filter( 'the_content', 'wpautop' );
  }
  ?>
</section>

<section class="two-col" id="procesos" aria-labelledby="actualidad-titulo">
  <header class="two-col__head">
    <h2 id="actualidad-titulo">Actualidad</h2>
    <p>Noticias, comunicados y momentos de la Dirección de Gestión y Aseguramiento de la Calidad.</p>
  </header>
  <div class="panel news-panel" id="noticias">
    <h3>Noticias y comunicados</h3>
    <?php
    $noticias = new WP_Query(
      array(
        'post_type'           => 'noticia',
        'posts_per_page'      => 8, // se muestran las que quepan junto a la galería (main.js); 4 como mínimo
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
      )
    );
    if ( $noticias->have_posts() ) :
      echo '<ul class="news-list">';
      while ( $noticias->have_posts() ) :
        $noticias->the_post();
        ?>
        <li class="news-item">
          <div class="news-thumb">
            <?php echo uleam_noticia_media( get_the_ID(), 'medium', 'news-thumb__img' ); // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de wp_get_attachment_image. ?>
          </div>
          <div class="news-item__text">
            <a class="news-item__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></time>
          </div>
        </li>
        <?php
      endwhile;
      echo '</ul>';
      wp_reset_postdata();
    else :
      echo '<p>No hay noticias publicadas todavía.</p>';
    endif;
    ?>
    <a class="news-more" href="<?php echo esc_url( get_post_type_archive_link( 'noticia' ) ); ?>">Ver todas las noticias <?php echo uleam_icon( 'arrow' ); ?></a>
  </div>

  <?php
  $fotos = uleam_carrusel_fotos();
  $total = count( $fotos );
  ?>
  <?php if ( $fotos ) : ?>
    <div class="panel gallery" id="galeria" role="region" aria-roledescription="carrusel" aria-label="Galería de fotos de la Dirección">
      <div class="gallery__track" tabindex="0" aria-live="off">
        <?php foreach ( $fotos as $i => $foto ) : ?>
          <?php
          $alt   = '' !== $foto['texto'] ? $foto['texto'] : trim( (string) get_post_meta( $foto['id'], '_wp_attachment_image_alt', true ) );
          $alt   = '' !== $alt ? $alt : get_the_title( $foto['id'] );
          $fondo = uleam_carrusel_desenfocada( $foto['id'] );
          ?>
          <figure class="gallery__slide<?php echo $i ? '' : ' is-active'; ?>"<?php echo $i ? ' aria-hidden="true"' : ''; ?> role="group" aria-roledescription="diapositiva" aria-label="<?php echo esc_attr( ( $i + 1 ) . ' de ' . $total ); ?>">
            <div class="gallery__media">
              <?php if ( $fondo ) : ?>
                <img class="gallery__backdrop" src="<?php echo esc_url( $fondo ); ?>" alt="" aria-hidden="true" loading="<?php echo $i ? 'lazy' : 'eager'; ?>" decoding="async" />
              <?php endif; ?>
              <?php
              echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de wp_get_attachment_image.
                $foto['id'],
                'large',
                false,
                array(
                  'class'    => 'gallery__img',
                  'loading'  => $i ? 'lazy' : 'eager',
                  'decoding' => 'async',
                  'alt'      => $alt,
                  'sizes'    => '(max-width: 960px) 92vw, 44vw',
                )
              );
              ?>
              <button type="button" class="gallery__ampliar" aria-label="<?php echo esc_attr( 'Ampliar foto: ' . $alt ); ?>"><i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i></button>
            </div>
            <?php // Sin texto ni descripción, el pie no ocupa espacio: la foto usa toda la altura. ?>
            <figcaption class="gallery__caption<?php echo '' === $foto['texto'] && '' === $foto['descripcion'] ? ' screen-reader-text' : ''; ?>">
              <?php if ( '' === $foto['texto'] && '' === $foto['descripcion'] ) : ?>
                <?php echo esc_html( $alt ); ?>
              <?php else : ?>
                <?php if ( '' !== $foto['texto'] ) : ?>
                  <p class="gallery__titulo">
                    <?php if ( $foto['url'] ) : ?>
                      <a href="<?php echo esc_url( uleam_url( $foto['url'] ) ); ?>"><?php echo esc_html( $foto['texto'] ); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <?php else : ?>
                      <?php echo esc_html( $foto['texto'] ); ?>
                    <?php endif; ?>
                  </p>
                <?php endif; ?>
                <?php if ( '' !== $foto['descripcion'] ) : ?>
                  <p class="gallery__desc"><?php echo esc_html( $foto['descripcion'] ); ?></p>
                <?php endif; ?>
              <?php endif; ?>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
      <?php if ( $total > 1 ) : ?>
        <div class="gallery__controls">
          <div class="gallery__dots">
            <?php foreach ( $fotos as $i => $foto ) : ?>
              <button type="button" class="gallery__dot<?php echo $i ? '' : ' is-active'; ?>" aria-label="<?php echo esc_attr( 'Ver foto ' . ( $i + 1 ) . ' de ' . $total ); ?>"<?php echo $i ? '' : ' aria-current="true"'; ?>><span class="gallery__dot-fill"></span></button>
            <?php endforeach; ?>
          </div>
          <button type="button" class="gallery__btn gallery__btn--prev" aria-label="Foto anterior"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
          <button type="button" class="gallery__btn gallery__btn--toggle" aria-label="Pausar el cambio automático de fotos" data-label-play="Reanudar el cambio automático de fotos" data-label-pause="Pausar el cambio automático de fotos"><i class="fa-solid fa-pause" aria-hidden="true"></i></button>
          <button type="button" class="gallery__btn gallery__btn--next" aria-label="Foto siguiente"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</section>

<section class="library" id="documentos" aria-labelledby="biblioteca-titulo">
  <div class="library__head">
    <h2 id="biblioteca-titulo">Biblioteca documental</h2>
    <a class="news-more" href="<?php echo esc_url( home_url( '/aseguramiento-de-la-calidad/' ) ); ?>">Explorar el repositorio completo <?php echo uleam_icon( 'arrow' ); ?></a>
  </div>
  <form class="filters" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" aria-label="Buscar documentos">
    <input type="hidden" name="en" value="documento" />
    <label class="screen-reader-text" for="biblio-q">Nombre o código del documento</label>
    <input id="biblio-q" type="search" name="s" placeholder="Nombre o código del documento…" />

    <label class="screen-reader-text" for="biblio-area">Área</label>
    <select id="biblio-area" name="area">
      <option value="">Todas las áreas</option>
      <?php
      $raiz  = get_term_by( 'slug', 'aseguramiento-de-la-calidad', 'seccion' );
      $areas = $raiz ? uleam_seccion_hijas( $raiz->term_id ) : array();
      foreach ( $areas as $area ) {
        echo '<option value="' . esc_attr( $area->slug ) . '">' . esc_html( $area->name ) . '</option>';
      }
      ?>
    </select>

    <label class="screen-reader-text" for="biblio-tipo">Tipo de documento</label>
    <select id="biblio-tipo" name="tipo_documento">
      <option value="">Todos los tipos</option>
      <?php
      $tipos = get_terms( array( 'taxonomy' => 'tipo_documento', 'hide_empty' => true, 'meta_key' => 'orden', 'orderby' => 'meta_value_num' ) );
      foreach ( (array) $tipos as $tipo ) {
        echo '<option value="' . esc_attr( $tipo->slug ) . '">' . esc_html( $tipo->name ) . '</option>';
      }
      ?>
    </select>

    <label class="screen-reader-text" for="biblio-anio">Año</label>
    <select id="biblio-anio" name="anio">
      <option value="">Todos los años</option>
      <?php
      $anios = get_terms( array( 'taxonomy' => 'anio', 'hide_empty' => true ) );
      $anios = is_array( $anios ) ? $anios : array();
      usort(
        $anios,
        function ( $a, $b ) {
          return uleam_periodo_cmp( $a->name, $b->name );
        }
      );
      foreach ( $anios as $anio ) {
        echo '<option value="' . esc_attr( $anio->slug ) . '">' . esc_html( $anio->name ) . '</option>';
      }
      ?>
    </select>

    <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Buscar</button>
  </form>

  <h3 class="library__sub">Documentos recientes</h3>
  <div class="docs" id="docsList">
    <?php
    $docs = new WP_Query(
      array(
        'post_type'      => 'documento',
        'post_status'    => 'publish',
        'posts_per_page' => 8,
        'tax_query'      => array(
          array(
            'taxonomy' => 'seccion',
            'operator' => 'EXISTS',
          ),
        ),
        'orderby'        => array(
          'date' => 'DESC',
          'ID'   => 'DESC',
        ),
      )
    );
    if ( $docs->have_posts() ) :
      while ( $docs->have_posts() ) :
        $docs->the_post();
        $doc_url = get_post_meta( get_the_ID(), '_documento_url', true );
        $datos   = array();
        foreach ( array( 'tipo_documento', 'anio' ) as $tax ) {
          $t = get_the_terms( get_the_ID(), $tax );
          if ( $t && ! is_wp_error( $t ) ) {
            $datos[] = $t[0]->name;
          }
        }
        ?>
        <div class="doc">
          <div class="doc-icon"><i class="<?php echo esc_attr( uleam_doc_icono( (string) $doc_url ) ); ?>" aria-hidden="true"></i></div>
          <div>
            <strong>
              <?php if ( $doc_url ) : ?>
                <a href="<?php echo esc_url( $doc_url ); ?>" target="_blank" rel="noopener"><?php the_title(); ?></a>
              <?php else : ?>
                <?php the_title(); ?>
              <?php endif; ?>
            </strong>
            <small><?php echo esc_html( implode( ' · ', $datos ) ); ?></small>
          </div>
        </div>
        <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p>Aún no hay documentos publicados.</p>';
    endif;
    ?>
  </div>
</section>

<?php
get_footer();
