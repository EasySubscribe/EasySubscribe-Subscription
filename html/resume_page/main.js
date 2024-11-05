document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");

  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const [customer_id, session_id] = decodedData.split(":"); // Supponiamo che siano separati da ':'

    // Rimuovi il parametro 'data' dall'URL
    urlParams.delete("data");
    window.history.replaceState(
      {},
      document.title,
      window.location.pathname + "?" + urlParams.toString()
    );

    // Invia i dati a PHP
    fetch("path/to/resume.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ customer_id, session_id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          console.error(data.message);
          // Gestisci l'errore (es. mostra un messaggio all'utente)
        } else {
          // Gestisci i dati delle sottoscrizioni ricevuti
          console.log(data.subscriptions);
          // Potresti anche generare un QR Code qui se necessario
        }
      })
      .catch((error) => console.error("Errore nella richiesta:", error));
  } else {
    console.error("Nessun dato trovato nell'URL.");
  }
});
