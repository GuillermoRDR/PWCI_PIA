<?php require base_path('views/partials/legacy.php');
legacyStart($curso['titulo'], 'detalle_curso'); ?>
<div class="container">
  <nav class="course-meta"><?php foreach ($categorias as $cat): ?><a class="btn-link"
        href="<?= e(url('/buscar?categoria_id=' . (int) $cat['id'])) ?>"><?= e($cat['nombre']) ?></a> <?php endforeach; ?>
  </nav>
  <div class="course-header"><img class="course-banner-img" src="<?= e(foto($curso['imagen'])) ?>" alt="Portada">
    <div class="course-header-content">
      <h1 class="course-title"><?= e($curso['titulo']) ?></h1>
      <div class="course-meta"><span>Instructor: <?= e($curso['instructor']) ?></span><span class="rating-stars">★
          <?= number_format((float) $curso['promedio_calificacion'], 1) ?>/5</span><span><?= count($niveles) ?>
          niveles</span></div>
      <p><?= nl2br(e($curso['descripcion'])) ?></p>
    </div>
  </div>
  <div class="course-layout">
    <main class="main-section">
      <div class="card-box">
        <h3>Contenido y niveles</h3>
        <?php foreach ($niveles as $nivel):
          $tiene = in_array((string) $nivel['id'], array_map('strval', $accesosNivel), true); ?>
          <div class="level-card">
            <div class="level-header"><strong><?= (int) $nivel['numero'] ?>. <?= e($nivel['titulo']) ?></strong></div>
            <p><?= e($nivel['descripcion']) ?></p>
            <?php if ($tiene): ?>
              <?php if (!empty($nivel['video_url'])):
                $videoHost = strtolower((string) parse_url((string) $nivel['video_url'], PHP_URL_HOST));
                $youtubeId = null;
                if (in_array($videoHost, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be'], true)) {
                  $youtubePath = trim((string) parse_url((string) $nivel['video_url'], PHP_URL_PATH), '/');
                  parse_str((string) parse_url((string) $nivel['video_url'], PHP_URL_QUERY), $youtubeQuery);
                  $youtubeId = $videoHost === 'youtu.be' ? $youtubePath : ($youtubeQuery['v'] ?? (str_starts_with($youtubePath, 'shorts/') ? substr($youtubePath, 7) : null));
                  if (!is_string($youtubeId) || !preg_match('/^[A-Za-z0-9_-]{11}$/', $youtubeId))
                    $youtubeId = null;
                }
                $videoEsArchivo = str_starts_with((string) $nivel['video_url'], 'cursos/recursos/') || (bool) preg_match('/\.(mp4|webm)(?:\?.*)?$/i', (string) $nivel['video_url']);
                ?>
                <div class="level-video">
                  <?php if ($youtubeId): ?>
                    <iframe src="https://www.youtube-nocookie.com/embed/<?= e($youtubeId) ?>"
                      title="Video del nivel <?= e($nivel['titulo']) ?>" loading="lazy"
                      allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                      allowfullscreen></iframe>
                  <?php elseif ($videoEsArchivo): ?>
                    <video controls preload="none" playsinline src="<?= e(url('/nivel-video?id=' . (int) $nivel['id'])) ?>">Tu
                      navegador no puede reproducir este video.</video>
                  <?php else: ?>
                    <a class="btn-buy" target="_blank" rel="noopener noreferrer"
                      href="<?= e(url('/nivel-video?id=' . (int) $nivel['id'])) ?>">Ver video del nivel</a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              <?php if ($nivel['archivos']): ?>
                <h4>Materiales</h4>
                <div class="level-resources"><?php foreach ($nivel['archivos'] as $archivo): ?>
                    <?php if ($archivo['tipo'] === 'texto'): ?>
                      <article class="resource-card resource-text"><span class="resource-type">Texto de la lección</span>
                        <p><?= nl2br(e($archivo['texto'])) ?></p>
                      </article>
                    <?php else: ?>
                      <a class="resource-card resource-link" target="_blank" rel="noopener noreferrer"
                        href="<?= e(url('/recurso?id=' . (int) $archivo['id'])) ?>"><span
                          class="resource-type"><?= e(strtoupper($archivo['tipo'])) ?></span><strong><?= e($archivo['nombre'] ?: 'Abrir material') ?></strong><span
                          class="resource-action">Abrir archivo ↗</span></a>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div><?php endif; ?>
              <?php if (in_array((string) $nivel['id'], array_map('strval', $progreso), true)): ?>
                <p>✓ Nivel completado</p><?php else: ?>
                <form method="post" action="<?= e(url('/nivel-completar')) ?>"><?php csrfPortal(); ?><input type="hidden"
                    name="nivel_id" value="<?= (int) $nivel['id'] ?>"><input type="hidden" name="curso_id"
                    value="<?= (int) $curso['id'] ?>"><button type="submit" class="btn-buy">Marcar completado</button></form>
              <?php endif; ?>
            <?php elseif ($alumno && $curso['modalidad_precio'] === 'por_nivel'): ?>
              <p>$<?= number_format((float) $nivel['precio'], 2) ?> MXN</p>
              <form action="<?= e(url('/solicitar-nivel')) ?>" method="post"><?php csrfPortal(); ?><input type="hidden"
                  name="curso_id" value="<?= (int) $curso['id'] ?>"><input type="hidden" name="nivel_id"
                  value="<?= (int) $nivel['id'] ?>"><?php if ((float) $nivel['precio'] > 0): ?><label>Forma de pago<select
                      name="metodo">
                      <option value="transferencia">Transferencia</option>
                    </select></label><?php endif; ?><button class="btn-buy"
                  type="submit"><?= (float) $nivel['precio'] > 0 ? 'Solicitar nivel' : 'Inscribirme gratis' ?></button>
              </form><?php if ((float) $nivel['precio'] > 0): ?>
                <form action="<?= e(url('/paypal-iniciar')) ?>" method="post"><?php csrfPortal(); ?><input type="hidden"
                    name="curso_id" value="<?= (int) $curso['id'] ?>"><input type="hidden" name="nivel_id"
                    value="<?= (int) $nivel['id'] ?>"><button class="btn-buy" type="submit">Pagar nivel con PayPal
                    Sandbox</button></form><?php endif; ?><?php else: ?>
              <p>🔒 Inscríbete para acceder.</p><?php endif; ?>
          </div><?php endforeach; ?>
      </div>
      <div class="card-box">
        <h3>Comentarios de alumnos</h3>
        <div id="comentarios" data-url="<?= e(url('/api/v1/cursos/' . (int) $curso['id'] . '/comentarios')) ?>"
          data-placeholder="<?= e(url('/images/placeholder-curso.svg')) ?>"><?php foreach ($comentarios as $item): ?>
            <div class="comment-item"><strong><?= e($item['alumno']) ?></strong> ·
              <?= e(fechaPortal($item['creado_en'])) ?>
              <p><?= e($item['comentario']) ?></p>
            </div><?php endforeach; ?>
        </div><?php if ($certificado): ?>
          <form id="form-comentario" method="post" action="<?= e(url('/comentar')) ?>"><?php csrfPortal(); ?><input
              type="hidden" name="curso_id" value="<?= (int) $curso['id'] ?>"><label>Calificación<select
                name="calificacion"><?php for ($n = 5; $n >= 1; $n--): ?>
                  <option value="<?= $n ?>"><?= $n ?>/5</option><?php endfor; ?>
              </select></label><label>Opinión<textarea name="comentario" required minlength="3"
                maxlength="2000"></textarea></label><button class="btn-buy">Publicar</button></form><?php endif; ?>
      </div>
    </main>
    <aside class="side-section">
      <div class="card-box">
        <h3><?= $curso['modalidad_precio'] === 'por_nivel' ? 'Compra por nivel' : 'Curso completo' ?></h3>
        <?php if ($curso['modalidad_precio'] === 'curso_completo'): ?>
          <div class="price-tag">$<?= number_format((float) $curso['precio_completo'], 2) ?> MXN</div>
          <?php if ($alumno && !$acceso): ?>
            <form action="<?= e(url('/inscribir')) ?>" method="post"><?php csrfPortal(); ?><input type="hidden"
                name="curso_id"
                value="<?= (int) $curso['id'] ?>"><?php if ((float) $curso['precio_completo'] > 0): ?><label>Forma de
                  pago<select name="metodo">
                    <option value="transferencia">Transferencia</option>
                  </select></label><?php endif; ?><button
                class="btn-buy"><?= (float) $curso['precio_completo'] > 0 ? 'Solicitar curso' : 'Inscribirme gratis' ?></button>
            </form><?php if ((float) $curso['precio_completo'] > 0): ?>
              <form action="<?= e(url('/paypal-iniciar')) ?>" method="post"><?php csrfPortal(); ?><input type="hidden"
                  name="curso_id" value="<?= (int) $curso['id'] ?>"><button class="btn-buy" type="submit">Pagar curso con
                  PayPal Sandbox</button></form><?php endif; ?><?php elseif ($acceso): ?>
            <p>Ya estás inscrito.</p><?php endif; ?><?php else: ?>
          <p>Elige el nivel que quieres adquirir en la lista.</p><?php endif; ?><?php if (!$alumno): ?><a class="btn-buy"
            href="<?= e(url('/login')) ?>">Ingresa para inscribirte</a><?php endif; ?><?php if ($certificado): ?><a
            class="btn-buy" href="<?= e(url('/certificado?curso_id=' . (int) $curso['id'])) ?>">Ver
            certificado</a><?php endif; ?>
      </div>
    </aside>
  </div>
</div>
<script src="<?= e(url('/js/curso.js')) ?>" defer></script><?php legacyEnd(); ?>