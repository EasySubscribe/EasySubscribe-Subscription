<?php
/*
  Template Name: Manager
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
  $manager_page_title = __('manager_page_title', 'easy_subscribe');
  $site_name = esc_html(get_bloginfo('name'));
  $manager_title = __('manager_title', 'easy_subscribe');
  $manager_client_text = __('manager_client_text', 'easy_subscribe');
  $manager_profit_text = __('manager_profit_text', 'easy_subscribe');
  $manager_no_user_text = __('manager_no_user_text', 'easy_subscribe');
} else {
  // Percorsi per lo sviluppo locale
  $base_url = '/..';  // Cambia con il percorso corretto per lo sviluppo locale
  $locale = 'it-IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
  $manager_page_title = "Manager";
  $site_name = 'EasySubscribe'; // Nome del sito di fallback per PHP locale
  $manager_title = "Gestione <span class='color-header'>Utenti</span>";
  $manager_client_text = "Clienti Attivi";
  $manager_profit_text = "Profitti del Mese";
  $manager_no_user_text = "Nessun Utente Attivo";
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
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/manager.css" />
    <script src="<?php echo $base_url; ?>/assets/js/manager.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>-->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>
    <title><?php echo $manager_page_title; ?> - <?php echo $site_name; ?></title>
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
            <h1><?php echo $manager_title; ?></h1>
            <span class="line d-flex mx-auto"></span>
          </div>
        </div>
      </section>

      <div class="container fade-in">
      <div class="row">
        <div class="col-12 col-md-6">
            <div class="card ms-5 me-5 mb-5 zoom" id="card">
              <div class="card-body d-flex flex-column align-items-center justify-content-center fade-in">
                <!-- Icona -->
                <!--<i class="fa-solid fa-people fa-3x mb-3 mt-2 text-primary bounce"></i>-->
                <div class="d-inline">
                  <i class="fa-solid fa-person fa-3x mb-3 mt-2 text-primary bounce"></i>
                  <i class="fa-solid fa-person-dress fa-3x mb-3 mt-2 text-primary bounce"></i>
                </div>
                

                <!-- Titolo -->
                <h5 class="card-title mb-3"><?php echo $manager_client_text; ?></h5>

                <!-- Numero -->
                <div class="display-2 text-success" id="active-count">0</div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="card ms-5 me-5 mb-5 zoom" id="card">
              <div class="card-body d-flex flex-column align-items-center justify-content-center fade-in">
                <!-- Icona -->
                <i class="fa-solid fa-coins fa-3x mb-3 mt-2 text-warning bounce"></i>

                <!-- Titolo -->
                <h5 class="card-title mb-3"><?php echo $manager_profit_text; ?></h5>

                <!-- Numero -->
                <div class="display-2 text-success" id="profit-amount">€ 0.00</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card" id="card">
            <div class="m-4 table-responsive">
                <div id="subscription-container" class="text-center"><?php echo $manager_no_user_text; ?></div>
            </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
