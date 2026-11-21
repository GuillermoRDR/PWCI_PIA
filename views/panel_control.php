<?php require base_path('views/partials/legacy.php');
legacyStart('Panel del instructor', 'panel_control'); ?>
<div class="container">
  <div class="page-header">
    <h1 class="page-title">Panel del instructor</h1><a class="btn-primary" href="<?= e(url('/curso-nuevo')) ?>">Crear
      curso</a>
  </div>
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-data">
        <h4>Cursos</h4>
        <p><?= count($cursos) ?></p>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-data">
        <h4>Ingresos filtrados</h4>
        <p>$<?= number_format(array_sum(array_column($ventas, 'ingresos')), 2) ?></p>
      </div>
    </div>
  </div>
  <section class="card-section">
    <h2>Mis cursos</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Curso</th>
            <th>Niveles</th>
            <th>Alumnos</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody><?php foreach ($cursos as $c): ?>
            <tr>
              <td><?= e($c['titulo']) ?></td>
              <td><?= (int) $c['niveles_count'] ?></td>
              <td><?= (int) $c['alumnos_count'] ?></td>
              <td><?= e($c['estado']) ?></td>
              <td>
                <div class="action-btns"><a class="btn-sm btn-edit"
                    href="<?= e(url('/curso-editar?id=' . (int) $c['curso_id'])) ?>">Editar</a>
                  <form method="post" action="<?= e(url('/curso-baja')) ?>" data-confirmar="¿Dar de baja este curso?">
                    <?php csrfPortal(); ?><input type="hidden" name="curso_id" value="<?= (int) $c['curso_id'] ?>"><button
                      class="btn-sm btn-disable">Dar de baja</button></form>
                </div>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  <section class="card-section">
    <h2>Ventas por curso</h2>
    <form class="filter-form" action="<?= e(url('/profesor')) ?>" method="get"><label>Desde<input class="form-control"
          name="desde" type="date" value="<?= e($filtros['desde'] ?? '') ?>"></label><label>Hasta<input
          class="form-control" name="hasta" type="date"
          value="<?= e($filtros['hasta'] ?? '') ?>"></label><label>Categoría<select class="form-control"
          name="categoria_id">
          <option value="">Todas</option><?php foreach ($categorias as $cat): ?>
            <option value="<?= (int) $cat['categoria_id'] ?>"
              <?= (int) ($filtros['categoria_id'] ?? 0) === (int) $cat['categoria_id'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?>
            </option><?php endforeach; ?>
        </select></label><label>Estado<select class="form-control" name="activos">
          <option value="">Todos</option>
          <option value="1" <?= ($filtros['activos'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
        </select></label><button class="btn-primary">Filtrar</button></form>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Curso</th>
            <th>Creación</th>
            <th>Alumnos</th>
            <th>Avance promedio</th>
            <th>Ingresos</th>
            <th>Detalle</th>
          </tr>
        </thead>
        <tbody><?php foreach ($ventas as $v): ?>
            <tr>
              <td><?= e($v['titulo']) ?></td>
              <td><?= e(fechaPortal($v['creado_en'])) ?></td>
              <td><?= (int) $v['alumnos'] ?></td>
              <td><?= number_format((float) $v['avance_promedio'], 1) ?> niveles</td>
              <td>$<?= number_format((float) $v['ingresos'], 2) ?></td>
              <td><a href="<?= e(url('/ventas-detalle?id=' . (int) $v['curso_id'])) ?>">Ver alumnos</a></td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <h3>Total $<?= number_format(array_sum(array_column($ventas, 'ingresos')), 2) ?> MXN</h3>
  </section>
  <section class="card-section">
    <h2>Ingresos por método</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Método</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody><?php foreach ($metodos as $m): ?>
            <tr>
              <td><?= e($m['metodo']) ?></td>
              <td>$<?= number_format((float) $m['total'], 2) ?></td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div><?php legacyEnd(); ?>