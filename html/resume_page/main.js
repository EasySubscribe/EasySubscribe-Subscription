document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");

  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const [customer_id, session_id] = decodedData.split(":"); // Supponiamo che siano separati da ':'
    localStorage.clear();

    // Rimuovi il parametro 'data' dall'URL
    //const cleanUrl = window.location.origin + window.location.pathname;
    //window.history.replaceState(null, "", cleanUrl);

    // Invia i dati a PHP
    fetch("resume.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ customer_id, session_id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          console.log("ERROR", data);
          errorDialog("Errore", "Errore nella richiesta: " + data.message);
        } else {
          // Gestisci i dati delle sottoscrizioni ricevuti
          console.log(data);
          const subscriptionsSection = document.getElementById("subscriptions");
          data.data.forEach((element) => {
            const subProd = {
              product_name: element.product.name,
            };
            const subProdText = JSON.stringify(subProd);
            const htmlText = `<div class="col-12 col-md-3 mt-3 fade-in">
          <div class="card text-center" id="card">
            <img
              class="mx-auto m-3"
              src="${element.product.images[0]}"
              alt=""
              width="100"
            />
            <h4>${subProd.product_name}</h4>
            <p class="fw-light">
              <small style="color: #808080"
                >${element.product.description}</small
              >
            </p>
            <p>Attiva dal: ${new Date(
              element.subscriptions.created * 1000
            ).toLocaleDateString()} <br />Si rinnova il: ${new Date(
              element.subscriptions.current_period_end * 1000
            ).toLocaleDateString()}</p>

            <div class="text-center" style="display: block;">
              <a
                type="button"
                class="btn btn-blue fw-bold mb-3 ms-3 me-3 qrCode"
                target="_blank"
                data-product-name='${subProdText}'
                >Ottieni il QRCode</a
              >
              <a
                type="button"
                class="btn btn-blue text-danger fw-bold mb-3 ms-3 me-3"
                target="_blank"
                onclick="doSubscribeCancel()"
              >
                Cancella Abbonamento
              </a>
            </div>
          </div>
        </div>`;
            subscriptionsSection.innerHTML += htmlText;
          });
          // Potresti anche generare un QR Code qui se necessario
          document.querySelectorAll(".qrCode").forEach((button) => {
            const productName = button.getAttribute("data-product-name");
            button.addEventListener("click", () => getQRCode(productName));
          });

          localStorage.setItem("data", JSON.stringify(data));
        }
      })
      .catch((error) =>
        errorDialog("Errore", "Errore nella richiesta:" + error)
      );
  } else {
    errorDialog("Errore", "Si è verificato un problema, riprova più tardi.");
  }
});

function getQRCode(subProd) {
  const url = "http://localhost:3000/html/read_qrcode/index.html?data=".concat(
    btoa(subProd)
  );
  console.log(url);
  qrCodeDialog("Ecco il qrCode", url).then((result) => {
    if (result.isConfirmed) {
      downloadQRCode();
      simpleDialog("QRCode Scaricato", null);
    }
  });
}

function downloadQRCode() {
  const qrCodeImage = document.querySelector("#qrcode img");
  qrCodeImage.style.padding = "20px !important"; // Aggiungi padding attorno all'immagine
  qrCodeImage.style.backgroundColor = "#ffffff"; // Seleziona l'immagine del QR Code
  if (qrCodeImage) {
    const qrCodeURL = qrCodeImage.src;
    const link = document.createElement("a");
    link.href = qrCodeURL;
    link.download = "QRCode.png"; // Nome del file scaricato
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } else {
    console.error("Immagine QR Code non trovata.");
  }
}
