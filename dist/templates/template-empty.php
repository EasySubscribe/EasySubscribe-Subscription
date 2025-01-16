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
?>
<html lang="<?= get_locale(); ?>" data-lt-installed="true">
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
    <title><?php echo esc_html(get_the_title()); ?> - <?php echo esc_html(get_bloginfo('name')); ?></title>
  </head>
  <body>
    <?php require __DIR__ . '/../inc/header.php'; ?>

    <div id="primary" class="content-area">
      <main id="main" class="site-main">
        <div class="elementor">
          <div class="elementor-inner">
            <div class="elementor-section-wrap">
              <?php
                while (have_posts()) : the_post();
                  the_content();
                endwhile;
              ?>
            </div>
          </div>
        </div>
      </main>
    </div>

    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
