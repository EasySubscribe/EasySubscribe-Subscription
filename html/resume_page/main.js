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
              product_image: element.product.images[0],
              //product_image: "../core/img/logo.png",
              subscription_id: element.subscriptions.id,
              customer_name: element.subscriptions.customer.name,
            };
            const subProdText = JSON.stringify(subProd);
            const htmlText = `<div class="col-12 col-md-4 mt-3 fade-in">
          <div class="card text-center" id="card">
            <img
              style="border-radius: 7px;"
              class="mx-auto m-3"
              id="${subProd.product_name + subProd.subscription_id}"
              src="${element.product.images[0]}"
              alt=""
              width="100"
            />
            <canvas id="${
              subProd.subscription_id
            }" style="display:none;"></canvas>
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

        pdf.setFont("helvetica", "bold"); // Imposta il font Helvetica in grassetto
        pdf.text("GENERAL TERMS OF SALE", 85, 90);
        pdf.setFont("helvetica", "normal"); // Ripristina il font normale per il testo successivo
        pdf.setFontSize(8);

        const generalTerms = [
          "To be valid, the e-ticket (electronic ticket) is subject to the terms of sale of Weezevent, and possibly those of the organizer that you agreed to when ordering. REMINDER: This e-ticket is not refundable. Unless otherwise agreed by the organizer, e-ticket is personal, not transferable or exchangeable.",
          "CONTROL: Access to the event is under the control of validity of your e-ticket. This e-ticket is only valid for the location, session, date and hour written on the e-ticket. Past the start time, access to the event is not guaranteed and does not entitle to any refund. We therefore advise you to arrive before the start of the event. To be valid, this e-ticket must be printed on white A4 blank paper, without changing the print size and with a good quality. E-tickets partially printed, dirty, damaged or illegible will be invalid and may be denied by the organizer. The organizer also reserves the right to accept or refuse other media, including electronic (mobile phone, tablet, etc ...).",
          "Each e-ticket has a barcode allowing access to the event to one person. To be valid the payment of this e-ticket must not have been rejected by the credit card owner used for ordering. In this case the barcode is deactivated. At the door, you must be in possession of a valid official ID with photo. Following the inspection, the e-ticket must be retained until the end of the event. In some cases the organizer will issue you a ticket to two strains (whether or not reveal the rental fee).",
          "FRAUD: It is prohibited to reproduce, use, copy, duplicate, counterfeit this e-ticket in any manner whatsoever, under pain of criminal prosecution. Similarly, any order placed with a way to bribe to get an e-ticket will result in criminal prosecution and the invalidity of such e-ticket.",
          "LIABILITY: The purchaser remains responsible for the use made of e-tickets, and if lost, stolen or duplicate a valid e-ticket, only the first person who holds the e-ticket can access the event. Weezevent is not responsible for abnormalities that may occur during the ordering, processing or printing the e-ticket to the extent that it has not caused intentionally or by negligence in case of loss, theft or unauthorized use of e-ticket.",
          "EVENT: The events are and remain the sole responsibility of the organizer. The acquisition of this e-ticket wins if adherence to rules of the place of the event and / or organizer. In case of cancellation or postponement of the event, a refund of the ticket without costs (transport, hotels, etc ...) will be subject to the conditions of the organizer (you can find his email ad",
        ];

        const generalTerms1 = pdf.splitTextToSize(generalTerms[0], 115); // Imposta la larghezza massima
        const generalTerms2 = pdf.splitTextToSize(generalTerms[1], 115); // Imposta la larghezza massima
        const generalTerms3 = pdf.splitTextToSize(generalTerms[2], 115); // Imposta la larghezza massima
        const generalTerms4 = pdf.splitTextToSize(generalTerms[3], 115); // Imposta la larghezza massima
        const generalTerms5 = pdf.splitTextToSize(generalTerms[4], 115); // Imposta la larghezza massima
        const generalTerms6 = pdf.splitTextToSize(generalTerms[5], 115); // Imposta la larghezza massima

        // Aggiungi il testo che va a capo
        pdf.text(generalTerms1, 85, 95);
        pdf.text(generalTerms2, 85, 109);
        pdf.text(generalTerms3, 85, 135);
        pdf.text(generalTerms4, 85, 155);
        pdf.text(generalTerms5, 85, 169);
        pdf.text(generalTerms6, 85, 186);

        pdf.setFontSize(10);

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

        if (product.product_image) {
          const imageUrl = "image-proxy.php?url=" + product.product_image;

          fetch(imageUrl)
            .then((response) => response.blob())
            .then((blob) => {
              const reader = new FileReader();
              reader.readAsDataURL(blob);
              reader.onloadend = () => {
                pdf.addImage(reader.result, "JPEG", 10, 95, 65, 90);
                pdf.save(
                  "QRCode_" +
                    product.product_name +
                    "_expire_" +
                    product.subscription_renew_date +
                    ".pdf"
                );
              };
            })
            .catch((error) =>
              console.error("Errore nel recupero dell'immagine:", error)
            );
          //const img = document.getElementById(
          //  product.product_name + product.subscription_id
          //);
          //const canvas = document.getElementById(product.subscription_id);
          //const ctx = canvas.getContext("2d");
          //console.log(img, canvas, ctx);
          //// Assicurati che l'immagine sia caricata
          //img.onload = function () {
          //  console.log("ONLOAD");
          //  canvas.width = img.width;
          //  canvas.height = img.height;
          //
          //  // Disegna l'immagine sul canvas
          //  ctx.drawImage(img, 0, 0, img.width, img.height);
          //
          //  // Converti il contenuto del canvas in Base64
          //  const base64Image = canvas.toDataURL("image/png");
          //
          //  console.log(base64Image); // Verifica il Base64 nel log
          //
          //  // Una volta che l'immagine è caricata, aggiungila al PDF
          //  pdf.addImage(base64Image, "PNG", 10, 100, 70, 50);
          //  // Dopo aver aggiunto l'immagine, possiamo salvare il PDF
          //  pdf.save(
          //    "QRCode_" +
          //      product.product_name +
          //      "_expire_" +
          //      product.subscription_renew_date +
          //      ".pdf"
          //  );
          //
          //  // Usa il Base64 nel generatore di PDF
          //};
          // Carica l'immagine in formato Data URL
          //fetch(product.product_image)
          //  .then((response) => response.blob())
          //  .then((blob) => {
          //    const img = new Image();
          //    const imgURL = URL.createObjectURL(blob);
          //
          //    img.onload = function () {
          //      // Una volta che l'immagine è caricata, aggiungila al PDF
          //      pdf.addImage(img, "JPG", 10, 100, 70, 50);
          //      // Dopo aver aggiunto l'immagine, possiamo salvare il PDF
          //      pdf.save(
          //        "QRCode_" +
          //          product.product_name +
          //          "_expire_" +
          //          product.subscription_renew_date +
          //          ".pdf"
          //      );
          //    };
          //
          //    img.src = imgURL; // Assegna l'URL dell'immagine
          //  })
          //  .catch((error) => {
          //    console.error("Errore nel caricare l'immagine:", error);
          //  });
        }

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
