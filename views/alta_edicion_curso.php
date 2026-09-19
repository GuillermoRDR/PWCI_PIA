<?php
$categorias = $categorias ?? [
  ['id' => 1, 'nombre' => 'Programación Web'],
  ['id' => 2, 'nombre' => 'Diseño UI/UX'],
  ['id' => 3, 'nombre' => 'Bases de Datos']
];

$curso = $curso ?? null;
/*
$curso = $curso ?? [
  'id' => 101,
  'titulo' => 'Desarrollo Web Full Stack con PHP y MySQL',
  'descripcion' => 'Aprende a construir aplicaciones web completas desde cero utilizando PHP, MySQL, HTML5 y arquitectura MVC.',
  'imagen' => 'https://via.placeholder.com/800x400',
  'instructor' => 'Lic. Guillermo René Dávila Roque',
  'instructor_id' => 5,
  'promedio_calificacion' => 4.8,
  'precio_completo' => 1250.00,
  'es_comprado' => false,
  'niveles' => [
    [
      'id' => 1,
      'numero' => 1,
      'titulo' => 'Nivel 1: Fundamentos de PHP y Sintaxis Básica',
      'descripcion' => 'Introducción a variables, estructuras de control y funciones en PHP.',
      'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
      'archivos' => [
        ['tipo' => 'pdf', 'nombre' => 'Guia_Sintaxis_PHP.pdf', 'url' => '#'],
        ['tipo' => 'link', 'nombre' => 'Documentación Oficial PHP', 'url' => 'https://php.net']
      ]
    ],
    [
      'id' => 2,
      'numero' => 2,
      'titulo' => 'Nivel 2: Bases de Datos MySQL y Stored Procedures',
      'descripcion' => 'Diseño de tablas, procedimientos almacenados y consultas complejas.',
      'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
      'archivos' => [
        ['tipo' => 'pdf', 'nombre' => 'Estructura_BD_Script.sql', 'url' => '#']
      ]
    ]
  ]
];*/
$esEdicion = !is_null($curso);

