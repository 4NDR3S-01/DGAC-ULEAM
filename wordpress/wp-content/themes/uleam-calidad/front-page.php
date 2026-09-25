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
    <h1><?php echo wp_kses_post( uleam_opt( 'uleam_hero_titulo' ) ); ?></h1>
    <p><?php echo esc_html( uleam_opt( 'uleam_hero_subtitulo' ) ); ?></p>
    <a class="btn" href="#direccion">Conócenos →</a>
  </div>
  <?php $uleam_hero_url = wp_get_attachment_image_url( (int) uleam_opt( 'uleam_hero_imagen' ), 'full' ); ?>
  <div class="hero-image" role="img" aria-label="Campus universitario"<?php echo $uleam_hero_url ? ' style="background-image:url(' . esc_url( $uleam_hero_url ) . ');"' : ''; ?>></div>
</section>

<section class="section" id="direccion">
  <h2>Accesos rápidos</h2>
  <div class="quick-grid">
    <div class="card"><div class="icon"><?php echo uleam_icon( 'chart' ); ?></div><h3>Indicadores</h3><a href="#desempeno">Ver más →</a></div>
    <div class="card"><div class="icon"><?php echo uleam_icon( 'clipboard' ); ?></div><h3>Evaluaciones</h3><a href="#desempeno">Ver más →</a></div>
    <div class="card"><div class="icon"><?php echo uleam_icon( 'refresh' ); ?></div><h3>Procesos</h3><a href="#procesos">Ver más →</a></div>
    <div class="card"><div class="icon"><?php echo uleam_icon( 'folder' ); ?></div><h3>Documentos</h3><a href="#documentos">Ver más →</a></div>
    <div class="card"><div class="icon"><?php echo uleam_icon( 'graduate' ); ?></div><h3>Desempeño</h3><a href="#desempeno">Ver más →</a></div>
    <div class="card"><div class="icon"><?php echo uleam_icon( 'mail' ); ?></div><h3>Contacto</h3><a href="#contacto">Ver más →</a></div>
  </div>
</section>

<section class="two-col" id="procesos">
  <div class="panel" id="desempeno">
    <h3>Evaluación de Desempeño</h3>
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
      <a class="btn" href="#documentos">Ver resultados</a>
    <?php else : ?>
      <div class="tabs">
        <button class="tab active" data-text="Consulta los resultados de la evaluación de desempeño académico y administrativo del período 2025-1.">2025-1</button>
      </div>
      <p id="periodText">Consulta los resultados de la evaluación de desempeño académico y administrativo del período 2025-1.</p>
      <a class="btn" href="#documentos">Ver resultados</a>
    <?php endif; ?>
  </div>

  <div class="panel" id="noticias">
    <h3>Noticias y comunicados</h3>
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
            <?php if ( has_post_thumbnail() ) : ?>
              <?php the_post_thumbnail( 'thumbnail', array( 'style' => 'width:92px;height:64px;object-fit:cover;border-radius:5px' ) ); ?>
            <?php endif; ?>
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
    <a class="news-more" href="<?php echo esc_url( get_post_type_archive_link( 'noticia' ) ); ?>">Ver todas las noticias →</a>
  </div>
</section>

<section class="library" id="documentos">
  <h3>Biblioteca documental</h3>
  <form class="filters" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="hidden" name="post_type" value="documento" />
    <input type="search" name="s" placeholder="Buscar documento..." value="<?php echo get_search_query(); ?>" />

    <select name="tipo_documento">
      <option value="">Tipo de documento</option>
      <?php
      $tipos = get_terms( array( 'taxonomy' => 'tipo_documento', 'hide_empty' => false ) );
      foreach ( $tipos as $tipo ) {
        $sel = ( isset( $_GET['tipo_documento'] ) && $_GET['tipo_documento'] === $tipo->slug ) ? ' selected' : '';
        echo '<option value="' . esc_attr( $tipo->slug ) . '"' . $sel . '>' . esc_html( $tipo->name ) . '</option>';
      }
      ?>
    </select>

    <select name="proceso">
      <option value="">Proceso</option>
      <?php
      $procesos = get_terms( array( 'taxonomy' => 'proceso', 'hide_empty' => false ) );
      foreach ( $procesos as $proceso ) {
        $sel = ( isset( $_GET['proceso'] ) && $_GET['proceso'] === $proceso->slug ) ? ' selected' : '';
        echo '<option value="' . esc_attr( $proceso->slug ) . '"' . $sel . '>' . esc_html( $proceso->name ) . '</option>';
      }
      ?>
    </select>

    <select name="anio">
      <option value="">Año</option>
      <?php
      $anios = get_terms( array( 'taxonomy' => 'anio', 'hide_empty' => false ) );
      foreach ( $anios as $anio ) {
        $sel = ( isset( $_GET['anio'] ) && $_GET['anio'] === $anio->slug ) ? ' selected' : '';
        echo '<option value="' . esc_attr( $anio->slug ) . '"' . $sel . '>' . esc_html( $anio->name ) . '</option>';
      }
      ?>
    </select>

    <button type="submit">Buscar</button>
  </form>

  <div class="docs" id="docsList">
    <?php
    $doc_args = array(
      'post_type'      => 'documento',
      'posts_per_page' => 12,
    );
    if ( isset( $_GET['s'] ) && $_GET['s'] !== '' ) {
      $doc_args['s'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
    }
    $tax_query = array();
    if ( isset( $_GET['tipo_documento'] ) && $_GET['tipo_documento'] !== '' ) {
      $tax_query[] = array( 'taxonomy' => 'tipo_documento', 'field' => 'slug', 'terms' => sanitize_title( wp_unslash( $_GET['tipo_documento'] ) ) );
    }
    if ( isset( $_GET['proceso'] ) && $_GET['proceso'] !== '' ) {
      $tax_query[] = array( 'taxonomy' => 'proceso', 'field' => 'slug', 'terms' => sanitize_title( wp_unslash( $_GET['proceso'] ) ) );
    }
    if ( isset( $_GET['anio'] ) && $_GET['anio'] !== '' ) {
      $tax_query[] = array( 'taxonomy' => 'anio', 'field' => 'slug', 'terms' => sanitize_title( wp_unslash( $_GET['anio'] ) ) );
    }
    if ( ! empty( $tax_query ) ) {
      $doc_args['tax_query'] = $tax_query;
    }

    $docs = new WP_Query( $doc_args );
    if ( $docs->have_posts() ) :
      while ( $docs->have_posts() ) :
        $docs->the_post();
        $doc_url = get_post_meta( get_the_ID(), '_documento_url', true );
        $tipo    = get_the_terms( get_the_ID(), 'tipo_documento' );
        $tipo_n  = ( $tipo && ! is_wp_error( $tipo ) ) ? $tipo[0]->name : 'Archivo';
        ?>
        <div class="doc">
          <div class="doc-icon"><?php echo uleam_icon( 'file' ); ?></div>
          <div>
            <strong>
              <?php if ( $doc_url ) : ?>
                <a href="<?php echo esc_url( $doc_url ); ?>" target="_blank" rel="noopener"><?php the_title(); ?></a>
              <?php else : ?>
                <?php the_title(); ?>
              <?php endif; ?>
            </strong>
            <small><?php echo esc_html( $tipo_n ); ?></small>
          </div>
        </div>
        <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p>No se encontraron documentos con ese criterio.</p>';
    endif;
    ?>
  </div>
</section>

<?php
get_footer();
