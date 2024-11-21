document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("loader");
  loader.style.visibility = "hidden";

  let hasScanned = false; // Flag per evitare scansioni multiple

  // Funzione per decodificare il parametro `data`
  function decodeAndVerify(data) {
    try {
      const decodedData = atob(data); // Decodifica base64
      const subProd = JSON.parse(decodedData); // Parsing JSON
      verifySubscription(subProd); // Validazione
    } catch (error) {
      console.error(
        "Errore nella decodifica o validazione del parametro `data`:",
        error
      );
      simpleDialog("Errore", "Il QR Code non contiene dati validi.");
    }
  }

  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");
  if (data) {
    // Decodifica e verifica immediatamente se il parametro `data` è presente nell'URL
    decodeAndVerify(data);
  }

  // Configura l'evento di scansione
  //document.getElementById("startScan").addEventListener("click", function () {
  //  const readerElement = document.getElementById("reader");
  //  readerElement.style.display = "block";
  //  //hideResultScan();
  //
  //  const onScanSuccess = (decodedText, decodedResult) => {
  //    if (hasScanned) return; // Evita di processare ulteriori scansioni
  //    hasScanned = true; // Imposta il flag a true dopo la prima scansione
  //
  //    readerElement.style.display = "none"; // Nasconde il lettore
  //    try {
  //      const data = decodedText.split("data=")[1];
  //      if (data) {
  //        decodeAndVerify(data); // Decodifica e verifica il contenuto del QR Code
  //        html5QrcodeScanner.clear();
  //      } else {
  //        simpleDialog(
  //          "Errore",
  //          "Il QR Code non contiene il parametro `data`."
  //        );
  //        //hasScanned = false; // Resetta il flag in caso di errore
  //      }
  //    } catch (error) {
  //      console.error("Errore durante la scansione del QR Code:", error);
  //      simpleDialog("Errore", "Impossibile leggere il QR Code.");
  //      //hasScanned = false; // Resetta il flag in caso di errore
  //    }
  //  };
  //
  //  const html5QrcodeScanner = new Html5QrcodeScanner("reader", {
  //    fps: 60, // Frame per secondo
  //    qrbox: 250, // Dimensioni del box per il QR Code
  //  });
  //
  //  html5QrcodeScanner.render(onScanSuccess);
  //});
});

// Funzione per decodificare il parametro `data`
function decodeAndVerify(data) {
  try {
    const decodedData = atob(data); // Decodifica base64
    const subProd = JSON.parse(decodedData); // Parsing JSON
    verifySubscription(subProd); // Validazione
  } catch (error) {
    console.error(
      "Errore nella decodifica o validazione del parametro `data`:",
      error
    );
    simpleDialog("Errore", "Il QR Code non contiene dati validi.");
  }
}

function startScan() {
  const scan = document.getElementById("result-scan");
  scan.style.display = "none";
  let hasScanned = false; // Flag per evitare scansioni multiple

  const readerElement = document.getElementById("reader");
  readerElement.style.display = "block";

  const onScanSuccess = (decodedText, decodedResult) => {
    if (hasScanned) return; // Evita di processare ulteriori scansioni
    hasScanned = true; // Imposta il flag a true dopo la prima scansione

    readerElement.style.display = "none"; // Nasconde il lettore
    try {
      const data = decodedText.split("data=")[1];
      if (data) {
        decodeAndVerify(data); // Decodifica e verifica il contenuto del QR Code
        html5QrcodeScanner.clear();
      } else {
        errorDialog(
          "Errore",
          "Il QR Code non contiene il parametro `data`. Riprova."
        );
        //hasScanned = false; // Resetta il flag in caso di errore
      }
    } catch (error) {
      console.error("Errore durante la scansione del QR Code:", error);
      errorDialog("Errore", "Impossibile leggere il QR Code.");
      //hasScanned = false; // Resetta il flag in caso di errore
    }
  };

  const html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    {
      qrbox: {
        width: 270,
        height: 270,
      },
      fps: 60,
      videoConstraints: {
        facingMode: { exact: "environment" },
      },
    },
    false
  );

  html5QrcodeScanner.render(onScanSuccess);

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
      src="${sub.product.product_image}"
      alt=""
      width="100"
    />
    <h4>${sub.product.product_name}</h4>
    <p class="fw-light">
      <small style="color: #808080"
        >${sub.product.product_description}</small
      >
    </p>
    <p><small style="color: #808080"
    >Utente:</small
    > ${sub.subscription.customer.name}</p>
     <p>Attiva dal: ${new Date(
       sub.subscription.created * 1000
     ).toLocaleDateString()} <br />Prossimo addebito il: ${new Date(
    sub.subscription.current_period_end * 1000
  ).toLocaleDateString()}</p>
  ${
    isActive
      ? `<span class="badge text-bg-success mx-auto mb-3">Active</span>`
      : `<span class="badge text-bg-danger mx-auto mb-3">Not Active</span>`
  }
  </div>`;
  scan.innerHTML = html;
}

async function verifySubscription(subProd) {
  const loader = document.getElementById("loader");
  loader.style.visibility = "visible";
  const subscription_id = subProd.subscription_id;
  let response = {
    product: subProd,
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
      src="${sub.product.product_image}"
      alt=""
      width="100"
    />
    <h4>${sub.product.product_name}</h4>
    <p class="fw-light">
      <small style="color: #808080"
        >${sub.product.product_description}</small
      >
    </p>
    <p><small style="color: #808080"
    >Utente:</small
    > ${sub.subscription.customer.name}</p>
     <p>Attiva dal: ${new Date(
       sub.subscription.created * 1000
     ).toLocaleDateString()} <br />Prossimo addebito il: ${new Date(
    sub.subscription.current_period_end * 1000
  ).toLocaleDateString()}</p>
  ${
    activeUser
      ? `<span class="badge text-bg-success mx-auto">Active</span>`
      : `<span class="badge text-bg-danger mx-auto">Not Active</span>`
  }
  </div>`;
  return html;
}
