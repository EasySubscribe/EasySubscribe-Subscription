<?php
/**
 * Title: Header
 * Slug: EasySubscribe
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Header columns with logo, title, tagline and links.
 *
 * @package WordPress
 * @subpackage EasySubscribe
 * @since EasySubscribe 1.0
 */

// Verifica se siamo su WordPress
if (defined('ABSPATH')) {
    // Percorsi per WordPress (usa il tema attivo)
    $base_url = get_template_directory_uri();
    $home_url = home_url('/');
    $terms_and_condition = home_url('/terms-and-conditions/');
    $scan = home_url('/scan/');
    $contact = home_url('/contact-me/');
    wp_head();
    $locale = get_locale(); // Recupera la lingua di WordPress
} else {
    // Percorsi per lo sviluppo locale
    $base_url = '/../dist';  // Cambia con il percorso corretto per lo sviluppo locale
    $home_url = '/../dist/index.php';
    $terms_and_condition = '/../dist/templates/template-policy.php';
    $scan = '/../dist/templates/template-read-qrcode.php';
    $contact = '/../dist/templates/template-contact-me.php';
    $locale = 'it_IT'; // Imposta una lingua di fallback per il PHP locale (esempio: en_US)
}
?>
<!-- File personalizzati -->
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/header.css" />

<nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid">
    <?php 
      // Genera un parametro di cache busting (ad esempio, un timestamp)
      //$cache_bust = '?cache_bust=' . time(); 
      $cache_bust = ''; 
    ?>
      <a href="<?php echo $home_url . $cache_bust; ?>" class="navbar-brand ms-2 slide-in-left">
        <img
          src="<?php echo $base_url; ?>/assets/images/easy.png"
          alt="Antonio Rausa"
          style="max-height: 50px"
          class="zoom_simple"
        />
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse slide-in-right" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php /* if (defined('ABSPATH')) : ?>
        <?php
        // Siamo in WordPress, otteniamo i link salvati
        $redirect_links = get_option('redirect_links', []);
        foreach ($redirect_links as $link) {
            // Mostra i link salvati
            echo '<li class="nav-item">';
            echo '<a class="nav-link active fw-bold" href="' . esc_url($link['link']) . '">' . esc_html($link['name']) . '</a>';
            echo '</li>';
        }
        ?>
          <?php else : ?>
          <?php
          // Siamo in locale, usa i link locali
          */
          ?>
          <?php 
            // Genera un parametro di cache busting (ad esempio, un timestamp)
            //$cache_bust = '?cache_bust=' . time();
            $cache_bust = ''; 
          ?>
          <li class="nav-item">
            <a class="nav-link active fw-bold" id="terms_and_condition_text" href="<?php echo $terms_and_condition . $cache_bust; ?>"></a>
          </li>
          <li class="nav-item">
            <a class="nav-link active fw-bold" id="scan_text" href="<?php echo $scan . $cache_bust; ?>"></a>
          </li>
          <li class="nav-item">
            <a class="nav-link active fw-bold" id="contact_text" href="<?php echo $contact . $cache_bust; ?>"></a>
          </li>
          <!--<li class="nav-item">
              <a class="nav-link active fw-bold" id="home_text" href="<?php echo $home_url; ?>"></a>
          </li>-->
          <?php //endif; ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?php echo $base_url; ?>/assets/images/languages/<?php echo $locale; ?>.png" alt="Flags" class="flags">
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#" onclick="setLanguage('it_IT')"><img src="<?php echo $base_url; ?>/assets/images/languages/it_IT.png" alt="Flags" class="flags-menu"> Italiano</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('en_GB')"><img src="<?php echo $base_url; ?>/assets/images/languages/en_GB.png" alt="Flags" class="flags-menu"> Inglese</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('es_ES')"><img src="<?php echo $base_url; ?>/assets/images/languages/es_ES.png" alt="Flags" class="flags-menu"> Spagnolo</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('fr_FR')"><img src="<?php echo $base_url; ?>/assets/images/languages/fr_FR.png" alt="Flags" class="flags-menu"> Francese</a></li>
              <li><a class="dropdown-item" href="#" onclick="setLanguage('de_DE')"><img src="<?php echo $base_url; ?>/assets/images/languages/de_DE.png" alt="Flags" class="flags-menu"> Tedesco</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
