<?php
/*
  Template Name: Customer Billing
  Template URI: https://www.easysubscribe.it/template
  Author: Giovanni Lamarmora
  Author URI: https://giovannilamarmora.github.io
  Description: Template personalizzato per la pagina di iscrizione su EasySubscribe.
  Version: 1.0
  License: GNU General Public License v2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: easy-subscribe
*/
// Verifica se siamo su WordPress
if (defined('ABSPATH')) {
  // Percorsi per WordPress (usa il tema attivo)
  $base_url = get_template_directory_uri();
  $locale = get_locale(); // Recupera la lingua di WordPress
  $customers_subscription_title = __('customers_subscription_title', 'easy_subscribe');
  $site_name = esc_html(get_bloginfo('name'));
} else {
  // Percorsi per lo sviluppo locale
  $base_url = '/..';  // Cambia con il percorso corretto per lo sviluppo locale
  $locale = 'it-IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
  $customers_subscription_title = "Abbonamenti";
  $site_name = 'EasySubscribe'; // Nome del sito di fallback per PHP locale
}
?>
<html lang="<?= $locale; ?>" data-lt-installed="true">
  <head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, nofollow, noarchive" />

    <link rel="shortcut icon" href="https://www.easysubscribe.it/wp-content/uploads/2025/01/easy.png" />
    <meta
      content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"
      name="viewport"
    />
    <?php require __DIR__ . '/../inc/shared.php'; ?>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/customers-billing.css" />
    <script src="<?php echo $base_url; ?>/assets/js/customers-billing.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <title><?php echo $customers_subscription_title; ?> - <?php echo $site_name; ?></title>
  </head>
  <body>
    <?php require __DIR__ . '/../inc/header.php'; ?>

    <!-- Loader -->
    <div id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>
    
    <div class="container d-flex justify-content-center">
      <div class="content">
        <h1 class="m-4 text-center fade-in" id="resume-title"></h1>
        <span class="line mx-auto d-flex fade-in"></span>
        <div class="row mt-3 justify-content-center" id="subscriptions"></div>
      </div>
    </div>

    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
