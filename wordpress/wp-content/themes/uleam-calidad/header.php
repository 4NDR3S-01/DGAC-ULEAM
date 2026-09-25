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
  <div class="topbar__contact">
    <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', uleam_opt( 'uleam_telefono' ) ) ); ?>">
      <i class="fa-solid fa-phone" aria-hidden="true"></i>
      <?php echo esc_html( uleam_opt( 'uleam_telefono' ) ); ?>
    </a>
    <a href="mailto:<?php echo esc_attr( uleam_opt( 'uleam_email' ) ); ?>">
      <i class="fa-solid fa-envelope" aria-hidden="true"></i>
      <?php echo esc_html( uleam_opt( 'uleam_email' ) ); ?>
    </a>
  </div>
  <div class="topbar__social">
    <span>Síguenos</span>
    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
  </div>
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
