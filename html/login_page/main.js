const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

async function handleSubmit(event) {
  event.preventDefault(); // Previene il comportamento di default del form
  const email = document.getElementById("submitEmail").value;

  const isValidEmail = emailRegex.test(email);

  if (isValidEmail)
    simpleDialog("Email inviata", "Controlla la tua Email per accedere");
  else
    errorDialog(
      "Email invalida",
      "Inserisci un'indirizzo email valido per accedere"
    );

  // Esegui una richiesta POST sicura per inviare l'email al server PHP
  //const response = await fetch("/path/to/your/backend.php", {
  //    method: "POST",
  //    headers: {
  //        "Content-Type": "application/json"
  //    },
  //    body: JSON.stringify({ email })
  //});
  //const data = await response.json();
  //if (data.success) {
  //    alert("Email inviata con successo!");
  //} else {
  //    alert("Errore: " + data.message);
  //}
}
