<?php
function change_language_based_on_cookie($locale) {
    if (isset($_COOKIE['site_language'])) {
        $locale = $_COOKIE['site_language']; // Prende la lingua dal cookie
    }
    return $locale;
}

add_filter('locale', 'change_language_based_on_cookie');

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

if (!is_admin()) {
  add_filter('show_admin_bar', '__return_false');
}

// Aggiungi la pagina di impostazioni nel menu di amministrazione
function easysubscribe_register_settings_page() {
  add_menu_page(
      'EasySubscribe Settings',
      'EasySubscribe',
      'manage_options',
      'easysubscribe-settings',
      'easysubscribe_settings_page_html',
      'dashicons-admin-generic',
      20
  );
}
add_action('admin_menu', 'easysubscribe_register_settings_page');

function easysubscribe_settings_page_html() {
  // Verifica le autorizzazioni
  if (!current_user_can('manage_options')) {
      return;
  }

  // Salva i dati se inviati
  if (isset($_POST['easysubscribe_save_settings'])) {
      $stripe_tables = [];
      //$redirect_links = [];

      // Gestione delle tabelle Stripe
      if (isset($_POST['stripe_pricing_table_ids']) && is_array($_POST['stripe_pricing_table_ids']) && isset($_POST['stripe_publishable_keys']) && is_array($_POST['stripe_publishable_keys'])) {
          foreach ($_POST['stripe_pricing_table_ids'] as $key => $table_id) {
              if (isset($_POST['stripe_publishable_keys'][$key])) {
                  $stripe_tables[] = [
                      'pricing-table-id' => sanitize_text_field($table_id),
                      'publishable-key' => sanitize_text_field($_POST['stripe_publishable_keys'][$key])
                  ];
              }
          }
      }

      // Verifica e salva i dati
      /*if (isset($_POST['easysubscribe_save_settings'])) {
        $redirect_links = [];
        // Verifica che redirect_links sia un array
            if (isset($_POST['redirect_links']) && is_array($_POST['redirect_links'])) {
                foreach ($_POST['redirect_links'] as $link) {
                    // Verifica che ogni elemento di link sia un array
                    if (isset($link['name']) && isset($link['link'])) {
                        $redirect_links[] = [
                            'name' => sanitize_text_field($link['name']),
                            'link' => esc_url($link['link'])
                        ];
                    }
                }
            }
      
            // Salva i dati solo se sono validi
            if (!empty($redirect_links)) {
                update_option('redirect_links', $redirect_links);
                echo '<div class="updated"><p>Impostazioni salvate!</p></div>';
            }
        }*/

      update_option('stripe_pricing_tables', $stripe_tables);
      //update_option('redirect_links', $redirect_links);
      echo '<div class="updated"><p>Impostazioni salvate!</p></div>';
  }

  // Recupera i valori salvati
  $stripe_tables = get_option('stripe_pricing_tables', []);
  //$redirect_links = get_option('redirect_links', []);
  //$saved_links = isset($redirect_links) ? $redirect_links : [];
  
  // Aggiungi una verifica per assicurarti che $stripe_tables sia un array
  if (!is_array($stripe_tables)) {
      $stripe_tables = [];
  }

  //if (!is_array($redirect_links)) {
  //  $redirect_links = [];
  //}
  ?>
  <div class="wrap">
      <h1>Impostazioni EasySubscribe</h1>
      <form method="post">
      <h3 scope="row">Codici delle tabelle di prezzo</h3>
          <table class="form-table">
              <tr valign="top">
                  <td>
                      <div id="pricing-tables">
                          <?php
                          // Visualizza ogni tabella salvata
                          foreach ($stripe_tables as $index => $table) {
                              ?>
                              <div class="pricing-table" style="margin-bottom: 20px;">
                                  <label for="stripe_pricing_table_ids_<?php echo $index; ?>">ID Tabella:</label>
                                  <?php if (is_array($table) && isset($table['pricing-table-id'])): ?>
                                      <input type="text" name="stripe_pricing_table_ids[]" value="<?php echo esc_attr($table['pricing-table-id']); ?>" class="form-control" />
                                  <?php else: ?>
                                      <!-- Gestisci il caso in cui $table non è un array -->
                                      <input type="text" name="stripe_pricing_table_ids[]" value="" class="form-control" />
                                  <?php endif; ?>
                                  <label for="stripe_publishable_keys_<?php echo $index; ?>">Chiave Pubblicabile:</label>
                                  <?php if (is_array($table) && isset($table['publishable-key'])): ?>
                                      <input type="text" name="stripe_publishable_keys[]" value="<?php echo esc_attr($table['publishable-key']); ?>" class="form-control" />
                                  <?php else: ?>
                                      <!-- Gestisci il caso in cui $table non è un array -->
                                      <input type="text" name="stripe_publishable_keys[]" value="" class="form-control" />
                                  <?php endif; ?>
                                  <button type="button" onclick="removePricingTable(<?php echo $index; ?>)" class="btn-blue">Rimuovi Tabella</button>
                              </div>
                              <?php
                          }
                          ?>
                      </div>
                      <button type="button" onclick="addPricingTable()" class="btn-blue">Aggiungi Tabella</button>
                  </td>
              </tr>
          </table>

            <!--<h3>Link di Redirect</h3>
            <table class="form-table">
                <tr valign="top">
                    <td>
                        <div id="redirect-links">-->
                            <?php
                            // Ottieni le lingue disponibili
                            /*$available_languages = get_available_languages(); // Restituisce un array con le lingue disponibili in WordPress

                            // Se non ci sono lingue disponibili, imposta le lingue di default
                            if (empty($available_languages)) {
                                $available_languages = ['it', 'en']; // Imposta le lingue di default (esempio)
                            }
                        
                            // Visualizza ogni link salvato
                            foreach ($redirect_links as $index => $link) {
                                ?>
                                <div class="redirect-link" style="margin-bottom: 20px;">
                                    <!-- Input per il nome, che cambia per ogni lingua -->
                                    <label for="redirect_link_name_<?php echo $index; ?>">Nome dell'elemento:</label>
                                    <input type="text" id="redirect_link_name_<?php echo $index; ?>" 
                                           name="redirect_links[<?php echo $index; ?>][name]" 
                                           value="<?php echo esc_attr($link['name']); ?>" 
                                           class="form-control" />

                                    <!-- Selettore lingua per ogni input -->
                                    <label for="redirect_link_language_<?php echo $index; ?>">Lingua:</label>
                                    <select name="redirect_links[<?php echo $index; ?>][language]" 
                                            class="form-control">
                                        <?php
                                        // Cicla le lingue disponibili
                                        foreach ($available_languages as $language) {
                                            $selected = (isset($link['language']) && $link['language'] === $language) ? 'selected' : '';
                                            echo "<option value=\"$language\" $selected>" . ucfirst($language) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    
                                    <!-- Link di redirect, che rimane invariato -->
                                    <label for="redirect_link_<?php echo $index; ?>">Link di Redirect:</label>
                                    <input type="text" name="redirect_links[<?php echo $index; ?>][link]" 
                                           value="<?php echo esc_url($link['link']); ?>" 
                                           class="form-control" />
                                    
                                    <button type="button" onclick="removeRedirectLink(<?php echo $index; ?>)" class="btn-blue">Rimuovi Link</button>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <button type="button" onclick="addRedirectLink()" class="btn-blue">Aggiungi Link</button>
                    </td>
                </tr>
            </table>*/
            // TODO: Rimuovi questo ? > se abiliti il menù personalizzato" 
            ?> 

          <?php submit_button('Salva Impostazioni', 'primary', 'easysubscribe_save_settings'); ?>
      </form>
  </div>

  <style>
      :root {
        --header-color: #084b83;
        --body-color: #f0f6f6;
        --card-color: #e6f1f1;
        --btn-color: #d0e6e6;
        --btn-touch-color: #cadfdf;
      }
      /** Inputs */
      .form-floating .form-control:focus:placeholder-shown ~ label {
          border-color: rgba(240, 246, 246, 0.25) !important;
      }

      .form-control {
          height: 50px;
          width: 500px;
          border-radius: 10px !important;
          background-color: #f0f6f6 !important;
          border: 1px solid #d0e6e6 !important;
          transition: border-color 0.3s, background-color 0.3s;
          outline: none !important;
      }

      .form-control:focus {
          border-color: #d0e6e6 !important;
          background-color: #f0f6f6 !important;
          outline: none !important;
          box-shadow: 0 0 0 0.2rem rgba(208, 230, 230, 0.5) !important;
      }

      .form-control:active {
          border-color: #d0e6e6 !important;
          background-color: #f0f6f6 !important;
      }
      /** END Inputs */

      .btn-blue {
          width: 150px;
          min-height: 40px;
          --bs-btn-padding-y: 0.5rem !important;
          border-radius: 5px;
          background-color: var(--btn-color) !important;
          border: none !important;
          outline: none !important;
          transition: background-color 0.3s, box-shadow 0.3s;
      }

      .btn-blue:hover {
          background-color: #cadfdf !important;
      }

      .btn-blue:active,
      .btn-blue.active {
          background-color: var(--btn-touch-color) !important;
          outline: 3px solid var(--card-color) !important;
          box-shadow: 0 0 0 6px var(--btn-touch-color), 0 0 8px rgba(0, 0, 0, 0.1);
      }

      .pricing-table {
          margin-bottom: 20px;
      }

      #pricing-tables {
          margin-bottom: 20px;
      }
  </style>

  <script>
      function addPricingTable() {
          const container = document.getElementById('pricing-tables');
          const index = container.children.length;
          const newRow = document.createElement('div');
          newRow.classList.add('pricing-table');
          newRow.style.marginBottom = '20px';
          newRow.innerHTML = `
              <label for="stripe_pricing_table_ids_${index}">ID Tabella:</label>
              <input type="text" name="stripe_pricing_table_ids[]" class="form-control" />
              <label for="stripe_publishable_keys_${index}">Chiave Pubblicabile:</label>
              <input type="text" name="stripe_publishable_keys[]" class="form-control" />
              <button type="button" onclick="removePricingTable(${index})" class="btn-blue">Rimuovi Tabella</button>
          `;
          container.appendChild(newRow);
      }

      function removePricingTable(index) {
          const container = document.getElementById('pricing-tables');
          container.removeChild(container.children[index]);
      }

        /*function addRedirectLink() {
            const container = document.getElementById('redirect-links');
            const index = container.children.length;

            const newRow = document.createElement('div');
            newRow.classList.add('redirect-link');
            newRow.style.marginBottom = '20px';

            newRow.innerHTML = `
                <!-- Input per il nome, che cambia per ogni lingua -->
                <label for="redirect_link_name_${index}">Nome dell'elemento:</label>
                <input type="text" id="redirect_link_name_${index}" 
                       name="redirect_links[${index}][name]" 
                       class="form-control" />

                <!-- Selettore lingua per ogni input -->
                <label for="redirect_link_language_${index}">Lingua:</label>
                <select name="redirect_links[${index}][language]" class="form-control">
                    <?php foreach ($available_languages as $language) { ?>
                        <option value="<?php echo $language; ?>"><?php echo ucfirst($language); ?></option>
                    <?php } ?>
                </select>
                    
                <!-- Link di redirect, che rimane invariato -->
                <label for="redirect_link_${index}">Link di Redirect:</label>
                <input type="text" name="redirect_links[${index}][link]" 
                       class="form-control" />
                    
                <button type="button" onclick="removeRedirectLink(${index})" class="btn-blue">Rimuovi Link</button>
            `;
                    
            container.appendChild(newRow);
        }


      // Rimuovi un link di redirect
      function removeRedirectLink(index) {
          const container = document.getElementById('redirect-links');
          container.removeChild(container.children[index]);
      }*/
  </script>
  <?php
}

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

      // Customer Billing.js
      'customers_generic_error_title' => __('customers_generic_error_title', 'easy_subscribe'),
      'customers_generic_error_text' => __('customers_generic_error_text', 'easy_subscribe'),
      'customers_request_error_text' => __('customers_request_error_text', 'easy_subscribe'),
      'customers_subscription_active_from' => __('customers_subscription_active_from', 'easy_subscribe'),
      'customers_subscription_renew_on' => __('customers_subscription_renew_on', 'easy_subscribe'),
      'customers_handle_payments_title' => __('customers_handle_payments_title', 'easy_subscribe'),
      'customers_get_qr_code_title' => __('customers_get_qr_code_title', 'easy_subscribe'),
      'customers_download_code' => __('customers_download_code', 'easy_subscribe'),
      'customers_close_download_code' => __('customers_close_download_code', 'easy_subscribe'),
      'customers_cancel_subscription_title' => __('customers_cancel_subscription_title', 'easy_subscribe'),
      'customers_cancel_subscription_confirm_title' => __('customers_cancel_subscription_confirm_title', 'easy_subscribe'),
      'customers_cancel_subscription_confirm_subtitle' => __('customers_cancel_subscription_confirm_subtitle', 'easy_subscribe'),
      'customers_cancel_subscription_confirm_button' => __('customers_cancel_subscription_confirm_button', 'easy_subscribe'),
      'customers_cancel_subscription_deny_button' => __('customers_cancel_subscription_deny_button', 'easy_subscribe'),
      'customers_cancel_subscription_reject_title' => __('customers_cancel_subscription_reject_title', 'easy_subscribe'),
      'customers_cancel_subscription_reject_subtitle' => __('customers_cancel_subscription_reject_subtitle', 'easy_subscribe'),
      'customers_page_title' => __('customers_page_title', 'easy_subscribe'),
      'customers_cancel_subscription_error_policy' => __('customers_cancel_subscription_error_policy', 'easy_subscribe'),
      'customers_cancel_subscription_error_generic' => __('customers_cancel_subscription_error_generic', 'easy_subscribe'),
      'customers_cancel_subscription_error_unknown' => __('customers_cancel_subscription_error_unknown', 'easy_subscribe'),
      'customers_cancel_subscription_success_title' => __('customers_cancel_subscription_success_title', 'easy_subscribe'),
      'customers_cancel_subscription_success_subtitle' => __('customers_cancel_subscription_success_subtitle', 'easy_subscribe'),
      'customers_get_qr_code_download_error' => __('customers_get_qr_code_download_error', 'easy_subscribe'),
      'customers_get_qr_code_download_success_title' => __('customers_get_qr_code_download_success_title', 'easy_subscribe'),
      'customers_get_qr_code_download_success_subtitle' => __('customers_get_qr_code_download_success_subtitle', 'easy_subscribe'),
      'customers_pdf_welcome_text' => __('customers_pdf_welcome_text', 'easy_subscribe'),
      'customers_pdf_event_text' => __('customers_pdf_event_text', 'easy_subscribe'),
      'customers_pdf_terms_title' => __('customers_pdf_terms_title', 'easy_subscribe'),
      'customers_pdf_terms_part_1' => __('customers_pdf_terms_part_1', 'easy_subscribe'),
      'customers_pdf_terms_part_2' => __('customers_pdf_terms_part_2', 'easy_subscribe'),
      'customers_pdf_terms_part_3' => __('customers_pdf_terms_part_3', 'easy_subscribe'),
      'customers_pdf_terms_part_4' => __('customers_pdf_terms_part_4', 'easy_subscribe'),
      'customers_pdf_terms_part_5' => __('customers_pdf_terms_part_5', 'easy_subscribe'),
      'customers_pdf_terms_part_6' => __('customers_pdf_terms_part_6', 'easy_subscribe'),
      'customers_pdf_event_access_part_1' => __('customers_pdf_event_access_part_1', 'easy_subscribe'),
      'customers_pdf_event_access_part_2' => __('customers_pdf_event_access_part_2', 'easy_subscribe'),
      'customers_pdf_event_access_part_3' => __('customers_pdf_event_access_part_3', 'easy_subscribe'),

      // Manager.js
      'manager_generic_error_title' => __('manager_generic_error_title', 'easy_subscribe'),
      'manager_generic_error_text' => __('manager_generic_error_text', 'easy_subscribe'),
      'manager_request_error_text' => __('manager_request_error_text', 'easy_subscribe'),
      'manager_subscription_error_text' => __('manager_subscription_error_text', 'easy_subscribe'),
      'manager_table_element_1' => __('manager_table_element_1', 'easy_subscribe'),
      'manager_table_element_2' => __('manager_table_element_2', 'easy_subscribe'),
      'manager_table_element_3' => __('manager_table_element_3', 'easy_subscribe'),
      'manager_table_element_4' => __('manager_table_element_4', 'easy_subscribe'),
      'manager_table_copy' => __('manager_table_copy', 'easy_subscribe'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_translate');
