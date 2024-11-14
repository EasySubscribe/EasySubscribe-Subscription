<?php
/* Template Name: Resume Page */
?>
<html lang="it-IT" data-lt-installed="true">
  <head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex, nofollow, noarchive" />

    <link rel="shortcut icon" href="favicon.ico" />
    <meta
      content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"
      name="viewport"
    />
    <?php require __DIR__ . '/../../html/core/script/script.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <link rel="stylesheet" href="./style.css" />
    <script src="./script.js"></script>
    <script src="./main.js"></script>
    <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <title>Abbonamenti - NeverlandKiz</title>
  </head>
  <body>
    <?php require __DIR__ . '/../../html/core/header/header.php'; ?>

    <!-- Loader -->
    <div id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>
    
    <div class="content mx-auto">
      <h1 class="m-4 text-center" id="resume-title">Benvenuto</h1>
      <span class="line d-flex mx-auto"></span>
      <div class="row mt-3 justify-content-center" id="subscriptions"></div>
      <p hidden class="text-center">Mock</p>
      <div hidden class="row mx-auto mt-3 justify-content-center">
        <div class="col-12 col-md-3 mt-3 fade-in">
          <div class="card text-center" id="card">
            <img
              class="mx-auto m-3"
              src="https://www.neverlandkiz.it/wp-content/uploads/2024/05/PHOTO-2024-05-16-18-41-34.jpg"
              alt=""
              width="100"
            />
            <h4>All you can Dance - Italy</h4>
            <p class="fw-light">
              <small style="color: #808080"
                >Neverland - Symposium - KizMi - KIMA</small
              >
            </p>
            <p>Attiva dal: 28/10/2024 <br />Si rinnova il: 28/10/2024</p>

            <div class="col-12 text-center">
              <a
                type="button"
                class="btn btn-blue fw-bold mb-3 ms-3 me-3"
                target="_blank"
                href="https://billing.stripe.com/p/login/test_9AQ2967en0pcgnK4gg"
                >Ottieni il QRCode</a
              >
              <a
                type="button"
                class="btn btn-blue text-danger fw-bold mb-3 ms-3 me-3"
                target="_blank"
                onclick="doSubscribeCancel()"
              >
                Cancella Abbonamento
              </a>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3 mt-3 fade-in">
          <div class="card text-center" id="card">
            <img
              class="mx-auto m-3"
              src="https://www.neverlandkiz.it/wp-content/uploads/2024/05/PHOTO-2024-05-16-18-41-34.jpg"
              alt=""
              width="100"
            />
            <h4>All you can Dance - Italy</h4>
            <p class="fw-light">
              <small style="color: #808080"
                >Neverland - Symposium - KizMi - KIMA</small
              >
            </p>
            <p>Attiva dal: 28/10/2024 <br />Si rinnova il: 28/10/2024</p>

            <div class="col-12 text-center">
              <a
                type="button"
                class="btn btn-blue fw-bold mb-3 ms-3 me-3"
                target="_blank"
                href="https://billing.stripe.com/p/login/test_9AQ2967en0pcgnK4gg"
                >Ottieni il QRCode</a
              >
              <a
                type="button"
                class="btn btn-blue text-danger fw-bold mb-3 ms-3 me-3"
                target="_blank"
                onclick="doSubscribeCancel()"
              >
                Cancella Abbonamento
              </a>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3 mt-3 fade-in">
          <div class="card text-center" id="card">
            <img
              class="mx-auto m-3"
              src="https://www.neverlandkiz.it/wp-content/uploads/2024/05/PHOTO-2024-05-16-18-41-34.jpg"
              alt=""
              width="100"
            />
            <h4>All you can Dance - Italy</h4>
            <p class="fw-light">
              <small style="color: #808080"
                >Neverland - Symposium - KizMi - KIMA</small
              >
            </p>
            <p>Attiva dal: 28/10/2024 <br />Si rinnova il: 28/10/2024</p>

            <div class="col-12 text-center">
              <a
                type="button"
                class="btn btn-blue fw-bold mb-3 ms-3 me-3"
                target="_blank"
                href="https://billing.stripe.com/p/login/test_9AQ2967en0pcgnK4gg"
                >Ottieni il QRCode</a
              >
              <a
                type="button"
                class="btn btn-blue text-danger fw-bold mb-3 ms-3 me-3"
                target="_blank"
                onclick="doSubscribeCancel()"
              >
                Cancella Abbonamento
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../../html/core/footer/footer.php'; ?>
  </body>
</html>
