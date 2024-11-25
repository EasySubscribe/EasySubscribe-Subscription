document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("loader");
  const urlParams = new URLSearchParams(window.location.search);
  let data = urlParams.get("data");

  if (data) localStorage.setItem("data", data);
  else data = localStorage.getItem("data");

  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const [customer_ids_string, session_id] = decodedData.split(":"); // Supponiamo che siano separati da ':'
    const subscriptionsSection = document.getElementById("subscriptions");

    // Rimuovi il parametro 'data' dall'URL
    const cleanUrl = window.location.origin + window.location.pathname;
    window.history.replaceState(null, "", cleanUrl);

    let customer_name = "";

    // Invia i dati a PHP
    fetch("resume.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ customer_ids_string, session_id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          errorDialog("Errore", "Errore nella richiesta: " + data.message);
        } else {
          // Gestisci i dati delle sottoscrizioni ricevuti
          if (data.data) {
            data.data.forEach((element) => {
              if (element.subscriptions.customer.name)
                customer_name = element.subscriptions.customer.name;
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
              //const subProdText = subProd.subscription_id;
              const htmlText = `<div class="col-12 col-md-4 mt-3 fade-in">
      <div class="card text-center" id="card">
        <img
          style="border-radius: 7px;"
          class="mx-auto m-3 mt-4"
          id="${element.product.name + element.subscriptions.id}"
          src="${element.product.images[0]}"
          alt=""
          width="100"
        />
        <canvas id="${element.subscriptions.id}" style="display:none;"></canvas>
        <h4>${element.product.name}</h4>
        ${
          element.product.description
            ? `
        <p class="fw-light">
          <small style="color: #808080"
            >${element.product.description}</small
          >
        </p>`
            : "<br>"
        }
        <p>Attivo dal: ${formatDateIntl(
          new Date(element.subscriptions.created * 1000).toLocaleDateString()
        )} <br />${
                getExpiredDate(element, false)
                  ? "Si rinnova il: " +
                    formatDateIntl(getExpiredDate(element, false))
                  : ""
              }</p>

        <div class="text-center" style="display: block;">
          <a
            type="button"
            class="btn btn-blue fw-bold mb-2 ms-3 me-3 billing d-inline"
            target="_blank"
            data-customer-id='${element.subscriptions.customer.id}'
            >Gestisci Pagamento</a
          >
          <a
            type="button"
            class="btn btn-blue fw-bold mb-2 ms-3 me-3 qrCode d-inline"
            target="_blank"
            data-product-name='${subProdText}'
            >Ottieni il QRCode</a
          >
          <a
            type="button"
            class="btn btn-blue text-danger fw-bold mb-4 ms-3 me-3 cancel"
            target="_blank"
            data-expiration='${
              getExpiredDate(element, true, 25) + ":" + element.subscriptions.id
            }'
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
            document.querySelectorAll(".billing").forEach((button) => {
              const customer_id = button.getAttribute("data-customer-id");
              button.addEventListener("click", () =>
                redirectToBillingPortal(customer_id)
              );
            });
            document.querySelectorAll(".cancel").forEach((button) => {
              const expirationDate = button
                .getAttribute("data-expiration")
                .split(":")[0];
              const subscription_id = button
                .getAttribute("data-expiration")
                .split(":")[1];
              button.addEventListener("click", () =>
                confirmDialogSimple(
                  "Sicuro di voler disdire l'abbonamento?",
                  "Non sarai in grado di annulare una volta confermato!",
                  "Si Disdici!",
                  "No, Annulla!"
                ).then((result) => {
                  if (result.isConfirmed)
                    cancelSubscription(expirationDate, subscription_id);
                  else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                  )
                    errorDialog(
                      "Operazione Annullata",
                      "Abbonamento non disdetto"
                    );
                })
              );
            });
          }
        }

        const resumeTitle = document.getElementById("resume-title");
        resumeTitle.textContent = "Benvenuto " + customer_name;
        loader.style.display = "none";
      })
      .catch((error) => {
        loader.style.display = "none";
        error = error;
        console.error("Si è verificato un'errore: ", error);
        errorDialog(
          "Errore",
          "Si è verificato un problema, riprova più tardi."
        );
      });
  } else {
    loader.style.display = "none";
    errorDialog(
      "Errore",
      "Si è verificato un problema, riprova più tardi."
    ).then((result) => {
      window.location.href =
        window.location.origin + "/html/login_page/index.php";
    });
  }
});

function cancelSubscription(expirationDate, subscriptionId) {
  const today = new Date(); // Ottieni la data odierna
  const expDate = new Date(expirationDate); // Converti la data di scadenza in oggetto Date

  // Controlla se `expirationDate` è una data valida
  if (!isNaN(expDate.getTime())) {
    // Verifica se la data di scadenza è futura rispetto ad oggi
    if (expDate > today) {
      return errorDialog(
        "Errore",
        `Non è possibile cancellare la sottoscrizione poiché non è stata rispettata la policy sulla cancellazione. L'abbonamento potrà essere cancellato dal giorno ${formatDateIntl(
          expDate.toLocaleDateString()
        )}.`
      );
    }
  } else {
    // Gestione errore: `expirationDate` non è valida
    return htmlDialog(
      "Errore",
      null,
      "error",
      `<p>Errore durante la disdetta della sottoscrizione.<br>Si prega di contattare <a href='mailto:info@neverlandkiz.it'>info@neverlandkiz.it</a>.</p>`
    );
  }

  // Qui aggiungi la chiamata all'API per cancellare la sottoscrizione
  fetch(`cancel-subscription.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ subscriptionId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        return errorDialog(
          "Errore",
          data.message || "Errore sconosciuto durante la cancellazione."
        );
      }
      return simpleDialog(
        "Operazione Eseguita!",
        "Il tuo abbonamento è stato disdetto con successo."
      );
    })
    .catch((error) => {
      console.error("Errore durante la cancellazione:", error);
      return htmlDialog(
        "Errore",
        null,
        "error",
        `<p>Errore durante la disdetta della sottoscrizione.<br>Si prega di contattare <a href='mailto:info@neverlandkiz.it'>info@neverlandkiz.it</a>.</p>`
      );
    });
}

