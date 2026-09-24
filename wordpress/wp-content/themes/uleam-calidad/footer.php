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
      <p>☎ <?php echo esc_html( uleam_opt( 'uleam_telefono' ) ); ?><br>
      ✉ <?php echo esc_html( uleam_opt( 'uleam_email' ) ); ?><br>
      📍 <?php echo esc_html( uleam_opt( 'uleam_direccion' ) ); ?></p>
    </div>
    <div>
      <h4>Síguenos</h4>
      <p style="font-size:28px">f &nbsp; ◎ &nbsp; ▶</p>
    </div>
  </div>
  <div class="copy">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Universidad Laica Eloy Alfaro de Manabí - Todos los derechos reservados.</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
