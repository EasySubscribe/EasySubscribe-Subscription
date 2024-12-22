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
    <?php require __DIR__ . '/../inc/shared.php'; ?>
    <link rel="stylesheet" href="../assets/css/contact-me.css" />
    <script src="../assets/js/contact-me.js"></script>
    <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <title>Contact - NeverlandKiz</title>
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
            <h1>Still <span class="color-header">Not Sure?</span></h1>
            <span class="line d-flex mx-auto"></span>
          </div>
        </div>
      </section>

      <div class="container fade-in">
        <div class="card" id="card">
          <div class="row m-0">
            <div class="col-12 col-md-5 p-0">
              <div id="contact-preview"></div>
              <!--<img src="../resume_page/example.jpeg" height="500" style="background-repeat: no-repeat; background-size: contain;
                  background-position-x: center;">-->
            </div>
            <div class="col-1 separator hidden_mobile"></div>
            <div class="col-12 col-md-6 my-auto">
              <div class="contact-container">
                <h3 class="fw-bold color-header">Contattami</h3>
                <p>Do you have any questions? Feel free to write it in this form. We will reply to you as soon as possible.</p>
                <form id="emailForm" onsubmit="handleSubmit(event)">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" placeholder="Mario">
                    <label for="name" id="test">Name</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" placeholder="name@example.com">
                    <label for="email">Email address</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="phone" placeholder="name@example.com">
                    <label for="phone">Phone Number</label>
                  </div>
                  <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder="Leave a comment here" id="description" style="height: 100px"></textarea>
                    <label for="description">Comments</label>
                  </div>
                  <button
                    id="submitEmailBtn"
                    type="submit"
                    class="btn btn-blue fw-bold mt-3 float-end w-150"
                    disabled
                  >
                    Invia
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../inc/footer.php'; ?>
  </body>
</html>
