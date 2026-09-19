<?php
$categorias = $categorias ?? [];
$cursosDestacados = $cursosDestacados ?? [];
$cursosRecientes = $cursosRecientes ?? [];
$cursosMejorCalificados = $cursosMejorCalificados ?? [];
$cursosMasVendidos = $cursosMasVendidos ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de Cursos en Línea - Inicio</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/styles.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>

    <form href="index.php?action=resultado_busqueda" method="GET" class="search-bar">
      <input type="hidden" name="action" value="search">
      <input type="text" name="query" placeholder="Buscar curso activo..." required>
      <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

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

  <div class="main-container">

    <aside class="sidebar">
      <h3><i class="fa-solid fa-layer-group"></i> Categorías</h3>
      <ul class="category-list">
        <?php if (!empty($categorias)): ?>
          <?php foreach ($categorias as $cat): ?>
            <li>
              <a href="index.php?action=category&id=<?= htmlspecialchars($cat['id']) ?>">
                <i class="fa-solid fa-folder"></i> <?= htmlspecialchars($cat['nombre']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li><a href="#"><i class="fa-solid fa-code"></i> IT & Software</a></li>
          <li><a href="#"><i class="fa-solid fa-bullhorn"></i> Marketing</a></li>
          <li><a href="#"><i class="fa-solid fa-paint-brush"></i> Diseño</a></li>
        <?php endif; ?>
      </ul>
    </aside>

    <main class="content">

      <section>
        <h2 class="section-title"><i class="fa-solid fa-clock"></i> Cursos Más Recientes</h2>
        <div class="cards-grid">
          <?php if (!empty($cursosRecientes)): ?>
            <?php foreach ($cursosRecientes as $curso): ?>
              <div class="card">
                <img src="https://placehold.co/300x150/ffffff/D9A05B" alt="Imagen del curso">
                <div class="card-body">
                  <h4 class="card-title"><?= htmlspecialchars($curso['titulo']) ?></h4>
                  <a href="index.php?action=detalle_curso" class="btn btn-primary">Ver detalle</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="card">
              <img src="https://placehold.co/300x150/ffffff/D9A05B" alt="Curso Demo">
              <div class="card-body">
                <h4 class="card-title">Desarrollo Web Full Stack</h4>
                <p class="card-info">Aprende PHP, MySQL y HTML5 desde cero.</p>
                <a href="index.php?action=detalle_curso" class="btn btn-primary">Ver detalle</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <section>
        <h2 class="section-title"><i class="fa-solid fa-star"></i> Mejor Calificados</h2>
        <div class="cards-grid">
          <?php if (!empty($cursosMejorCalificados)): ?>
            <?php foreach ($cursosMejorCalificados as $curso): ?>
              <div class="card">
                <img src="https://placehold.co/300x150/ffffff/D9A05B" alt="Imagen del curso">
                <div class="card-body">
                  <h4 class="card-title"><?= htmlspecialchars($curso['titulo']) ?></h4>
                  <div class="card-rating">
                    <i class="fa-solid fa-star"></i> 4.9 / 5.0
                  </div>
                  <a href="index.php?action=detalle_curso" class="btn btn-primary">Ver detalle</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="card">
              <img src="https://placehold.co/300x150/ffffff/D9A05B" alt="Curso Demo">
              <div class="card-body">
                <h4 class="card-title">Diseño de Interfaces UI/UX</h4>
                <div class="card-rating">
                  <i class="fa-solid fa-star"></i> 4.8 / 5.0
                </div>
                <a href="index.php?action=detalle_curso" class="btn btn-primary">Ver detalle</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <section>
        <h2 class="section-title"><i class="fa-solid fa-fire"></i> Más Vendidos</h2>
        <div class="cards-grid">
          <?php if (!empty($cursosMasVendidos)): ?>
            <?php foreach ($cursosMasVendidos as $curso): ?>
              <div class="card">
                <img src="https://placehold.co/300x150/ffffff/D9A05B" alt="Imagen del curso">
                <div class="card-body">
                  <h4 class="card-title"><?= htmlspecialchars($curso['titulo']) ?></h4>
                  <a href="index.php?action=detalle_curso" class="btn btn-primary">Ver detalle</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="card">
              <img src="https://placehold.co/300x150/ffffff/D9A05B" alt="Curso Demo">
              <div class="card-body">
                <h4 class="card-title">Estrategias de Marketing Digital</h4>
                <a href="index.php?action=detalle_curso" class="btn btn-primary">Ver detalle</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>

    </main>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy. Todos los derechos reservados.</p>
  </footer>

</body>

</html>