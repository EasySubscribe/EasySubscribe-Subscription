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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Stripe\StripeClient;

$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];
$stripe = new StripeClient($stripeSecretKey);

// Logger
$log = new Logger('stripe');
$log->pushHandler(new StreamHandler(LOG_FILE, Logger::DEBUG));

header('Content-Type: application/json');

// Ricezione input
$data = json_decode(file_get_contents('php://input'), true);
$product_ids_string = $data['product_ids_string'] ?? '';
$session_id = $data['session_id'] ?? '';
$email = $data['email'] ?? '';

$log->info('-------------------------------------------------');
$log->info('Manager Page per ' . $email, ['session_id' => $session_id]);

// Validazione input
if (empty($product_ids_string) || empty($session_id) || empty($email)) {
    echo json_encode(['error' => true, 'message' => 'Dati non validi.']);
    exit;
}

$product_ids = explode(',', $product_ids_string);

try {
    // Recupero e validazione della sessione
    $log->info('Validazione della sessione iniziata', ['session_id' => $session_id]);
    $secret = $stripe->apps->secrets->find([
        'scope' => [
            'type' => 'user',
            'user' => $email
        ],
        'name' => 'SESSION_ID',
        'expand' => ['payload']
    ]);

    if (!$secret || $secret->payload !== $session_id) {
        echo json_encode(['error' => true, 'message' => 'Session ID non valido.']);
        exit;
    }

    $log->info('Sessione validata con successo.');

    $log->info('Ricerca sottoscrizioni per products', ['product_ids' => $product_ids]);

    // Recupera tutti i prodotti necessari
    $products = [];
    foreach ($product_ids as $product_id) {
        $products[$product_id] = $stripe->products->retrieve($product_id);
    }

    // Variabili per la paginazione
    $validSubscriptions = [];
    $hasMore = true;
    $nextPage = null;

    // Inizia il ciclo di paginazione
    while ($hasMore) {
        $params = [
            'query' => "status:'active'",
            'limit' => 100, // Limita a 10 sottoscrizioni per richiesta
            'expand' => ['data.customer'],
        ];

        // Aggiungi next_page se esiste
        if ($nextPage) {
            $params['page'] = $nextPage;
            $log->info('Caricamento pagina successiva', ['page' => $nextPage]);
        }

        // Esegui la ricerca delle sottoscrizioni attive
        $subscriptions = $stripe->subscriptions->search($params);

        // Processa le sottoscrizioni
        foreach ($subscriptions->data as $subscription) {
            // Filtra le sottoscrizioni che contengono uno dei prodotti richiesti
            foreach ($subscription->items->data as $item) {
                if (in_array($item->price->product, $product_ids)) {
                    // Aggiungi la sottoscrizione alla lista con il prodotto associato
                    $validSubscriptions[] = [
                        'subscription' => $subscription,
                        'product' => $products[$item->price->product] // Associa il prodotto dalla lista
                    ];
                    break; // Interrompi il ciclo se il prodotto è trovato
                }
            }
        }

        // Verifica se ci sono più pagine
        $hasMore = $subscriptions->has_more;
        if ($hasMore) {
            // Aggiorna next_page per la pagina successiva
            $nextPage = $subscriptions->next_page;
            $log->info('Pagina successiva disponibile', ['next_page' => $nextPage]);
        } else {
            $log->info('Nessuna pagina successiva, fine della paginazione.');
        }
    }
    $log->info('Totale sottoscrizioni elaborate', ['count' => count($validSubscriptions)]);

    // Restituisci la risposta con tutte le sottoscrizioni valide
    echo json_encode(['error' => false, 'data' => $validSubscriptions]);

} catch (Exception $e) {
    $log->error('Errore durante il recupero delle sottoscrizioni', ['message' => $e->getMessage()]);
    echo json_encode(['error' => true, 'message' => 'Errore durante il recupero delle sottoscrizioni.']);
}
