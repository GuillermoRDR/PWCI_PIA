<?php
////session_start();
require_once __DIR__ . '/../helpers/auth.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php?action=loginPage"); // login
    exit;
}else {
  verificarRol([1, 3]); // Asegurado    
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
  <title>AutoSiniestros · Pagos</title>
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
  <h1 class="h1">Pagos · Deducible / Pago total</h1>
  <p class="sub">Según el dictamen: con deducible, sin deducible, reparación o pérdida total.</p>

  <div class="split" style="margin-top:12px">
    <div class="card pad" style="background: rgba(255,255,255,.04)">
      <h2 style="margin:0 0 8px">Resumen</h2>
      <table class="table">
        <tbody>
          <tr><th>Folio</th><td>AS-2026-0012</td></tr>
          <tr><th>Estatus</th><td><span class="badge warn">Aceptado con deducible</span></td></tr>
          <tr><th>Monto deducible</th><td><b>$4,500.00 MXN</b></td></tr>
          <tr><th>Fecha límite sugerida</th><td>2026-03-10</td></tr>
          <tr><th>Compromiso reparación/pago</th><td>2026-03-18</td></tr>
        </tbody>
      </table>

      <div class="btns">
        <button class="btn primary" type="button" data-only="asegurado" onclick="alert(' pago registrado')">Pagar deducible</button>
        <button class="btn" type="button" data-only="supervisor,ajustador" onclick="alert(' marcar como recibido')">Marcar pago recibido</button>
      </div>
      
    </div>

    <aside class="card pad">
      <h2 style="margin:0 0 8px">Subir comprobante</h2>
      <label>Comprobante (PDF/JPG)</label>
      <input type="file" accept="application/pdf,image/*" />
      <label>Referencia / folio bancario</label>
      <input placeholder="ej. 11223344" />
      <div class="btns">
        <button class="btn" type="button" onclick="alert(' comprobante subido')">Subir</button>
        <a class="btn" href="/index.php?action=claims_list">Ir al listado</a>
      </div>

      
    </aside>
  </div>
</section>

    
  </main>

  <script src="/pages/js/app.js"></script>
</body>
</html>
