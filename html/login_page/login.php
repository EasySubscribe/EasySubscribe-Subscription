<?php
require __DIR__ . '/../../vendor/autoload.php';

// Carica le variabili d'ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Cambia il percorso se necessario
$dotenv->load();

// Accedi alle variabili d'ambiente
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];
$smtpHost = $_ENV['SMTP_HOST'];
$smtpUsername = $_ENV['SMTP_USERNAME'];
$smtpPassword = $_ENV['SMTP_PASSWORD'];
$smtpPort = $_ENV['SMTP_PORT'];

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Stripe\StripeClient;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Ramsey\Uuid\Uuid; // Assicurati di avere il pacchetto `ramsey/uuid` installato

// Configura Monolog per il logging
$log = new Logger('stripe');
$log->pushHandler(new StreamHandler(__DIR__ . '/../../app.log', Logger::DEBUG));

// Imposta il logger per Stripe
\Stripe\Stripe::setLogger($log);

header('Content-Type: application/json');

// Ricevi l'input JSON
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => true, 'message' => 'Indirizzo email non valido.']);
    exit;
}

// Inizializza Stripe con la tua API key
$stripe = new StripeClient($stripeSecretKey);

try {
    // Logga l'email prima di effettuare la richiesta
    $log->info('Ricerca email per customer in Stripe', ['email' => $email]);

    // Esegui la ricerca dei clienti in Stripe
    $customers = $stripe->customers->search([
        'query' => 'email:"' . $email . '"',
    ]);

    // Logga la risposta della richiesta
    $log->info('Risultato della ricerca clienti', ['response' => $customers]);

    // Verifica se esistono risultati
    if (count($customers->data) > 0) {
        $customer_id = $customers->data[0]->id; // Prendi il primo cliente dalla lista
        // Genera un SESSION_ID unico
        $sessionId = Uuid::uuid4()->toString();
        $log->info('Generato SESSION_ID', ['session_id' => $sessionId]);

        // Salva il SESSION_ID su Stripe
        $expirationTime = time() + 86400; // Scadenza a 24 ore
        $stripe->apps->secrets->create([
            'name' => 'SESSION_ID',
            'payload' => $sessionId,
            'scope' => ['type' => 'user', 'user' => $customer_id],
            'expires_at' => $expirationTime,
        ]);
        $log->info('SESSION_ID salvato su Stripe', ['session_id' => $sessionId]);

        $log->info('Preparazione email da inviare per customer ' . $customer_id . ' e session id '. $sessionId);

        // Carica il contenuto del file HTML
        $templatePath = __DIR__ . '/../../html/email/index.html';

        if (!file_exists($templatePath)) {
            $log->error('File template per path '. $templatePath . ' non è stato trovato');
        } else $log->info('File template per path '. $templatePath . ' è stato trovato');

        $emailBody = file_get_contents($templatePath);

        // Crea l'URL sostituendo i segnaposto con i valori reali
        $resetUrl = 'http://pc-giovanni:3000/html/resume_page/index.php?data=' . base64_encode("$customer_id:$sessionId");

        // Sostituisci il segnaposto nel body dell'email
        $emailBody = str_replace('{{RESET_URL}}', $resetUrl, $emailBody);

        // Invia l'email
        $mail = new PHPMailer(true);
        try {
            // Configura il server SMTP
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUsername;
            $mail->Password   = $smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $smtpPort;

            // Impostazioni destinatario e mittente
            $mail->setFrom($smtpUsername, 'NeverlandKiz');
            $mail->addAddress($email);

            // Contenuto dell'email
            $mail->isHTML(true);
            $mail->Subject = 'Accedi al portale';
            $mail->Body = $emailBody;

            // Invia l'email
            $mail->send();
            $log->info('Email inviata con successo', ['email' => $email]);
            echo json_encode(['error' => false, 'message' => 'Email inviata con successo.', 'customer_id' => $customer_id, 'session_id' => $sessionId, 'email' => $email]);
        } catch (Exception $e) {
            $log->error('L\'email non è stata inviata: ' . $mail->ErrorInfo);
            echo json_encode(['error' => true, 'message' => 'L\'email non è stata inviata: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['error' => true, 'error_message' => 'Email non presente su Stripe']);
    }
} catch (Exception $e) {
    $log->error('Errore durante la ricerca: ' . $e->getMessage());
    echo json_encode(['error' => 'Errore durante la ricerca: ' . $e->getMessage()]);
}
