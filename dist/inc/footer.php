<?php
/**
 * Title: Footer
 * Slug: EasySubscribe
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Footer columns with logo, title, tagline and links.
 *
 * @package WordPress
 * @subpackage EasySubscribe
 * @since EasySubscribe 1.0
 */

// Verifica se siamo su WordPress
if (defined('ABSPATH')) {
    // Percorsi per WordPress (usa il tema attivo)
    $base_url = get_template_directory_uri();
    $phone_text = __('phone_text', 'easy_subscribe');
} else {
    // Percorsi per lo sviluppo locale
    $base_url = '/../dist';  // Cambia con il percorso corretto per lo sviluppo locale
    $phone_text = 'Telefono: ';
}
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/footer.css" />

<footer class="text-white text-center text-lg-start mt-5 fade-in">
  <div class="container p-4">
    <div class="row" style="margin-bottom: -10px">
      <div class="col-lg-4 col-md-12" id="footer-logo-col">
        <img
          class="footer-logo"
          src="<?php echo $base_url; ?>/assets/images/easy.png"
          alt="EasySubscribe"
        />
      </div>
      <div class="col-lg-4 col-md-6" id="footer-follow-col">
        <h5 id="follow_text" class="text-uppercase"></h5>
        <div class="social">
          <a href="https://www.facebook.com/share/15ZaQKxccb/?mibextid=wwXIfr" target="_blank" class="link facebook" target="_parent">
            <span class="fab fa-facebook"></span>
          </a>
          <a href="https://www.instagram.com/easy_subscribe_kizz" target="_blank" class="link instagram" target="_parent">
            <span class="fab fa-instagram"></span>
          </a>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
          <h5 class="text-uppercase" id="contact_text"></h5>
          <p class="text-white">
              Email: 
              <a class="text-white" href="mailto:info@easysubscribe.it">info@easysubscribe.it</a><br>
              <?php echo $phone_text; ?> 
              <a class="text-white" href="tel:+393770839135">+39 377 083 9135</a>
          </p>
      </div>

    </div>
  </div>
  <div
    class="text-center p-3"
    style="background-color: rgba(255, 255, 255, 0.1)"
  >
    © 2025 <a class="text-white" href="#">EasySubscribe</a>
  </div>
</footer>
