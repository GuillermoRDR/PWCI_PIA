<?php
////session_start();

?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Registro</title>
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
        <a class="pill" href="/index.php?action=loginPage">Inicio</a>
        <a class="pill" href="/index.php?action=registerPage">Registro</a>
        <a class="pill" href="/index.php?action=dashboard">Dashboard</a>
        <a class="pill" href="/index.php?action=claim_create">Nuevo Siniestro</a>
        <a class="pill" href="/index.php?action=claims_list">Listado / Consulta</a>
        <a class="pill" href="/index.php?action=claim_detail">Detalle</a>
        <a class="pill" href="/index.php?action=approvals">Aprobaciones</a>
        <a class="pill" href="/index.php?action=followup">Seguimiento</a>
        <a class="pill" href="/index.php?action=payments">Pagos</a>
        <a class="pill" href="/index.php?action=guarantees_close">Garantías / Cierre</a>
        <a class="pill" href="/index.php?action=editarPerfil">Actualizar Perfil</a>
        <span class="badge" title="Rol simulado para probar vistas">
          Rol: <b id="roleLabel">Asegurado</b>
        </span>
        <select id="mockRole" aria-label="Rol">
          <option value="asegurado">Asegurado</option>
          <option value="ajustador">Ajustador</option>
          <option value="supervisor">Supervisor</option>
        </select>
      </nav>
    </div>
  </header>

  <main class="container">
    <div class="split">
      <section class="card pad">
        <h1 class="h1">Registro de usuarios</h1>
        <p class="sub">El registro cambia por tipo de usuario; este  captura el mínimo requerido.</p>

        <form id="registerForm" action="/index.php?action=registrar" method="post" enctype="multipart/form-data">
          <div class="row">
            <div>
              <label for="nombre">Nombre</label>
              <input name="nombre" id="nombre" required placeholder="Nombre(s)" />
            </div>
            <div>
              <label for="apellidoPaterno">Apellido Paterno</label>
              <input name="apellidoPaterno" id="apellidoPaterno" required placeholder="Apellido Paterno" />
            </div>
            <div>
              <label for="apellidoMaterno">Apellido Materno</label>
              <input name="apellidoMaterno" id="apellidoMaterno" required placeholder="Apellido Materno" />
            </div>
          </div>
          <div class="row">
            <div>
              <label for="fechaNacimiento">Fecha de nacimiento</label>
              <input name="fechaNacimiento" id="fechaNacimiento" type="date" required />
              
            </div>
            <div>
              <label for="genero">Género</label>
              <select name="genero" id="genero" required>
                <option value="" selected disabled>Selecciona…</option>
                <option>Femenino</option>
                <option>Masculino</option>
                <option>No binario</option>
                <option>Prefiero no decir</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div>
              <label for="foto">Foto</label>
              <input id="foto" name="foto" type="file" accept="image/*" />
              <!-- Preview -->
              <img id="preview" src=""/>
            </div>
            <div>
              <label for="rol">Tipo de usuario</label>
              <select id="rol" name="rol" required>
                <option value="" selected disabled>Selecciona…</option>
                <option value="3">Asegurado</option>
                <option value="4">Ajustador</option>
                <option value="2">Supervisor</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div>
              <label for="correo">Correo electrónico</label>
              <input id="correo" name="correo" type="email" required placeholder="correo@dominio.com" />
            </div>
            <div>
              <label for="alias">Alias</label>
              <input id="alias" name="alias" required placeholder="ej. ajustador_mty01" />
            </div>
          </div>
          <div class="row">
            <div>
              <label for="contrasenia">Contraseña</label>
              <input id="contrasenia" name="contrasenia" type="password" required placeholder="Mín. 10 caracteres" />
              
            </div>
            <div>
              <label for="contrasenia2">Confirmar contraseña</label>
              <input id="contrasenia2" name="contrasenia2" type="password" required placeholder="Repite la contraseña" />
            </div>
          </div>

          <div class="btns">
            <button class="btn primary" type="submit">Registrarme</button>
            <a class="btn" href="/pages/login.php">Volver</a>
          </div>
        </form>
      </section>

      <aside class="card pad">
        <h2 style="margin:0 0 8px">Seguridad (propuesta)</h2>
        <ul style="color:var(--muted); margin:0; padding-left:18px; line-height:1.55">
          <li>Hash de contraseña con <b>bcrypt</b> (PHP: password_hash/password_verify).</li>
          <li>Bloqueo temporal por intentos fallidos (rate limit).</li>
          <li>Reglas de autorización por rol:
            <ul>
              <li><b>Supervisor</b>: ve todos los siniestros.</li>
              <li><b>Ajustador</b>: solo los registrados por él.</li>
              <li><b>Asegurado</b>: solo sus siniestros.</li>
            </ul>
          </li>
          <li>Subida de archivos con validación: tipo, tamaño, sanitizar nombre.</li>
          <li>Sesiones seguras: regenerar session_id, cookies HttpOnly/SameSite.</li>
        </ul>
      </aside>
    </div> 
  </main>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="/pages/js/app.js"></script>
</body>
</html>
