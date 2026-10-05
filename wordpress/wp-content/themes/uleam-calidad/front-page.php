<?php
/**
 * Portada del tema ULEAM Calidad
 *
 * @package uleam-calidad
 */

get_header();
?>

<section class="hero" id="inicio">
  <div class="hero-copy">
    <p class="hero-kicker">ULEAM · Aseguramiento de la Calidad</p>
    <h1><?php echo wp_kses_post( uleam_opt( 'uleam_hero_titulo' ) ); ?></h1>
    <p><?php echo esc_html( uleam_opt( 'uleam_hero_subtitulo' ) ); ?></p>
    <?php if ( uleam_opt( 'uleam_hero_boton' ) ) : ?>
      <a class="btn" href="<?php echo esc_url( uleam_opt( 'uleam_hero_boton_url' ) ); ?>"><?php echo esc_html( uleam_opt( 'uleam_hero_boton' ) ); ?></a>
    <?php endif; ?>
  </div>
  <?php $uleam_hero_url = wp_get_attachment_image_url( (int) uleam_opt( 'uleam_hero_imagen' ), 'full' ); ?>
  <div class="hero-image" role="img" aria-label="Campus universitario"<?php echo $uleam_hero_url ? ' style="background-image:url(' . esc_url( $uleam_hero_url ) . ');"' : ''; ?>></div>
</section>

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

<section class="two-col" id="procesos">
  <div class="panel" id="desempeno">
    <h2>Evaluación de Desempeño</h2>
    <?php
    $periodos = get_terms(
      array(
        'taxonomy'   => 'periodo',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
      )
    );
    if ( ! empty( $periodos ) && ! is_wp_error( $periodos ) ) :
      $primero = true;
      ?>
      <div class="tabs">
        <?php foreach ( $periodos as $periodo ) : ?>
          <button class="tab<?php echo $primero ? ' active' : ''; ?>" data-text="<?php echo esc_attr( $periodo->description ); ?>"><?php echo esc_html( $periodo->name ); ?></button>
          <?php $primero = false; ?>
        <?php endforeach; ?>
      </div>
      <p id="periodText"><?php echo esc_html( reset( $periodos )->description ); ?></p>
      <a class="btn" href="<?php echo esc_url( home_url( '/aseguramiento-de-la-calidad/#evaluacion-del-desempeno' ) ); ?>">Ver resultados</a>
    <?php else : ?>
      <div class="tabs">
        <button class="tab active" data-text="Consulta los resultados de la evaluación de desempeño académico y administrativo del período 2025-1.">2025-1</button>
      </div>
      <p id="periodText">Consulta los resultados de la evaluación de desempeño académico y administrativo del período 2025-1.</p>
      <a class="btn" href="<?php echo esc_url( home_url( '/aseguramiento-de-la-calidad/#evaluacion-del-desempeno' ) ); ?>">Ver resultados</a>
    <?php endif; ?>
  </div>

  <div class="panel" id="noticias">
    <h2>Noticias y comunicados</h2>
    <?php
    $noticias = new WP_Query(
      array(
        'post_type'      => 'noticia',
        'posts_per_page' => 3,
      )
    );
    if ( $noticias->have_posts() ) :
      while ( $noticias->have_posts() ) :
        $noticias->the_post();
        ?>
        <div class="news-item">
          <div class="news-thumb">
            <?php echo uleam_noticia_media( get_the_ID(), 'medium', 'news-thumb__img' ); // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de wp_get_attachment_image. ?>
          </div>
          <div>
            <strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
            <small><?php echo esc_html( get_the_date() ); ?></small>
          </div>
        </div>
        <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p>No hay noticias publicadas todavía.</p>';
    endif;
    ?>
    <a class="news-more" href="<?php echo esc_url( get_post_type_archive_link( 'noticia' ) ); ?>">Ver todas las noticias <?php echo uleam_icon( 'arrow' ); ?></a>
  </div>
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
