<?php
/*
  Template Name: Empty Template
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
} else {
  // Percorsi per lo sviluppo locale
  $base_url = '/..';  // Cambia con il percorso corretto per lo sviluppo locale
}
?>
<html lang="it-IT" data-lt-installed="true">
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
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >
    <title>Contact - EasySubscribe</title>
  </head>
  <body>
    <?php require __DIR__ . '/../inc/header.php'; ?>

    
    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
