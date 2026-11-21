<?php require base_path('views/partials/legacy.php');
legacyStart('Página no encontrada', 'styles'); ?>
<main class="content" style="max-width:700px;margin:4rem auto;text-align:center">
  <h1>Página no encontrada</h1>
  <p>La dirección solicitada no existe.</p><a class="btn btn-primary" href="<?= e(url('/')) ?>">Volver al inicio</a>
</main><?php legacyEnd(); ?>