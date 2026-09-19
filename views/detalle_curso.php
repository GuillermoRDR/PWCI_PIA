<?php
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
  ],
  'comentarios' => [
    [
      'id' => 1,
      'usuario' => 'Ana Martínez',
      'avatar' => 'https://placehold.co/400x400/ffffff/D9A05B',
      'calificacion' => 5,
      'fecha' => '12/Ene/2026 15:30',
      'comentario' => 'Excelente curso, el instructor explica los temas con mucha claridad.',
      'eliminado' => false
    ],
    [
      'id' => 2,
      'usuario' => 'Usuario_Anonimo',
      'avatar' => 'https://placehold.co/400x400/ffffff/D9A05B',
      'calificacion' => 1,
      'fecha' => '15/Ene/2026 09:20',
      'comentario' => '',
      'eliminado' => true,
      'causa_eliminacion' => 'Comentario con lenguaje inapropiado y ofensivo.'
    ]
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
  <title><?= htmlspecialchars($curso['titulo']) ?> - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/detalle_curso.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>
    <a href="index.php?action=home" class="btn-link"><i class="fa-solid fa-house"></i> Volver al Inicio</a>
  </header>

  <div class="container">

    <div class="course-header">
      <img class="course-banner-img" src="https://placehold.co/800x400/ffffff/D9A05B" alt="Imagen de portada">
      <div class="course-header-content">
        <h1 class="course-title"><?= htmlspecialchars($curso['titulo']) ?></h1>
        <div class="course-meta">
          <span><i class="fa-solid fa-user-tie"></i> Instructor: <strong><?= htmlspecialchars($curso['instructor']) ?></strong></span>
          <span class="rating-stars">
            <i class="fa-solid fa-star"></i> <?= number_format($curso['promedio_calificacion'], 1) ?> / 5.0
          </span>
          <span><i class="fa-solid fa-layer-group"></i> Niveles: <?= count($curso['niveles']) ?></span>
        </div>
        <p><?= htmlspecialchars($curso['descripcion']) ?></p>
      </div>
    </div>

    <div class="course-layout">

      <main class="main-section">

        <!-- Niveles del Curso -->
        <div class="card-box">
          <h3><i class="fa-solid fa-list-check"></i> Contenido y Niveles del Curso</h3>

          <?php foreach ($curso['niveles'] as $nivel): ?>
            <div class="level-card">
              <div class="level-header">
                <span><?= htmlspecialchars($nivel['titulo']) ?></span>
              </div>
              <p><?= htmlspecialchars($nivel['descripcion']) ?></p>

              <!-- Si el curso está comprado o el nivel es gratuito, mostrar reproductor de video -->
              <?php if ($curso['es_comprado']): ?>
                <div class="video-container">
                  <video controls poster="https://via.placeholder.com/800x400?text=Video+del+Nivel">
                    <source src="<?= htmlspecialchars($nivel['video_url']) ?>" type="video/mp4">
                    Tu navegador no soporta reproducción de video HTML5.
                  </video>
                </div>

                <!-- Material adjunto (PDF, Links, etc.) -->
                <h4>Archivos y Recursos:</h4>
                <ul class="attachments-list">
                  <?php foreach ($nivel['archivos'] as $archivo): ?>
                    <li>
                      <a href="<?= htmlspecialchars($archivo['url']) ?>" target="_blank">
                        <i class="fa-solid <?= $archivo['tipo'] === 'pdf' ? 'fa-file-pdf' : 'fa-link' ?>"></i>
                        <?= htmlspecialchars($archivo['nombre']) ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <div style="margin-top: 10px; font-size: 0.9rem; opacity: 0.8;">
                  <i class="fa-solid fa-lock"></i> Compra el curso para desbloquear el video y los archivos adjuntos.
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="card-box">
          <h3><i class="fa-solid fa-comments"></i> Comentarios de Alumnos</h3>

          <?php if (!empty($curso['comentarios'])): ?>
            <?php foreach ($curso['comentarios'] as $comentario): ?>
              <div class="comment-item">
                <div class="comment-header">
                  <img src="https://placehold.co/400x400/ffffff/D9A05B" class="comment-avatar" alt="Avatar">
                  <span class="comment-author"><?= htmlspecialchars($comentario['usuario']) ?></span>

                  <?php if (!$comentario['eliminado']): ?>
                    <span class="rating-stars">
                      <?= str_repeat('<i class="fa-solid fa-star"></i>', $comentario['calificacion']) ?>
                    </span>
                  <?php endif; ?>

                  <span class="comment-date"><?= htmlspecialchars($comentario['fecha']) ?></span>
                </div>

                <?php if ($comentario['eliminado']): ?>
                  <div class="comment-deleted">
                    <i class="fa-solid fa-ban"></i>
                    <em>Este comentario ha sido eliminado por el administrador. Motivo: <?= htmlspecialchars($comentario['causa_eliminacion']) ?></em>
                  </div>
                <?php else: ?>
                  <p><?= htmlspecialchars($comentario['comentario']) ?></p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p>Aún no hay comentarios para este curso.</p>
          <?php endif; ?>
        </div>

      </main>

      <aside class="side-section">

        <div class="card-box">
          <h3><i class="fa-solid fa-cart-shopping"></i> Comprar Curso</h3>
          <div class="price-tag">
            $<?= number_format($curso['precio_completo'], 2) ?>
          </div>

          <?php if (!$curso['es_comprado']): ?>
            <form action="index.php?action=buy_course" method="POST">
              <input type="hidden" name="curso_id" value="<?= $curso['id'] ?>">

              <div style="margin-bottom: 15px;">
                <label for="metodo_pago" style="font-weight: bold; display: block; margin-bottom: 5px;">Forma de Pago:</label>
                <select name="metodo_pago" id="metodo_pago" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #CCC;" required>
                  <option value="paypal">PayPal</option>
                </select>
              </div>

              <button type="submit" class="btn-buy"><i class="fa-solid fa-credit-card"></i> Inscribirme Ahora</button>
            </form>
          <?php else: ?>
            <div style="text-align: center; font-weight: bold; color: green; padding: 10px; background: #D4EDDA; border-radius: 6px;">
              <i class="fa-solid fa-circle-check"></i> Ya estás inscrito en este curso
            </div>
          <?php endif; ?>
        </div>

      </aside>

    </div>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy. Todos los derechos reservados.</p>
  </footer>

</body>

</html>