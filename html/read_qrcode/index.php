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
    <script src="./script.js"></script>
    <!-- Include the html5-qrcode library -->
    <script
      src="https://unpkg.com/html5-qrcode"
      type="text/javascript"
    ></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <!--<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>-->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <!-- loading ZXingBrowser via UNPKG -->
    <script type="text/javascript" src="https://unpkg.com/@zxing/browser@latest"></script>
    <title>QR Code Reader</title>
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
      <h1 class="m-4 text-center" style="text-shadow: h-shadow v-shadow blur-radius #111">
        Scansiona QRCode
      </h1>
      <div class="card text-center fade-in" id="card">
        <video id="reader" class="mx-auto" style="display: none; border-radius: 20px; height: auto; width: auto; max-height: 300px; max-width: 300px"></video>
      </div>
      <div id="result-scan"></div>
      <button id="startScan" type="button" class="btn btn-blue mt-4 fade-in" onclick="startScan()">
        Scansiona
      </button>
    </div>

    <?php require __DIR__ . '/../../html/core/footer/footer.php'; ?>
  </body>
</html>
