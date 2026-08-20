<?php
////session_start();
require_once __DIR__ . '/../helpers/auth.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php?action=loginPage"); // login
    exit;
}else {
  verificarRol([1, 2, 3, 4]); // Administrador, Supervisor, Asegurado, Ajustador    
}
$rol = $_SESSION['usuario']['rol'];
$nombreRol = '';
switch($rol){
    case 1:
        $nombreRol = 'Administrador';
    break;
    case 2:
        $nombreRol = 'Supervisor';
    break;
    case 3:
        $nombreRol = 'Asegurado';
    break;
    case 4:
        $nombreRol = 'Ajustador';
    break;
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Detalle de siniestro</title>
  <link rel="stylesheet" href="/pages/css/styles.css" />
</head>
<body>
  <header class="nav">
    <div class="nav-inner">
      <a class="brand" href="/index.php?action=loginPage">
        <div class="logo"><span>AS</span></div>
        <div>
          <div style="font-weight:900">AutoSiniestros</div>
          <div style="font-size:12px;color:var(--muted2);margin-top:2px"></div>
        </div>
      </a>
      <nav class="nav-links">
        <a class="pill" href="/index.php?action=dashboard">Dashboard</a>
        <a class="pill" href="/index.php?action=claim_create">Nuevo Siniestro</a>
        <a class="pill" href="/index.php?action=claims_list">Listado / Consulta</a>
        <a class="pill" href="/index.php?action=claim_detail">Detalle</a>
        <a class="pill" href="/index.php?action=approvals">Aprobaciones</a>
        <a class="pill" href="/index.php?action=followup">Seguimiento</a>
        <a class="pill" href="/index.php?action=payments">Pagos</a>
        <a class="pill" href="/index.php?action=guarantees_close">Garantías / Cierre</a>
        <a class="pill" href="/index.php?action=editarPerfil">Actualizar Perfil</a>
        <a class="pillLogout" href="/index.php?action=logout">Cerrar sesión</a>
        <span class="badge">
          Rol:
          <b><?php echo $nombreRol; ?></b>
        </span>
      </nav>
    </div>
  </header>

  <main class="container">
    
  <div class="split">
    <section class="card pad">
      <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px; flex-wrap:wrap">
        <div>
          <h1 class="h1" style="margin-bottom:4px">Detalle del siniestro</h1>
          <p class="sub">

              Folio
              <b>
                  #AS-<?= $detalle['id_siniestro'] ?>
              </b>

              · Fecha
              <b>
                  <?= $detalle['fecha'] ?>
              </b>

              · Estatus

              <span class="badge warn">

                  <?= $detalle['nombre_estado'] ?? 'Pendiente' ?>

              </span>

          </p>
        </div>
        <div class="btns" style="margin-top:0">
          <a class="btn" href="/index.php?action=followup&id=<?= $detalle['id_siniestro'] ?>">Ir a seguimiento</a>
          <a class="btn" href="/pages/payments.php">Ver pagos</a>
          <a class="btn" href="/pages/approvals.php" data-only="supervisor">Autorizar / decidir</a>
        </div>
      </div>

      <h2 style="margin:14px 0 6px">Datos principales</h2>
      <table class="table">

      <tbody>

      <tr>
          <th>Compañía</th>
          <td><?= $detalle['compania'] ?></td>

          <th>Póliza</th>
          <td><?= $detalle['numero_poliza'] ?></td>
      </tr>

      <tr>
          <th>Asegurado</th>
          <td><?= $detalle['asegurado'] ?></td>

          <th>Correo</th>
          <td><?= $detalle['correo'] ?></td>
      </tr>

      <tr>
          <th>Unidad</th>

          <td>
              <?= $detalle['marca'] ?>
              <?= $detalle['modelo'] ?>
              <?= $detalle['anio'] ?>
          </td>

          <th>Placas</th>
          <td><?= $detalle['placas'] ?></td>
      </tr>

      <tr>
          <th>Número de serie</th>
          <td><?= $detalle['numero_serie'] ?></td>

          <th>Ubicación</th>
          <td><?= $detalle['ubicacion'] ?></td>
      </tr>

      </tbody>
      </table>

      <h2 style="margin:14px 0 6px">Descripción</h2>
      <div class="card pad" style="background: rgba(255,255,255,.04)">

          <?= nl2br(htmlspecialchars($detalle['descripcion'])) ?>

      </div>

      <h2 style="margin:14px 0 6px">Multimedia</h2>
      <div class="gallery">

      <?php foreach($detalle['multimedia'] as $m): ?>

          <?php if(str_contains($m['mime_type'], 'image')): ?>

              <div class="thumb">

                  <img
                      src="/index.php?action=verMultimedia&id=<?= $m['id_multimedia'] ?>"
                      style="
                          width:100%;
                          height:100%;
                          object-fit:cover;
                          border-radius:14px;
                      "
                  >

              </div>

          <?php else: ?>

              <div class="thumb">

                  <video
                      controls
                      style="
                          width:100%;
                          height:100%;
                          border-radius:14px;
                      "
                  >

                      <source
                          src="/index.php?action=verMultimedia&id=<?= $m['id_multimedia'] ?>"
                          type="<?= $m['mime_type'] ?>"
                      >

                  </video>

              </div>

          <?php endif; ?>

      <?php endforeach; ?>

      </div>
    </section>

    <!-- <aside class="card pad">
      <h2 style="margin:0 0 8px">Acciones por rol</h2>

      <div class="step" data-only="supervisor">
        <div class="top"><div class="t">Supervisor</div><span class="badge">Decisión</span></div>
        <div class="d">Autorizar / rechazar, definir estatus y fecha compromiso.</div>
        <div class="btns">
          <a class="btn primary" href="/pages/approvals.php">Ir a Aprobaciones</a>
        </div>
      </div>

      <div class="step" data-only="ajustador">
        <div class="top"><div class="t">Ajustador</div><span class="badge">Evidencia</span></div>
        <div class="d">Subir fotos/videos adicionales y responder comentarios.</div>
        <div class="btns">
          <a class="btn" href="/pages/followup.php">Responder</a>
        </div>
      </div>

      <div class="step" data-only="asegurado">
        <div class="top"><div class="t">Asegurado</div><span class="badge">Seguimiento</span></div>
        <div class="d">Ver avance y hacer preguntas.</div>
        <div class="btns">
          <a class="btn" href="/pages/followup.php">Comentar</a>
        </div>
      </div>
    </aside>-->
  </div>

    
  </main>

  <script src="/pages/js/app.js"></script>
</body>
</html>
