<?php
// Gestione dinamica del caricamento di autoload.php
if (file_exists(dirname(__DIR__, 5) . '/wp-load.php') || defined('ABSPATH')) {
    // Siamo su WordPress
    require_once dirname(__DIR__, 4) . '/plugins/easy-subscribe-dependency/vendor/autoload.php';
    define('LOG_FILE', dirname(__DIR__, 4) . '/debug.log');
    $dotenvPath = dirname(__DIR__, 4); // Percorso relativo per WordPress
} else {
    // Siamo in ambiente PHP locale
    require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
    define('LOG_FILE', dirname(__DIR__, 3) . '/app.log');
    $dotenvPath = dirname(__DIR__, 3); // Percorso relativo per ambiente locale
}

// Carica le variabili d'ambiente
$dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
$dotenv->load();

// Accedi alle variabili d'ambiente
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];

// Inizializza Stripe
use Stripe\StripeClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Configura Monolog per il logging
$log = new Logger('stripe');
$log->pushHandler(new StreamHandler(LOG_FILE, Logger::DEBUG));

// Imposta il logger per Stripe
\Stripe\Stripe::setLogger($log);

// Inizializza Stripe con la tua API key
$stripe = new StripeClient($stripeSecretKey);

// Ricevi l'input JSON
$data = json_decode(file_get_contents('php://input'), true);
$subscription_id = $data['subscription_id'] ?? '';

$log->info('-------------------------------------------------');
$log->info('Lettura Biglietto', ['subscription_id' => $subscription_id]);

if (!$subscription_id) {
    $log->error('Subscription mancante');
    echo json_encode(['error' => 'Dati mancanti']);
    exit;
}

try {
    $log->info('Recupero dei dettagli della sottoscrizione ' . $subscription_id);
    // Recupero dei dettagli della sottoscrizione
    $subscription = $stripe->subscriptions->retrieve($subscription_id, [
        'expand' => ['customer', 'items.data.price.product'],
    ]);

    $product = null;

    // Recupera il prodotto dalla sottoscrizione (presupponendo che ci sia almeno un item)
    if (!empty($subscription->items->data)) {
        $product = $subscription->items->data[0]->price->product;
    }

    if ($subscription->status === 'active') {
        $log->info('Sottoscrizione ' . $subscription_id . ' è attiva');
        $response = [
            'status' => 'success',
            'message' => 'Sottoscrizione attiva. Accesso consentito.',
            'subscription_details' => $subscription,
            'product_details' => $product,
        ];
    } else {
        $log->info('Sottoscrizione ' . $subscription_id . ' non è attiva');
        $response = [
            'status' => 'failed',
            'message' => 'Sottoscrizione non attiva. Accesso negato.',
            'subscription_details' => $subscription,
            'product_details' => $product,
        ];
    }
} catch (Exception $e) {
    $log->error('Errore durante la verifica della sottoscrizione: ' . $e->getMessage());
    $response = [
        'status' => 'error',
        'message' => 'Errore durante la verifica della sottoscrizione: ' . $e->getMessage(),
    ];
}

// Invia la risposta JSON a JavaScript
header('Content-Type: application/json');
echo json_encode($response);
