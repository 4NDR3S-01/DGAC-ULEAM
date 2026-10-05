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

<a class="skip-link" href="#main">Saltar al contenido</a>

<div class="topbar" role="region" aria-label="Contacto y redes sociales">
  <div class="topbar__contact">
    <?php if ( uleam_opt( 'uleam_telefono' ) ) : ?>
      <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', uleam_opt( 'uleam_telefono' ) ) ); ?>">
        <i class="fa-solid fa-phone" aria-hidden="true"></i>
        <?php echo esc_html( uleam_opt( 'uleam_telefono' ) ); ?>
      </a>
    <?php endif; ?>
    <?php if ( uleam_opt( 'uleam_email' ) ) : ?>
      <a href="mailto:<?php echo esc_attr( uleam_opt( 'uleam_email' ) ); ?>">
        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
        <?php echo esc_html( uleam_opt( 'uleam_email' ) ); ?>
      </a>
    <?php endif; ?>
  </div>
  <?php if ( uleam_redes() ) : ?>
    <div class="topbar__social">
      <span>Síguenos</span>
      <?php foreach ( uleam_redes() as $red ) : ?>
        <a class="topbar__social-link" href="<?php echo esc_url( $red[0] ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $red[1] ); ?>" aria-label="<?php echo esc_attr( $red[1] . ' ULEAM (se abre en otra pestaña)' ); ?>">
          <i class="<?php echo esc_attr( $red[2] ); ?>" aria-hidden="true"></i>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
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
    <button type="submit" aria-label="Buscar"><?php echo uleam_icon( 'search' ); ?></button>
  </form>
</header>

<nav class="nav" aria-label="Menú principal">
  <button class="nav-toggle" id="navToggle" type="button" aria-expanded="false" aria-controls="primaryMenu" aria-label="Abrir menú de navegación">
    <i class="fa-solid fa-bars" aria-hidden="true"></i>
    <span>Menú</span>
  </button>
  <?php
  wp_nav_menu(
    array(
      'theme_location' => 'primary',
      'menu_id'        => 'primaryMenu',
      'menu_class'     => 'nav-inner',
      'container'      => false,
      'fallback_cb'    => 'uleam_menu_fallback',
      'depth'          => 3,
    )
  );
  ?>
</nav>

<main id="main" class="site-main">
