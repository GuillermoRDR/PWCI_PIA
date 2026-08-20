<?php
////session_start();
require_once __DIR__ . '/../helpers/auth.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php?action=loginPage"); // login
    exit;
}else {
  verificarRol([1, 4]); // Administrador, Supervisor, Asegurado, Ajustador    
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
  <title>AutoSiniestros · Registrar siniestro</title>
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
      <h1 class="h1">Registrar siniestro</h1>
      <p class="sub">Solo visible para <b>Ajustadores</b> (en este  se oculta por rol).</p>


      <div class="contenedor-busqueda">

          <label for="buscarAsegurado">
              Buscar asegurado
          </label>

          <input
              type="text"
              id="buscarAsegurado"
              placeholder="Nombre, póliza, placas, VIN o correo"
              autocomplete="off"
          >

          <div id="resultadosBusqueda"></div>

      </div>

      <!-- ##################################### Información del asegurado ##################################### -->

      <div class="datos-asegurado">

          <h2>Información del asegurado</h2>
          <!-- ##################################### Cliente ##################################### -->

          <div class="card-info">

              <h3>Cliente</h3>

              <div class="info-grid">

                  <div class="foto-container">

                      <img
                          id="imgAsegurado"
                          src="/multimedia/default-user.png"
                          alt="Foto asegurado"
                      >
                  </div>

                  <div>

                      <p>
                          <strong>Nombre completo:</strong>
                          <span id="txtAsegurado">-</span>
                      </p>

                      <p>
                          <strong>Correo:</strong>
                          <span id="txtCorreo">-</span>
                      </p>

                  </div>

              </div>

          </div>

          <!-- ##################################### Compañía ##################################### -->

          <div class="card-info">

              <h3>Compañía aseguradora</h3>

              <p>
                  <strong>Nombre:</strong>
                  <span id="txtCompania">-</span>
              </p>

          </div>

          <!-- ##################################### Póliza ##################################### -->

          <div class="card-info">

              <h3>Póliza</h3>

              <div class="info-grid">

                  <div>

                      <p>
                          <strong>Número de póliza:</strong>
                          <span id="txtPoliza">-</span>
                      </p>

                      <p>
                          <strong>Estado:</strong>
                          <span id="txtEstadoPoliza">-</span>
                      </p>

                  </div>

                  <div>

                      <p>
                          <strong>Fecha inicio:</strong>
                          <span id="txtFechaInicio">-</span>
                      </p>

                      <p>
                          <strong>Fecha final:</strong>
                          <span id="txtFechaFinal">-</span>
                      </p>

                  </div>

              </div>

          </div>

          <!-- ##################################### Unidad ##################################### -->

          <div class="card-info">

              <h3>Unidad asegurada</h3>

              <div class="info-grid">

                  <div>

                      <p>
                          <strong>Placas:</strong>
                          <span id="txtPlacas">-</span>
                      </p>

                      <p>
                          <strong>Número de serie:</strong>
                          <span id="txtNumeroSerie">-</span>
                      </p>

                  </div>

                  <div>

                      <p>
                          <strong>Marca:</strong>
                          <span id="txtMarca">-</span>
                      </p>

                      <p>
                          <strong>Modelo:</strong>
                          <span id="txtModelo">-</span>
                      </p>

                      <p>
                          <strong>Año:</strong>
                          <span id="txtAnio">-</span>
                      </p>

                  </div>

              </div>

          </div>

      </div>
            <form action="/index.php?action=registrarSiniestro" method="POST" enctype="multipart/form-data">
              
          <input type="hidden" name="id_Unidad" id="idUnidad">
          <input type="hidden" name="id_Usuario" id="idUsuario">
          <input type="hidden" name="id_Poliza" id="idPoliza">

          <!--<input type="hidden" name="id_unidad" id="idUnidad">-->

          <h2>Datos del siniestro</h2>

          <div class="row">

              <div>
                  <label>Fecha y hora</label>

                  <input
                      required
                      type="datetime-local"
                      name="fecha">
              </div>

              <div>
                  <label>Ubicación</label>

                  <input
                      required
                      name="ubicacion"
                      placeholder="Dirección / referencia">
              </div>

          </div>

          <div class="row">

              <div>

                  <label>
                      ¿Otras unidades involucradas?
                  </label>

                  <select
                      required
                      name="hubo_unidades_involucradas">

                      <option value="0">No</option>
                      <option value="1">Sí</option>

                  </select>

              </div>

          </div>

          <label>
              Descripción del asegurado
          </label>

          <textarea
              required
              name="descripcion"
          ></textarea>

          <h2>Evidencia</h2>
          <div class="row">
              <div>
                  <label>Fotos</label>
                  <input
                      type="file"
                      name="fotos[]"
                      accept="image/*"
                      multiple
                  >
              </div>

              <div class="preview-grid" id="previewFotos"></div>
              <div>
                  <label>Videos</label>
                  <input
                      type="file"
                      name="videos[]"
                      accept="video/*"
                      multiple
                  >
              </div>
              <div class="preview-videos" id="previewVideos"></div>
          </div>
          <div class="btns">
              <button
                  class="btn primary"
                  type="submit">
                  Guardar siniestro
              </button>

          </div>

      </form>
    </section>

    
  </main>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="/pages/js/crear_Siniestro.js"></script>
</body>
</html>
