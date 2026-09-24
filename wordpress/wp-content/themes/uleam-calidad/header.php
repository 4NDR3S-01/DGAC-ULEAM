<?php
/**
 * Cabecera del tema
 *
 * @package uleam-calidad
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="topbar">
  <div>☎ <?php echo esc_html( uleam_opt( 'uleam_telefono' ) ); ?> &nbsp;&nbsp; ✉ <?php echo esc_html( uleam_opt( 'uleam_email' ) ); ?></div>
  <div>Síguenos: &nbsp; f &nbsp; ◎ &nbsp; ▶</div>
</div>

<header class="header">
  <div class="brand">
    <div class="logo-box">
      <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <div class="logo-circle">U</div>
        <div class="uleam">Uleam<small>UNIVERSIDAD LAICA ELOY ALFARO DE MANABÍ</small></div>
      <?php endif; ?>
    </div>
    <div class="divider"></div>
    <div class="title">Dirección de Gestión<br>y Aseguramiento de la Calidad</div>
  </div>
  <form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="search" name="s" placeholder="Buscar en el sitio..." value="<?php echo get_search_query(); ?>" />
    <button type="submit">⌕</button>
  </form>
</header>

<nav class="nav">
  <?php
  wp_nav_menu(
    array(
      'theme_location' => 'primary',
      'menu_class'     => 'nav-inner',
      'container'      => false,
      'fallback_cb'    => 'uleam_menu_fallback',
      'depth'          => 1,
    )
  );
  ?>
</nav>
