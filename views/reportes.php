<?php require base_path('views/partials/legacy.php');
legacyStart('Reporte de usuarios', 'reportes');
$tipo = in_array($_GET['tipo_usuario'] ?? 'profesor', ['alumno', 'profesor'], true) ? $_GET['tipo_usuario'] : 'profesor'; ?>
<div class="container">
  <div class="page-header">
    <h1 class="page-title">Reporte de usuarios activos</h1><button class="btn-print" type="button"
      onclick="window.print()">Imprimir</button>
  </div>
  <div class="filter-card">
    <form method="get" action="<?= e(url('/reportes')) ?>" class="filter-form"><label>Rol<select class="form-control"
          name="tipo_usuario">
          <option value="profesor" <?= $tipo === 'profesor' ? 'selected' : '' ?>>Instructor</option>
          <option value="alumno" <?= $tipo === 'alumno' ? 'selected' : '' ?>>Alumno</option>
        </select></label><button class="btn-print">Aplicar</button></form>
  </div>
  <div class="card-table">
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Ingreso</th><?php if ($tipo === 'profesor'): ?>
              <th>Cursos ofrecidos</th>
              <th>Ganancias</th><?php else: ?>
              <th>Cursos inscritos</th>
              <th>Terminados</th><?php endif; ?>
          </tr>
        </thead>
        <tbody><?php foreach ($reporte as $u):
          if ($u['rol'] !== $tipo)
            continue; ?>
            <tr>
              <td><?= e($u['nombre_usuario']) ?></td>
              <td><?= e($u['nombre_completo']) ?></td>
              <td><?= e(fechaPortal($u['registrado_en'])) ?></td><?php if ($tipo === 'profesor'): ?>
                <td><?= (int) $u['cursos'] ?></td>
                <td>$<?= number_format((float) $u['ganancias'], 2) ?> MXN</td><?php else: ?>
                <td><?= (int) $u['inscripciones'] ?></td>
                <td><?= $u['inscripciones'] ? round(100 * (int) $u['terminados'] / (int) $u['inscripciones']) : 0 ?>%</td>
              <?php endif; ?>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div><?php legacyEnd(); ?>