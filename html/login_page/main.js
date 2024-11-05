const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

async function handleSubmit(event) {
  event.preventDefault();
  const emailInput = document.getElementById("submitEmail"); // Riferimento all'input
  const email = emailInput.value; // Ottieni il valore dell'input

  const isValidEmail = emailRegex.test(email);

  if (isValidEmail) {
    try {
      const response = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });
      const data = await response.json();

      if (!data.error) {
        simpleDialog(
          "Email inviata",
          "Controlla l'indirizzo email " + data.email + " per accedere"
        );
        emailInput.value = ""; // Svuota l'input dell'email
      } else {
        errorDialog(
          "Email non trovata",
          "L'email " + email + " non è registrata sul portale"
        );
      }
    } catch (error) {
      errorDialog(
        "Errore di rete",
        "Si è verificato un problema, riprova più tardi."
      );
    }
  } else {
    errorDialog(
      "Email invalida",
      "Inserisci un'indirizzo email valido per accedere"
    );
  }
}
