<?php
// URL dell'immagine fornita come parametro (es. ?url=http://example.com/image.png)
if (isset($_GET['url'])) {
    $url = $_GET['url'];

    // Invia una richiesta GET al server remoto
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($httpCode === 200 && $imageData) {
        // Imposta i tipi di contenuto corretti
        header("Content-Type: " . $contentType);
        header("Access-Control-Allow-Origin: *");
        echo $imageData;
    } else {
        http_response_code(404);
        echo "Immagine non trovata.";
    }
} else {
    http_response_code(400);
    echo "URL non specificato.";
}
