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

function enqueue_translate() {
  // Registra lo script
  wp_enqueue_script(
    'translation_script', // Handle dello script
    get_template_directory_uri() . '/assets/js/translation.js', // URL dello script
    array(), // Dipendenze
    '1.0',
    false // Assicura il caricamento in footer
  );

  // Passa le traduzioni al file JavaScript
  wp_localize_script('translation_script', 'translation', array(
      // Header and Footer
      'terms_and_condition_text' => __('terms_and_condition_text', 'easy_subscribe'),
      'scan_text' => __('scan_text', 'easy_subscribe'),
      'contact_text' => __('contact_text', 'easy_subscribe'),
      'home_text' => __('home_text', 'easy_subscribe'),
      'follow_text' => __('follow_text', 'easy_subscribe'),
      'phone_text'=> __('phone_text', 'easy_subscribe'),
      'contact_text' => __('contact_text', 'easy_subscribe'),

      // Index
      'access_button' => __('access_button', 'easy_subscribe'),
      'back_button'   => __('back_button', 'easy_subscribe'),
      'login_text' => __('login_text', 'easy_subscribe'), 
      'welcome_text' => __('welcome_text', 'easy_subscribe'), 
      'email_placeholder' => __('email_placeholder', 'easy_subscribe'), 
      'email_help' => __('email_help', 'easy_subscribe'), 
      'organizer_button' => __('organizer_button', 'easy_subscribe'), 
      'get_pass' => __('get_pass', 'easy_subscribe'), 

      // Login.js
      'network_error_title' => __('network_error_title', 'easy_subscribe'), 
      'network_error_text' => __('network_error_text', 'easy_subscribe'),
      'invalid_email_title' => __('invalid_email_title', 'easy_subscribe'),
      'invalid_email_text' => __('invalid_email_text', 'easy_subscribe'),
      'sent_email_title' => __('sent_email_title', 'easy_subscribe'),
      'sent_email_text' => __('sent_email_text', 'easy_subscribe'),
      'email_not_found_title' => __('email_not_found_title', 'easy_subscribe'),
      'email_not_found_text' => __('email_not_found_text', 'easy_subscribe'),
      'generic_error_title' => __('generic_error_title', 'easy_subscribe'),
      'generic_error_text' => __('generic_error_text', 'easy_subscribe'),

      // Terms and condition
      'terms_and_condition_title' => __('terms_and_condition_title', 'easy_subscribe'),
      'terms_and_condition_intro' => __('terms_and_condition_intro', 'easy_subscribe'),
      'terms_and_condition_scope' => __('terms_and_condition_scope', 'easy_subscribe'),
      'terms_and_condition_changes' => __('terms_and_condition_changes', 'easy_subscribe'),
      'terms_and_condition_subscription' => __('terms_and_condition_subscription', 'easy_subscribe'),
      'terms_and_condition_subscription_plans' => __('terms_and_condition_subscription_plans', 'easy_subscribe'),
      'terms_and_condition_subscription_duration' => __('terms_and_condition_subscription_duration', 'easy_subscribe'),
      'terms_and_condition_assistance' => __('terms_and_condition_assistance', 'easy_subscribe'),
      'terms_and_condition_responsibility' => __('terms_and_condition_responsibility', 'easy_subscribe'),
      'terms_and_condition_applicable_law' => __('terms_and_condition_applicable_law', 'easy_subscribe'),
      
      // Contact Me Page
      'contact_page_title' => __('contact_page_title', 'easy_subscribe'),
      'contact_me_title' => __('contact_me_title', 'easy_subscribe'),
      'contact_me_subtitle' => __('contact_me_subtitle', 'easy_subscribe'),
      'contact_me_form_name' => __('contact_me_form_name', 'easy_subscribe'),
      'contact_me_form_email' => __('contact_me_form_email', 'easy_subscribe'),
      'contact_me_form_phone' => __('contact_me_form_phone', 'easy_subscribe'),
      'contact_me_form_description' => __('contact_me_form_description', 'easy_subscribe'),
      'contact_me_form_send' => __('contact_me_form_send', 'easy_subscribe'),

      // Contact-Me.js
      'contact_me_sent_email_title' => __('contact_me_sent_email_title', 'easy_subscribe'),
      'contact_me_sent_email_text' => __('contact_me_sent_email_text', 'easy_subscribe'),
    
      // Read QRCode
      'read_qr_title' => __('read_qr_title', 'easy_subscribe'),
      'read_qr_button' => __('read_qr_button', 'easy_subscribe'),

      // Read QRCode.js
      'read_qr_error_title' => __('read_qr_error_title', 'easy_subscribe'),
      'read_qr_error_data_not_valid' => __('read_qr_error_data_not_valid', 'easy_subscribe'),
      'read_qr_error_data' => __('read_qr_error_data', 'easy_subscribe'),
      'read_qr_error_camera' => __('read_qr_error_camera', 'easy_subscribe'),
      'read_qr_error_focus' => __('read_qr_error_focus', 'easy_subscribe'),
      'read_qr_access_allowed' => __('read_qr_access_allowed', 'easy_subscribe'),
      'read_qr_access_denied' => __('read_qr_access_denied', 'easy_subscribe'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_translate');
