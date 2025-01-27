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

// Accedi alla chiave Stripe
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
$invoice_now = $data['invoice_now'] ?? true; // Imposta `true` come valore di default

$log->info('-------------------------------------------------');
$log->info('Cancella sottoscrizione con id', ['subscription' => $subscription_id]);

if ($subscription_id) {
    try {
        $log->info('Inizio cancellazione sottoscrizione', ['subscription_id' => $subscription_id]);

        // Cancella la sottoscrizione
        $deletedSubscription = $stripe->subscriptions->cancel($subscription_id, [
            'invoice_now' => $invoice_now,
        ]);

        $log->info('Sottoscrizione cancellata con successo', ['subscription' => $deletedSubscription]);

        // Restituisci la risposta di successo
        echo json_encode([
            'error' => false,
            'data' => $deletedSubscription,
        ]);
    } catch (Exception $e) {
        $log->error('Errore durante la cancellazione della sottoscrizione', ['message' => $e->getMessage()]);
        echo json_encode(['error' => true, 'message' => 'Errore durante la cancellazione della sottoscrizione: ' . $e->getMessage()]);
    }
} else {
    $log->warning('Dati non validi forniti per la cancellazione della sottoscrizione');
    echo json_encode(['error' => true, 'message' => 'Dati non validi.']);
}
