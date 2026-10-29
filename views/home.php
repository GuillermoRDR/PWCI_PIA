<?php require base_path('views/partials/head.php') ?>
<?php require base_path('views/partials/nav.php') ?>

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

  <?php require base_path('views/partials/footer.php'); ?>

  <script src="/js/main.js"></script>