$error = $error ?? null;
$mensaje = $mensaje ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $esEdicion ? 'Editar Curso' : 'Crear Nuevo Curso' ?> - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/alta_edicion_curso.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>
    <a href="index.php?action=panel_control" class="btn-link">
      <i class="fa-solid fa-arrow-left"></i> Volver al Panel
    </a>
  </header>

  <div class="container">
    <h2 class="page-title">
      <i class="fa-solid <?= $esEdicion ? 'fa-pen-to-square' : 'fa-circle-plus' ?>"></i>
      <?= $esEdicion ? 'Editar Curso' : 'Alta de Nuevo Curso' ?>
    </h2>

    <form action="index.php?action=<?= $esEdicion ? 'update_course' : 'store_course' ?>" method="POST" enctype="multipart/form-data">

      <?php if ($esEdicion): ?>
        <input type="hidden" name="curso_id" value="<?= htmlspecialchars($curso['id'] ?? '') ?>">
      <?php endif; ?>

      <div class="form-card">
        <h3 class="section-title"><i class="fa-solid fa-circle-info"></i> Información General del Curso</h3>

        <div class="form-group">
          <label for="titulo">Título del Curso *</label>
          <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Ej. Desarrollo Web Full Stack con PHP y MySQL" value="<?= htmlspecialchars($curso['titulo'] ?? '') ?>" required>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="categoria_id">Categoría *</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
              <option value="">Seleccione una categoría</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($curso['categoria_id']) && $curso['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="precio_completo">Precio del Curso Completo ($) *</label>
            <input type="number" step="0.01" min="0" id="precio_completo" name="precio_completo" class="form-control" placeholder="0.00" value="<?= $curso['precio_completo'] ?? '0.00' ?>" required>
            <span class="help-text">Establece 0.00 si deseas ofrecer el curso completo totalmente gratis.</span>
          </div>
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción del Curso *</label>
          <textarea id="descripcion" name="descripcion" class="form-control" rows="4" placeholder="Describe los objetivos y lo que aprenderá el estudiante..." required><?= htmlspecialchars($curso['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="imagen">Imagen de Portada / Banner *</label>
          <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*" <?= $esEdicion ? '' : 'required' ?>>
          <?php if ($esEdicion && !empty($curso['imagen'])): ?>
            <span class="help-text">Imagen actual registrada. Selecciona un archivo solo si deseas cambiarla.</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-card">
        <h3 class="section-title"><i class="fa-solid fa-layer-group"></i> Niveles del Curso y Contenido Multimedia</h3>
        <p style="margin-bottom: 15px; font-size: 0.9rem;">Recuerda que cada nivel requiere <strong>obligatoriamente un video</strong> para poder publicarse.</p>

        <div id="levelsContainer">
          <?php if ($esEdicion && !empty($curso['niveles'])): ?>
            <?php foreach ($curso['niveles'] as $index => $nivel): ?>
              <div class="level-box" data-level="<?= $index + 1 ?>">
                <div class="level-header">
                  <span>Nivel <span class="level-number"><?= $index + 1 ?></span></span>
                  <button type="button" class="btn-remove-level" onclick="removeLevel(this)"><i class="fa-solid fa-trash"></i> Eliminar Nivel</button>
                </div>

                <div class="form-grid">
                  <div class="form-group">
                    <label>Título del Nivel *</label>
                    <input type="text" name="niveles[<?= $index ?>][titulo]" class="form-control" value="<?= htmlspecialchars($nivel['titulo']) ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label>Descripción del Nivel *</label>
                  <textarea name="niveles[<?= $index ?>][descripcion]" class="form-control" rows="2" required><?= htmlspecialchars($nivel['descripcion']) ?></textarea>
                </div>

                <div class="form-group">
                  <label><i class="fa-solid fa-video"></i> URL o Archivo del Video Obligatorio *</label>
                  <input type="url" name="niveles[<?= $index ?>][video_url]" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($nivel['video_url'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                  <label><i class="fa-solid fa-file-pdf"></i> Archivo Adjunto (PDF, SQL, Código, etc.)</label>
                  <input type="file" name="niveles_archivos_<?= $index ?>" class="form-control">
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="level-box" data-level="1">
              <div class="level-header">
                <span>Nivel <span class="level-number">1</span></span>
                <button type="button" class="btn-remove-level" onclick="removeLevel(this)"><i class="fa-solid fa-trash"></i> Eliminar Nivel</button>
              </div>

              <div class="form-grid">
                <div class="form-group">
                  <label>Título del Nivel *</label>
                  <input type="text" name="niveles[0][titulo]" class="form-control" placeholder="Ej. Introducción y Conceptos Básicos" required>
                </div>
              </div>

              <div class="form-group">
                <label>Descripción del Nivel *</label>
                <textarea name="niveles[0][descripcion]" class="form-control" rows="2" placeholder="Temas abarcados en este nivel..." required></textarea>
              </div>

              <div class="form-group">
                <label><i class="fa-solid fa-video"></i> URL del Video Obligatorio *</label>
                <input type="url" name="niveles[0][video_url]" class="form-control" placeholder="Ej. https://mi-servidor.com/videos/nivel1.mp4" required>
              </div>

              <div class="form-group">
                <label><i class="fa-solid fa-file-pdf"></i> Archivo Adjunto (PDF, Recursos, etc.)</label>
                <input type="file" name="niveles_archivos_0" class="form-control">
              </div>
            </div>
          <?php endif; ?>
        </div>

        <button type="button" class="btn-add-level" onclick="addLevel()">
          <i class="fa-solid fa-plus"></i> Agregar Otro Nivel
        </button>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-floppy-disk"></i> <?= $esEdicion ? 'Guardar Cambios del Curso' : 'Publicar Curso' ?>
      </button>
    </form>
  </div>

  <script>
    let levelCount = document.querySelectorAll('.level-box').length;

    function addLevel() {
      levelCount++;
      const container = document.getElementById('levelsContainer');
      const index = levelCount - 1;

      const levelHTML = `
                <div class="level-box" data-level="${levelCount}">
                    <div class="level-header">
                        <span>Nivel <span class="level-number">${levelCount}</span></span>
                        <button type="button" class="btn-remove-level" onclick="removeLevel(this)"><i class="fa-solid fa-trash"></i> Eliminar Nivel</button>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Título del Nivel *</label>
                            <input type="text" name="niveles[${index}][titulo]" class="form-control" placeholder="Ej. Nivel Avanzado" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción del Nivel *</label>
                        <textarea name="niveles[${index}][descripcion]" class="form-control" rows="2" placeholder="Temas abarcados..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-video"></i> URL del Video Obligatorio *</label>
                        <input type="url" name="niveles[${index}][video_url]" class="form-control" placeholder="https://..." required>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-file-pdf"></i> Archivo Adjunto (PDF, Recursos, etc.)</label>
                        <input type="file" name="niveles_archivos_${index}" class="form-control">
                    </div>
                </div>
            `;

      container.insertAdjacentHTML('beforeend', levelHTML);
    }

    function removeLevel(button) {
      const levelBox = button.closest('.level-box');
      const totalLevels = document.querySelectorAll('.level-box').length;

      if (totalLevels <= 1) {
        alert('El curso debe contener al menos un (1) nivel obligatoriamente.');
        return;
      }

      levelBox.remove();
      reindexLevels();
    }

    function reindexLevels() {
      const levels = document.querySelectorAll('.level-box');
      levelCount = levels.length;

      levels.forEach((box, i) => {
        const numSpan = box.querySelector('.level-number');
        if (numSpan) numSpan.textContent = i + 1;
      });
    }
  </script>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy. Todos los derechos reservados.</p>
  </footer>

</body>

</html>