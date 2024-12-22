const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

document.addEventListener("DOMContentLoaded", function () {
  const loader = document.getElementById("loader");
  loader.style.visibility = "hidden";

  const card = document.getElementById("card");
  const front = document.getElementById("front");
  const back = document.getElementById("back");
  const flipButton = document.getElementById("flipButton");
  const flipButtonDesktop = document.getElementById("flipButtonDesktop");
  const flipButtonBack = document.getElementById("flipButtonBack");
  back.style.display = "none";

  // Funzione per capovolgere la card
  function flipCard() {
    card.classList.toggle("flipped");

    // Nascondi o mostra le sezioni
    if (card.classList.contains("flipped")) {
      front.style.display = "none"; // Nascondi la sezione anteriore
      back.style.display = "block"; // Mostra la sezione posteriore
      flipButtonDesktop.textContent = "Back";
    } else {
      front.style.display = "block"; // Mostra la sezione anteriore
      back.style.display = "none"; // Nascondi la sezione posteriore
      flipButtonDesktop.textContent = "Login";
    }
  }
  flipButton.addEventListener("click", flipCard);
  flipButtonDesktop.addEventListener("click", flipCard);
  flipButtonBack.addEventListener("click", flipCard);

  /** Cambio colore al click del bottone */
  const buttons = document.querySelectorAll(".btn-blue");

  // Aggiungiamo listener a tutti i bottoni con la classe 'btn-blue'
  buttons.forEach((button) => {
    button.addEventListener("touchstart", () => {
      button.classList.add("active");
    });

    button.addEventListener("touchend", () => {
      button.classList.remove("active");
    });
  });

  // Bottone Email disabilitato
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  const emailInput = document.getElementById("submitEmail");
  const submitButton = document.getElementById("submitEmailBtn");
  const submitOrganizzationBtn = document.getElementById(
    "submitOrganizzationBtn"
  );

  emailInput.addEventListener("input", () => {
    submitButton.disabled =
      !emailInput.value.trim() || !emailRegex.test(emailInput.value); // trim() rimuove spazi vuoti
    submitOrganizzationBtn.disabled =
      !emailInput.value.trim() || !emailRegex.test(emailInput.value); // trim() rimuove spazi vuoti
  });
});

async function handleSubmit(event) {
  console.log(event, event.submitter);
  event.preventDefault();
  const emailInput = document.getElementById("submitEmail"); // Riferimento all'input
  const email = emailInput.value; // Ottieni il valore dell'input

  const loader = document.getElementById("loader");
  loader.style.visibility = "visible";

  const isValidEmail = emailRegex.test(email);
  const redirect_url = window.location.origin;

  if (isValidEmail && event.submitter.id === "submitEmailBtn") {
    try {
      const response = await fetch("inc/stripe/login-customers.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, redirect_url }),
      });
      const data = await response.json();

      loader.style.visibility = "hidden";
      validateResponse(data);
    } catch (error) {
      loader.style.visibility = "hidden";
      errorDialog(
        "Errore di rete",
        "Si è verificato un problema, riprova più tardi."
      );
    }
  } else if (isValidEmail && event.submitter.id === "submitOrganizzationBtn") {
    try {
      const response = await fetch("inc/stripe/login-collaborators.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, redirect_url }),
      });
      const data = await response.json();

      loader.style.visibility = "hidden";
      validateResponse(data);
    } catch (error) {
      loader.style.visibility = "hidden";
      errorDialog(
        "Errore di rete",
        "Si è verificato un problema, riprova più tardi."
      );
    }
  } else {
    loader.style.visibility = "hidden";
    errorDialog(
      "Email invalida",
      "Inserisci un'indirizzo email valido per accedere"
    );
  }

  function validateResponse(data) {
    loader.style.visibility = "hidden";
    if (!data.error) {
      simpleDialog(
        "Email inviata",
        "Controlla l'indirizzo email " + data.email + " per accedere"
      );
      emailInput.value = ""; // Svuota l'input dell'email
    } else if (data.error && data.code == "ERROR_STRIPE_404") {
      errorDialog(
        "Email non trovata",
        "L'email " + email + " non è registrata sul portale"
      );
    } else {
      errorDialog("Errore", "Si è verificato un problema, riprova più tardi.");
    }
  }
}
