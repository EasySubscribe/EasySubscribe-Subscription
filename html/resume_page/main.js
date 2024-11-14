document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("loader");
  const urlParams = new URLSearchParams(window.location.search);
  let data = urlParams.get("data");

  if (data) localStorage.setItem("data", data);
  else data = localStorage.getItem("data");

  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const [customer_id, session_id] = decodedData.split(":"); // Supponiamo che siano separati da ':'

    // Rimuovi il parametro 'data' dall'URL
    const cleanUrl = window.location.origin + window.location.pathname;
    window.history.replaceState(null, "", cleanUrl);

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
          errorDialog("Errore", "Errore nella richiesta: " + data.message);
        } else {
          // Gestisci i dati delle sottoscrizioni ricevuti
          const subscriptionsSection = document.getElementById("subscriptions");
          data.data.forEach((element) => {
            const subProd = {
              //product_id: element.product.id,
              product_name: element.product.name,
              product_description: element.product.description,
              //product_image: element.product.images[0],
              product_image: "example.jpeg",
              subscription_id: element.subscriptions.id,
              customer_name: element.subscriptions.customer.name,
            };
            const subProdText = JSON.stringify(subProd);
            const htmlText = `<div class="col-12 col-md-4 mt-3 fade-in">
          <div class="card text-center" id="card">
            <img
              style="border-radius: 7px;"
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
        }
        const resumeTitle = document.getElementById("resume-title");
        resumeTitle.textContent =
          "Benvenuto " + data.data[0].subscriptions.customer.name;
        loader.style.display = "none";
      })
      .catch((error) => {
        loader.style.display = "none";
        errorDialog("Errore", "Errore nella richiesta:" + error);
      });
  } else {
    errorDialog("Errore", "Si è verificato un problema, riprova più tardi.");
  }
});

function getQRCode(subProd) {
  const product = JSON.parse(subProd);
  const url = "http://pc-giovanni:3000/html/read_qrcode/index.php?data=".concat(
    btoa(subProd)
  );
  console.log(url);
  qrCodeDialog(product.product_name, null, url).then((result) => {
    if (result.isConfirmed) {
      downloadProductPDF(product);
    }
  });
}

function downloadProductPDF(product) {
  const qrCodeImage = document.querySelector("#qrcode img");

  if (!qrCodeImage) {
    console.error("Immagine QR Code non trovata.");
    return;
  }

  html2canvas(qrCodeImage, { backgroundColor: "#ffffff" })
    .then((canvas) => {
      const qrCodeDataURL = canvas.toDataURL("image/png");

      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF();

      // Imposta il colore di sfondo
      pdf.setFillColor(230, 241, 241); // RGB per #e6f1f1
      pdf.rect(
        0,
        0,
        pdf.internal.pageSize.width,
        pdf.internal.pageSize.height,
        "F"
      ); // Riempie lo sfondo con il colore

      // Aggiungi il logo in alto a sinistra con larghezza maggiore
      const logo = new Image();
      logo.src = "../core/img/logo.png"; // Cambia con l'URL del tuo logo
      logo.onload = function () {
        // Aumenta la larghezza e l'altezza del logo
        pdf.addImage(logo, "PNG", 10, 10, 75, 20); // Larghezza 75, altezza 20

        console.log(qrCodeDataURL);
        // Aggiungi il QR Code (più grande) in alto a destra
        pdf.addImage(qrCodeDataURL, "PNG", 150, 10, 50, 50); // QR code più grande

        pdf.setFontSize(14);
        pdf.setFont("helvetica", "bold"); // Imposta il font Helvetica in grassetto
        pdf.text(`${product.product_name}`, 10, 60);
        pdf.setFont("helvetica", "normal"); // Ripristina il font normale per il testo successivo

        // Aggiungi una descrizione del QR code
        pdf.setFontSize(10);
        pdf.setFont("helvetica", "bold"); // Imposta il font Helvetica in grassetto
        pdf.text(
          "Ciao " +
            product.customer_name +
            ", Accedi all'Evento Mostrando il QR Code!",
          10,
          70
        );
        pdf.setFont("helvetica", "normal"); // Ripristina il font normale per il testo successivo
        pdf.text(
          "Evento/i a cui puoi accedere: " + product.product_description,
          10,
          80
        );
        if (product.product_image) {
          // Esempio di utilizzo
          scaricaEConvertiImmagineInDataUrl(product.product_image)
            .then((dataUrl) => {
              // A questo punto, puoi utilizzare il Data URL come preferisci
              console.log("Data URL dell'immagine:", dataUrl);

              const img = new Image();
              img.src = dataUrl;
              img.onload = function () {
                pdf.addImage(img, "JPG", 10, 100, 70, 50);
              };
            })
            .catch((error) => {
              console.error(
                "Errore nel caricare l'immagine come Data URL:",
                error
              );
            });
        }

        // Testo che va a capo
        const descriptionText = [
          "Per accedere all'evento, mostra il QR Code alla reception all'ingresso. Scansionando il codice, il nostro sistema verificherà il tuo accesso e ti permetterà di entrare senza problemi.",
          "Assicurati di avere il QR Code pronto sul tuo dispositivo mobile o su una stampa cartacea per un ingresso rapido e semplice.",
        ];

        const lines1 = pdf.splitTextToSize(descriptionText[0], 180); // Imposta la larghezza massima
        const lines2 = pdf.splitTextToSize(descriptionText[1], 180); // Imposta la larghezza massima

        // Aggiungi il testo che va a capo
        pdf.text(lines1, 10, pdf.internal.pageSize.height - 30);
        pdf.text(lines2, 10, pdf.internal.pageSize.height - 20);

        // Aggiungi il footer in basso a destra
        const footerText =
          "Per informazioni contattaci su https://www.neverlandkiz.it";
        pdf.setFontSize(8);
        pdf.text(footerText, 10, pdf.internal.pageSize.height - 10);

        // Salva il PDF
        pdf.save(
          "QRCode_" +
            product.product_name +
            "_expire_" +
            product.subscription_renew_date +
            ".pdf"
        );

        simpleDialog(
          "QRCode Scaricato",
          "Ora puoi accedere a ".concat(product.product_name)
        );
      };
    })
    .catch((error) => {
      errorDialog(
        "Errore di rete",
        "Si è verificato un problema, riprova più tardi."
      );
    });
}

async function scaricaEConvertiImmagineInDataUrl(url) {
  try {
    // Scarica l'immagine come un Blob
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Errore nel caricare l'immagine");
    }
    const blob = await response.blob();

    // Crea un Data URL dal Blob
    const dataUrl = await blobToDataUrl(blob);
    return dataUrl; // Restituisce il Data URL
  } catch (error) {
    console.error("Errore durante il download o la conversione:", error);
    throw error; // Propaga l'errore
  }
}

// Funzione per convertire un Blob in Data URL
function blobToDataUrl(blob) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onloadend = () => resolve(reader.result); // La result è il Data URL
    reader.onerror = reject; // In caso di errore nella lettura
    reader.readAsDataURL(blob); // Converte il Blob in Data URL
  });
}

function downloadQRCodeImage() {
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
