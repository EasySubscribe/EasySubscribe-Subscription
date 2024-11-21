---
icon: file-doc
---

# Documentazione

## Flusso Completo

#### Diagramma di Flusso

<figure><img src=".gitbook/assets/NeverlandKiz Flow.jpg" alt=""><figcaption><p>Diagram draw.io</p></figcaption></figure>

{% file src=".gitbook/assets/NeverlandKiz Flow.drawio" %}
File Draw.io
{% endfile %}

{% file src=".gitbook/assets/NeverlandKiz Flow.pdf" %}
PDF draw.io
{% endfile %}

## Accesso via Email - Sequence Diagram (Sezione 2 e 3)

<figure><img src=".gitbook/assets/Untitled (8).png" alt=""><figcaption><p>SequenceDiagram</p></figcaption></figure>

{% code overflow="wrap" %}
```mermaid
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

## Sottoscrizioni e Generazione QRCode - Sequence Diagram (Sezione 4)

<figure><img src=".gitbook/assets/Accesso Portale (1).png" alt=""><figcaption><p>Sequence Diagram</p></figcaption></figure>

{% code overflow="wrap" %}
```mermaid
title Flusso Utente Pagina Subscription

actor Utente
participant Email
participant NeverlandKiz
participant Stripe

Utente->Email: Accede all'email e clicca sul Link
Email->NeverlandKiz: Apertura WebPage
NeverlandKiz->NeverlandKiz: Decodifica Customer e Session ID
NeverlandKiz->Stripe: GET /v1/apps/secrets/find (SessionID)
note right of NeverlandKiz #FFBF65:--curl location request GET 'https://api.stripe.com/v1/apps/secrets/find'\nheader 'Content-Type: application/x-www-form-urlencoded'\nheader 'Authorization: Bearer ******'\ndata-urlencode 'scope[type]=user'\ndata-urlencode 'scope[user]=customer_id'\ndata-urlencode 'name=SESSION_ID'\ndata-urlencode 'expand[]=payload'

group #red if #white [Sessione non esistente]
Stripe--#red>NeverlandKiz:  Torna errore se la sessione non esiste
NeverlandKiz--#red>Utente:  Mostriamo un'errore
end

NeverlandKiz->NeverlandKiz: Validazione Session ID
group #2f2e7b  if #white [Session ID Valido]
    NeverlandKiz->Stripe: GET /v1/subscriptions?customer
    note right of NeverlandKiz #FFBF65:--curl location GET'https://api.stripe.com/v1/subscriptions\n?customer=customer_id&expand[]=data.customer&status=active'\nheader 'Authorization: Bearer ******'
    Stripe-->NeverlandKiz: Restituisce lista sottoscrizioni con Dati Utente e lista di Prodotti
    loop #2f2e7b #white per ogni prodotto
        NeverlandKiz->NeverlandKiz: Validazione Subscription (Active or Not)
        NeverlandKiz->Stripe: GET /v1/products/{id_prodotto}
        note right of NeverlandKiz #FFBF65:--curl location  GET'https://api.stripe.com/v1/products/product_id'\nheader 'Authorization: Bearer *****'
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

* **QRCode.js**: Un'altra libreria molto usata che supporta diverse opzioni di personalizzazione.

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

***

## Verifica QRCode per accesso

### Scansione QRCode

<figure><img src=".gitbook/assets/Flusso LetturaQRCode.png" alt=""><figcaption><p>Sequence Diagram</p></figcaption></figure>

{% code overflow="wrap" %}
```mermaid
title Verifica della Sottoscrizione tramite QR Code

actor Utente
participant Fotocamera del Controllore
participant NeverlandKiz
participant Stripe

Utente->Fotocamera del Controllore: Mostra QR Code per scansione
Fotocamera del Controllore->Fotocamera del Controllore: Può scansionare via telefono \noppure tramite l'indirizzo dedicato
Fotocamera del Controllore->NeverlandKiz:Apertura URL con JSON \n(customer_name, subscription_id, dati prodotto)
NeverlandKiz->Stripe: GET /v1/subscriptions/{subscription_id} per verificare stato
note right of NeverlandKiz #FFBF65:--curl GET location 'https://api.stripe.com/v1/subscriptions/sub_id?expand[]=customer'\nheader 'Authorization: ••••••'

    Stripe-->NeverlandKiz:Ritorna dettagli sottoscrizione (Se attiva)
group #2f2e7b  if #white [Sottoscrizione attiva]
    NeverlandKiz->NeverlandKiz: Validazione Sottoscrizione
    NeverlandKiz->NeverlandKiz: Mostra conferma di accesso consentito all'evento
else Sottoscrizione non attiva
end

group #red  if #white [Sottoscrizione non attiva]
    Stripe-->NeverlandKiz: Ritorna errore o stato inattivo
    NeverlandKiz->NeverlandKiz: Mostra messaggio di errore o accesso negato
end
```
{% endcode %}

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
```mermaid
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

## Contattami (Sezione 5)

<figure><img src=".gitbook/assets/Untitled (9).png" alt=""><figcaption><p>Contattami SequenceDiagram</p></figcaption></figure>

{% code overflow="wrap" %}
```mermaid
title Flusso della sezione Contattami

actor Utente #e6f1f1
participant NeverlandKiz #e6f1f1
participant SMTP #e6f1f1

Utente -> NeverlandKiz: Inserisce le informazioni di contatto (nome, email, messaggio)
NeverlandKiz -> NeverlandKiz: Valida i dati forniti dall'utente
    NeverlandKiz --#red> Utente: Notifica errore (es. campi mancanti o email non valida)
group #2f2e7b  if #white [Dati validi]
else Dati validi
    NeverlandKiz -> NeverlandKiz: Genera il template email per l'utente
    NeverlandKiz -> SMTP: Invia email di conferma all'utente
    SMTP -> Utente: Riceve email di conferma

    NeverlandKiz -> NeverlandKiz: Genera il template email per Neverland
    NeverlandKiz -> SMTP: Invia email con il messaggio all'admin Neverland
    SMTP -> NeverlandKiz: Riceve email con il messaggio
end

```
{% endcode %}
