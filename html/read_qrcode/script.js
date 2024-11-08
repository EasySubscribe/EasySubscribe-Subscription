document.getElementById("startScan").addEventListener("click", function () {
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
});
document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const data = urlParams.get("data");
  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const subProd = JSON.parse(decodedData);
  }
});

function openModal() {
  const modal = document.getElementById("openModalButton");
  modal.click();
}

document.querySelector("button").addEventListener("click", function () {
  const checkIcon = document.querySelector(".check-icon");

  checkIcon.style.display = "none";

  setTimeout(function () {
    checkIcon.style.display = "block";
  }, 10);
});
