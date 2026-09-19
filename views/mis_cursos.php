<?php
$categorias = $categorias ?? [
  ['id' => 1, 'nombre' => 'Programación Web'],
  ['id' => 2, 'nombre' => 'Diseño UI/UX'],
  ['id' => 3, 'nombre' => 'Bases de Datos']
];

$misCursos = $misCursos ?? [
  [
    'id' => 101,
    'titulo' => 'Desarrollo Web Full Stack con PHP y MySQL',
    'imagen' => 'https://placehold.co/400x200/ffffff/D9A05B',
    'categoria' => 'Programación Web',
    'fecha_inscripcion' => '2026-01-10',
    'ultimo_acceso' => '2026-02-18 16:45',
    'progreso' => 100,
    'calificacion_dada' => 5,
    'comentario_dado' => 'Excelente curso, aprendí bastante sobre arquitectura MVC.'
  ],
  [
    'id' => 102,
    'titulo' => 'Diseño de Interfaces UI/UX con Figma',
    'imagen' => 'https://placehold.co/400x200/ffffff/D9A05B',
    'categoria' => 'Diseño UI/UX',
    'fecha_inscripcion' => '2026-02-01',
    'ultimo_acceso' => '2026-02-20 10:15',
    'progreso' => 45,
    'calificacion_dada' => null,
    'comentario_dado' => null
  ],
  [
    'id' => 103,
    'titulo' => 'React para Desarrollo Web',
    'imagen' => 'https://placehold.co/400x200/ffffff/D9A05B',
    'categoria' => 'Programación Web',
    'fecha_inscripcion' => '2025-08-01',
    'ultimo_acceso' => '2025-10-05 10:15',
    'progreso' => 0,
    'calificacion_dada' => null,
    'comentario_dado' => null
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
  <title>Mis Cursos - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/mis_cursos.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>
    <div class="nav-links">
      <a href="index.php?action=home" class="btn-link"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="index.php?action=mi_perfil" class="btn-link"><i class="fa-solid fa-user"></i> Mi Perfil</a>
      <a href="index.php?action=logearse" class="btn-link"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </div>
  </header>

  <div class="container">
    <h2 class="page-title"><i class="fa-solid fa-book-bookmark"></i> Mis Cursos Inscritos</h2>

    <div class="filter-card">
      <h3><i class="fa-solid fa-filter"></i> Filtrar Mis Cursos</h3>
      <form action="index.php" method="GET" class="filter-form">
        <input type="hidden" name="action" value="kardex">

        <div class="form-group">
          <label for="categoria">Categoría</label>
          <select name="categoria_id" id="categoria" class="form-control">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="fecha_desde">Fecha Inscripción (Desde)</label>
          <input type="date" id="fecha_desde" name="fecha_desde" class="form-control">
        </div>

        <div class="form-group">
          <label for="fecha_hasta">Fecha Inscripción (Hasta)</label>
          <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control">
        </div>

        <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Filtrar</button>
      </form>
    </div>

    <div class="courses-grid">
      <?php if (!empty($misCursos)): ?>
        <?php foreach ($misCursos as $curso): ?>
          <div class="course-card">
            <img src="<?= htmlspecialchars($curso['imagen']) ?>" alt="Imagen del curso">
            <div class="course-card-body">
              <h3 class="course-card-title"><?= htmlspecialchars($curso['titulo']) ?></h3>

              <div class="course-meta-info">
                <span><i class="fa-solid fa-folder"></i> Categoría: <?= htmlspecialchars($curso['categoria']) ?></span>
                <span><i class="fa-solid fa-calendar-plus"></i> Inscrito el: <?= htmlspecialchars($curso['fecha_inscripcion']) ?></span>
                <span><i class="fa-solid fa-clock-rotate-left"></i> Último acceso: <?= htmlspecialchars($curso['ultimo_acceso']) ?></span>
              </div>

              <div class="progress-container">
                <div class="progress-label">
                  <span>Progreso</span>
                  <span><?= $curso['progreso'] ?>%</span>
                </div>
                <div class="progress-bar-bg">
                  <div class="progress-bar-fill" style="width: <?= $curso['progreso'] ?>%;"></div>
                </div>
              </div>

              <?php if ($curso['progreso'] == 100): ?>
                <div class="completed-section">
                  <a href="index.php?action=download_certificate&curso_id=<?= $curso['id'] ?>" class="btn-cert">
                    <i class="fa-solid fa-file-pdf"></i> Descargar Constancia
                  </a>

                  <?php if ($curso['calificacion_dada']): ?>
                    <div style="font-size: 0.85rem; color: var(--color-btn); text-align: center;">
                      <i class="fa-solid fa-star"></i> Ya evaluaste este curso (<?= $curso['calificacion_dada'] ?>/5)
                    </div>
                  <?php else: ?>
                    <button class="btn-filter" style="width: 100%;" onclick="openRatingModal(<?= $curso['id'] ?>, '<?= htmlspecialchars($curso['titulo']) ?>')">
                      <i class="fa-solid fa-star"></i> Evaluar Curso
                    </button>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <a href="index.php?action=course_detail&id=<?= $curso['id'] ?>" class="btn-action">
                <i class="fa-solid fa-circle-play"></i> Continuar Curso
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; text-align: center; font-size: 1.1rem; opacity: 0.8;">
          No tienes cursos inscritos o ningún curso coincide con los filtros seleccionados.
        </p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal para Dejar Calificación y Comentario -->
  <div id="ratingModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="closeRatingModal()">&times;</span>
      <h3 id="modalCourseTitle">Evaluar Curso</h3>
      <form action="index.php?action=add_review" method="POST">
        <input type="hidden" id="modal_curso_id" name="curso_id" value="">

        <div class="form-group" style="margin-bottom: 15px;">
          <label for="calificacion">Calificación (1 al 5)</label>
          <select name="calificacion" id="calificacion" class="form-control" required>
            <option value="5">5 - Excelente</option>
            <option value="4">4 - Muy Bueno</option>
            <option value="3">3 - Regular</option>
            <option value="2">2 - Malo</option>
            <option value="1">1 - Muy Malo</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
          <label for="comentario">Comentario / Reseña</label>
          <textarea name="comentario" id="comentario" class="form-control" rows="4" placeholder="Escribe tu opinión sobre el contenido y el instructor..." required></textarea>
        </div>

        <button type="submit" class="btn-filter" style="width: 100%;">Enviar Evaluación</button>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy. Todos los derechos reservados.</p>
  </footer>

  <script>
    function openRatingModal(cursoId, cursoTitulo) {
      document.getElementById('modal_curso_id').value = cursoId;
      document.getElementById('modalCourseTitle').textContent = 'Evaluar: ' + cursoTitulo;
      document.getElementById('ratingModal').style.display = 'flex';
    }

    function closeRatingModal() {
      document.getElementById('ratingModal').style.display = 'none';
    }
  </script>
</body>

</html>