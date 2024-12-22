<?php
// URL dell'API per ottenere il contenuto di un file da un repository pubblico
$owner = 'NeverlandKiz'; // Sostituisci con il nome utente del repository GitHub
$repo = 'NeverlandKiz-Subscription'; // Sostituisci con il nome del repository
$file_path = 'html/login_page/index.php'; // Sostituisci con il percorso del file all'interno del repository
$branch = 'main'; // Nome del ramo (ad esempio 'main' o 'master')

// Costruisci l'URL dell'API
$api_url = "https://raw.githubusercontent.com/$owner/$repo/refs/heads/$branch/$file_path";

// Esegui una richiesta GET all'API di GitHub
$options = [
    "http" => [
        "header" => "User-Agent: PHP"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);

// Se la risposta è stata ricevuta correttamente, decodifica il contenuto
if ($response !== false) {
    $data = json_decode($response, true);
    echo $response; // Mostra il contenuto del file
    if (isset($data['content'])) {
        // Decodifica il contenuto base64
        $file_content = base64_decode($data['content']);
        echo $file_content; // Mostra il contenuto del file
    } else {
        echo "Errore: il file non è stato trovato o non è accessibile.";
    }
} else {
    echo "Errore nella richiesta API.";
}
?>
