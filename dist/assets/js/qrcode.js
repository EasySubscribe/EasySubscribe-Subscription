document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("loader");
  loader.style.visibility = "hidden";

  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");
  if (data) {
    // Decodifica e verifica immediatamente se il parametro `data` è presente nell'URL
    decodeAndVerify(data);
    const cleanUrl = window.location.origin + window.location.pathname;
    window.history.replaceState(null, "", cleanUrl);
  }
});

function decodeAndVerify(data) {
  try {
    const decodedData = atob(data); // Decodifica base64
    //const subProd = JSON.parse(decodedData); // Parsing JSON
    verifySubscription(decodedData); // Validazione
  } catch (error) {
    console.error(
      "Errore nella decodifica o validazione del parametro `data`:",
      error
    );
    errorDialog(
      translations.read_qr_error_title,
      translations.read_qr_error_data_not_valid
    );
  }
}

function startScan() {
  const scan = document.getElementById("result-scan");
  scan.style.display = "none";
  let hasScanned = false; // Flag per evitare scansioni multiple

  const readerElement = document.getElementById("reader");
  readerElement.style.display = "block";

  const codeReader = new ZXingBrowser.BrowserQRCodeReader();

  codeReader
    .decodeOnceFromVideoDevice(null, "reader")
    .then((result) => {
      if (hasScanned) return; // Evita scansioni multiple
      hasScanned = true; // Imposta il flag per evitare ulteriori scansioni

      readerElement.style.display = "none"; // Nasconde il lettore
      try {
        const data = result.text.split("data=")[1];
        if (data) {
          decodeAndVerify(data); // Decodifica e verifica il contenuto del QR Code
        } else {
          console.error(
            "Errore nella decodifica o validazione del parametro `data`:",
            error
          );
          errorDialog(
            translations.read_qr_error_title,
            translations.read_qr_error_data_not_valid
          );
        }
      } catch (error) {
        console.error("Errore durante la scansione del QR Code:", error);
        errorDialog(
          translations.read_qr_error_title,
          translations.read_qr_error_data
        );
      } finally {
        () => {
          const videoElement = readerElement.querySelector("video");
          if (videoElement && videoElement.srcObject) {
            const stream = videoElement.srcObject;
            const tracks = stream.getTracks(); // Ottieni tutti i "tracks" del flusso
            tracks.forEach((track) => track.stop()); // Ferma ciascuno dei tracks
            videoElement.srcObject = null; // Rimuove la sorgente video
          }
        };
      }
    })
    .catch((error) => {
      console.error("Errore durante l'inizializzazione dello scanner:", error);
      readerElement.style.display = "none"; // Nasconde il lettore
      errorDialog(
        translations.read_qr_error_title,
        translations.read_qr_error_camera
      );
    });

  const videoElement = document.querySelector("video"); // Seleziona l'elemento video
  const focusIndicator = document.createElement("div"); // Indicatore per la messa a fuoco

  // Stile per l'indicatore
  focusIndicator.style.position = "absolute";
  focusIndicator.style.bottom = "80px";
  focusIndicator.style.left = "50%";
  focusIndicator.style.transform = "translate(-50%, -50%)";
  focusIndicator.style.padding = "10px 10px";
  focusIndicator.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
  focusIndicator.style.color = "white";
  focusIndicator.style.fontSize = "16px";
  focusIndicator.style.borderRadius = "5px";
  focusIndicator.style.display = "none"; // Nascondi inizialmente
  focusIndicator.textContent = translations.read_qr_error_focus;
  document.body.appendChild(focusIndicator); // Aggiungi l'indicatore al body

  // Funzione per ottenere e selezionare la videocamera ultrawide (se disponibile)
  function startVideoWithUltraWide() {
    // Richiedi l'accesso alla fotocamera per ottenere i permessi
    navigator.mediaDevices
      .getUserMedia({ video: true })
      .then((stream) => {
        // Rilascia lo stream iniziale (serve solo per i permessi)
        stream.getTracks().forEach((track) => track.stop());

        // Ora possiamo chiamare enumerateDevices
        return navigator.mediaDevices.enumerateDevices();
      })
      .then((devices) => {
        const videoDevices = devices.filter(
          (device) => device.kind === "videoinput"
        );

        //simpleDialog("Fotocamera", JSON.stringify(videoDevices));
        console.log("Dispositivi video:", videoDevices); // Log per debug

        // Cerca la fotocamera ultrawide
        const ultrawideCamera = videoDevices.find(
          (device) =>
            device.label.toLowerCase().includes("ultra-grandangolo") ||
            device.label.toLowerCase().includes("ultrawide")
        );

        if (ultrawideCamera) {
          console.log("Fotocamera ultrawide trovata:", ultrawideCamera.label);
          return navigator.mediaDevices.getUserMedia({
            video: { deviceId: { exact: ultrawideCamera.deviceId } },
          });
        } else {
          console.warn(
            "Fotocamera ultrawide non trovata. Uso quella predefinita."
          );
          return navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: "environment" } },
          });
        }
      })
      .then((stream) => {
        videoElement.srcObject = stream; // Assegna il flusso video all'elemento
      })
      .catch((err) => {
        console.error("Errore durante l’accesso alla fotocamera:", err);
        errorDialog(
          translations.read_qr_error_title,
          translations.read_qr_error_camera
        );
      });
  }

  // Inizializza il video con la fotocamera ultrawide o fallback
  if (videoElement) {
    if (isIOS()) startVideoWithUltraWide();

    videoElement.addEventListener("click", () => {
      const track = videoElement.srcObject.getVideoTracks()[0];
      if ("focusMode" in track.getCapabilities()) {
        // Mostra l'indicatore di messa a fuoco
        focusIndicator.style.display = "block";

        track
          .applyConstraints({
            facingMode: { ideal: "environment" },
            frameRate: { ideal: 30, max: 60 },
            advanced: [{ focusMode: "continuous" }],
          })
          .then(() => {
            // Nascondi l'indicatore dopo 1 secondo
            setTimeout(() => {
              focusIndicator.style.display = "none";
            }, 1000);
          })
          .catch((err) => {
            focusIndicator.style.display = "none"; // Nascondi in caso di errore
            console.error("Errore durante l'applicazione del focus:", err);
          });
      } else {
        console.warn(
          "La messa a fuoco manuale non è supportata su questo dispositivo."
        );
      }
    });
  }
}

