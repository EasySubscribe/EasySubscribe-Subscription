<?php
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
$log->pushHandler(new StreamHandler(LOG_FILE, Logger::DEBUG));

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

    $log->info('Preparazione email da inviare al customer ' . $email . '.');

    // Carica il contenuto del file HTML
    $templatePath = __DIR__ . '/../../email-templates/email-contact-me.html';
    $templatePathInfo = __DIR__ . '/../../email-templates/email-contact-info.html';

    if (!file_exists($templatePath)) {
        $log->error('File template per path '. $templatePath . ' non è stato trovato');
    } else $log->info('File template per path '. $templatePath . ' è stato trovato');

    if (!file_exists($templatePathInfo)) {
        $log->error('File template per path '. $templatePathInfo . ' non è stato trovato');
    } else $log->info('File template per path '. $templatePathInfo . ' è stato trovato');

    $emailBody = file_get_contents($templatePath);
    $emailBodyInfo = file_get_contents($templatePathInfo);

    // Crea l'URL sostituendo i segnaposto con i valori reali

    // Sostituisci il segnaposto nel body dell'email
    $emailBodyInfo = str_replace('{{NAME}}', $name, $emailBodyInfo);
    $emailBodyInfo = str_replace('{{EMAIL}}', $email, $emailBodyInfo);
    $emailBodyInfo = str_replace('{{PHONENUMBER}}', $phone, $emailBodyInfo);
    $emailBodyInfo = str_replace('{{DESCRIPTION}}', $description, $emailBodyInfo);

    sendEmail($log, 'Contact Me', $email, $emailBody, null);
    sendEmail($log,'Sei stato contattato', $email, $emailBodyInfo, null);

    echo json_encode(['error' => false, 'message' => 'Email inviata con successo.', 'email' => $email]);

function sendEmail($log, $emailSubject, $emailAddress, $emailBodyToSend, $emailAdmin){
    $smtpHost = $_ENV['SMTP_HOST'];
    $smtpUsername = $_ENV['SMTP_USERNAME'];
    $smtpPassword = $_ENV['SMTP_PASSWORD'];
    $smtpPort = $_ENV['SMTP_PORT'];
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
        $mail->setFrom($smtpUsername, 'EasySubscribe');
        $mail->addAddress($emailAdmin ?? $emailAddress);

        // Contenuto dell'email
        $mail->isHTML(true);
        $mail->Subject = $emailSubject;
        $mail->Body = $emailBodyToSend;

        // Invia l'email
        $mail->send();
        $log->info('Email inviata con successo', ['email' => $emailAddress]);
    } catch (Exception $e) {
        $log->error('L\'email non è stata inviata: ' . $mail->ErrorInfo);
        echo json_encode(['error' => true, 'message' => 'L\'email non è stata inviata: ' . $mail->ErrorInfo]);
    }
}
