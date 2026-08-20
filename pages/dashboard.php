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

<?php if($rol == 4): ?> <!-- Solo ajustadores y admin pueden ver esto -->

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Dashboard</title>
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
        <a class="pill" href="/index.php?action=followup">Seguimiento</a>
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

  <div class="card pad">

    <h2>
        Rtambien ve esto
    </h2>

    <p class="sub">
        Aprobar o rechazar
    </p>

    <section class="card pad">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap">
      <div>
        <h1 class="h1" style="margin-bottom:4px">Dashboard</h1>
        <p class="sub">Bienvenida/o, <?php echo $_SESSION['usuario']['nombre']; ?></p>
        
      </div>
      <div class="btns" style="margin-top:0">
        <a class="btn primary" href="/index.php?action=claims_list">Ir a mis siniestros</a>
        <a class="btn" href="/index.php?action=claim_create" data-only="ajustador">Registrar siniestro</a>
        <a class="btn" href="/index.php?action=approvals" data-only="supervisor">Revisar aprobaciones</a>
      </div>
    </div>
    
      <div class="kpi" style="margin-top:16px">
        <div class="n">1</div>
        <div class="l">Requieren acción</div>
      </div>
      <div class="kpi" style="margin-top:10px">
        <div class="n">0</div>
        <div class="l">Pagos pendientes (mock)</div>
      </div>
    </div>
    <div class="timeline">
      <div class="step">
        <div class="top"><div class="t">Última actividad</div><span class="badge warn">Pendiente</span></div>
        <div class="d">Siniestro #AS-2026-0012: esperando autorización del supervisor.</div>
      </div>
      <div class="step">
        <div class="top"><div class="t">Compromiso</div><span class="badge ok">En curso</span></div>
        <div class="d">Fecha estimada de reparación / pago: <b>2026-03-18</b> (ejemplo).</div>
      </div>
    </div>
  </section>
</div>


  
<?php endif; ?>

<?php if($rol == 2): ?> <!-- Solo supervisores y admin pueden ver esto -->

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Dashboard</title>
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
        <a class="pill" href="/index.php?action=claims_list">Listado / Consulta</a>
        <a class="pill" href="/index.php?action=approvals">Aprobaciones</a>
        <a class="pill" href="/index.php?action=followup">Seguimiento</a>
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
<div class="card pad">

    <h2>
        Revisar siniestros
    </h2>

    <p class="sub">
        Aprobar o rechazar
    </p>

</div>

<?php endif; ?>

<?php if($rol == 3): ?> <!-- Solo asegurados pueden ver esto -->

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Dashboard</title>
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
        <a class="pill" href="/index.php?action=claims_list">Listado / Consulta</a>
        <a class="pill" href="/index.php?action=claim_detail">Detalle</a>
        <a class="pill" href="/index.php?action=followup">Seguimiento</a>
        <a class="pill" href="/index.php?action=payments">Pagos</a>
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


  <div class="card pad">

      <h2>
          Mis pólizas
      </h2>

  </div>

<?php endif; ?>

<?php if($rol == 1): ?> <!-- Solo ajustadores y admin pueden ver esto -->

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Dashboard</title>
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
        <a class="pill" href="/index.php?action=approvals">Aprobaciones</a>
        <a class="pill" href="/index.php?action=followup">Seguimiento</a>
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

  <div class="card pad">

    <h2>
        Rtambien ve esto
    </h2>

    <p class="sub">
        Aprobar o rechazar
    </p>

    <section class="card pad">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap">
      <div>
        <h1 class="h1" style="margin-bottom:4px">Dashboard</h1>
        <p class="sub">Bienvenida/o, <?php echo $_SESSION['usuario']['nombre']; ?></p>
        
      </div>
      <div class="btns" style="margin-top:0">
        <a class="btn primary" href="/index.php?action=claims_list">Ir a mis siniestros</a>
        <a class="btn" href="/index.php?action=claim_create" data-only="ajustador">Registrar siniestro</a>
        <a class="btn" href="/index.php?action=approvals" data-only="supervisor">Revisar aprobaciones</a>
      </div>
    </div>
    
      <div class="kpi" style="margin-top:16px">
        <div class="n">1</div>
        <div class="l">Requieren acción</div>
      </div>
      <div class="kpi" style="margin-top:10px">
        <div class="n">0</div>
        <div class="l">Pagos pendientes (mock)</div>
      </div>
    </div>
    <div class="timeline">
      <div class="step">
        <div class="top"><div class="t">Última actividad</div><span class="badge warn">Pendiente</span></div>
        <div class="d">Siniestro #AS-2026-0012: esperando autorización del supervisor.</div>
      </div>
      <div class="step">
        <div class="top"><div class="t">Compromiso</div><span class="badge ok">En curso</span></div>
        <div class="d">Fecha estimada de reparación / pago: <b>2026-03-18</b> (ejemplo).</div>
      </div>
    </div>
  </section>
</div>


  
<?php endif; ?>


