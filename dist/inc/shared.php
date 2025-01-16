<?php
// Verifica se siamo su WordPress
if (defined('ABSPATH')) {
    // Percorsi per WordPress
    $base_url = get_stylesheet_directory_uri(); // URL del tema figlio
    $is_local = false;
} else {
    // Percorsi per lo sviluppo locale
    $base_url = '/../dist';
    $is_local = true;
}
?>
<!-- CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
/>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>

<!-- File personalizzati -->
<script src="<?php echo $base_url; ?>/assets/js/sweetalert.js"></script>
<script type="module" src="<?php echo $base_url; ?>/assets/js/utils.js"></script>
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css" />
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/animation.css" />

<?php if ($is_local): ?>
<script src="<?php echo $base_url; ?>/assets/js/translation.js"></script>
<?php endif; ?>
