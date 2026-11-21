<?php require base_path('views/partials/legacy.php');
legacyStart('Buscar cursos', 'resultado_busqueda'); ?>
<div class="container">
  <h1 class="page-title">Buscar cursos</h1>
  <form action="<?= e(url('/buscar')) ?>" method="get" class="filter-form"><label>Curso o profesor<input
        class="form-control" name="q" value="<?= e($_GET['q'] ?? '') ?>"></label><label>Categoría<select
        class="form-control" name="categoria_id">
        <option value="">Todas</option><?php foreach ($categorias as $cat): ?>
          <option value="<?= (int) $cat['id'] ?>" <?= (string) ($_GET['categoria_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?></option><?php endforeach; ?>
      </select></label><label>Desde<input class="form-control" type="date" name="desde"
        value="<?= e($_GET['desde'] ?? '') ?>"></label><label>Hasta<input class="form-control" type="date" name="hasta"
        value="<?= e($_GET['hasta'] ?? '') ?>"></label><button class="btn-primary">Filtrar</button></form>
  <div class="courses-grid"><?php foreach ($cursosEncontrados as $curso): ?>
      <article class="course-card"><img src="<?= e(foto($curso['imagen'])) ?>" alt="Portada">
        <div class="course-card-body">
          <h3><?= e($curso['titulo']) ?></h3>
          <p><?= e($curso['instructor']) ?> · <?= e($curso['calificacion']) ?>/5</p>
          <p><?= e(mb_strimwidth($curso['descripcion'], 0, 120, '…')) ?></p>
          <p>
            <?= $curso['modalidad_precio'] === 'por_nivel' ? 'Precio por nivel' : '$' . number_format((float) $curso['precio'], 2) . ' MXN' ?>
          </p><a class="btn-primary" href="<?= e(url('/curso?id=' . (int) $curso['id'])) ?>">Ver curso</a>
        </div>
      </article><?php endforeach; ?>
  </div><?php if (!$cursosEncontrados): ?>
    <p>No hay cursos con estos filtros.</p><?php endif; ?>
</div><?php legacyEnd(); ?>