<?php
/* Template Name: Login Page */
?>
<html lang="it-IT" data-lt-installed="true">
  <head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, nofollow, noarchive" />

    <link rel="shortcut icon" href="https://www.easysubscribe.it/wp-content/uploads/2025/01/easy.png" />
    <meta
      content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"
      name="viewport"
    />
    <?php require __DIR__ . '/../inc/shared.php'; ?>
    <link rel="stylesheet" href="../assets/css/manager.css" />
    <script src="../assets/js/manager.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <title>Manager - EasySubscribe</title>
  </head>
  <body>
  <?php require __DIR__ . '/../inc/header.php'; ?>

    <div id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>

    <div class="content">
      <section id="login-btn" class="mx-auto">
        <div class="row">
          <div class="col-12 fade-in" style="margin-bottom: -30px">
            <h1>Gestione <span class="color-header">Utenti</span></h1>
            <span class="line d-flex mx-auto"></span>
          </div>
        </div>
      </section>

      <div class="container fade-in">
      <div class="row">
        <div class="col-12 col-md-6">
            <div class="card ms-5 me-5 mb-5 zoom" id="card">
              <div class="card-body d-flex flex-column align-items-center justify-content-center fade-in">
                <!-- Icona -->
                <i class="fa-solid fa-person fa-3x mb-3 mt-2 text-primary bounce"></i>

                <!-- Titolo -->
                <h5 class="card-title mb-3">Clienti Attivi</h5>

                <!-- Numero -->
                <div class="display-2 text-success" id="active-count">0</div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="card ms-5 me-5 mb-5 zoom" id="card">
              <div class="card-body d-flex flex-column align-items-center justify-content-center fade-in">
                <!-- Icona -->
                <i class="fa-solid fa-coins fa-3x mb-3 mt-2 text-warning bounce"></i>

                <!-- Titolo -->
                <h5 class="card-title mb-3">Profitti del Mese</h5>

                <!-- Numero -->
                <div class="display-2 text-success" id="profit-amount">€ 0.00</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card" id="card">
            <div class="m-4 table-responsive">
                <div id="subscription-container" class="text-center">Nessun Utente Attivo</div>
            </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
