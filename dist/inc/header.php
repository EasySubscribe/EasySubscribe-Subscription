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
    $locale = get_locale(); // Recupera la lingua di WordPress
} else {
    // Percorsi per lo sviluppo locale
    $base_url = '/../dist';  // Cambia con il percorso corretto per lo sviluppo locale
    $home_url = '/../dist/index.php';
    $terms_and_condition = '/../dist/templates/template-policy.php';
    $scan = '/../dist/templates/template-read-qrcode.php';
    $contact = '/../dist/templates/template-contact-me.php';
    $locale = 'it_IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
}
?>
<!-- File personalizzati -->
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
        <?php /* if (defined('ABSPATH')) : ?>
        <?php
        // Siamo in WordPress, otteniamo i link salvati
        $redirect_links = get_option('redirect_links', []);
        foreach ($redirect_links as $link) {
            // Mostra i link salvati
            echo '<li class="nav-item">';
            echo '<a class="nav-link active fw-bold" href="' . esc_url($link['link']) . '">' . esc_html($link['name']) . '</a>';
            echo '</li>';
        }
        ?>
          <?php else : ?>
          <?php
          // Siamo in locale, usa i link locali
          */
          ?>
          <li class="nav-item">
            <a class="nav-link active fw-bold" id="terms_and_condition_text" href="<?php echo $terms_and_condition; ?>"></a>
          </li>
          <li class="nav-item">
              <a class="nav-link active fw-bold" id="scan_text" href="<?php echo $scan; ?>"></a>
          </li>
          <li class="nav-item">
              <a class="nav-link active fw-bold" id="contact_text" href="<?php echo $contact; ?>"></a>
          </li>
          <!--<li class="nav-item">
              <a class="nav-link active fw-bold" id="home_text" href="<?php echo $home_url; ?>"></a>
          </li>-->
          <?php //endif; ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?php echo $base_url; ?>/assets/images/languages/<?php echo $locale; ?>.png" alt="Flags" class="flags">
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#" onclick="setLanguage('it_IT')"><img src="<?php echo $base_url; ?>/assets/images/languages/it_IT.png" alt="Flags" class="flags-menu"> Italiano</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('en_GB')"><img src="<?php echo $base_url; ?>/assets/images/languages/en_GB.png" alt="Flags" class="flags-menu"> Inglese</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('es_ES')"><img src="<?php echo $base_url; ?>/assets/images/languages/es_ES.png" alt="Flags" class="flags-menu"> Spagnolo</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('fr_FR')"><img src="<?php echo $base_url; ?>/assets/images/languages/fr_FR.png" alt="Flags" class="flags-menu"> Francese</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('de_DE')"><img src="<?php echo $base_url; ?>/assets/images/languages/de_DE.png" alt="Flags" class="flags-menu"> Tedesco</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
</nav>
<script>
  function setLanguage(languageCode) {
    // Imposta un cookie con la lingua selezionata
    document.cookie = "site_language=" + languageCode + "; path=/";
    location.reload(); // Ricarica la pagina per applicare la nuova lingua
  }
</script>
