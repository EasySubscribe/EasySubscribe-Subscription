document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("loader");
  loader.style.visibility = "hidden";

  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");
  if (data) {
    // Decodifica e verifica immediatamente se il parametro `data` è presente nell'URL
    decodeAndVerify(data);
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
    errorDialog("Errore", "Il QR Code contiene dati non validi.");
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
          errorDialog("Errore", "Il QR Code contiene dati non validi.");
        }
      } catch (error) {
        console.error("Errore durante la scansione del QR Code:", error);
        errorDialog("Errore", "Impossibile leggere il QR Code.");
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
      errorDialog("Errore", "Impossibile accedere alla fotocamera.");
    });

  document.addEventListener("DOMContentLoaded", () => {
    const videoElement = document.querySelector("video"); // Seleziona il video DOM una volta disponibile

    if (videoElement) {
      videoElement.addEventListener("click", (event) => {
        const track = videoElement.srcObject.getVideoTracks()[0];
        if ("focusMode" in track.getCapabilities()) {
          track
            .applyConstraints({
              advanced: [{ focusMode: "continuous" }],
            })
            .catch((err) =>
              console.error("Errore durante l'applicazione del focus:", err)
            );
        } else {
          console.warn(
            "La messa a fuoco manuale non è supportata su questo dispositivo."
          );
        }
      });
    }
  });
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
    <p class="fw-light">
      <small style="color: #808080"
        >${sub.product.description}</small
      >
    </p>
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
  const loader = document.getElementById("loader");
  loader.style.visibility = "visible";
  let response = {
    product: null,
    subscription: null,
  };
  try {
    fetch("qrcode.php", {
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
            `Accesso ${activeUser ? "Consentito" : "Vietato"}.`,
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
            `Accesso Vietato.`,
            null,
            "error",
            getHTMLData(response, false)
          );
        } else {
          loader.style.visibility = "hidden";
          errorDialog("Errore", result.message);
        }
      });
  } catch (error) {
    loader.style.visibility = "hidden";
    errorDialog("Errore nella verifica", result.message);
  }
}

function getHTMLData(sub, activeUser) {
  const html = `<hr><div class="card text-center fade show" id="card">
    <img
      style="border-radius: 7px;"
      class="mx-auto m-3"
      src="${sub.product.images[0]}"
      alt=""
      width="100"
    />
    <h4>${sub.product.name}</h4>
    <p class="fw-light">
      <small style="color: #808080"
        >${sub.product.description}</small
      >
    </p>
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
