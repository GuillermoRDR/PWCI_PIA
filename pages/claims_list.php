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
  <title>AutoSiniestros · Consulta de siniestros</title>
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
  <h1 class="h1">Consulta / Listado de siniestros</h1>
  <p class="sub">Consulta por rango de fechas y criterios combinados. La visibilidad depende del rol (mock).</p>

  <form id="filterForm" method="GET" action="/index.php"  class="card pad" style="margin-top:12px; background: rgba(255,255,255,.04)">
    <input type="hidden" name="action" value="claims_list">
    <div class="row">
      <div>
        <label for="desde">Desde</label>
        <input id="desde" name="desde" type="date" />
      </div>
      <div>
        <label for="hasta">Hasta</label>
        <input id="hasta" name="hasta" type="date" />
      </div>
    </div>
    <div class="row">
      <div>
        <label for="estatus">Estatus</label>
        <select id="estatus" name="estatus">
          <option value="">Todos</option>
          <option value="rechazado">Rechazado</option>
          <option value="aceptado">Aceptado</option>
          <option value="aceptado_con_deducible">Aceptado con pago de deducible</option>
          <option value="aceptado_sin_deducible">Aceptado sin pago de deducible</option>
          <option value="reparacion">Aplica pago reparación</option>
          <option value="perdida_total">Pérdida total</option>
        </select>
      </div>
      <div>
        <label for="q">Búsqueda</label>
        <input id="q" name="q"  placeholder="placas, póliza, compañía" />
      </div>
    </div>
    <div class="btns">
      <button class="btn" type="submit">Aplicar</button>
      <a class="btn" href="/index.php?action=claim_create" data-only="ajustador">Registrar siniestro</a>
    </div>
    
  </form>

  <div style="margin-top:14px; overflow:auto">
    <table class="table" id="claimsTable">
      <thead>
        <tr>
          <th>Folio</th>
          <th>Fecha</th>
          <th>Compañía</th>
          <th>Unidad</th>
          <th>Estatus</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
          <?php if(!empty($siniestros)): ?>

            <?php foreach($siniestros as $s): ?>

                <tr>

                    <td><?= $s['folio'] ?></td>

                    <td><?= $s['fecha'] ?></td>

                    <td><?= $s['compania'] ?></td>

                    <td><?= $s['unidad'] ?></td>

                    <td>
                        <?= $s['estado'] ?? 'Pendiente' ?>
                    </td>

                    <td>

                        <a
                            class="btn"
                            href="/index.php?action=claim_detail&id=<?= $s['id_siniestro'] ?>"
                        >
                            Ver detalle
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

          <?php else: ?>

              <tr>

                  <td colspan="6">

                      No se encontraron siniestros

                  </td>

              </tr>

          <?php endif; ?>

        </tbody>
    </table>
  </div>
</section>

    
  </main>

  <script src="/pages/js/app.js"></script>
</body>
</html>
