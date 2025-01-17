document.addEventListener("DOMContentLoaded", function () {
  const apiUrl = getApiBaseUrl(incType.STRIPE_FROM_TEMPLATE);
  const baseUrl = getApiBaseUrl(incType.BASE_URL);

  console.log("API-URL: ", apiUrl);
  console.log("BASE-URL: ", baseUrl);

  const loader = document.getElementById("loader");
  const urlParams = new URLSearchParams(window.location.search);
  let data = urlParams.get("data");

  if (data) localStorage.setItem("_user_data", data);
  else data = localStorage.getItem("_user_data");

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
    //fetch("../inc/stripe/customers-billing.php", {
    fetch(apiUrl + "customers-billing.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ customer_ids_string, session_id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          errorDialog(
            translations.customers_generic_error_title,
            translations.customers_request_error_text + data.message
          ).then((result) => {
            window.location.href = baseUrl;
          });
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
                //product_image: "../core/img/easy.png",
                subscription_id: element.subscriptions.id,
                customer_name: element.subscriptions.customer.name,
              };
              const subProdText = JSON.stringify(subProd);
              //const subProdText = subProd.subscription_id;
              const htmlText = `<div class="col-12 col-md-4 mt-3 fade-in">
      <div class="card text-center zoom" id="card">
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
                getExpiredDate(element, false) !=
                new Date(
                  element.subscriptions.created * 1000
                ).toLocaleDateString()
                  ? "Si rinnova il: " +
                    formatDateIntl(getExpiredDate(element, false))
                  : ""
              }</p>

        <div class="text-center" style="display: block;">
          <a
            type="button"
            class="btn btn-blue fw-bold mb-2 ms-3 me-3 billing"
            target="_blank"
            data-customer-id='${element.subscriptions.customer.id}'
            >${translations.customers_handle_payments_title}</a
          >
          <a
            type="button"
            class="btn btn-blue fw-bold mb-2 ms-3 me-3 qrCode"
            target="_blank"
            data-product-name='${subProdText}'
            >${translations.customers_get_qr_code_title}</a
          >
          <a
            type="button"
            class="btn btn-blue text-danger fw-bold mb-4 ms-3 me-3 cancel"
            target="_blank"
            data-expiration='${
              getExpiredDate(element, true, 25) +
              "::" +
              element.subscriptions.id
            }'
          >
          ${translations.customers_cancel_subscription_title}
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
                .split("::")[0];
              const subscription_id = button
                .getAttribute("data-expiration")
                .split("::")[1];
              button.addEventListener("click", () =>
                confirmDialogSimple(
                  translations.customers_cancel_subscription_confirm_title,
                  translations.customers_cancel_subscription_confirm_subtitle,
                  translations.customers_cancel_subscription_confirm_button,
                  translations.customers_cancel_subscription_deny_button
                ).then((result) => {
                  if (result.isConfirmed)
                    cancelSubscription(expirationDate, subscription_id);
                  else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                  )
                    errorDialog(
                      translations.customers_cancel_subscription_reject_title,
                      translations.customers_cancel_subscription_reject_subtitle
                    );
                })
              );
            });
          }
        }

        const resumeTitle = document.getElementById("resume-title");
        resumeTitle.innerHTML = translations.customers_page_title.replace(
          "#CUSTOMER_NAME#",
          customer_name
        );
        // resumeTitle.textContent = "Benvenuto " + customer_name;
        loader.style.display = "none";
      })
      .catch((error) => {
        loader.style.display = "none";
        error = error;
        console.error("Si è verificato un'errore: ", error);
        errorDialog(
          translations.customers_generic_error_title,
          translations.customers_generic_error_text
        ).then((result) => {
          window.location.href = baseUrl;
        });
      });
  } else {
    loader.style.display = "none";
    errorDialog(
      translations.customers_generic_error_title,
      translations.customers_generic_error_text
    ).then((result) => {
      window.location.href = baseUrl;
    });
  }
});

function cancelSubscription(expirationDate, subscription_id) {
  const apiUrl = getApiBaseUrl(incType.STRIPE_FROM_TEMPLATE);

  const loader = document.getElementById("loader");
  const today = new Date(); // Ottieni la data odierna
  const expDate = new Date(expirationDate); // Converti la data di scadenza in oggetto Date

  // Controlla se `expirationDate` è una data valida
  if (!isNaN(expDate.getTime())) {
    // Verifica se la data di scadenza è futura rispetto ad oggi
    if (expDate > today) {
      return htmlDialog(
        translations.customers_generic_error_title,
        null,
        "error",
        translations.customers_cancel_subscription_error_policy.replace(
          "#CANCEL_DAY#",
          expDate.toLocaleDateString()
        )
        //`<p>Non è possibile cancellare la sottoscrizione poiché non è stata rispettata la <a href='template-policy.php'>policy sulla cancellazione.</a> L'abbonamento potrà essere cancellato dal giorno ${formatDateIntl(
        //  expDate.toLocaleDateString()
        //)}.</p>`
      );
    }
  } else {
    // Gestione errore: `expirationDate` non è valida
    return htmlDialog(
      translations.customers_generic_error_title,
      null,
      "error",
      translations.customers_cancel_subscription_error_generic
      //`<p>Errore durante la disdetta della sottoscrizione.<br>Si prega di contattare <a href='mailto:info@easysubscribe.it'>info@easysubscribe.it</a>.</p>`
    );
  }
  loader.style.display = "flex";
  // Qui aggiungi la chiamata all'API per cancellare la sottoscrizione
  //fetch(`../inc/stripe/cancel-subscription.php`, {
  fetch(apiUrl + `cancel-subscription.php`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ subscription_id }),
  })
    .then((response) => response.json())
    .then((data) => {
      loader.style.display = "none";
      if (data.error) {
        return errorDialog(
          translations.customers_generic_error_title,
          translations.customers_cancel_subscription_error_unknown
        );
      }
      return simpleDialog(
        translations.customers_cancel_subscription_success_title,
        translations.customers_cancel_subscription_success_subtitle
      ).then((result) => {
        if (result.isConfirmed) window.location.reload();
      });
    })
    .catch((error) => {
      loader.style.display = "none";
      console.error("Errore durante la cancellazione:", error);
      return htmlDialog(
        translations.customers_generic_error_title,
        null,
        "error",
        translations.customers_cancel_subscription_error_generic
        //`<p>Errore durante la disdetta della sottoscrizione.<br>Si prega di contattare <a href='mailto:info@easysubscribe.it'>info@easysubscribe.it</a>.</p>`
      );
    });
}

