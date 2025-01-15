<?php
/*
  Template Name: Contact-Me
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
  $contact_page_title = __('contact_page_title', 'easy_subscribe');
  $site_name = esc_html(get_bloginfo('name'));
} else {
  // Percorsi per lo sviluppo locale
  $base_url = '/..';  // Cambia con il percorso corretto per lo sviluppo locale
  $locale = 'it-IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
  $contact_page_title = "Contact";
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
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/contact-me.css" />
    <script src="<?php echo $base_url; ?>/assets/js/contact-me.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >
    <title><?php echo $contact_page_title; ?> - <?php echo $site_name; ?></title>
  </head>
  <body>
    <?php require __DIR__ . '/../inc/header.php'; ?>

    <div id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>

    <div class="content">
      <section id="login-btn" class="mx-auto">
        <div class="row">
          <div class="col-12 fade-in" style="margin-bottom: -30px">
            <h1 id="contact_me_title"></h1>
            <span class="line d-flex mx-auto"></span>
          </div>
        </div>
      </section>

      <div class="container fade-in">
        <div class="card" id="card">
          <div class="row m-0">
            <div class="col-12 col-md-5 p-0">
              <div id="contact-preview"></div>
              <!--<img src="../resume_page/example.jpeg" height="500" style="background-repeat: no-repeat; background-size: contain;
                  background-position-x: center;">-->
            </div>
            <div class="col-1 separator hidden_mobile"></div>
            <div class="col-12 col-md-6 my-auto">
              <div class="contact-container">
                <h3 class="fw-bold color-header" id="contact_page_title"></h3>
                <p id="contact_me_subtitle"></p>
                <form id="emailForm" onsubmit="handleSubmit(event)">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" placeholder="Mario">
                    <label for="name" id="contact_me_form_name"></label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" placeholder="name@example.com">
                    <label for="email" id="contact_me_form_email"></label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="phone" placeholder="name@example.com">
                    <label for="phone" id="contact_me_form_phone"></label>
                  </div>
                  <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder="Leave a comment here" id="description" style="height: 100px"></textarea>
                    <label for="description" id="contact_me_form_description"></label>
                  </div>
                  <button
                    id="submitContactEmailBtn"
                    type="submit"
                    class="btn btn-blue fw-bold mt-3 float-end w-150"
                    disabled
                  >
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
