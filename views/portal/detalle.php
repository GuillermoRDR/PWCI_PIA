<?php require base_path('views/portal/layout.php');
inicioPortal($curso['titulo'], $aviso); ?>
<nav class="acciones panel" aria-label="Categorías"><?php foreach ($categorias as $categoria): ?><a
      class="boton secundario"
      href="<?= e(url('/buscar?categoria_id=' . (int) $categoria['id'])) ?>"><?= e($categoria['nombre']) ?></a><?php endforeach; ?>
</nav>
<section class="panel detalle"><img src="<?= e(foto($curso['imagen'])) ?>"
    onerror="this.src='<?= e(url('/images/placeholder-curso.svg')) ?>'" alt="Portada del curso">
  <div>
    <p><?= nl2br(e($curso['descripcion'])) ?></p>
    <p>Profesor: <?= e($curso['instructor']) ?> · Calificación: <?= e($curso['promedio_calificacion']) ?>/5</p>
    <?php if ($curso['modalidad_precio'] === 'curso_completo'): ?>
      <p class="etiqueta">
        <?= (float) $curso['precio_completo'] > 0 ? '$' . number_format((float) $curso['precio_completo'], 2) . ' MXN' : 'Gratis' ?>
      </p>
      <?php if ($alumno && !$acceso): ?>
        <form method="post" action="<?= e(url('/inscribir')) ?>"><?php csrfPortal(); ?><input type="hidden" name="curso_id"
            value="<?= (int) $curso['id'] ?>"><?php if ((float) $curso['precio_completo'] > 0): ?><label for="metodo">Forma de
              pago</label><select id="metodo" name="metodo">
              <option value="transferencia">Transferencia</option>
              <option value="paypal">PayPal</option>
            </select>
            <p class="muted">La solicitud requiere confirmación manual. No se realiza un cargo en línea.</p>
          <?php endif; ?><button><?= (float) $curso['precio_completo'] > 0 ? 'Solicitar inscripción' : 'Inscribirme' ?></button>
        </form><?php endif; ?>
    <?php else: ?>
      <p class="etiqueta">Compra por nivel</p><?php endif; ?>
    <?php if ($certificado): ?><a class="boton" href="<?= e(url('/certificado?curso_id=' . (int) $curso['id'])) ?>">Ver
        certificado</a><?php endif; ?>
    <?php if (!$alumno): ?>
      <p><a class="boton" href="<?= e(url('/login')) ?>">Ingresa para inscribirte</a></p><?php endif; ?>
  </div>
</section>
<section class="panel">
  <h2>Niveles</h2><?php if (!$niveles): ?>
    <p class="vacio">Todavía no hay niveles.</p><?php endif; ?>
  <?php foreach ($niveles as $nivel):
    $tiene = in_array((string) $nivel['id'], array_map('strval', $accesosNivel), true); ?>
    <div class="nivel">
      <h3><?= (int) $nivel['numero'] ?>. <?= e($nivel['titulo']) ?></h3>
      <p><?= e($nivel['descripcion']) ?></p>
      <?php if ($tiene): ?>
        <?php if ($nivel['video_url']): ?>
          <form method="post" target="_blank" action="<?= e(url('/nivel-abrir')) ?>"><?php csrfPortal(); ?><input
              type="hidden" name="nivel_id" value="<?= (int) $nivel['id'] ?>"><input type="hidden" name="curso_id"
              value="<?= (int) $curso['id'] ?>"><button>Abrir video</button></form><?php endif; ?>
        <?php foreach ($nivel['archivos'] as $archivo): ?>      <?php if ($archivo['tipo'] === 'texto'): ?>
            <p><?= nl2br(e($archivo['texto'])) ?></p><?php elseif ($archivo['url'] !== '#'): ?>
            <p><a target="_blank" rel="noopener noreferrer" href="<?= e($archivo['url']) ?>"><?= e($archivo['nombre']) ?></a>
            </p><?php endif; ?><?php endforeach; ?>
        <?php if (in_array((string) $nivel['id'], array_map('strval', $progreso), true)): ?><span
            class="etiqueta">Completado</span><?php else: ?>
          <form method="post" action="<?= e(url('/nivel-completar')) ?>"><?php csrfPortal(); ?><input type="hidden"
              name="nivel_id" value="<?= (int) $nivel['id'] ?>"><input type="hidden" name="curso_id"
              value="<?= (int) $curso['id'] ?>"><button>Marcar completado</button></form><?php endif; ?>
      <?php elseif ($curso['modalidad_precio'] === 'por_nivel' && $alumno): ?>
        <p class="etiqueta">
          <?= (float) $nivel['precio'] > 0 ? '$' . number_format((float) $nivel['precio'], 2) . ' MXN' : 'Gratis' ?></p>
        <form method="post" action="<?= e(url('/solicitar-nivel')) ?>"><?php csrfPortal(); ?><input type="hidden"
            name="curso_id" value="<?= (int) $curso['id'] ?>"><input type="hidden" name="nivel_id"
            value="<?= (int) $nivel['id'] ?>"><?php if ((float) $nivel['precio'] > 0): ?><label>Forma de pago<select
                name="metodo">
                <option value="transferencia">Transferencia</option>
                <option value="paypal">PayPal</option>
              </select></label>
            <p class="muted">Confirmación manual del administrador.</p>
          <?php endif; ?><button><?= (float) $nivel['precio'] > 0 ? 'Solicitar nivel' : 'Inscribirme gratis' ?></button>
        </form>
      <?php else: ?><span class="muted">Inscríbete para acceder.</span><?php endif; ?>
    </div><?php endforeach; ?>
</section>
<section class="panel">
  <h2>Opiniones</h2>
  <div id="comentarios" data-placeholder="<?= e(url('/images/placeholder-curso.svg')) ?>"
    data-url="<?= e(url('/api/v1/cursos/' . (int) $curso['id'] . '/comentarios')) ?>">
    <?php foreach ($comentarios as $comentario): ?>
      <div class="mensaje"><img class="avatar" src="<?= e(foto($comentario['avatar'])) ?>"
          onerror="this.src='<?= e(url('/images/placeholder-curso.svg')) ?>'"
          alt=""><strong><?= e($comentario['alumno']) ?></strong><?= $comentario['calificacion'] === null ? '' : ' · ' . (int) $comentario['calificacion'] . '/5' ?><small>
          · <?= e(fechaPortal($comentario['creado_en'])) ?></small>
        <p><?= e($comentario['comentario']) ?></p>
      </div><?php endforeach; ?><?php if (!$comentarios): ?>
      <p>Sin opiniones todavía.</p><?php endif; ?>
  </div>
  <?php if ($certificado): ?>
    <h3>Deja tu opinión</h3>
    <form id="form-comentario" method="post" action="<?= e(url('/comentar')) ?>"><?php csrfPortal(); ?><input
        type="hidden" name="curso_id" value="<?= (int) $curso['id'] ?>"><label>Calificación<select
          name="calificacion"><?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>"><?= $i ?>/5</option><?php endfor; ?>
        </select></label><label>Comentario<textarea name="comentario" required minlength="3"
          maxlength="2000"></textarea></label><button>Publicar</button></form><?php endif; ?>
</section>
<script src="<?= e(url('/js/curso.js')) ?>" defer></script><?php finPortal(); ?>