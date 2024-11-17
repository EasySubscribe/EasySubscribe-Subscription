<?php
require __DIR__ . '/../../vendor/autoload.php';

// Carica le variabili d'ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Cambia il percorso se necessario
$dotenv->load();

// Accedi alle variabili d'ambiente
$smtpHost = $_ENV['SMTP_HOST'];
$smtpUsername = $_ENV['SMTP_USERNAME'];
$smtpPassword = $_ENV['SMTP_PASSWORD'];
$smtpPort = $_ENV['SMTP_PORT'];

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Ramsey\Uuid\Uuid; // Assicurati di avere il pacchetto `ramsey/uuid` installato

// Configura Monolog per il logging
$log = new Logger('stripe');
$log->pushHandler(new StreamHandler(__DIR__ . '/../../app.log', Logger::DEBUG));

header('Content-Type: application/json');

// Ricevi l'input JSON
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$description = $data['description'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => true, 'message' => 'Indirizzo email non valido.']);
    exit;
}

try {
    $log->info('Preparazione email da inviare al customer ' . $email . '.');

    // Carica il contenuto del file HTML
    $templatePath = __DIR__ . '/../../html/email/index.html';

    if (!file_exists($templatePath)) {
        $log->error('File template per path '. $templatePath . ' non è stato trovato');
    } else $log->info('File template per path '. $templatePath . ' è stato trovato');

    $emailBody = file_get_contents($templatePath);

    // Crea l'URL sostituendo i segnaposto con i valori reali
    $resetUrl = 'http://pc-giovanni:3000/html/resume_page/index.php?data=';

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
        echo json_encode(['error' => false, 'message' => 'Email inviata con successo.', 'email' => $email]);
    } catch (Exception $e) {
        $log->error('L\'email non è stata inviata: ' . $mail->ErrorInfo);
        echo json_encode(['error' => true, 'message' => 'L\'email non è stata inviata: ' . $mail->ErrorInfo]);
    }
} catch (Exception $e) {
    $log->error('Errore durante la ricerca: ' . $e->getMessage());
    echo json_encode(['error' => 'Errore durante la ricerca: ' . $e->getMessage()]);
}
