/*document.getElementById("startScan").addEventListener("click", function () {
  const readerElement = document.getElementById("reader");
  const errorElement = document.getElementById("error");
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
      errorElement.innerText =
        "Errore nell'avvio della fotocamera. Verifica i permessi o utilizza un server HTTPS.";
    });
});*/

document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");
  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const subProd = JSON.parse(decodedData);
    openModal(subProd);
  }
});

function openModalStatic() {
  const modalBtn = document.getElementById("openResultScan");
  modalBtn.click();
}

function openModal(product) {
  const modalBtn = document.getElementById("openResultScan");
  const modalCont = document.getElementById("modal-content");

  let activeUser =
    new Date(product.subscription_renew_date_timestamp * 1000) >= new Date()
      ? true
      : false;

  const modalContent = `<div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalQRCodeResult">
              Accesso ${activeUser ? "Consentito" : "Vietato"} per ${
    product.customer_name
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
              <p>Attiva dal: ${
                product.subscription_start_date
              } <br />Si rinnova il: ${product.subscription_renew_date}</p>
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

document.querySelector("button").addEventListener("click", function () {
  const checkIcon = document.querySelector(".check-icon");

  checkIcon.style.display = "none";

  setTimeout(function () {
    checkIcon.style.display = "block";
  }, 10);
});
