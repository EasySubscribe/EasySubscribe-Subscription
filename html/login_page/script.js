document.addEventListener("DOMContentLoaded", () => {
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
