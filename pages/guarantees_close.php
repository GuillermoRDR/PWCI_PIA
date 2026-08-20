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
  <title>AutoSiniestros · Garantías y cierre</title>
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
  <h1 class="h1">Garantías y cierre del siniestro</h1>
  <p class="sub">Registro de garantía de reparación y finalización del siniestro.</p>

  <div class="split" style="margin-top:12px">
    <div class="card pad" style="background: rgba(255,255,255,.04)">
      <h2 style="margin:0 0 8px">Garantía</h2>
      <div class="row">
        <div>
          <label>¿Aplica garantía?</label>
          <select>
            <option>Sí</option>
            <option>No</option>
          </select>
        </div>
        <div>
          <label>Vigencia (días)</label>
          <input type="number" min="0" step="1" value="90" />
        </div>
      </div>
      <label>Términos / notas</label>
      <textarea placeholder="Detalle de garantía, partes cubiertas, exclusiones…"></textarea>

      <h2 style="margin:14px 0 6px">Cierre</h2>
      <div class="row">
        <div>
          <label>Fecha de cierre</label>
          <input type="date" />
        </div>
        <div>
          <label>Motivo de cierre</label>
          <select>
            <option>Reparación concluida</option>
            <option>Pago realizado (pérdida total)</option>
            <option>Rechazo confirmado</option>
          </select>
        </div>
      </div>
      <label>Evidencia de entrega / cierre</label>
      <input type="file" multiple />

      <div class="btns">
        <button class="btn primary" type="button" data-only="supervisor" onclick="alert(' siniestro cerrado')">Cerrar siniestro</button>
        <a class="btn" href="/index.php?action=claim_detail">Volver al detalle</a>
      </div>
      
    </div>

    <aside class="card pad">
      <h2 style="margin:0 0 8px">Estado del siniestro</h2>
      <div class="timeline">
        <div class="step">
          <div class="top"><div class="t">Dictamen</div><span class="badge ok">Hecho</span></div>
          <div class="d">Aceptado con deducible.</div>
        </div>
        <div class="step">
          <div class="top"><div class="t">Pago deducible</div><span class="badge warn">Pendiente</span></div>
          <div class="d">Se espera comprobante del asegurado.</div>
        </div>
        <div class="step">
          <div class="top"><div class="t">Reparación</div><span class="badge">En curso</span></div>
          <div class="d">Fotos de reparación (si aplica) se cargan en Aprobaciones.</div>
        </div>
        <div class="step">
          <div class="top"><div class="t">Cierre</div><span class="badge">Por iniciar</span></div>
          <div class="d">Registro de garantía y entrega de unidad.</div>
        </div>
      </div>

      
    </aside>
  </div>
</section>

    
  </main>

  <script src="/pages/js/app.js"></script>
</body>
</html>
