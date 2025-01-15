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
      flipButtonDesktop.textContent = translations.back_button;
    } else {
      front.style.display = "block"; // Mostra la sezione anteriore
      back.style.display = "none"; // Nascondi la sezione posteriore
      flipButtonDesktop.textContent = translations.access_button;
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
  //const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
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
  event.preventDefault();
  const emailInput = document.getElementById("submitEmail"); // Riferimento all'input
  const email = emailInput.value; // Ottieni il valore dell'input

  const loader = document.getElementById("loader");
  loader.style.visibility = "visible";

  const isValidEmail = emailRegex.test(email);
  const redirect_url = window.location.origin;
  const apiUrl = getApiBaseUrl("stripe");

  console.log("API URL:", apiUrl);

  if (isValidEmail && event.submitter.id === "submitEmailBtn") {
    try {
      const response = await fetch(apiUrl + "login-customers.php", {
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
        translations.network_error_title,
        translations.network_error_text
      );
    }
  } else if (isValidEmail && event.submitter.id === "submitOrganizzationBtn") {
    try {
      const response = await fetch(apiUrl + "login-collaborators.php", {
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
        translations.network_error_title,
        translations.network_error_text
      );
    }
  } else {
    loader.style.visibility = "hidden";
    errorDialog(
      translations.invalid_email_title,
      translations.invalid_email_text
    );
  }

  function validateResponse(data) {
    loader.style.visibility = "hidden";
    if (!data.error) {
      simpleDialog(
        translations.sent_email_title,
        translations.sent_email_text.replace("#EMAIL#", data.email)
      );
      emailInput.value = ""; // Svuota l'input dell'email
    } else if (data.error && data.code == "ERROR_STRIPE_404") {
      errorDialog(
        translations.email_not_found_title,
        translations.email_not_found_text.replace("#EMAIL#", email)
      );
    } else {
      errorDialog(
        translations.generic_error_title,
        translations.generic_error_text
      );
    }
  }
}