async function redirectToBillingPortal(customerId) {
  const apiUrl = getApiBaseUrl(incType.STRIPE_FROM_TEMPLATE);

  loader.style.display = "flex";
  //const response = await fetch("../inc/stripe/billing.php", {
  const response = await fetch(apiUrl + "billing.php", {
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
    errorDialog(
      translations.customers_generic_error_title,
      translations.customers_generic_error_text
    );
  }
}

function getQRCode(subProd) {
  const product = JSON.parse(subProd);
  const redirect_url = window.location.origin;
  const url = redirect_url
    .concat("/dist/templates/template-read-qrcode.php?data=")
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
    errorDialog(
      translations.customers_generic_error_title,
      translations.customers_get_qr_code_download_error
    );
    return;
  }

  html2canvas(qrCodeImage, { backgroundColor: "#ffffff" })
    .then((canvas) => {
      const imageUrl = getApiBaseUrl(incType.IMAGE_URL);
      const apiUrl = getApiBaseUrl(incType.API);
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
      //logo.src = "/../dist/assets/images/easy.png"; // Cambia con l'URL del tuo logo
      logo.src = imageUrl + "easy.png"; // Cambia con l'URL del tuo logo

      logo.onerror = function () {
        console.error("Logo non trovato. Download interrotto.");
        loader.style.display = "none";
        errorDialog(
          translations.customers_generic_error_title,
          translations.customers_get_qr_code_download_error
        );
      };

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
          translations.customers_pdf_welcome_text.replace(
            "#CUSTOMER_NAME#",
            product.customer_name
          ),
          10,
          70
        );
        if (product.product_description) {
          pdf.setFont("helvetica", "normal"); // Ripristina il font normale per il testo successivo
          pdf.text(
            translations.customers_pdf_event_text + product.product_description,
            10,
            80
          );
        }

        pdf.setFont("helvetica", "bold"); // Imposta il font Helvetica in grassetto
        pdf.text(translations.customers_pdf_terms_title, 85, 90);
        pdf.setFont("helvetica", "normal"); // Ripristina il font normale per il testo successivo
        pdf.setFontSize(8);

        // Array di testo da aggiungere al PDF
        const generalTerms = [
          translations.customers_pdf_terms_part_1,
          translations.customers_pdf_terms_part_2,
          translations.customers_pdf_terms_part_3,
          translations.customers_pdf_terms_part_4,
          translations.customers_pdf_terms_part_5,
          translations.customers_pdf_terms_part_6,
        ];

        // Imposta la larghezza massima per il testo
        const maxWidth = 115;

        // Posizione verticale iniziale
        let verticalPosition = 95;

        // Dimensione font (per calcolare l'altezza delle righe)
        const fontSize = pdf.internal.getFontSize(); // Dimensione font attuale
        const lineHeight = fontSize * 0.352778; // Altezza riga in mm (1 pt = 0.352778 mm)

        // Gestione dinamica del testo
        generalTerms.forEach((term) => {
          // Dividi il testo in righe in base alla larghezza massima
          const lines = pdf.splitTextToSize(term, maxWidth);

          // Stampa il blocco di testo nel PDF
          pdf.text(lines, 85, verticalPosition);

          // Calcola l'altezza occupata da questo blocco di testo
          const blockHeight = lines.length * lineHeight;

          // Aggiorna la posizione verticale per il prossimo blocco
          verticalPosition += blockHeight + (lines.length > 5 ? 4 : 3);
          console.log(
            `Blocco di ${lines.length} righe: Altezza = ${blockHeight}, Posizione = ${verticalPosition}`
          );
        });

        pdf.setFontSize(10);

        // Testo che va a capo
        const descriptionText = [
          translations.customers_pdf_event_access_part_1,
          translations.customers_pdf_event_access_part_2,
        ];

        const lines1 = pdf.splitTextToSize(descriptionText[0], 180); // Imposta la larghezza massima
        const lines2 = pdf.splitTextToSize(descriptionText[1], 180); // Imposta la larghezza massima

        // Aggiungi il testo che va a capo
        pdf.text(lines1, 10, pdf.internal.pageSize.height - 30);
        pdf.text(lines2, 10, pdf.internal.pageSize.height - 20);

        // Aggiungi il footer in basso a destra
        const footerText = translations.customers_pdf_event_access_part_3;
        pdf.setFontSize(8);
        pdf.text(footerText, 10, pdf.internal.pageSize.height - 10);

        if (product.product_image) {
          //const imageUrl =
          //  "../inc/api/image-proxy.php?url=" + product.product_image;

          const imageUrl =
            apiUrl + "image-proxy.php?url=" + product.product_image;

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
                  pdf.save("Ticket_" + product.product_name + ".pdf");
                  loader.style.display = "none";
                  simpleDialog(
                    translations.customers_get_qr_code_download_success_title,
                    translations.customers_get_qr_code_download_success_subtitle.concat(
                      product.product_name
                    )
                  );
                };
              };
            })
            .catch((error) => {
              loader.style.display = "none";
              console.error("Errore nel recupero dell'immagine:", error);
              errorDialog(
                translations.customers_generic_error_title,
                translations.customers_generic_error_text
              );
            });
        } else {
          pdf.save("Ticket_" + product.product_name + ".pdf");
          loader.style.display = "none";
          simpleDialog(
            translations.customers_get_qr_code_download_success_title,
            translations.customers_get_qr_code_download_success_subtitle.concat(
              product.product_name
            )
          );
        }
      };
    })
    .catch((error) => {
      loader.style.display = "none";
      errorDialog(
        translations.customers_generic_error_title,
        translations.customers_generic_error_text
      );
    });
}
