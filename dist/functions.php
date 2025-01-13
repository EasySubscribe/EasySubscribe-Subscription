<?php
/* Template Name: Script Shared */

function easy_subscribe_load_textdomain() {
  error_log('Tentativo di caricare le traduzioni...');
  $loaded = load_theme_textdomain('easy_subscribe', get_template_directory() . '/languages');
  if ($loaded) {
      error_log('Traduzioni caricate correttamente!');
  } else {
      error_log('Errore nel caricamento delle traduzioni.');
  }
}
add_action('after_setup_theme', 'easy_subscribe_load_textdomain');