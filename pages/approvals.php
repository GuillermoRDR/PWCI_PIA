<?php
////session_start();
require_once __DIR__ . '/../helpers/auth.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php?action=loginPage"); // login
    exit;
}else {
  verificarRol([1, 2]); // Administrador, Supervisor
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
  <title>AutoSiniestros · Aprobaciones</title>
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
  <h1 class="h1">Aprobaciones / Dictamen</h1>
  <p class="sub">Pantalla de supervisor para autorizar o rechazar y definir deducible, reparación o pérdida total.</p>

  

  <div class="split" data-only="supervisor" style="margin-top:12px">
    <div class="card pad" style="background: rgba(255,255,255,.04)">
      <h2 style="margin:0 0 8px">Siniestro a revisar</h2>
      <div class="btns" style="margin-top:0">
        <span class="badge warn">AS-2026-0012</span>
        <span class="badge">Versa 2022 · ABC-1234</span>
        <span class="badge">Póliza POL-12345678</span>
      </div>
      
      <div class="btns">
        <a class="btn" href="/pages/claim_detail.php">Ver detalle</a>
      </div>

      <h2 style="margin:14px 0 6px">Dictamen</h2>
      <label>Estatus</label>
      <select required>
        <option value="" disabled selected>Selecciona…</option>
        <option>1. Rechazado</option>
        <option>2. Aceptado</option>
        <option>3. Aceptado con pago de deducible</option>
        <option>4. Aceptado sin pago de deducible</option>
        <option>5. Aplica pago para reparación de la unidad</option>
        <option>6. Pérdida total, aplica pago completo de la unidad</option>
      </select>

      <div class="row">
        <div>
          <label>Monto deducible (si aplica)</label>
          <input type="number" min="0" step="0.01" placeholder="0.00" />
        </div>
        <div>
          <label>¿Requiere pago deducible?</label>
          <select>
            <option>No</option>
            <option>Sí</option>
          </select>
        </div>
      </div>

      <label>Fecha compromiso (reparación o pago)</label>
      <input type="date" required />

      <label>Comentarios del supervisor</label>
      <textarea placeholder="Razones, condiciones, observaciones…"></textarea>

      <div class="row">
        <div>
          <label>Fotos de reparación (si aplica)</label>
          <input type="file" accept="image/*" multiple />
        </div>
        <div>
          <label>Documento adicional</label>
          <input type="file" multiple />
        </div>
      </div>

      <div class="btns">
        <button class="btn primary" type="button" onclick="alert(' dictamen guardado')">Guardar dictamen</button>
        <a class="btn" href="/pages/claims_list.php">Volver al listado</a>
      </div>
    </div>

    <aside class="card pad">
      <h2 style="margin:0 0 8px">Checklist rápido</h2>
      <div class="timeline">
        <div class="step">
          <div class="top"><div class="t">Multimedia completa</div><span class="badge ok">OK</span></div>
          <div class="d">Fotos del daño + evidencia general.</div>
        </div>
        <div class="step">
          <div class="top"><div class="t">Descripción y póliza</div><span class="badge ok">OK</span></div>
          <div class="d">Póliza válida y datos del asegurado.</div>
        </div>
        <div class="step">
          <div class="top"><div class="t">Decisión</div><span class="badge warn">Pendiente</span></div>
          <div class="d">Selecciona estatus y fecha compromiso.</div>
        </div>
      </div>
      
    </aside>
  </div>
</section>

    
  </main>

  <script src="/pages/js/app.js"></script>
</body>
</html>
