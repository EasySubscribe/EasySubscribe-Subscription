<?php
/**
 * Title: Header
 * Slug: EasySubscribe
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Header columns with logo, title, tagline and links.
 *
 * @package WordPress
 * @subpackage EasySubscribe
 * @since EasySubscribe 1.0
 */

// Verifica se siamo su WordPress
if (defined('ABSPATH')) {
    // Percorsi per WordPress (usa il tema attivo)
    $base_url = get_template_directory_uri();
    $home_url = home_url('/');
    $terms_and_condition = home_url('/terms-and-conditions/');
    $scan = home_url('/scan/');
    $contact = home_url('/contact-me/');
    wp_head();
} else {
    // Percorsi per lo sviluppo locale
    $base_url = '/../dist';  // Cambia con il percorso corretto per lo sviluppo locale
    $home_url = '/../dist/index.php';
    $terms_and_condition = '/../dist/templates/template-policy.php';
    $scan = '/../dist/templates/template-read-qrcode.php';
    $contact = '/../dist/templates/template-contact-me.php';
}
?>

<!-- File personalizzati -->
<script src="<?php echo $base_url; ?>/assets/js/sweetalert.js"></script>
<script src="<?php echo $base_url; ?>/assets/js/utils.js"></script>
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css" />
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/animation.css" />

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css" />

<nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid">
      <a href="<?php echo $home_url; ?>" class="navbar-brand ms-2 slide-in-left">
        <img
          src="<?php echo $base_url; ?>/assets/images/easy.png"
          alt="Antonio Rausa"
          style="max-height: 50px"
          class="zoom_simple"
        />
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse slide-in-right" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active fw-bold" id="terms_and_condition_text" href="<?php echo $terms_and_condition; ?>"></a>
          </li>
          <li class="nav-item">
              <a class="nav-link active fw-bold" id="scan_text" href="<?php echo $scan; ?>"></a>
          </li>
          <li class="nav-item">
              <a class="nav-link active fw-bold" id="contact_text" href="<?php echo $contact; ?>"></a>
          </li>
          <li class="nav-item">
              <a class="nav-link active fw-bold" id="home_text" href="<?php echo $home_url; ?>"></a>
          </li>
        </ul>
      </div>
    </div>
</nav>