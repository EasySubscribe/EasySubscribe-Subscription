<?php
require __DIR__ . '/../../vendor/autoload.php'; // Assicurati che il percorso sia corretto

// Carica le variabili d'ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Cambia il percorso se necessario
$dotenv->load();

// Accedi alle variabili d'ambiente
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];

// Inizializza Stripe
use Stripe\StripeClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Configura Monolog per il logging
$log = new Logger('stripe');
$log->pushHandler(new StreamHandler(__DIR__ . '/../../app.log', Logger::DEBUG));

// Imposta il logger per Stripe
\Stripe\Stripe::setLogger($log);

// Inizializza Stripe con la tua API key
$stripe = new StripeClient($stripeSecretKey);

// Ricevi l'input JSON
$data = json_decode(file_get_contents('php://input'), true);
$subscription_id = $data['subscription_id'] ?? '';

if (!$subscription_id) {
    $log->error('Subscription mancante');
    echo json_encode(['error' => 'Dati mancanti']);
    exit;
}

try {
    // Recupero dei dettagli della sottoscrizione
    $subscription = $stripe->subscriptions->retrieve($subscription_id, [
        'expand' => ['customer'],
    ]);

    if ($subscription->status === 'active') {
        $response = [
            'status' => 'success',
            'message' => 'Sottoscrizione attiva. Accesso consentito.',
            'subscription_details' => $subscription
        ];
    } else {
        $response = [
            'status' => 'failed',
            'message' => 'Sottoscrizione non attiva. Accesso negato.',
            'subscription_details' => $subscription
        ];
    }
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Errore durante la verifica della sottoscrizione: ' . $e->getMessage()
    ];
}

// Invia la risposta JSON a JavaScript
header('Content-Type: application/json');
echo json_encode($response);