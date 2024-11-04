---
icon: wordpress
---

# Wordpress

{% embed url="https://www.neverlandkiz.it" %}
Homepage
{% endembed %}

{% embed url="https://www.neverlandkiz.it/tickets-copy/" %}
Tickets
{% endembed %}

## Integrazione Wordpress con HTML

Su WordPress, puoi creare una pagina utilizzando HTML, CSS e JavaScript personalizzati, ma è importante capire come integrarla correttamente nel sistema. Ecco due modi per farlo:

1. **Usare un plugin per pagine personalizzate**:
   * Puoi caricare i tuoi file HTML, CSS e JS tramite il **File Manager** del tuo hosting o usando un plugin come **File Manager** per WordPress.
   * Successivamente, puoi creare una nuova pagina su WordPress e usare un plugin come **Insert HTML Snippet** o **Custom HTML Block** per inserire il tuo codice personalizzato nella pagina.
2. **Creare un template personalizzato per una pagina**:
   * Se hai accesso al file manager di WordPress, puoi creare un template personalizzato. Vai nella cartella del tema attivo (`wp-content/themes/tuo-tema/`) e crea un nuovo file PHP, ad esempio `pagina-personalizzata.php`.
   *   All'inizio del file, aggiungi questa intestazione per indicare a WordPress che si tratta di un template di pagina:

       ```php
       <?php
       /*
       Template Name: Pagina Personalizzata
       */
       ?>
       ```
   * In questo file, puoi inserire il tuo codice HTML, CSS e JS.
   * Poi, nel pannello di WordPress, vai a "Pagine" > "Aggiungi Nuova" e seleziona il tuo template personalizzato nella sezione "Attributi pagina".

In questo modo, WordPress gestirà la pagina utilizzando il file che hai creato con il tuo codice HTML, CSS e JS.

## Header e Footer come Componenti su WordPress

Sì, con WordPress puoi evitare di copiare e incollare codice per header e footer su ogni pagina HTML. Ecco come:

* **Usa i Template di WordPress**: WordPress ha file predefiniti per header (`header.php`) e footer (`footer.php`). Inserendo il codice HTML comune per l’header e il footer all'interno di questi file, WordPress li includerà automaticamente nelle tue pagine.
*   **Includi i Template negli Altri File**: Su WordPress, ogni pagina può caricare l’header e il footer utilizzando le funzioni `get_header()` e `get_footer()`. Queste funzioni includono automaticamente i file `header.php` e `footer.php`.

    Esempio per una pagina PHP personalizzata:

    ```php
    <?php
    /* Template Name: Custom Page */
    get_header();
    ?>

    <!-- Contenuto della pagina personalizzata qui -->

    <?php
    get_footer();
    ```

## Posizione dei File PHP per le Chiamate Stripe

Per il file PHP che gestisce le chiamate all’API di Stripe, hai due opzioni per posizionarlo in WordPress:

1. **Crea un file all'interno della cartella del tema**: Inserisci il file PHP per le chiamate API di Stripe nella cartella del tema WordPress, come `/wp-content/themes/tuo-tema/stripe-calls.php`. Aggiungi un URL di endpoint personalizzato per richiamare il file.
2. **Crea una funzione all'interno del tema o plugin**: Un approccio più pulito e modulare è creare una funzione personalizzata nel file `functions.php` del tema (o, meglio ancora, in un plugin dedicato) per gestire queste chiamate. Usando le API REST di WordPress, puoi creare endpoint che eseguono chiamate API come quelle di Stripe e rispondono ai dati del frontend senza esporre file diretti.

Esempio di endpoint personalizzato in `functions.php`:

```php
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/stripe', [
        'methods' => 'POST',
        'callback' => 'handle_stripe_request',
        'permission_callback' => '__return_true' // Rendi sicuro questo callback per l'accesso
    ]);
});

function handle_stripe_request(WP_REST_Request $request) {
    // Qui va il codice PHP per la chiamata a Stripe
}
```

Questo approccio semplifica anche la gestione della sicurezza, poiché gli endpoint REST possono essere protetti da permessi utente.

## Integrazione di HTML, CSS e JS in WordPress

Per integrare il tuo HTML, CSS e JavaScript:

1. **HTML**: Trasforma i tuoi file HTML principali in template di pagina WordPress, convertendoli in file `.php` da posizionare nella directory del tema. Ad esempio, un file `about.php` per la pagina "Chi siamo". In WordPress, ogni template richiede l'inclusione degli header e footer con `get_header()` e `get_footer()`.
2. **CSS e JavaScript**: Carica i file CSS e JavaScript nel tema WordPress. Inseriscili nella cartella del tema (`/wp-content/themes/tuo-tema/css/` per i CSS e `/js/` per JavaScript), quindi usa `wp_enqueue_style()` e `wp_enqueue_script()` in `functions.php` per includerli in modo dinamico.

Esempio di come registrare e caricare CSS e JavaScript in `functions.php`:

```php
phpCopia codicefunction load_custom_scripts() {
    // Caricamento del CSS
    wp_enqueue_style('custom-style', get_template_directory_uri() . '/css/style.css');
    
    // Caricamento del JavaScript
    wp_enqueue_script('custom-script', get_template_directory_uri() . '/js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'load_custom_scripts');
```

In questo modo, CSS e JavaScript verranno caricati automaticamente su tutte le pagine del sito WordPress senza doverli inserire manualmente in ogni file HTML.

Seguendo questi passaggi, renderai la tua applicazione basata su HTML, CSS e JavaScript perfettamente integrata e funzionante all’interno di WordPress, mantenendo al contempo sicurezza e modularità.
