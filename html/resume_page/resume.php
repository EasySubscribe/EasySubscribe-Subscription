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
$customer_id = $data['customer_id'] ?? '';
$session_id = $data['session_id'] ?? '';

if ($customer_id && $session_id) {
    try {
        $log->info('Ricerca secret', ['customer' => $customer_id, 'session' => $session_id]);
        // Effettua la richiesta per trovare il segreto
        $secret = $stripe->apps->secrets->find([
            'scope' => [
                'type' => 'user',
                'user' => $customer_id
            ],
            'name' => 'SESSION_ID',
            'expand' => ['payload']
        ]);

        // Se la sessione non esiste, gestisci l'errore
        if (!$secret || $secret->payload != $session_id) {
            $log->error('Il session ID ' . $secret->payload . ' non corrisponde al session id '. $session_id);
            echo json_encode(['error' => true, 'message' => 'Session ID non valido.']);
            exit;
        }

        $log->info('Secret validato, ricerca subscriptions per customer '. $customer_id);
        
        // Recupera le sottoscrizioni del cliente
        $subscriptions = $stripe->subscriptions->all([
            'customer' => $customer_id,
            'expand' => ['data.customer'],
            'status' => 'active'
        ]);

        $log->info('Subscripion trovate per customer '. $customer_id);
        // Prepara un array per le sottoscrizioni valide
        $validSubscriptions = [];

        foreach ($subscriptions->data as $subscription) {
            if ($subscription->status === 'active') {
                $product = $stripe->products->retrieve($subscription->items->data[0]->price->product);
                $validSubscriptions[] = [
                    'subscriptions' => $subscription,
                    'product' => $product
                ];
            }
        }

        // Output JSON con le sottoscrizioni valide
        echo json_encode([
            'error' => false,
            'data' => $validSubscriptions
        ]);

    } catch (Exception $e) {
        echo json_encode(['error' => true, 'message' => 'Errore durante il recupero delle sottoscrizioni: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Dati non validi.']);
    exit;
}
