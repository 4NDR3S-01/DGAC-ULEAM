<?php
/**
 * Pie de página del tema
 *
 * Todo se edita sin código: textos y redes en Apariencia → Personalizar → "ULEAM - Contenido del sitio",
 * y los enlaces rápidos en Apariencia → Menús (ubicación "Pie de página: enlaces rápidos").
 *
 * @package uleam-calidad
 */
?>
</main>

<footer class="footer" id="contacto">
  <div class="footer-grid">
    <div>
      <h2 class="footer-title">Sobre la Dirección</h2>
      <p><?php echo wp_kses_post( uleam_opt( 'uleam_sobre' ) ); ?></p>
    </div>
    <nav aria-label="Enlaces rápidos">
      <h2 class="footer-title">Enlaces rápidos</h2>
      <?php
      wp_nav_menu(
        array(
          'theme_location' => 'footer',
          'container'      => false,
          'menu_class'     => 'footer-links',
          'depth'          => 1,
          'fallback_cb'    => 'uleam_footer_menu_fallback',
        )
      );
      ?>
    </nav>
    <div>
      <h2 class="footer-title">Contacto</h2>
      <ul class="footer-contact">
        <?php if ( uleam_opt( 'uleam_telefono' ) ) : ?>
          <li>
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
            <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', uleam_opt( 'uleam_telefono' ) ) ); ?>"><?php echo esc_html( uleam_opt( 'uleam_telefono' ) ); ?></a>
          </li>
        <?php endif; ?>
        <?php if ( uleam_opt( 'uleam_email' ) ) : ?>
          <li>
            <?php echo uleam_icon( 'mail' ); ?>
            <a href="mailto:<?php echo esc_attr( uleam_opt( 'uleam_email' ) ); ?>"><?php echo esc_html( uleam_opt( 'uleam_email' ) ); ?></a>
          </li>
        <?php endif; ?>
        <?php if ( uleam_opt( 'uleam_direccion' ) ) : ?>
          <li>
            <?php echo uleam_icon( 'location' ); ?>
            <span><?php echo esc_html( uleam_opt( 'uleam_direccion' ) ); ?></span>
          </li>
        <?php endif; ?>
      </ul>
    </div>
    <?php if ( uleam_redes() ) : ?>
      <div>
        <h2 class="footer-title">Síguenos</h2>
        <div class="footer-social">
          <?php foreach ( uleam_redes() as $red ) : ?>
            <a href="<?php echo esc_url( $red[0] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $red[1] . ' ULEAM (se abre en otra pestaña)' ); ?>">
              <i class="<?php echo esc_attr( $red[2] ); ?>" aria-hidden="true"></i>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <div class="copy">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Universidad Laica Eloy Alfaro de Manabí - Todos los derechos reservados.</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
