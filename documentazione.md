---
icon: file-doc
---

# Documentazione

## Invio Email per ottenimento del QRCode

#### Diagramma di Flusso

<figure><img src=".gitbook/assets/NeverlandKiz Flow.jpg" alt=""><figcaption><p>Diagram draw.io</p></figcaption></figure>

{% file src=".gitbook/assets/NeverlandKiz Flow.drawio" %}
File Draw.io
{% endfile %}

{% file src=".gitbook/assets/NeverlandKiz Flow.pdf" %}
PDF draw.io
{% endfile %}

### Accesso via Email - Sequence Diagram (Sezione 2 e 3)

<figure><img src=".gitbook/assets/Untitled (8).png" alt=""><figcaption><p>SequenceDiagram</p></figcaption></figure>

{% code overflow="wrap" %}
```
title Flusso Utente per Accesso con invio Email

actor Utente #e6f1f1
participant NeverlandKiz #e6f1f1
participant Stripe #e6f1f1
participant SMTP #e6f1f1

Utente->NeverlandKiz: Inserisce l'email e clicca su Accedi

NeverlandKiz->Stripe: GET /v1/customers/search (email)
note right of NeverlandKiz #FFBF65:--curl --location --request GET 'https://api.stripe.com/v1/customers/search' \nheader 'Content-Type: application/x-www-form-urlencoded'\nheader 'Authorization: Bearer *****'\ndata-urlencode 'query=email:"email@gmail.com"'

group #red if #white [Utente non esistente]
Stripe--#red>NeverlandKiz:  Restituisce una risposta OK ma senza dati
NeverlandKiz--#red>Utente:  Mostriamo un'errore
end

group #2f2e7b  else #white [Utente esistente]
NeverlandKiz->NeverlandKiz: Generazione Session ID
    note right of NeverlandKiz #FFBF65: --Crea un identificativo univoco per questa sessione.

        NeverlandKiz->Stripe: POST /v1/apps/secrets
        note right of NeverlandKiz #FFBF65:--curl location 'https://api.stripe.com/v1/apps/secrets'\nheader 'Content-Type: application/x-www-form-urlencoded' \nheader 'Authorization: Bearer *****' \ndata-urlencode 'scope%5Btype%5D=user'\ndata-urlencode 'scope%5Buser%5D=email@gmail.com'\ndata-urlencode 'name=SESSION_ID'\ndata-urlencode 'payload=VWWJ-1729791826381-3D9C13C1'\ndata-urlencode 'expires_at=1730893263'

        NeverlandKiz<--Stripe:Conferma Salvataggio

NeverlandKiz->NeverlandKiz:Preparazione Email con Customer ID e Session ID in Base64

NeverlandKiz->NeverlandKiz:Ottenimento Template HTML da inviare via email.\nInserimento URL Generato precedentemente con Customer e Session

NeverlandKiz->SMTP:Invio email con HTML

SMTP-->Utente:Email ricevuta con link di verifica
end
```
{% endcode %}

### Sottoscrizioni e Generazione QRCode - Sequence Diagram (Sezione 4)

<figure><img src=".gitbook/assets/Untitled (6).png" alt=""><figcaption><p>Sequence Diagram</p></figcaption></figure>

{% code overflow="wrap" %}
```
title Flusso Utente per QR Code con Stripe (Parte 2)

participant Utente
participant Email
participant NeverlandKiz
participant Stripe

Utente->Email: Accede all'email e clicca sul Link
Email->NeverlandKiz: Apertura WebPage
NeverlandKiz->NeverlandKiz: Decodifica Customer e Session ID
NeverlandKiz->Stripe: GET /v1/apps/secrets/find (SessionID)
NeverlandKiz<--Stripe: Torna errore se la sessione non esiste
NeverlandKiz->NeverlandKiz: Validazione Session ID
alt Session ID Valido
    NeverlandKiz->Stripe: GET /v1/subscriptions?customer
    Stripe-->NeverlandKiz: Restituisce lista sottoscrizioni con Dati Utente e lista di Prodotti
    loop per ogni prodotto
        NeverlandKiz->NeverlandKiz: Validazione Subscription (Active or Not)
        NeverlandKiz->Stripe: GET /v1/products/{id_prodotto}
        Stripe-->NeverlandKiz: Restituisce i dati del Prodotto
    end
end
NeverlandKiz->NeverlandKiz: Mapping Dati da reindirizzare sull'HTML
NeverlandKiz->NeverlandKiz: Generazione QRCode al click


```
{% endcode %}

