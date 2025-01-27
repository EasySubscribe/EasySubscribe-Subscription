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
$customer_id = $data['customer_id'] ?? '';
$return_url = $data['return_url'] ?? ''; // URL di default

$log->info('-------------------------------------------------');
$log->info('Redirect to Billing per', ['customer' => $customer_id]);

if ($customer_id) {
    try {
        $log->info('Creazione della sessione billing portal', ['customer' => $customer_id]);

        // Crea la sessione per il billing portal
        $billingSession = $stripe->billingPortal->sessions->create([
            'customer' => $customer_id,
            'return_url' => $return_url,
        ]);

        $log->info('Sessione creata con successo', ['url' => $billingSession->url]);

        // Restituisci l'URL della sessione
        echo json_encode([
            'error' => false,
            'data' => $billingSession,
        ]);
    } catch (Exception $e) {
        $log->error('Errore durante la creazione della sessione', ['message' => $e->getMessage()]);
        echo json_encode(['error' => true, 'message' => 'Errore durante la creazione della sessione: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Dati non validi.']);
}
