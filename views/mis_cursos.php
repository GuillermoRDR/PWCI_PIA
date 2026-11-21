<?php require base_path('views/partials/legacy.php');
legacyStart('Mi Kardex', 'mis_cursos'); ?>
<div class="container">
  <h1 class="page-title">Mis cursos inscritos</h1>
  <div class="filter-card">
    <h2>Filtrar cursos</h2>
    <form class="filter-form" action="<?= e(url('/alumno')) ?>" method="get"><label>Categoría<select
          class="form-control" name="categoria_id">
          <option value="">Todas</option><?php foreach ($categorias as $cat): ?>
            <option value="<?= (int) $cat['categoria_id'] ?>"
              <?= (int) ($filtros['categoria_id'] ?? 0) === (int) $cat['categoria_id'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?>
            </option><?php endforeach; ?>
        </select></label><label>Inscripción desde<input class="form-control" name="desde" type="date"
          value="<?= e($filtros['desde'] ?? '') ?>"></label><label>Hasta<input class="form-control" name="hasta"
          type="date" value="<?= e($filtros['hasta'] ?? '') ?>"></label><label>Terminación<select class="form-control"
          name="terminados">
          <option value="">Todos</option>
          <option value="1" <?= ($filtros['terminados'] ?? '') === '1' ? 'selected' : '' ?>>Terminados</option>
        </select></label><label>Estado<select class="form-control" name="activos">
          <option value="">Todos</option>
          <option value="1" <?= ($filtros['activos'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
        </select></label><button class="btn-filter">Filtrar</button></form>
  </div>
  <div class="courses-grid">
    <?php foreach ($cursos as $c):
      $total = (int) $c['total_niveles'];
      $avance = $total ? min(100, round(100 * (int) $c['niveles_completados'] / $total)) : 0; ?>
      <article class="course-card"><img src="<?= e(foto($c['imagen_url'])) ?>" alt="Portada">
        <div class="course-card-body">
          <h2 class="course-card-title"><?= e($c['titulo']) ?></h2>
          <div class="course-meta-info"><span>Inscripción: <?= e(fechaPortal($c['inscrito_en'])) ?></span><span>Última
              actividad: <?= e(fechaPortal($c['ultima_actividad'])) ?></span><span>Terminación:
              <?= e(fechaPortal($c['completada_en'])) ?></span><span>Estado: <?= e($c['inscripcion_estado']) ?> /
              <?= e($c['curso_estado']) ?></span></div>
          <div class="progress-container">
            <div class="progress-label"><span>Progreso</span><span><?= $avance ?>%</span></div>
            <div class="progress-bar-bg">
              <div class="progress-bar-fill" style="width:<?= $avance ?>%"></div>
            </div>
          </div><?php if ($c['completada_en']): ?><a class="btn-cert"
              href="<?= e(url('/certificado?curso_id=' . (int) $c['curso_id'])) ?>">Ver
              certificado</a><?php endif; ?><?php if ($c['curso_estado'] === 'activo'): ?><a class="btn-action"
              href="<?= e(url('/curso?id=' . (int) $c['curso_id'])) ?>">Continuar curso</a><?php endif; ?>
        </div>
      </article><?php endforeach; ?>
  </div><?php if (!$cursos): ?>
    <p>No hay cursos con esos filtros.</p><?php endif; ?>
</div><?php legacyEnd(); ?>