async function redirectToBillingPortal(customerId) {
  loader.style.display = "flex";
  const response = await fetch("billing.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      customer_id: customerId,
      return_url: window.location.href, // Cambia l'URL di ritorno
    }),
  });

  const result = await response.json();

  if (!result.error && result.data && result.data.url) {
    // Redirect all'URL del billing portal
    window.location.href = result.data.url;
  } else {
    loader.style.display = "none";
    console.error("Errore:", result.message || "Errore sconosciuto");
    errorDialog("Errore", "Si è verificato un errore. Per favore, riprova.");
  }
}

function getQRCode(subProd) {
  const product = JSON.parse(subProd);
  const redirect_url = window.location.origin;
  const url = redirect_url
    .concat("/html/read_qrcode/index.php?data=")
    .concat(btoa(product.subscription_id));
  console.log(url);
  qrCodeDialog(product.product_name, null, url).then((result) => {
    if (result.isConfirmed) {
      downloadProductPDF(product);
    }
  });
}

function downloadProductPDF(product) {
  loader.style.display = "flex";
  const qrCodeImage = document.querySelector("#qrcode img");
  qrCodeImage.width = 2048;
  qrCodeImage.height = 2048;

  if (!qrCodeImage) {
    console.error("Immagine QR Code non trovata.");
    loader.style.display = "none";
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
        if (product.product_description) {
          pdf.setFont("helvetica", "normal"); // Ripristina il font normale per il testo successivo
          pdf.text(
            "Evento/i a cui puoi accedere: " + product.product_description,
            10,
            80
          );
        }

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
          "EVENT: The events are and remain the sole responsibility of the organizer. The acquisition of this e-ticket wins if adherence to rules of the place of the event and / or organizer. In case of cancellation or postponement of the event, a refund of the ticket without costs (transport, hotels, etc ...) will be subject to the conditions of the organizer (you can find his email address above in Additional information) who receives the income from the sale of e-tickets.",
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
                const img = new Image();
                img.src = reader.result; // Carica i dati Base64 nell'immagine
                img.onload = () => {
                  const imgWidth = 65; // Larghezza desiderata
                  const aspectRatio = img.height / img.width; // Rapporto di aspetto
                  const imgHeight = imgWidth * aspectRatio; // Altezza calcolata dinamicamente

                  pdf.addImage(
                    reader.result,
                    "JPEG",
                    10,
                    95,
                    imgWidth,
                    imgHeight
                  ); // Usa le dimensioni calcolate
                  pdf.save("QRCode_" + product.product_name + ".pdf");
                  loader.style.display = "none";
                  simpleDialog(
                    "Download Iniziato",
                    "Ora puoi accedere a ".concat(product.product_name)
                  );
                };
              };
            })
            .catch((error) => {
              loader.style.display = "none";
              errorDialog(
                "Errore",
                "Errore nel recupero dell'immagine:",
                error
              );
            });
        } else {
          pdf.save("QRCode_" + product.product_name + ".pdf");
          loader.style.display = "none";
          simpleDialog(
            "Download Iniziato",
            "Ora puoi accedere a ".concat(product.product_name)
          );
        }
      };
    })
    .catch((error) => {
      loader.style.display = "none";
      errorDialog(
        "Errore di rete",
        "Si è verificato un problema, riprova più tardi."
      );
    });
}
