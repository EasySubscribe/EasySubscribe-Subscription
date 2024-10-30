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
