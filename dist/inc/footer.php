<?php
/**
 * Footer Template for EasySubscribe
 * Contains the footer content including logo, social media links, and contact information.
 */

// Verifica se siamo su WordPress
if (defined('ABSPATH')) {
    // Percorsi per WordPress (usa il tema attivo)
    $base_url = get_template_directory_uri();
    $follow_text = __('follow_text', 'easy_subscribe');
    $phone_text = __('phone_text', 'easy_subscribe');
    $contact_text = __('contact_text', 'easy_subscribe');
} else {
    // Percorsi per lo sviluppo locale
    $base_url = '/../dist';  // Cambia con il percorso corretto per lo sviluppo locale
    $follow_text = 'Follow Us';
    $phone_text = 'Telefono: ';
    $contact_text = 'Contattami';
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
        <h5 class="text-uppercase"><?php echo $follow_text; ?></h5>
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
          <h5 class="text-uppercase"><?php echo $contact_text; ?></h5>
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
    © 2025 Copyright:
    <a class="text-white" href="#">EasySubscribe</a>
  </div>
</footer>
