<?php
$metricas = $metricas ?? [
  'total_ingresos' => 18450.00,
  'total_alumnos' => 124,
  'total_cursos' => 5,
  'promedio_calificacion' => 4.9
];

$categorias = $categorias ?? [
  ['id' => 1, 'nombre' => 'Programación Web'],
  ['id' => 2, 'nombre' => 'Diseño UI/UX'],
  ['id' => 3, 'nombre' => 'Bases de Datos']
];

$misCursos = $misCursos ?? [
  [
    'id' => 101,
    'titulo' => 'Desarrollo Web Full Stack con PHP y MySQL',
    'categoria' => 'Programación Web',
    'precio_completo' => 1250.00,
    'niveles_count' => 3,
    'alumnos_count' => 85,
    'estado' => 'activo'
  ],
  [
    'id' => 102,
    'titulo' => 'Diseño de Interfaces UI/UX con Figma',
    'categoria' => 'Diseño UI/UX',
    'precio_completo' => 900.00,
    'niveles_count' => 2,
    'alumnos_count' => 39,
    'estado' => 'inactivo'
  ]
];

$ventas = $ventas ?? [
  [
    'id' => 5001,
    'fecha' => '2026-02-18 14:20',
    'curso' => 'Desarrollo Web Full Stack con PHP y MySQL',
    'alumno' => 'Ana Martínez',
    'forma_pago' => 'PayPal',
    'monto' => 1250.00
  ],
  [
    'id' => 5002,
    'fecha' => '2026-02-19 09:15',
    'curso' => 'Desarrollo Web Full Stack con PHP y MySQL',
    'alumno' => 'Carlos López',
    'forma_pago' => 'PayPal',
    'monto' => 450.00
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
  <title>Panel de Control Instructor - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/panel_control.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy - Portal Instructor
    </a>
    <div class="nav-links">
      <a href="index.php?action=home" class="btn-link"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="index.php?action=mi_perfil" class="btn-link"><i class="fa-solid fa-user"></i> Mi Perfil</a>
      <a href="index.php?action=logearse" class="btn-link"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </div>
  </header>

  <div class="container">

    <div class="page-header">
      <h2 class="page-title"><i class="fa-solid fa-chart-line"></i> Panel de Control del Instructor</h2>
      <a href="index.php?action=alta_edicion_curso" class="btn-primary">
        <i class="fa-solid fa-circle-plus"></i> Crear Nuevo Curso
      </a>
    </div>

    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="kpi-data">
          <h4>Ingresos Totales</h4>
          <p>$<?= number_format($metricas['total_ingresos'], 2) ?></p>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
        <div class="kpi-data">
          <h4>Alumnos Inscritos</h4>
          <p><?= $metricas['total_alumnos'] ?></p>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-book-open-reader"></i></div>
        <div class="kpi-data">
          <h4>Cursos Creados</h4>
          <p><?= $metricas['total_cursos'] ?></p>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-star"></i></div>
        <div class="kpi-data">
          <h4>Calificación Promedio</h4>
          <p><?= number_format($metricas['promedio_calificacion'], 1) ?> / 5.0</p>
        </div>
      </div>
    </div>

    <div class="card-section">
      <h3><i class="fa-solid fa-layer-group"></i> Mis Cursos impartidos</h3>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Curso</th>
              <th>Categoría</th>
              <th>Niveles</th>
              <th>Precio Completo</th>
              <th>Alumnos</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($misCursos as $c): ?>
              <tr>
                <td><strong><?= htmlspecialchars($c['titulo']) ?></strong></td>
                <td><?= htmlspecialchars($c['categoria']) ?></td>
                <td><?= $c['niveles_count'] ?> niveles</td>
                <td>$<?= number_format($c['precio_completo'], 2) ?></td>
                <td><?= $c['alumnos_count'] ?></td>
                <td>
                  <?php if ($c['estado'] === 'activo'): ?>
                    <span class="badge badge-active">Activo</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="action-btns">
                    <a href="index.php?action=alta_edicion_curso&id=<?= $c['id'] ?>" class="btn-sm btn-edit" title="Editar curso y niveles">
                      <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>

                    <?php if ($c['estado'] === 'activo'): ?>
                      <a href="index.php?action=alta_edicion_curso" class="btn-sm btn-disable" onclick="return confirm('¿Deseas dar de baja este curso?');">
                        <i class="fa-solid fa-ban"></i> Desactivar
                      </a>
                    <?php else: ?>
                      <a href="index.php?action=alta_edicion_curso" class="btn-sm btn-enable">
                        <i class="fa-solid fa-check"></i> Activar
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-section">
      <h3><i class="fa-solid fa-receipt"></i> Reporte Detallado de Ventas e Ingresos</h3>

      <form action="index.php" method="GET" class="filter-form">
        <input type="hidden" name="action" value="instructor_dashboard">

        <div class="form-group">
          <label for="fecha_desde">Desde</label>
          <input type="date" id="fecha_desde" name="fecha_desde" class="form-control">
        </div>

        <div class="form-group">
          <label for="fecha_hasta">Hasta</label>
          <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control">
        </div>

        <div class="form-group">
          <label for="categoria_id">Categoría</label>
          <select id="categoria_id" name="categoria_id" class="form-control">
            <option value="">Todas</option>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="curso_id">Curso Específico</label>
          <select id="curso_id" name="curso_id" class="form-control">
            <option value="">Todos los cursos</option>
            <?php foreach ($misCursos as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['titulo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn-primary" style="height: 36px; padding: 0 12px; justify-content: center;">
          <i class="fa-solid fa-filter"></i> Filtrar
        </button>
      </form>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID Venta</th>
              <th>Fecha</th>
              <th>Curso</th>
              <th>Alumno</th>
              <th>Forma de Pago</th>
              <th>Monto Recibido</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($ventas)): ?>
              <?php foreach ($ventas as $v): ?>
                <tr>
                  <td>#<?= $v['id'] ?></td>
                  <td><?= htmlspecialchars($v['fecha']) ?></td>
                  <td><strong><?= htmlspecialchars($v['curso']) ?></strong></td>
                  <td><?= htmlspecialchars($v['alumno']) ?></td>
                  <td>
                    <i class="fa-solid <?= $v['forma_pago'] === 'PayPal' ? 'fa-brands fa-paypal' : 'fa-brands fa-paypal' ?>"></i>
                    <?= htmlspecialchars($v['forma_pago']) ?>
                  </td>
                  <td style="font-weight: bold; color: green;">+$<?= number_format($v['monto'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align: center; opacity: 0.8;">No hay ventas registradas en el periodo seleccionado.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy. Todos los derechos reservados.</p>
  </footer>

</body>

</html>