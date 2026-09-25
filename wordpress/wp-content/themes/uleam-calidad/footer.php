<?php
/**
 * Pie de página del tema
 *
 * @package uleam-calidad
 */
?>
<footer class="footer" id="contacto">
  <div class="footer-grid">
    <div>
      <h4>Sobre la Dirección</h4>
      <p><?php echo wp_kses_post( uleam_opt( 'uleam_sobre' ) ); ?></p>
    </div>
    <div>
      <h4>Enlaces rápidos</h4>
      <a href="#">Mapa del sitio</a><br>
      <a href="#">Normativa</a><br>
      <a href="#">Planes de mejora</a><br>
      <a href="#">Informes CACES</a>
    </div>
    <div>
      <h4>Contacto</h4>
      <ul class="footer-contact">
        <li>
          <span><?php echo esc_html( uleam_opt( 'uleam_telefono' ) ); ?></span>
        </li>
        <li>
          <?php echo uleam_icon( 'mail' ); ?>
          <span><?php echo esc_html( uleam_opt( 'uleam_email' ) ); ?></span>
        </li>
        <li>
          <?php echo uleam_icon( 'location' ); ?>
          <span><?php echo esc_html( uleam_opt( 'uleam_direccion' ) ); ?></span>
        </li>
      </ul>
    </div>
    <div>
      <h4>Síguenos</h4>
      <div class="footer-social">
        <a href="<?php echo esc_url( 'https://www.facebook.com/UleamEc' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook ULEAM">
          <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
        </a>
        <a href="<?php echo esc_url( 'https://www.instagram.com/uleam_ecuador_oficial/' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram ULEAM">
          <i class="fa-brands fa-instagram" aria-hidden="true"></i>
        </a>
        <a href="<?php echo esc_url( 'https://www.tiktok.com/@uleamecuador' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok ULEAM">
          <i class="fa-brands fa-tiktok" aria-hidden="true"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="copy">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Universidad Laica Eloy Alfaro de Manabí - Todos los derechos reservados.</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
