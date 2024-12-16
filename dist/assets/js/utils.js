const formatDateIntl = (inputDate) => {
  const [day, month, year] = inputDate.split("/");
  const date = new Date(`${year}-${month}-${day}`);

  return new Intl.DateTimeFormat("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  }).format(date);
};

function getExpiredDate(element, fullDate, daysToRemove = 0) {
  let meta = element.product.metadata;

  if (meta) {
    let monthsToAdd = parseInt(element.product.metadata.durata_minima, 10); // Assicurati che sia un numero
    if (monthsToAdd != undefined || monthsToAdd != null) {
      const createdDate = new Date(element.subscriptions.created * 1000); // Timestamp in secondi
      const expirationDate = new Date(createdDate); // Crea una copia separata

      expirationDate.setMonth(expirationDate.getMonth() + monthsToAdd); // Aggiungi i mesi

      // Rimuovi i giorni se `daysToRemove` è definito e maggiore di 0
      if (daysToRemove > 0) {
        expirationDate.setDate(expirationDate.getDate() - daysToRemove);
      }

      // Restituisci il risultato in base a `fullDate`
      if (fullDate) return expirationDate;
      return expirationDate.toLocaleDateString();
    }
  }
}

function isIOS() {
  return /iPhone|iPad|iPod/i.test(navigator.userAgent);
}
