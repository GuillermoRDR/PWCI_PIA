<?php require base_path('views/partials/legacy.php');
legacyStart('Aprende en Kdemy', 'styles'); ?>
<div class="main-container">
  <aside class="sidebar">
    <h3>Categorías</h3>
    <ul class="category-list"><?php foreach ($categorias as $cat): ?>
        <li><a href="<?= e(url('/buscar?categoria_id=' . (int) $cat['id'])) ?>"><?= e($cat['nombre']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </aside>
  <main class="content">
    <form class="search-bar" action="<?= e(url('/buscar')) ?>" method="get"><input name="q" aria-label="Buscar"
        placeholder="Cursos, temas o profesores"><button>Buscar</button></form>
    <?php foreach (['Cursos recientes' => $cursosRecientes, 'Mejor calificados' => $cursosMejorCalificados, 'Más vendidos' => $cursosMasVendidos] as $titulo => $lista): ?>
      <section>
        <h2 class="section-title"><?= e($titulo) ?></h2>
        <div class="cards-grid"><?php foreach ($lista as $curso): ?>
            <article class="card"><img src="<?= e(foto($curso['imagen'])) ?>" alt="Portada del curso">
              <div class="card-body">
                <h3 class="card-title"><?= e($curso['titulo']) ?></h3>
                <p><?= e(mb_strimwidth($curso['descripcion'], 0, 120, '…')) ?></p>
                <p>
                  <?= $curso['modalidad_precio'] === 'por_nivel' ? 'Precio por nivel' : ('$' . number_format((float) $curso['precio'], 2) . ' MXN') ?>
                </p><a class="btn btn-primary" href="<?= e(url('/curso?id=' . (int) $curso['id'])) ?>">Ver curso</a>
              </div>
            </article><?php endforeach; ?>
        </div><?php if (!$lista): ?>
          <p>Aún no hay cursos.</p><?php endif; ?>
      </section><?php endforeach; ?>
  </main>
</div><?php legacyEnd(); ?>