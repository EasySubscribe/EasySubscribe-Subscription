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
});
