<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Inicio</title>
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
      </nav>
    </div>
  </header>

  <main class="container">
    
    <section class="hero">
      <div class="card pad">
      </div>

      <div class="card pad">
        <h2 style="margin:0 0 8px">Iniciar sesión</h2>

        <form name="loginForm" action="/index.php?action=login" method="post" id="loginForm">
          <!--
          <label for="loginRol">Tipo de usuario</label>

          <select id="loginRol" name="rol" required>
            <option value="" selected disabled>Selecciona…</option>
            <option value="4">Ajustador</option>
            <option value="2">Supervisor</option>      
            <option value="3">Asegurado</option>
          </select> 
          -->

          <label for="loginCorreo">Correo</label>
          <input id="loginCorreo" name="correo" type="email" placeholder="correo@dominio.com" required />
          <label for="loginContrasenia">Contraseña</label>
          <input id="loginContrasenia" name="contrasenia" type="password" placeholder="••••••••••" required />

          <div class="btns">
            <button class="btn primary" type="submit">Entrar</button>
            <a class="btn" href="/index.php?action=registerPage">Crear cuenta</a>
          </div>
          
        </form>
        
      </div>

    </section>

    <section class="card pad" style="margin-top:16px">

      <h2 style="margin:0 0 10px">Estatus oficiales del siniestro</h2>

      <div class="btns" style="margin-top:0">
        <span class="badge bad">1. Rechazado</span>
        <span class="badge ok">2. Aceptado</span>
        <span class="badge warn">3. Aceptado con pago de deducible</span>
        <span class="badge ok">4. Aceptado sin pago de deducible</span>
        <span class="badge warn">5. Aplica pago para reparación</span>
        <span class="badge bad">6. Pérdida total (pago completo)</span>
      </div>

    </section>

  </main>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="/pages/js/app.js"></script>
</body>
</html>
