document.addEventListener("DOMContentLoaded", function () {
  const apiUrl = getApiBaseUrl(incType.STRIPE_FROM_TEMPLATE);
  const baseUrl = getApiBaseUrl(incType.BASE_URL);

  const loader = document.getElementById("loader");
  const urlParams = new URLSearchParams(window.location.search);
  let data = urlParams.get("data");

  if (data) localStorage.setItem("_manager_data", data);
  else data = localStorage.getItem("_manager_data");

  if (data) {
    // Decodifica il dato da base64
    const decodedData = atob(data); // Decodifica base64
    const [product_ids_string, session_id, email] = decodedData.split(":"); // Supponiamo che siano separati da ':'
    const subscriptionsSection = document.getElementById("subscriptions");

    // Rimuovi il parametro 'data' dall'URL
    const cleanUrl = window.location.origin + window.location.pathname;
    window.history.replaceState(null, "", cleanUrl);

    fetch(apiUrl + "manager.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        product_ids_string,
        session_id,
        email,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        loader.style.display = "none";
        if (data.error) {
          errorDialog(
            translations.manager_generic_error_title,
            translations.manager_generic_error_text
          );
        } else {
          if (data.data && data.data.length > 0) {
            renderTable(data.data, email);
          } else {
            errorDialog(
              translations.manager_generic_error_title,
              translations.manager_subscription_error_text
            );
          }
        }
      })
      .catch((error) => {
        loader.style.display = "none";
        console.error("Errore durante la richiesta:", error);
        errorDialog(
          translations.manager_generic_error_title,
          translations.manager_request_error_text
        );
      });
  } else {
    loader.style.display = "none";
    errorDialog(
      translations.manager_generic_error_title,
      translations.manager_generic_error_text
    ).then((result) => {
      window.location.href = baseUrl;
    });
  }

  function renderTable(subscriptions, email) {
    const tableContainer = document.getElementById("subscription-container");
    let tableHTML = `
      <table id="example1" class="display fade-in" data-page-length='10'>
        <thead>
          <tr>
            <th>${translations.manager_table_element_1}</th>
            <th>${translations.manager_table_element_2}</th>
            <th>${translations.manager_table_element_3}</th>
            <th class="text-end">${translations.manager_table_element_4}</th>
          </tr>
        </thead>
        <tbody>`;

    let total_earn = 0.0;

    subscriptions.forEach((item) => {
      // Parse dei metadata come JSON (se esiste il campo email_organizzatori)
      const emailOrganizzatori = JSON.parse(
        item.product.metadata.email_organizzatori || "[]"
      );

      // Filtra gli oggetti che contengono la specifica email
      const filteredEmail = emailOrganizzatori.filter(
        (or) => or.email === email
      );

      // Somma gli amount degli oggetti filtrati
      filteredEmail.forEach((or) => {
        total_earn += or.amount;
      });

      total_earn = Math.round(total_earn * 100) / 100;

      // Generazione della tabella HTML
      tableHTML += `
                <tr>
                  <td class="copy-data" data-copy='${
                    item.subscription.customer.name
                  }'>${item.subscription.customer.name}</td>
                  <td class="copy-data" data-copy='${
                    item.subscription.customer.email
                  }'>${item.subscription.customer.email}</td>
                  <td>${item.product.name}</td>
                  <td class="text-end">${formatDateIntl(
                    new Date(
                      item.subscription.created * 1000
                    ).toLocaleDateString()
                  )}</td>
                </tr>`;
    });

    tableHTML += `
        </tbody>
      </table>`;

    tableContainer.innerHTML = tableHTML;

    const clienti_totali = document.getElementById("active-count");
    clienti_totali.textContent = subscriptions.length;

    const profit_amount = document.getElementById("profit-amount");
    profit_amount.textContent = "€ " + total_earn;

    document.querySelectorAll(".copy-data").forEach((button) => {
      const productName = button.getAttribute("data-copy");
      button.addEventListener("click", () => copyName(productName));
    });

    // Inizializza la tabella DataTable dopo aver inserito il codice HTML
    new DataTable("#example1", {
      responsive: true,
      lengthMenu: [10, 15, 25, 50, 100], // Opzioni della select
      //dom: '<"top"lfB>rt<"bottom"ip>', // Separazione logica degli elementi
      layout: {
        bottom: {
          buttons: ["csv", "excel", "pdf"],
          //buttons: ["csv", "excel", "pdf", "print"],
        },
      },
      order: [], // Non specifica nessun ordinamento iniziale
      paging: true,
      searching: true,
      ordering: true,
      info: true,
    });
    // Seleziona tutti i bottoni generati da DataTables
    const exportButtons = document.querySelectorAll(".dt-buttons button");

    // Applica la classe Neverland a ciascun bottone
    exportButtons.forEach((button) => {
      button.style.width = "100px";
      button.style.marginBottom = "10px";
      button.classList.add("btn-blue"); // Aggiunge la classe 'btn-blue'
    });
  }

  // Funzione di copia che viene chiamata tramite onclick
  function copyName(name) {
    const selBox = document.createElement("textarea");
    selBox.style.position = "fixed";
    selBox.style.left = "0";
    selBox.style.top = "0";
    selBox.style.opacity = "0";
    selBox.value = name;
    document.body.appendChild(selBox);
    selBox.focus();
    selBox.select();
    document.execCommand("copy");
    document.body.removeChild(selBox);
    toastMessage("success", translations.manager_table_copy);
  }
});
