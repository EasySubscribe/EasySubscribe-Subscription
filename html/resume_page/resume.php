<?php
require __DIR__ . '/../../vendor/autoload.php'; // Assicurati che il percorso sia corretto

// Carica le variabili d'ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Cambia il percorso se necessario
$dotenv->load();

// Accedi alle variabili d'ambiente
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];

// Inizializza Stripe
use Stripe\StripeClient;

$stripe = new StripeClient($stripeSecretKey);

// Controlla se il parametro 'data' è presente nell'URL
if (isset($_GET['data'])) {
    $data = base64_decode($_GET['data']); // Decodifica da base64
    $decodedData = json_decode($data, true); // Decodifica JSON

    // Estrai customer_id e session_id
    $customer_id = $decodedData['customer_id'] ?? null;
    $session_id = $decodedData['session_id'] ?? null;

    if ($customer_id && $session_id) {
        try {
            // Verifica la validità della sessione
            $secrets = $stripe->apps->secrets->find($session_id);
            
            // Se la sessione non esiste, gestisci l'errore
            if (!$secrets) {
                echo 'Session ID non valido.';
                exit;
            }

            // Recupera le sottoscrizioni del cliente
            $subscriptions = $stripe->subscriptions->all(['customer' => $customer_id]);

            // Elaborazione delle sottoscrizioni
            foreach ($subscriptions->data as $subscription) {
                // Fai ciò che ti serve con ogni sottoscrizione
                // Puoi anche mappare i dati per l'output successivo
            }

            // Genera QR Code
            // Assicurati di avere la libreria "endroid/qr-code" installata per generare il QR Code
            $qrCode = new \Endroid\QrCode\QrCode('http://example.com?customer_id=' . $customer_id);
            header('Content-Type: image/png');
            echo $qrCode->writeString();

        } catch (Exception $e) {
            echo 'Errore durante il recupero delle sottoscrizioni: ' . $e->getMessage();
        }
    } else {
        echo 'Dati non validi.';
        exit;
    }
} else {
    echo 'Nessun dato trovato.';
    exit;
}