### Generazione QRCode

#### 1. **Librerie JavaScript**

Utilizzo di librerie JavaScript per generare QR code direttamente nel browser. Ecco alcune opzioni:

* **qrcode.js**: Una libreria leggera e semplice per generare QR code.

{% embed url="https://scanapp.org/html5-qrcode-docs/docs/intro" %}

{% code overflow="wrap" %}
```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>

<div id="qrcode"></div>
<script>
    $(document).ready(function() {
        $('#qrcode').qrcode({
            text: 'https://example.com', // Sostituisci con il tuo link
            width: 128,
            height: 128
        });
    });
</script>
```
{% endcode %}

*   **QRCode.js**: Un'altra libreria molto usata che supporta diverse opzioni di personalizzazione.

    {% code overflow="wrap" %}
    ```html
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>

    <div id="qrcode"></div>
    <script>
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "https://example.com", // Sostituisci con il tuo link
            width: 128,
            height: 128,
        });
    </script>
    ```
    {% endcode %}

#### 2. **API di terze parti**

Utilizzo API per generare QR code. Alcuni servizi offrono API REST che puoi chiamare per ottenere un'immagine QR code.

*   **GoQR.me**:

    {% code overflow="wrap" %}
    ```html
    <img src="https://api.qrserver.com/v1/create-qr-code/?data=https://example.com&size=150x150" alt="QR Code" />
    ```
    {% endcode %}

#### 3. **Plugin WordPress**

Plugin per WordPress che possono generare QR code facilmente. Alcuni esempi includono:

* **WP QR Code Generator**: Permette di generare QR code direttamente nei tuoi post e pagine.
* **QR Code Widget**: Aggiunge un widget per visualizzare QR code sul tuo sito.

***

## Verifica QRCode per accesso

### Scansione QRCode

<figure><img src=".gitbook/assets/Untitled (4).png" alt=""><figcaption><p>Sequence Diagram</p></figcaption></figure>

{% code overflow="wrap" %}
```
title Verifica della Sottoscrizione tramite QR Code

participant Utente
participant Fotocamera del Controllore
participant NeverlandKiz
participant Stripe

Utente->Fotocamera del Controllore: Mostra QR Code per scansione
Fotocamera del Controllore->NeverlandKiz: Apertura URL con JSON (customer_id, subscription_id, dati prodotto)
NeverlandKiz->Stripe: GET /v1/subscriptions/{subscription_id} per verificare stato

alt Sottoscrizione attiva
    Stripe-->NeverlandKiz: Ritorna dettagli sottoscrizione (attiva)
    NeverlandKiz->NeverlandKiz: Validazione Sottoscrizione
    NeverlandKiz->NeverlandKiz: Mostra conferma di accesso consentito all'evento
else Sottoscrizione non attiva
    Stripe-->NeverlandKiz: Ritorna errore o stato inattivo
    NeverlandKiz->NeverlandKiz: Mostra messaggio di errore o accesso negato
end

```
{% endcode %}

### Lettura QRCode

#### 1. **Librerie JavaScript per la Lettura**

Utilizzo di librerie JavaScript che possono accedere alla fotocamera del dispositivo e decodificare QR code.

*   **jsQR**: Una libreria JavaScript leggera per la decodifica dei QR code. Ecco un esempio di utilizzo:

    {% code overflow="wrap" %}
    ```html
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.js"></script>

    <video id="video" width="300" height="300"></video>
    <canvas id="canvas" style="display: none;"></canvas>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                video.srcObject = stream;
                video.setAttribute('playsinline', true); // Required to tell iOS we don't want fullscreen
                video.play();
                requestAnimationFrame(tick);
            });

        function tick() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code) {
                    alert('QR Code detected: ' + code.data);
                }
            }
            requestAnimationFrame(tick);
        }
    </script>
    ```
    {% endcode %}

