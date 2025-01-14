<?php
/*
  Theme Name: EasySubscribe
  Theme URI: https://www.easysubscribe.it
  Author: Giovanni Lamarmora
  Author URI: https://giovannilamarmora.github.io
  Description: Tema Per www.easysubscribe.it
  Version: 1.0
  License: GNU General Public License v2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: EasySubscribe
*/

// Verifica se siamo su WordPress
if (defined('ABSPATH')) {
  // Percorsi per WordPress
  $base_url = get_stylesheet_directory_uri(); // URL del tema figlio
  $locale = get_locale(); // Recupera la lingua di WordPress
  $site_name = esc_html(get_bloginfo('name'));
  $login_text = __('login_text', 'easy_subscribe');
  $welcome_text = __('welcome_text', 'easy_subscribe');
  $email_placeholder = __('email_placeholder', 'easy_subscribe');
  $email_help = __('email_help', 'easy_subscribe');
  $access_button = __('access_button', 'easy_subscribe');
  $organizer_button = __('organizer_button', 'easy_subscribe');
  $get_pass = __('get_pass', 'easy_subscribe');
  $back_button = __('back_button', 'easy_subscribe');
} else {
  // Percorsi per lo sviluppo locale
  function __($string, $domain){
    return $string;
  }
  $base_url = '/../dist';
  $locale = 'it-IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
  $site_name = 'EasySubscribe'; // Nome del sito di fallback per PHP locale
  $login_text = 'Inserisci la tua email per visualizzare o scaricare il Pass.'; // Testo di fallback per PHP locale
  $welcome_text = 'Welcome to <span class="color-header">EasySubscribe</span>';
  $email_placeholder = 'Email address';
  $email_help = 'Inserisci la tua email per accedere al portale.';
  $access_button = 'Accedi';
  $organizer_button = 'Sei un Organizzatore?';
  $get_pass = 'Ottieni il Pass';
  $back_button = 'Back';
}
?>
<!DOCTYPE html>
<html lang="<?= $locale; ?>" data-lt-installed="true">
  <head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, nofollow, noarchive" />
    <link rel="shortcut icon" href="https://www.easysubscribe.it/wp-content/uploads/2025/01/easy.png" />
    <meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
    <?php require __DIR__ . '/inc/shared.php'; ?>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/login.css" />
    <script src="<?php echo $base_url; ?>/assets/js/login.js"></script>
    <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >
    <title><?php echo $access_button; ?> - <?php echo $site_name; ?></title>
  </head>
  <body>
    <?php require __DIR__ . '/inc/header.php'; ?>

    <!-- Loader -->
    <div id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>
    
    <div class="content">
      <section id="login-btn" class="mx-auto">
        <div class="row">
          <div class="col-12 fade-in hidden_mobile" style="margin-bottom: -30px">
            <h1><?php echo $welcome_text; ?></h1>
            <span class="line d-flex mx-auto"></span>
          </div>
          <div class="col-12 fade-in hidden_desktop">
            <h1><?php echo $welcome_text; ?></h1>
          </div>
        </div>
        <div class="col-12 text-center mt-2 hidden_desktop">
          <button id="flipButtonDesktop" type="button" class="btn btn-blue fw-bold ms-2 w-150" target="_blank">
            <?php echo $access_button; ?>
          </button>
        </div>
      </section>

      <div class="container fade-in">
        <div class="card" id="card">
          <div id="front" class="front">
            <div class="row">
              <div class="col col-md-2 hidden_mobile separator" style="align-self: center">
                <div class="col-12 text-center">
                  <button id="flipButton" type="button" class="btn btn-blue fw-bold m-3" target="_blank">
                    <?php echo $access_button; ?>
                  </button>
                </div>
              </div>
              <div class="col-12 col-md-10">
                <stripe-pricing-table pricing-table-id="prctbl_1QO5F5Run27ngB3YRsFdHCSS"
                  publishable-key="pk_test_51Q5WXyRun27ngB3YbfBEWlPiAtjmSa5RFXOaF2HwQl1MD44Df3cFWVxy40LAlg0MrtTSKHDzbpENZDGO3JamWsCC00bm73xoRN">
                </stripe-pricing-table>
                <stripe-pricing-table pricing-table-id="prctbl_1QCmSdRun27ngB3Ya01NPXNY"
                  publishable-key="pk_test_51Q5WXyRun27ngB3YbfBEWlPiAtjmSa5RFXOaF2HwQl1MD44Df3cFWVxy40LAlg0MrtTSKHDzbpENZDGO3JamWsCC00bm73xoRN">
                </stripe-pricing-table>
              </div>
            </div>
          </div>
          <div id="back" class="back">
            <div class="row">
              <div class="col col-md-2 hidden_mobile separator" style="align-self: center">
                <div class="col-12 text-center">
                  <button id="flipButtonBack" type="button" class="btn btn-blue fw-bold m-3" target="_blank">
                    <?php echo $back_button; ?>
                  </button>
                </div>
              </div>
              <div class="col-12 col-md-10">
                <div class="m-3">
                  <h2><?php echo $get_pass; ?></h2>
                  <p><?php echo $login_text; ?></p>
                  <form id="emailForm" onsubmit="handleSubmit(event)">
                    <div class="mb-3 mt-3 mx-auto">
                      <label for="exampleInputEmail1" class="form-label"><?php echo $email_placeholder; ?></label>
                      <input type="email" class="form-control" id="submitEmail" aria-describedby="emailHelp" required />
                      <div id="emailHelp" class="form-text">
                        <?php echo $email_help; ?>
                      </div>
                      <button id="submitEmailBtn" type="submit" class="btn btn-blue fw-bold mt-3 mb-3 float-end w-150"
                        style="margin-left: 20px;" disabled>
                          <?php echo $access_button; ?>
                      </button>
                      <button id="submitOrganizzationBtn" class="btn btn-blue fw-bold mt-3 mb-3 float-end"
                        style="width: 200px" disabled>
                          <?php echo $organizer_button; ?>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/inc/footer.php'; ?>
  </body>
</html>
