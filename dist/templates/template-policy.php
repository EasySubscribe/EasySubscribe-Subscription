<?php
/*
  Template Name: Terms and Conditions
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
  $terms_and_condition_text = __('terms_and_condition_text', 'easy_subscribe');
  $site_name = esc_html(get_bloginfo('name'));
} else {
  // Percorsi per lo sviluppo locale
  $base_url = '/..';  // Cambia con il percorso corretto per lo sviluppo locale
  $locale = 'it-IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
  $terms_and_condition_text = "Termini e Condizioni";
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
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/policy.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >
    <title><?php echo $terms_and_condition_text; ?> - <?php echo $site_name; ?></title>
  </head>
  <body>
    <?php require __DIR__ . '/../inc/header.php'; ?>

    <div hidden id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>

    <div class="content">
      <section id="login-btn" class="mx-auto">
        <div class="row">
          <div class="col-12 fade-in" style="margin-bottom: -30px">
            <h1 id="terms_and_condition_title"></h1>
            <span class="line d-flex mx-auto"></span>
          </div>
        </div>
      </section>

      <div class="container fade-in">
        <div class="card" id="card">
            <div class="m-4">
                <div id="terms-and-conditions" class="terms">
                  <div id="terms_and_condition_intro"></div>
                  <div id="terms_and_condition_scope"></div>
                  <div id="terms_and_condition_changes"></div>
                  <div id="terms_and_condition_subscription"></div>
                  <div id="terms_and_condition_subscription_plans"></div>
                  <div id="terms_and_condition_subscription_duration"></div>
                  <div id="terms_and_condition_assistance"></div>
                  <div id="terms_and_condition_responsibility"></div>
                  <div id="terms_and_condition_applicable_law"></div>
                </div>
            </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
