<?php
/* Template Name: Login Page */
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
    <link rel="stylesheet" href="./style.css" />
    <script src="./script.js"></script>
    <script src="./main.js"></script>
    <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <title>Login - NeverlandKiz</title>
  </head>
  <body>
    <?php require __DIR__ . '/../../html/core/header/header.php'; ?>

    <!-- Loader -->
    <div id="loader" class="loader">
      <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
        <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
      </svg>
    </div>

    <div class="content">
      <section id="login-btn" class="mx-auto">
        <div class="row">
          <div class="col-12 fade-in">
            <h1>Welcome to NeverlandKiz</h1>
          </div>
        </div>
        <div class="col-12 text-center mt-2 hidden_desktop">
          <button
            id="flipButtonDesktop"
            type="button"
            class="btn btn-blue fw-bold ms-2 w-150"
            target="_blank"
          >
            Login
          </button>
        </div>
      </section>

      <div class="container fade-in">
        <div class="card" id="card">
          <div id="front" class="front">
            <div class="row">
              <div
                class="col col-md-2 hidden_mobile separator"
                style="align-self: center"
              >
                <div class="col-12 text-center">
                  <button
                    id="flipButton"
                    type="button"
                    class="btn btn-blue fw-bold m-3"
                    target="_blank"
                  >
                    Login
                  </button>
                </div>
              </div>
              <div class="col-12 col-md-10">
              <stripe-pricing-table pricing-table-id="prctbl_1QO5F5Run27ngB3YRsFdHCSS"
                publishable-key="pk_test_51Q5WXyRun27ngB3YbfBEWlPiAtjmSa5RFXOaF2HwQl1MD44Df3cFWVxy40LAlg0MrtTSKHDzbpENZDGO3JamWsCC00bm73xoRN">
              </stripe-pricing-table>
              <stripe-pricing-table pricing-table-id="prctbl_1QCmSdRun27ngB3Ya01NPXNY"
                publishable-key="pk_test_51Q5WXyRun27ngB3YbfBEWlPiAtjmSa5RFXOaF2HwQl1MD44Df3cFWVxy40LAlg0MrtTSKHDzbpENZDGO3JamWsCC00bm73xoRN">
              </stripe-pricing-table>
              </div>
            </div>
          </div>
          <div id="back" class="back">
            <div class="row">
              <div
                class="col col-md-2 hidden_mobile separator"
                style="align-self: center"
              >
                <div class="col-12 text-center">
                  <button
                    id="flipButtonBack"
                    type="button"
                    class="btn btn-blue fw-bold m-3"
                    target="_blank"
                  >
                    Back
                  </button>
                </div>
              </div>
              <div class="col-12 col-md-10">
                <div class="m-3">
                  <h2>Ottieni il Pass</h2>
                  <p>
                    Inserisci la tua email per visualizzare o scaricare il Pass.
                  </p>
                  <form id="emailForm" onsubmit="handleSubmit(event)">
                    <div class="mb-3 mt-3 mx-auto">
                      <label for="exampleInputEmail1" class="form-label"
                        >Email address</label
                      >
                      <input
                        type="email"
                        class="form-control"
                        id="submitEmail"
                        aria-describedby="emailHelp"
                        required
                      />
                      <div id="emailHelp" class="form-text">
                        Inserisci la tua email per accedere al portale.
                      </div>
                      <button
                        id="submitEmailBtn"
                        type="submit"
                        class="btn btn-blue fw-bold mt-3 mb-3 float-end w-150"
                        style="margin-left: 20px;"
                        disabled
                      >
                        Accedi
                      </button>
                      <button
                        id="submitOrganizzationBtn"
                        class="btn btn-blue fw-bold mt-3 mb-3 float-end"
                        style="width: 200px"
                        disabled
                      >
                        Sei un Organizzatore?
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../../html/core/footer/footer.php'; ?>
  </body>
</html>
