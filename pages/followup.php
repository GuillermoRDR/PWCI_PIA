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
  <title>AutoSiniestros · Seguimiento</title>
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
    
<section class="card pad">
  <h1 class="h1">Seguimiento · Comentarios y preguntas</h1>
  <p class="sub">El asegurado pregunta, el ajustador o supervisor responde. Se muestra fecha compromiso.</p>

  <div class="card pad" style="margin-top:12px; background: rgba(255,255,255,.04)">
    <div class="btns" style="margin-top:0">
      <span class="badge warn">
          Folio <?= $siniestro['folio'] ?>
      </span>
      <span class="badge">
          Compromiso:
          <?= $siniestro['fecha_compromiso']
              ?? 'Sin definir'
          ?>
      </span>
      <span class="badge ok">
          Estatus:
          <?= $siniestro['nombre_estado']
              ?? 'Pendiente'
          ?>

      </span>
    </div>
    
  </div>

    <div class="timeline" style="margin-top:14px">

    <?php foreach($comentarios as $c): ?>

        <div class="step">

            <div class="top">

                <div class="t">

                    <?= $c['nombre'] ?>

                    ·

                    <?= $c['nombre_rol'] ?>

                </div>

                <span class="badge">

                    <?= $c['fecha'] ?>

                </span>

            </div>

            <div class="d">

                <?= htmlspecialchars($c['comentario']) ?>

            </div>

        </div>

    <?php endforeach; ?>

    </div>

    <form
      method="POST"
      action="/index.php?action=followup"
      class="card pad"
      style="margin-top:14px; background: rgba(255,255,255,.04)">

      <input
          type="hidden"
          name="id_siniestro"
          value="<?= $siniestro['id_siniestro'] ?>"
      >

      <h2 style="margin:0 0 8px">
          Agregar comentario
      </h2>

      <label>Mensaje</label>

      <textarea
          name="comentario"
          required
          placeholder="Escribe tu pregunta/comentario…"
      ></textarea>

      <div class="btns">

          <button
              class="btn primary"
              type="submit"
          >
              Publicar
          </button>

          <a
              class="btn"
              href="/index.php?action=claim_detail&id=<?= $siniestro['id_siniestro'] ?>"
          >
              Volver al detalle
          </a>

      </div>

  </form>
</section>

    
  </main>

  <script src="/pages/js/app.js"></script>
</body>
</html>
