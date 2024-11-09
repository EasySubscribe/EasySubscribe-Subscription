<?php
/* Template Name: QRCode Page */
?>
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php require __DIR__ . '/../../html/core/script/script.php'; ?>
    <link rel="stylesheet" href="style.css" />
    <script src="script.js"></script>
    <!-- Include the html5-qrcode library -->
    <script
      src="https://unpkg.com/html5-qrcode"
      type="text/javascript"
    ></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <title>QR Code Reader</title>
  </head>
  <body>
    <?php require __DIR__ . '/../../html/core/header/header.php'; ?>

    <div class="content mx-auto">
      <h1 class="m-4 text-center" style="text-shadow: h-shadow v-shadow blur-radius #111">Scansiona QRCode</h1>
      <button id="startScan" type="button" class="btn btn-blue mt-5" onclick="openModalStatic()">
        Scansiona
      </button>
      <div id="reader" style="display: none"></div>
    </div>

    <button
      type="button"
      class="btn btn-primary"
      id="openResultScan"
      style="display: none"
      data-bs-toggle="modal"
      data-bs-target="#exampleModal"
    >
    </button>
    <div
      class="modal fade"
      id="exampleModal"
      tabindex="-1"
      aria-labelledby="modalQRCodeResult"
      aria-hidden="true"
    >
      <div class="modal-dialog mx-auto modal-fullscreen" id="modal-content">
      </div>
    </div>
    <?php require __DIR__ . '/../../html/core/footer/footer.php'; ?>
  </body>
</html>
