<?php
/*
  Template Name: Scan QRCode
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
  $read_qr_page_title = __('read_qr_page_title', 'easy_subscribe');
  $site_name = esc_html(get_bloginfo('name'));
} else {
  // Percorsi per lo sviluppo locale
  $base_url = '/..';  // Cambia con il percorso corretto per lo sviluppo locale
  $locale = 'it-IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
  $read_qr_page_title = "Lettura Biglietti";
  $site_name = 'EasySubscribe'; // Nome del sito di fallback per PHP locale
}
?>
<!DOCTYPE html>
<html lang="<?= $locale; ?>" data-lt-installed="true">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php require __DIR__ . '/../inc/shared.php'; ?>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/qrcode.css" />
    <script src="<?php echo $base_url; ?>/assets/js/qrcode.js"></script>
    <!-- Include the html5-qrcode library -->
    <script
      src="https://unpkg.com/html5-qrcode"
      type="text/javascript"
    ></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >
    <!--<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>-->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <!-- loading ZXingBrowser via UNPKG -->
    <script type="text/javascript" src="https://unpkg.com/@zxing/browser@latest"></script>
    <link rel="shortcut icon" href="https://www.easysubscribe.it/wp-content/uploads/2025/01/easy.png" />
    <title><?php echo $read_qr_page_title; ?> - <?php echo $site_name; ?></title>
  </head>
  <body>
  <?php require __DIR__ . '/../inc/header.php'; ?>

    <!-- Loader -->
    <div id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>

    <div class="content mx-auto text-center">
      <div class="col-12 fade-in" style="margin-bottom: -30px">
        <h1 id="read_qr_title" class="m-4 text-center" style="text-shadow: h-shadow v-shadow blur-radius #111">
        </h1>
        <span class="line d-flex mx-auto"></span>
      </div>
      <div class="card text-center fade-in" id="card">
        <!--<video id="reader" class="mx-auto" style="display: none; border-radius: 20px; height: auto; width: auto; max-height: 300px; max-width: 300px"></video>-->
        <video id="reader" style="display: none;" class="mx-auto"></video>
      </div>
      <div id="result-scan"></div>
      <button id="startScan" type="button" class="btn btn-blue mt-4 fade-in" onclick="startScan()">
      </button>
    </div>

    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
