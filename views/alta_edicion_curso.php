<?php require base_path('views/partials/legacy.php');
legacyStart($curso ? 'Editar curso' : 'Crear curso', 'alta_edicion_curso'); ?>
<div class="container">
  <h2 class="page-title"><?= $curso ? 'Editar curso' : 'Nuevo curso' ?></h2>
  <form id="courseForm" action="<?= e(url('/curso-guardar')) ?>" method="post" enctype="multipart/form-data">
    <?php csrfPortal(); ?><input type="hidden" name="curso_id" value="<?= (int) ($curso['curso_id'] ?? 0) ?>">
    <div class="form-card">
      <h3 class="section-title">Información general</h3>
      <div class="form-group"><label>Título *</label><input class="form-control" name="titulo" required minlength="3"
          maxlength="200" value="<?= e($curso['titulo'] ?? '') ?>"></div>
      <div class="form-grid">
        <div class="form-group"><label>Categoría *</label><select class="form-control" name="categoria_id" required>
            <option value="">Selecciona</option>
            <?php foreach ($categorias as $cat):
              if ($cat['estado'] !== 'activa')
                continue; ?>
              <option value="<?= (int) $cat['categoria_id'] ?>"
                <?= (int) ($curso['categoria_id'] ?? 0) === (int) $cat['categoria_id'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?>
              </option><?php endforeach; ?>
          </select></div>
        <div class="form-group"><label>Modalidad *</label><select class="form-control" name="modalidad_precio">
            <option value="curso_completo" <?= ($curso['modalidad_precio'] ?? '') === 'curso_completo' ? 'selected' : '' ?>>Curso
              completo</option>
            <option value="por_nivel" <?= ($curso['modalidad_precio'] ?? '') === 'por_nivel' ? 'selected' : '' ?>>Por nivel
            </option>
          </select></div>
      </div>
      <div class="form-group"><label>Precio completo (MXN)</label><input class="form-control" name="precio"
          type="number" min="0" step="0.01" value="<?= e($curso['precio'] ?? '0') ?>" required></div>
      <div class="form-group"><label>Descripción *</label><textarea class="form-control" name="descripcion"
          minlength="10" required><?= e($curso['descripcion'] ?? '') ?></textarea></div>
      <div class="form-group"><label>Portada JPG, PNG o WebP</label><input class="form-control" name="imagen_curso"
          type="file" accept="image/jpeg,image/png,image/webp"></div><input type="hidden" name="imagen_url"
        value="<?= e($curso['imagen_url'] ?? '') ?>">
    </div>
    <div class="form-card">
      <h3 class="section-title">Niveles y materiales</h3><?php if ($curso): ?>
        <p>Los niveles existentes se conservan; agrega niveles nuevos.</p>
        <?php foreach (($nivelesExistentes ?? []) as $nivel): ?>
          <p><strong><?= e($nivel['titulo']) ?></strong> · <?= e($nivel['precio']) ?> MXN</p><?php endforeach; endif; ?>
      <div id="levelsContainer"></div><button type="button" class="btn-add-level" id="addLevel">Agregar nivel</button>
    </div><button type="submit" class="btn-submit">Guardar curso</button>
  </form>
</div>
<template id="levelTemplate">
  <div class="level-box">
    <div class="level-header"><strong>Nivel <span class="level-number"></span></strong><button type="button"
        class="btn-remove-level">Quitar</button></div>
    <div class="form-grid">
      <div class="form-group"><label>Título *</label><input class="form-control" data-field="titulo" required
          minlength="3"></div>
      <div class="form-group"><label>Precio individual (MXN)</label><input class="form-control" data-field="precio"
          type="number" min="0" step="0.01" value="0"></div>
    </div>
    <div class="form-group"><label>Descripción</label><textarea class="form-control"
        data-field="descripcion"></textarea></div>
    <div class="form-group"><label>Video obligatorio: URL o MP4/WebM *</label><input class="form-control"
        data-field="video_url" type="url" placeholder="https://..."><input class="form-control" data-file="video"
        type="file" accept="video/mp4,video/webm"></div>
    <div class="form-group"><label>Texto de la lección</label><textarea class="form-control"
        data-field="texto"></textarea></div>
    <div class="form-grid">
      <div class="form-group"><label>PDF</label><input class="form-control" data-file="pdf" type="file"
          accept="application/pdf"></div>
      <div class="form-group"><label>Imagen</label><input class="form-control" data-file="imagen" type="file"
          accept="image/jpeg,image/png,image/webp"></div>
      <div class="form-group"><label>Archivo adicional (TXT, ZIP, audio, DOCX o PPTX)</label><input class="form-control"
          data-file="archivo" type="file" accept=".txt,.zip,.mp3,.wav,.docx,.pptx"></div>
      <div class="form-group"><label>Enlace externo</label><input class="form-control" data-field="enlace" type="url"
          placeholder="https://..."></div>
    </div>
  </div>
</template>
<script>(() => { const container = document.getElementById('levelsContainer'); let next = 0; function add() { const i = next++; const box = document.getElementById('levelTemplate').content.firstElementChild.cloneNode(true); box.querySelector('.level-number').textContent = i + 1; box.querySelectorAll('[data-field]').forEach(el => el.name = `niveles[${i}][${el.dataset.field}]`); box.querySelectorAll('[data-file]').forEach(el => el.name = `niveles_archivos[${i}][${el.dataset.file}]`); box.querySelector('.btn-remove-level').onclick = () => box.remove(); container.append(box) } document.getElementById('addLevel').onclick = add; if (!<?= $curso ? 'true' : 'false' ?>) add(); document.getElementById('courseForm').addEventListener('submit', ev => { if (!<?= $curso ? 'true' : 'false' ?> && !container.children.length) { ev.preventDefault(); Swal.fire('Falta un nivel', 'Agrega al menos un nivel.', 'warning'); return } for (const box of container.children) { if (!box.querySelector('[data-field="video_url"]').value && !box.querySelector('[data-file="video"]').files.length) { ev.preventDefault(); Swal.fire('Falta el video', 'Cada nivel necesita un video o una URL.', 'warning'); return } } }) })();</script>
<?php legacyEnd(); ?>