</nav>
<script>
    // Mappa dei codici di localizzazione alle lingue
    const localeToLanguageMap = {
    "it_IT": "it",
    "en_GB": "en",
    "es_ES": "es",
    "fr_FR": "fr",
    "de_DE": "de"
  };

  function setLanguage(languageCode) {
    // Salva la lingua nel localStorage
    localStorage.setItem('site_language', languageCode);
  
    // Ottieni la lingua selezionata
    const selectedLanguagePath = localeToLanguageMap[languageCode];
  
    // Recupera la base URL
    const baseUrl = getApiBaseUrl(incType.BASE_URL); // Esempio: http://localhost/wpgiovanni/
  
    // Ottieni l'URL corrente
    const currentUrl = new URL(window.location.href);
  
    // Rimuovi il baseUrl dalla path per lavorare sui segmenti relativi
    let relativePath = currentUrl.pathname.replace(new URL(baseUrl).pathname, "").split('/').filter(Boolean);
  
    // Verifica se il primo segmento corrisponde a una lingua supportata
    const supportedLanguages = Object.values(localeToLanguageMap);
    if (supportedLanguages.includes(relativePath[0])) {
      // Sostituisci la lingua nel percorso
      relativePath[0] = selectedLanguagePath;
    } else {
      // Aggiungi la lingua come primo segmento
      relativePath.unshift(selectedLanguagePath);
    }
  
    // Costruisci il nuovo percorso completo
    const newPath = `${baseUrl}${relativePath.join('/')}${currentUrl.search}`;
  
    // Esegui il redirect
    window.location.replace(newPath);
  }
  
  function checkAndUpdateLanguage() {
    const savedLanguage = localStorage.getItem('site_language'); // Lingua salvata o default
    const savedLanguagePath = localeToLanguageMap[savedLanguage]; // Ottieni il prefisso lingua (es. 'en')
  
    const currentUrl = new URL(window.location.href);
    const pathSegments = currentUrl.pathname.split('/').filter(Boolean); // Ottieni segmenti del path (senza slash iniziale e finale)
  
    // Verifica se il primo segmento del path corrisponde a una lingua supportata
    const supportedLanguages = Object.values(localeToLanguageMap);
    const currentLanguagePath = supportedLanguages.includes(pathSegments[1]) ? pathSegments[1] : null;
  
    if (currentLanguagePath && savedLanguagePath) {
      // Se la lingua nel path è diversa dalla lingua salvata, reindirizza
      if (currentLanguagePath !== savedLanguagePath) {
        pathSegments[1] = savedLanguagePath; // Cambia il prefisso lingua nel path
        const newPath = '/' + pathSegments.join('/') + currentUrl.search; // Ricrea il path completo
        window.location.replace(newPath);
      }
    } else {
      if (savedLanguage && !currentLanguagePath){
        // Se non c'è una lingua nel path, aggiungila
        pathSegments.splice(1, 0, savedLanguagePath); // Aggiungi la lingua dopo il prefisso principale
        const newPath = '/' + pathSegments.join('/') + currentUrl.search; // Ricrea il path completo
        window.location.replace(newPath);
      }
    }
  }
  
  // Chiamare la funzione quando la pagina viene caricata
  checkAndUpdateLanguage();
  /*
  // Recupera la lingua corrente di WordPress
  const wordpressLocale = <?php //echo get_locale(); ?>";

  // Imposta una lingua di fallback predefinita nel caso in cui get_locale() non restituisca un valore valido
  const defaultLanguage = "it_IT"; // Imposta la lingua predefinita

  // Verifica se wordpressLocale è una lingua valida
  const languageToSet = (wordpressLocale && wordpressLocale !== "undefined") ? wordpressLocale : defaultLanguage;

  // Aggiungi un log per vedere da dove viene presa la lingua
  console.log("Lingua di WordPress recuperata: " + wordpressLocale);
  console.log("Lingua utilizzata (con fallback se necessario): " + languageToSet);

  function init() {
    // Controlla se il cookie 'site_language' esiste
    if (document.cookie.indexOf("site_language=") === -1) {
      // Se il cookie non esiste, imposta la lingua di default
      setLanguage(languageToSet);  // Imposta la lingua di default
    }

    if (window.location.search.includes("cache_bust=")) {
      const cleanUrl = window.location.href.split("?")[0];
      window.history.replaceState(null, null, cleanUrl); // Rimuove il parametro cache_bust dall'URL
    }
  }

  function setLanguage(languageCode) {
    // Aggiungi un log per vedere quale lingua viene effettivamente impostata
    console.log("Lingua impostata tramite cookie: " + languageCode);

    // Ottieni la data di scadenza del cookie (opzionale, 7 giorni)
    const date = new Date();
    date.setTime(date.getTime() + 7 * 24 * 60 * 60 * 1000); // 7 giorni
    const expires = "; expires=" + date.toUTCString();

    // Imposta il cookie con attributi SameSite=None e Secure
    document.cookie = "site_language=" + languageCode + "; path=/; Secure; SameSite=Strict";

    // Ricarica la pagina per applicare la nuova lingua
    const cacheBustingUrl = location.href.split("?")[0] + "?cache_bust=" + new Date().getTime();
    window.location.replace(cacheBustingUrl);
  }

  init(); // Chiama la funzione init al caricamento della pagina
  */
</script>