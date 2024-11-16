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
    <title>Contact - NeverlandKiz</title>
  </head>
  <body>
    <?php require __DIR__ . '/../../html/core/header/header.php'; ?>

    <div class="content">
      <section id="login-btn" class="mx-auto">
        <div class="row">
          <div class="col-12" style="margin-bottom: -30px">
            <h1>Contattami</h1>
            <span class="line d-flex mx-auto"></span>
          </div>
        </div>
      </section>

      <div class="container">
        <div class="card" id="card">
          <div class="row m-0">
            <div class="col-12 col-md-5 pe-0 pe-md-3" id="contact-preview">
              <!--<img src="../resume_page/example.jpeg" height="500" style="background-repeat: no-repeat; background-size: contain;
                  background-position-x: center;">-->
            </div>
            <div class="col-1 separator hidden_mobile"></div>
            <div class="col-12 col-md-6">
              <div class="contact-container">
                <h3 class="fw-bold color-header">INFO</h3>
                <h1>Still <span class="color-header">Not Sure?</span></h1>
                <p>Do you have any questions? Feel free to write it in this form. We will reply to you as soon as possible.</p>
                <form>
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" onclick="getData()">
                    <label for="floatingInput" id="test">Name</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Email address</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Phone Number</label>
                  </div>
                  <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                    <label for="floatingTextarea2">Comments</label>
                  </div>
                  <button
                    id="submitEmailBtn"
                    type="submit"
                    class="btn btn-blue fw-bold mt-3 mb-4 float-end w-150"
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
    <?php require __DIR__ . '/../../html/core/footer/footer.php'; ?>
  </body>
</html>