function showData(sub, isActive) {
  const readerElement = document.getElementById("reader");
  readerElement.style.display = "none";
  const scan = document.getElementById("result-scan");
  scan.style.display = "block";
  const html = `<div class="card text-center fade show" id="card">
    <img
      style="border-radius: 7px;"
      class="mx-auto m-3"
      src="${sub.product.images[0]}"
      alt=""
      width="100"
    />
    <h4>${sub.product.name}</h4>
    ${
      sub.product.description
        ? `<p class="fw-light">
      <small style="color: #808080"
        >${sub.product.description}</small
      >
    </p>`
        : "<br>"
    }
    
    <p><small style="color: #808080"
    >Utente:</small
    > ${sub.subscription.customer.name}</p>
  ${
    isActive
      ? `<span class="badge text-bg-success mx-auto mb-3">Active</span>`
      : `<span class="badge text-bg-danger mx-auto mb-3">Not Active</span>`
  }
  </div>`;
  scan.innerHTML = html;
}

async function verifySubscription(subscription_id) {
  const apiUrl = getApiBaseUrl(incType.STRIPE_FROM_TEMPLATE);
  const loader = document.getElementById("loader");
  loader.style.visibility = "visible";
  let response = {
    product: null,
    subscription: null,
  };
  try {
    //fetch("/dist/inc/stripe/qrcode.php", {
    fetch(apiUrl + "qrcode.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ subscription_id }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.status === "success") {
          response.subscription = result.subscription_details;
          response.product = result.product_details;
          // Validazione Sottoscrizione
          let activeUser =
            new Date(result.subscription_details.current_period_end * 1000) >=
              new Date() && result.subscription_details.status == "active";
          loader.style.visibility = "hidden";
          showData(response, activeUser);
          htmlDialog(
            `${
              activeUser
                ? translations.read_qr_access_allowed
                : translations.read_qr_access_denied
            }`,
            null,
            "success",
            getHTMLData(response, activeUser)
          );
        } else if (result.status === "failed") {
          response.subscription = result.subscription_details;
          response.product = result.product_details;
          loader.style.visibility = "hidden";
          showData(response, false);
          htmlDialog(
            `${translations.read_qr_access_denied}`,
            null,
            "error",
            getHTMLData(response, false)
          );
        } else {
          loader.style.visibility = "hidden";
          errorDialog(translations.read_qr_error_title, result.message);
        }
      });
  } catch (error) {
    loader.style.visibility = "hidden";
    errorDialog(translations.read_qr_error_title, result.message);
  }
}

function getHTMLData(sub, activeUser) {
  const html = `<hr><div class="card text-center fade show" id="card" style="box-shadow: none !important;">
    <img
      style="border-radius: 7px;"
      class="mx-auto m-3"
      src="${sub.product.images[0]}"
      alt=""
      width="100"
    />
    <h4>${sub.product.name}</h4>
    ${
      sub.product.description
        ? `<p class="fw-light">
      <small style="color: #808080"
        >${sub.product.description}</small
      >
    </p>`
        : "<br>"
    }
    <p><small style="color: #808080"
    >Utente:</small
    > ${sub.subscription.customer.name}</p>
  ${
    activeUser
      ? `<span class="badge text-bg-success mx-auto">Active</span>`
      : `<span class="badge text-bg-danger mx-auto">Not Active</span>`
  }
  </div>`;
  return html;
}