#### 2. **API di Lettura QR Code**

Utilizzo API di terze parti che leggono QR code. Carichi l'immagine del QR code e l'API restituisce i dati.

*   **Zxing**: Un servizio online che fornisce API per la lettura di QR code. Puoi inviare un’immagine del QR code e ricevere i dati.

    {% code overflow="wrap" %}
    ```bash
    curl -X POST -F "file=@path/to/your/qr-code.png" "https://api.qrserver.com/v1/read-qr-code/"
    ```
    {% endcode %}

#### 3. **Plugin WordPress**

Plugin WordPress che possono leggere QR code.

* **QR Code Scanner**: Alcuni plugin offrono funzionalità di scansione QR code direttamente dal browser.

### Come capire se la sottoscrizione è attiva

1. **Status della sottoscrizione (`status`)**:
   * Se `status` è impostato su `"active"`, significa che la sottoscrizione è attiva. In questo caso, l'utente dovrebbe poter accedere all'evento. Gli altri valori possibili per `status` potrebbero indicare che la sottoscrizione è scaduta o in sospeso, come `"canceled"`, `"incomplete"`, `"past_due"`, o `"unpaid"`. Questi stati non garantirebbero l'accesso all'evento.
2. **Data di fine del periodo attuale (`current_period_end`)**:
   * `current_period_end` indica la data di scadenza del periodo corrente di sottoscrizione in formato timestamp. Se la data corrente è inferiore a `current_period_end`, allora la sottoscrizione è ancora valida. Questa verifica può essere utile per eventi a cui è possibile accedere solo finché il periodo di sottoscrizione è attivo.
3. **Metodo di pagamento (`collection_method`)**:
   * Se il valore di `collection_method` è `"charge_automatically"`, la sottoscrizione si rinnova automaticamente e Stripe si occuperà di addebitare il cliente. Se fosse `"send_invoice"`, Stripe invia una fattura al cliente e il pagamento potrebbe non essere automatico. In quest'ultimo caso, sarebbe utile verificare se il pagamento è stato ricevuto per confermare l'accesso.
4. **Billing Thresholds e cancellazioni (`cancel_at_period_end` e `canceled_at`)**:
   * `cancel_at_period_end`: Se questo valore è `true`, la sottoscrizione terminerà alla fine del periodo corrente, quindi anche se attiva ora, l’utente non avrà accesso una volta raggiunto `current_period_end`.
   * `canceled_at`: Se ha un valore diverso da `null`, la sottoscrizione è stata cancellata in precedenza, quindi potrebbe non essere considerata valida.

#### Logica di Validazione per Accesso all’Evento

1. **Verifica che `status` sia `"active"`.**
2. **Assicurati che l'attuale data sia precedente a `current_period_end`.**
3. **Verifica `cancel_at_period_end`** se vuoi evitare di concedere accesso a chi ha impostato la sottoscrizione per terminare.

Se tutte queste condizioni sono soddisfatte, allora l'utente dovrebbe essere autorizzato a partecipare all'evento.

## Cancellazione Sottoscrizione

<figure><img src=".gitbook/assets/Untitled (7).png" alt=""><figcaption></figcaption></figure>

{% code overflow="wrap" %}
```
title Cancellazione della Sottoscrizione

actor Utente
participant NeverlandKiz
participant Stripe

Utente->NeverlandKiz: Seleziona opzione per cancellare sottoscrizione


alt Verifica Pagamenti

    NeverlandKiz->NeverlandKiz: Validazione se la sottoscrizione è pagata \n (interval_count, created con metadata)
    alt Se Valido
        NeverlandKiz->NeverlandKiz: Popup Sicuro di voler cancellare?
        NeverlandKiz->Stripe: DELETE /v1/subscriptions/{subscription_id} per cancellare sottoscrizione
        Stripe-->NeverlandKiz: Conferma cancellazione
        NeverlandKiz->Utente: Notifica cancellazione avvenuta
    else Se non valido
        NeverlandKiz->Utente: Mostra messaggio di errore: "Impossibile cancellare."
    end
else Errore durante il recupero dettagli sottoscrizione
    NeverlandKiz->Utente: Mostra messaggio di errore
end


```
{% endcode %}

