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
          <div class="col-12">
            <h1>Contattami</h1>
            <span class="line d-flex mx-auto"></span>
          </div>
        </div>
      </section>

      <div class="container">
        <div class="card" id="card">
          <div class="row">
            <div class="col-6 p-3">
              <img src="../resume_page/example.jpeg" height="500" style="background-repeat: no-repeat; background-size: contain;
                  background-position-x: center;">
            </div>
            <div class="col-6">
              <h3 class="fw-bold mt-5">INFO</h3>
              <h1>Still Not Sure?</h1>
              <p>Do you have any questions? Feel free to write it in this form. We will reply to you as soon as possible.</p>
              <form>
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                  <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                  <label for="exampleInputPassword1" class="form-label">Password</label>
                  <input type="password" class="form-control" id="exampleInputPassword1">
                </div>
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="exampleCheck1">
                  <label class="form-check-label" for="exampleCheck1">Check me out</label>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php require __DIR__ . '/../../html/core/footer/footer.php'; ?>
  </body>
</html>
