<?php
require dirname(__DIR__, 3) . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Stripe\StripeClient;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Ramsey\Uuid\Uuid;

// Carica le variabili d'ambiente
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 3)); // Cambia il percorso se necessario
$dotenv->load();

$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];
$smtpHost = $_ENV['SMTP_HOST'];
$smtpUsername = $_ENV['SMTP_USERNAME'];
$smtpPassword = $_ENV['SMTP_PASSWORD'];
$smtpPort = $_ENV['SMTP_PORT'];

// Configura Monolog
$log = new Logger('stripe');
$log->pushHandler(new StreamHandler(dirname(__DIR__, 3) . '/app.log', Logger::DEBUG));

header('Content-Type: application/json');

// Ricevi input
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$redirect_url = $data['redirect_url'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => true, 'message' => 'Indirizzo email non valido.']);
    exit;
}

$stripe = new StripeClient($stripeSecretKey);

try {
    $log->info('Ricerca prodotti in Stripe per email', ['email' => $email]);

    // Query prodotti attivi
    $products = $stripe->products->search([
        'query' => "active:'true' AND -metadata['email_organizzatori']:'null'",
        'expand' => ['data'],
        'limit' => 100
    ]);    

    $log->info('Prodotti trovati, filtraggio per email_organizzatori');

    $matchingProducts = [];
    foreach ($products->data as $product) {
        $organizers = $product->metadata['email_organizzatori'] ?? '';
        $emailsArray = preg_split('/[,\s;]+/', $organizers);
        if (in_array($email, $emailsArray)) {
            $matchingProducts[] = $product->id;
        }
    }

    if (!empty($matchingProducts)) {
        $productIdsString = implode(',', $matchingProducts);
        $sessionId = Uuid::uuid4()->toString();
        $expirationTime = time() + 86400; // Scadenza a 24 ore

        // Creazione della sessione su Stripe con email
        $log->info('Creazione sessione per email', ['email' => $email]);
        $stripe->apps->secrets->create([
            'name' => 'SESSION_ID',
            'payload' => $sessionId,
            'scope' => ['type' => 'user', 'user' => $email],
            'expires_at' => $expirationTime,
        ]);
        $log->info('SESSION_ID salvato su Stripe', ['session_id' => $sessionId, 'email' => $email]);

        $resetUrl = $redirect_url . '/html/manager/index.php?data=' . base64_encode("$productIdsString:$sessionId:$email");

        $templatePath = __DIR__ . '/../../dist/email-templates/email-collaborators.html';
        $emailBody = file_exists($templatePath) ? file_get_contents($templatePath) : '';
        $emailBody = str_replace('{{RESET_URL}}', $resetUrl, $emailBody);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUsername;
            $mail->Password = $smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $smtpPort;

            $mail->setFrom($smtpUsername, 'NeverlandKiz');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Accedi al portale';
            $mail->Body = $emailBody;

            $mail->send();
            $log->info('Email inviata con successo', ['email' => $email]);
            echo json_encode(['error' => false, 'message' => 'Email inviata con successo.', 'product_ids' => $productIdsString, 'session_id' => $sessionId, 'email' => $email]);
        } catch (Exception $e) {
            $log->error('Errore invio email: ' . $mail->ErrorInfo);
            echo json_encode(['error' => true, 'message' => 'Errore invio email: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['error' => true, 'message' => 'Nessun prodotto trovato per l\'email fornita.']);
    }
} catch (Exception $e) {
    $log->error('Errore durante la ricerca: ' . $e->getMessage());
    echo json_encode(['error' => true, 'message' => 'Errore durante la ricerca: ' . $e->getMessage()]);
}
