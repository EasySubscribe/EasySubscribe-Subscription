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
$customer_ids_string = $data['customer_ids_string'] ?? '';
$session_id = $data['session_id'] ?? '';

$log->info('-------------------------------------------------');
$log->info('Customers Page per ' . $customer_ids_string, ['session_id' => $session_id]);

// Separa i customer_id in un array
$customer_ids = array_map('trim', explode(',', $customer_ids_string));

if (empty($customer_ids) || !$session_id) {
    echo json_encode(['error' => true, 'code' => 'ERROR_DATA_400', 'message' => 'Dati non validi.']);
    exit;
}

try {
    $validSubscriptions = [];
    
    // Esegui il controllo della secret solo per il primo customer
    $firstCustomerId = $customer_ids[0];
    $log->info('Ricerca secret', ['customer' => $firstCustomerId, 'session' => $session_id]);

    $secret = $stripe->apps->secrets->find([
        'scope' => [
            'type' => 'user',
            'user' => $firstCustomerId
        ],
        'name' => 'SESSION_ID',
        'expand' => ['payload']
    ]);

    if (!$secret || $secret->payload != $session_id) {
        $log->error('Il session ID ' . $secret->payload . ' non corrisponde al session id '. $session_id);
        echo json_encode(['error' => true, 'message' => 'Session ID non valido.']);
        exit;
    }

    $log->info('Secret validato.');

    // Itera su tutti i customer_ids per ottenere le loro sottoscrizioni
    foreach ($customer_ids as $customer_id) {
        $log->info('Ricerca sottoscrizioni per customer', ['customer' => $customer_id]);

        $subscriptions = $stripe->subscriptions->all([
            'customer' => $customer_id,
            'expand' => ['data.customer'],
            'status' => 'active',
            'limit' => 100
        ]);

        foreach ($subscriptions->data as $subscription) {
            $log->info('Sottoscrizione trovata', ['id' => $subscription->id, 'status' => $subscription->status]);
        
            if ($subscription->status === 'active') {
                $log->info('Sottoscrizione attiva elaborata', ['id' => $subscription->id]);
        
                if (!empty($subscription->items->data)) {
                    $productId = $subscription->items->data[0]->price->product;
                    $product = $stripe->products->retrieve($productId);
                    $validSubscriptions[] = [
                        'subscriptions' => $subscription,
                        'product' => $product
                    ];
                } else {
                    $log->warning('Sottoscrizione senza items', ['id' => $subscription->id]);
                }
            }
        }        
    }

    $log->info('Totale sottoscrizioni elaborate', ['count' => count($validSubscriptions)]);


    // Output JSON con le sottoscrizioni valide
    echo json_encode([
        'error' => false,
        'data' => $validSubscriptions
    ]);

} catch (Exception $e) {
    $log->error('Errore durante il recupero delle sottoscrizioni', ['exception' => $e->getMessage()]);
    echo json_encode(['error' => true, 'code' => 'ERROR_STRIPE_400', 'message' => 'Errore durante il recupero delle sottoscrizioni: ' . $e->getMessage()]);
}
