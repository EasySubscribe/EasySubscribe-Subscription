function confirmDialog(
  title,
  subtitle,
  confirmTitle,
  confirmSubtitle,
  rejectTitle,
  rejectSubtitle
) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-blue fw-bold w-popup",
      cancelButton: "btn btn-blue fw-bold w-popup",
      popup: "border_round",
    },
    buttonsStyling: true,
  });
  swalWithBootstrapButtons
    .fire({
      title: title,
      text: subtitle,
      icon: "warning",
      background: "#e6f1f1",
      showCancelButton: true,
      confirmButtonText: "Si Disdici!",
      cancelButtonText: "No, Annulla!",
      reverseButtons: false,
    })
    .then((result) => {
      if (result.isConfirmed) {
        swalWithBootstrapButtons.fire({
          title: confirmTitle,
          text: confirmSubtitle,
          icon: "success",
          background: "#e6f1f1",
        });
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire({
          title: rejectTitle,
          text: rejectSubtitle,
          icon: "error",
          background: "#e6f1f1",
        });
      }
    });
}

function errorDialog(title, subtitle) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-blue fw-bold w-popup",
      cancelButton: "btn btn-blue fw-bold w-popup",
      popup: "border_round",
    },
    buttonsStyling: true,
  });
  swalWithBootstrapButtons.fire({
    title: title,
    text: subtitle,
    icon: "error",
    background: "#e6f1f1",
    showCancelButton: false,
    reverseButtons: false,
  });
}

function simpleDialog(title, subtitle) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-blue fw-bold w-popup",
      cancelButton: "btn btn-blue fw-bold w-popup",
      popup: "border_round",
    },
    buttonsStyling: true,
  });
  swalWithBootstrapButtons.fire({
    title: title,
    text: subtitle,
    icon: "success",
    background: "#e6f1f1",
    showCancelButton: false,
    reverseButtons: false,
  });
}

function qrCodeDialog(title, subtitle, link) {
  const widthHeight = 512;
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-blue fw-bold w-popup",
      cancelButton: "btn btn-blue fw-bold w-popup",
      popup: "border_round",
    },
    buttonsStyling: true,
  });
  return swalWithBootstrapButtons.fire({
    title: title,
    text: subtitle,
    html: `${
      subtitle != null ? "<div class='mx-auto mb-2'>" + subtitle + "</div>" : ""
    }<div id='qrcode' class='mx-auto' style='display: flex; justify-content: center; width: 256px;height: 256px;'></div>`,
    background: "#e6f1f1",
    confirmButtonText: "Scarica",
    cancelButtonText: "Chiudi",
    showCancelButton: true,
    reverseButtons: false,
    willOpen: () => {
      // Genera il QR code dopo che il popup viene mostrato
      new QRCode(document.getElementById("qrcode"), {
        text: link, // Inserisci il link o il testo desiderato
        width: widthHeight,
        height: widthHeight,
        colorDark: "#000000",
        colorLight: "#e6f1f1",
        correctLevel: QRCode.CorrectLevel.H,
      });
    },
  });
}
