function doSubscribeCancel(){
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-blue fw-bold",
          cancelButton: "btn btn-blue fw-bold",
          popup: "border_round",
        },
        buttonsStyling: true
      });
      swalWithBootstrapButtons.fire({
        title: "Sicuro di voler disdire l'abbonamento?",
        text: "Non sarai in grado di annulare una volta confermato!",
        icon: "warning",
        background: "#e6f1f1",
        showCancelButton: true,
        confirmButtonText: "Si Disdici!",
        cancelButtonText: "No, Annulla!",
        reverseButtons: false
      }).then((result) => {
        if (result.isConfirmed) {
          swalWithBootstrapButtons.fire({
            title: "Operazione Eseguita!",
            text: "Il tuo abbonamento è stato disdetto",
            icon: "success",
            background: "#e6f1f1"
          });
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire({
            title: "Operazione Annullata",
            text: "Abbonamento non disdetto",
            icon: "error",
            background: "#e6f1f1"
          });
        }
      });
}