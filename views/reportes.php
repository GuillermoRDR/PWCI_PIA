<?php
$tipoReporte = $_GET['tipo_usuario'] ?? 'instructor';

$categorias = $categorias ?? [
  ['id' => 1, 'nombre' => 'Programación Web'],
  ['id' => 2, 'nombre' => 'Diseño UI/UX'],
  ['id' => 3, 'nombre' => 'Bases de Datos']
];

$reporteInstructores = $reporteInstructores ?? [
  [
    'nombre' => 'Carlos Mendoza',
    'fecha_ingreso' => '2025-03-15',
    'cursos_ofrecidos' => 4,
    'total_ganancias' => 24500.00
  ],
  [
    'nombre' => 'Laura Fernández',
    'fecha_ingreso' => '2025-06-20',
    'cursos_ofrecidos' => 2,
    'total_ganancias' => 12800.00
  ]
];

$reporteEstudiantes = $reporteEstudiantes ?? [
  [
    'nombre' => 'Ana Martínez',
    'fecha_ingreso' => '2026-01-10',
    'cursos_inscritos' => 3,
    'porcentaje_terminado' => 100.00
  ],
  [
    'nombre' => 'Jorge Gómez',
    'fecha_ingreso' => '2026-02-01',
    'cursos_inscritos' => 5,
    'porcentaje_terminado' => 60.00
  ]
];

$error = $error ?? null;
$mensaje = $mensaje ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reportes y Analytics - Administración</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/reportes.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-user-shield"></i> Portal Admin
    </a>
    <div class="nav-links">
      <a href="index.php?action=home" class="btn-link"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="index.php?action=logearse" class="btn-link"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </div>
  </header>

  <div class="container">

    <div class="page-header">
      <h2 class="page-title">
        <i class="fa-solid fa-chart-column"></i> Reportes
      </h2>
      <button onclick="window.print()" class="btn-print">
        <i class="fa-solid fa-print"></i> Imprimir / Exportar PDF
      </button>
    </div>

    <div class="filter-card">
      <h3><i class="fa-solid fa-sliders"></i> Parámetros del Reporte</h3>
      <form action="index.php" method="GET" class="filter-form">
        <input type="hidden" name="action" value="admin_reports">

        <div class="form-group">
          <label for="tipo_usuario">Tipo de Usuario *</label>
          <select name="tipo_usuario" id="tipo_usuario" class="form-control" onchange="this.form.submit()">
            <option value="instructor" <?= $tipoReporte === 'instructor' ? 'selected' : '' ?>>Instructores</option>
            <option value="estudiante" <?= $tipoReporte === 'estudiante' ? 'selected' : '' ?>>Estudiantes</option>
          </select>
        </div>

        <div class="form-group">
          <label for="categoria_id">Categoría de Interés</label>
          <select name="categoria_id" id="categoria_id" class="form-control">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="fecha_desde">Fecha Ingreso (Desde)</label>
          <input type="date" id="fecha_desde" name="fecha_desde" class="form-control">
        </div>

        <div class="form-group">
          <label for="fecha_hasta">Fecha Ingreso (Hasta)</label>
          <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control">
        </div>

        <button type="submit" class="btn-print" style="justify-content: center; height: 38px;">
          <i class="fa-solid fa-magnifying-glass"></i> Generar
        </button>
      </form>
    </div>

    <div class="card-table">

      <?php if ($tipoReporte === 'instructor'): ?>
        <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-chalkboard-user"></i> Reporte Detallado: Instructores</h3>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Nombre Completo</th>
                <th>Fecha de Ingreso</th>
                <th>Cursos Ofrecidos</th>
                <th>Total de Ganancias</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($reporteInstructores)): ?>
                <?php foreach ($reporteInstructores as $ins): ?>
                  <tr>
                    <td><?= htmlspecialchars($ins['nombre']) ?></td>
                    <td><?= htmlspecialchars($ins['fecha_ingreso']) ?></td>
                    <td><i class="fa-solid fa-book"></i> <?= $ins['cursos_ofrecidos'] ?> curso(s)</td>
                    <td style="font-weight: bold; color: green;">$<?= number_format($ins['total_ganancias'], 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" style="text-align: center; opacity: 0.8;">No se encontraron registros de instructores.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      <?php else: ?>
        <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-user-graduate"></i> Reporte Detallado: Estudiantes</h3>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Nombre Completo</th>
                <th>Fecha de Ingreso</th>
                <th>Cursos Inscritos</th>
                <th>% Cursos Terminados</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($reporteEstudiantes)): ?>
                <?php foreach ($reporteEstudiantes as $est): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($est['usuario']) ?></strong></td>
                    <td><?= htmlspecialchars($est['nombre']) ?></td>
                    <td><?= htmlspecialchars($est['fecha_ingreso']) ?></td>
                    <td><i class="fa-solid fa-graduation-cap"></i> <?= $est['cursos_inscritos'] ?> curso(s)</td>
                    <td>
                      <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: <?= $est['porcentaje_terminado'] ?>%;"></div>
                      </div>
                      <strong><?= number_format($est['porcentaje_terminado'], 1) ?>%</strong>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" style="text-align: center; opacity: 0.8;">No se encontraron registros de estudiantes.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy - Panel de Administración.</p>
  </footer>

</body>

</html>