<?php require base_path('views/portal/layout.php');
inicioPortal('Certificado'); ?>
<div class="acciones"><button type="button" onclick="window.print()">Imprimir o guardar PDF</button></div>
<?php $tieneFondo = is_file(base_path('public/images/certificado-fondo.jpg')); ?>
<section class="panel certificado<?= $tieneFondo ? ' certificado-con-fondo' : '' ?>" aria-label="Certificado del curso">
  <?php if ($tieneFondo): ?>
    <img class="certificado-fondo" src="<?= e(url('/images/certificado-fondo.jpg')) ?>" alt="">
  <?php endif; ?>
  <div class="certificado-contenido">
    <div class="certificado-marca"><span aria-hidden="true">🎓</span> Kdemy</div>
    <p>Kdemy certifica que</p>
    <h1><?= e($certificado['nombre_alumno_historico']) ?></h1>
    <p>completó satisfactoriamente el curso</p>
    <h2><?= e($certificado['nombre_curso_historico']) ?></h2>
    <p>Impartido por <?= e($certificado['nombre_certificador_historico']) ?></p>
    <p>Fecha de emisión: <?= e($certificado['emitido_en']) ?></p>
    <small>Folio <?= e($certificado['folio']) ?></small>
  </div>
</section>
<?php finPortal(); ?>