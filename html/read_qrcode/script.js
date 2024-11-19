document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("loader");
  loader.style.visibility = "hidden";
  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");
  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const subProd = JSON.parse(decodedData);
    verifySubscription(subProd);
  } else {
    document.getElementById("startScan").addEventListener("click", function () {
      const readerElement = document.getElementById("reader");
      readerElement.style.display = "block";

      const html5QrCode = new Html5Qrcode("reader");

      html5QrCode
        .start(
          { facingMode: "environment" },
          { fps: 10, qrbox: { width: 250, height: 250 } },
          (decodedText) => {
            document.getElementById("qr-result").innerText = decodedText;
            html5QrCode.stop();
            readerElement.style.display = "none";
          },
          (errorMessage) => {
            console.warn("Errore durante la scansione: ", errorMessage);
          }
        )
        .catch((err) => {
          console.error("Errore di avvio scanner QR: ", err);
          readerElement.innerHTML =
            "<p class='text-center'>Errore nell'avvio della fotocamera. Verifica i permessi o utilizza un server HTTPS.</p>";
        });
    });
  }
});

//document.querySelector("button").addEventListener("click", function () {
//  const checkIcon = document.querySelector(".check-icon");
//
//  checkIcon.style.display = "none";
//
//  setTimeout(function () {
//    checkIcon.style.display = "block";
//  }, 10);
//});

function openModalStatic() {
  const modalBtn = document.getElementById("openResultScan");
  modalBtn.click();
}

function openModal(product, subscriptions, activeUser) {
  const modalBtn = document.getElementById("openResultScan");
  const modalCont = document.getElementById("modal-content");

  const modalContent = `<div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalQRCodeResult">
              Accesso ${activeUser ? "Consentito" : "Vietato"} per ${
    subscriptions.customer.name
  }.
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="text-center">
              ${
                activeUser
                  ? `<div class="sa mx-auto">
                <div class="sa-success">
                  <div class="sa-success-tip"></div>
                  <div class="sa-success-long"></div>
                  <div class="sa-success-placeholder"></div>
                  <div class="sa-success-fix"></div>
                </div>
              </div>`
                  : `<div class="sa mx-auto">
                <div class="sa-danger">
                  <div class="sa-danger-tip"></div>
                  <div class="sa-danger-long"></div>
                  <div class="sa-danger-placeholder"></div>
                  <div class="sa-danger-fix"></div>
                </div>
              </div>`
              }

              <h4>${product.product_name}</h4>
              <p class="fw-light">
                <small style="color: #808080"
                  >${product.product_description}</small
                >
              </p>
              <p>Attiva dal: ${new Date(
                subscriptions.created * 1000
              ).toLocaleDateString()} <br />Prossimo addebito il: ${new Date(
    subscriptions.current_period_end * 1000
  ).toLocaleDateString()}</p>
              ${
                activeUser
                  ? `<span class="badge text-bg-success mb-3">Active</span>`
                  : `<span class="badge text-bg-danger mb-3">Not Active</span>`
              }
            </div>
          </div>
          <div class="modal-footer">
            <div class="row w-100">
              <div class="col-12">
                <button
                  type="button"
                  class="btn btn-blue w-100"
                  data-bs-dismiss="modal"
                >
                  Close
                </button>
              </div>
            </div>
            <div hidden class="row w-100">
              <div class="col-6">
                <button
                  type="button"
                  class="btn btn-secondary w-100"
                  data-bs-dismiss="modal"
                >
                  Close
                </button>
              </div>
              <div class="col-6">
                <button type="button" class="btn btn-primary w-100">
                  Save changes
                </button>
              </div>
            </div>
          </div>`;

  modalCont.innerHTML += modalContent;

  modalBtn.click();
}

function showData(sub, isActive) {
  const scan = document.getElementById("result-scan");
  const html = `<div class="card text-center fade show" id="card">
    <img
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
      ? `<span class="badge text-bg-success mx-auto mb-3" style="width: 50px">Active</span>`
      : `<span class="badge text-bg-danger mx-auto mb-3" style="width: 50px">Not Active</span>`
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
          // Validazione Sottoscrizione
          let activeUser =
            new Date(result.subscription_details.current_period_end * 1000) >=
            new Date()
              ? true
              : false;
          loader.style.visibility = "hidden";
          openModal(subProd, result.subscription_details, activeUser);
          response.subscription = result.subscription_details;
          showData(response, activeUser);
        } else if (result.status === "failed") {
          loader.style.visibility = "hidden";
          openModal(subProd, result.subscription_details, false);
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
