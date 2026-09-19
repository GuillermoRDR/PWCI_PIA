<?php
$busqueda = $_GET['q'] ?? '';
$categoriaSeleccionada = $_GET['categoria_id'] ?? '';

$categorias = $categorias ?? [
  ['id' => 1, 'nombre' => 'Programación Web'],
  ['id' => 2, 'nombre' => 'Diseño UI/UX'],
  ['id' => 3, 'nombre' => 'Bases de Datos'],
  ['id' => 4, 'nombre' => 'Ciberseguridad']
];

$cursosEncontrados = $cursosEncontrados ?? [
  [
    'id' => 101,
    'titulo' => 'Desarrollo Web Full Stack con PHP y MySQL',
    'instructor' => 'Carlos Mendoza',
    'categoria' => 'Programación Web',
    'descripcion' => 'Aprende a construir aplicaciones web dinámicas desde cero utilizando arquitectura MVC.',
    'precio' => 130.00,
    'calificacion' => 4.8
  ],
  [
    'id' => 102,
    'titulo' => 'Diseño de Interfaces Modernas con Figma',
    'instructor' => 'Laura Fernández',
    'categoria' => 'Diseño UI/UX',
    'descripcion' => 'Domina el diseño de experiencia de usuario y la creación de prototipos interactivos.',
    'precio' => 500.00,
    'calificacion' => 4.6
  ]
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados de Búsqueda - Kdemy</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/resultado_busqueda.css">
</head>

<body>
  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>

    <nav class="nav-links">
      <?php if (isset($_SESSION['user_id'])): ?>

      <?php else: ?>
        <a href="index.php?action=dashboard_admin" class="btn btn-secondary">
          <i class="fa-solid fa-chart-line"></i> Panel de Control
        </a>
        <a href="index.php?action=panel_control" class="btn btn-secondary">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="index.php?action=reportes" class="btn btn-secondary">
          <i class="fa-solid fa-chart-line"></i> Reportes
        </a>
        <a href="index.php?action=mis_cursos" class="btn btn-secondary">
          <i class="fa-solid fa-book-open"></i> Mis Cursos
        </a>
        <a href="index.php?action=logearse" class="btn btn-primary">
          <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
        </a>
        <a href="index.php?action=mi_perfil" class="btn btn-secondary">
          <i class="fa-solid fa-user"></i> Mi Perfil
        </a>
        <a href="index.php?action=logearse" class="btn btn-secondary">Iniciar Sesión
        </a>
        <a href="index.php?action=registrarse" class="btn btn-primary"> Registrarse
        </a>
      <?php endif; ?>
    </nav>
  </header>

  <div class="container">

    <div class="search-bar-card">
      <form action="index.php" method="GET" class="search-form">
        <input type="hidden" name="action" value="resultado_busqueda">

        <input type="text" name="q" class="form-control" placeholder="Buscar cursos por título o palabras clave..." value="<?= htmlspecialchars($busqueda) ?>">

        <select name="categoria_id" class="form-control">
          <option value="">Todas las Categorías</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= (string)$categoriaSeleccionada === (string)$cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-search">
          <i class="fa-solid fa-magnifying-glass"></i> Buscar
        </button>
      </form>
    </div>

    <div class="results-header">
      <h3>
        Resultados de Búsqueda
        <?php if (!empty($busqueda)): ?>
          para: <span>"<?= htmlspecialchars($busqueda) ?>"</span>
        <?php endif; ?>
      </h3>
    </div>

    <section>
      <?php if (!empty($cursosEncontrados)): ?>
        <div class="cards-grid">
          <?php foreach ($cursosEncontrados as $curso): ?>
            <div class="card">
              <img src="https://placehold.co/300x150/ffffff/D9A05B" alt="<?= htmlspecialchars($curso['titulo']) ?>">
              <div class="card-body">
                <h4 class="card-title"><?= htmlspecialchars($curso['titulo']) ?></h4>
                <?php if (!empty($curso['descripcion'])): ?>
                  <p class="card-info"><?= htmlspecialchars($curso['descripcion']) ?></p>
                <?php endif; ?>
                <a href="index.php?action=detalle_curso&id=<?= $curso['id'] ?? '' ?>" class="btn btn-primary">Ver detalle</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="no-results">
          <i class="fa-solid fa-magnifying-glass-minus"></i>
          <h2>No se encontraron cursos activos</h2>
          <p style="margin-top: 10px; opacity: 0.8;">Intenta ajustar el término de búsqueda o selecciona otra categoría.</p>
        </div>
      <?php endif; ?>
    </section>

  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy - Todos los derechos reservados.</p>
  </footer>

</body>

</html>