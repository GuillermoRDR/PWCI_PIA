<?php require base_path('views/portal/layout.php');
inicioPortal($curso ? 'Editar curso' : 'Nuevo curso'); ?>
<section class="panel">
  <form action="<?= e(url('/curso-guardar')) ?>" method="post" enctype="multipart/form-data">
    <?php csrfPortal(); ?><input type="hidden" name="curso_id" value="<?= (int) ($curso['curso_id'] ?? 0) ?>">
    <label>Título<input name="titulo" required maxlength="200" minlength="3"
        value="<?= e($curso['titulo'] ?? '') ?>"></label><label>Descripción<textarea name="descripcion" required
        minlength="10"><?= e($curso['descripcion'] ?? '') ?></textarea></label>
    <label>Categoría<select name="categoria_id" required>
        <option value="">Elige una categoría</option>
        <?php foreach ($categorias as $cat):
          if ($cat['estado'] !== 'activa')
            continue; ?>
          <option value="<?= (int) $cat['categoria_id'] ?>" <?= (int) ($curso['categoria_id'] ?? 0) === (int) $cat['categoria_id'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?></option><?php endforeach; ?>
      </select></label>
    <label>Modalidad<select name="modalidad_precio">
        <option value="curso_completo" <?= ($curso['modalidad_precio'] ?? '') === 'curso_completo' ? 'selected' : '' ?>>
          Curso completo</option>
        <option value="por_nivel" <?= ($curso['modalidad_precio'] ?? '') === 'por_nivel' ? 'selected' : '' ?>>Por nivel
        </option>
      </select></label>
    <label>Precio del curso completo (MXN)<input type="number" name="precio" min="0" step="0.01"
        value="<?= e($curso['precio'] ?? '0') ?>" required></label>
    <p class="muted">Usa 0 para un curso completo gratuito. En modalidad por nivel, el precio se indica abajo.</p>
    <label>Imagen (URL o ruta de Firebase Storage)<input name="imagen_url" value="<?= e($curso['imagen_url'] ?? '') ?>"
        maxlength="2048"></label>
    <p class="muted">Si no hay imagen se muestra la portada predeterminada.</p>
    <label>O subir portada a Firebase Storage<input type="file" name="imagen_curso"
        accept="image/jpeg,image/png,image/webp"></label>
    <h2><?= $curso ? 'Agregar nivel (opcional)' : 'Primer nivel' ?></h2><label>Título del nivel<input
        name="niveles[0][titulo]" <?= $curso ? '' : 'required' ?>></label><label>Descripción<textarea
        name="niveles[0][descripcion]"></textarea></label><label>Video obligatorio del nivel (URL)<input type="url"
        name="niveles[0][video_url]" <?= $curso ? '' : 'required' ?>></label><label>Precio individual (MXN)<input
        type="number" name="niveles[0][precio]" min="0" step="0.01" value="0"></label><label>Texto adicional<textarea
        name="niveles[0][texto]"></textarea></label>
    <div class="grid"><label>PDF (URL)<input type="url" name="niveles[0][pdf]"></label><label>Imagen (URL)<input
          type="url" name="niveles[0][imagen]"></label><label>Enlace externo (URL)<input type="url"
          name="niveles[0][enlace]"></label></div>
    <p class="muted">Para agregar más niveles, edita el curso después de guardarlo. Los niveles existentes se conservan.
    </p><button>Guardar curso</button>
  </form>
</section><?php finPortal(); ?>