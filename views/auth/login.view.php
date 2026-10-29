<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= url('/css/inicio_sesion.css') ?>">
</head>


<body>

  <header>
    <a href="<?= url('/') ?>" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>
    <a href="<?= url('/register') ?>" class="btn-link">Registrarse</a>
  </header>

  <div class="login-wrapper">
    <div class="login-card">
      <h2>Iniciar Sesión</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success">
          <i class="fa-solid fa-circle-check"></i>
          <span><?= htmlspecialchars($mensaje) ?></span>
        </div>
      <?php endif; ?>

      <div id="jsAlert" class="alert alert-danger" style="display: none;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span id="jsAlertMessage"></span>
      </div>

      <form id="loginForm" novalidate>

        <div class="form-group">
          <label for="email">Correo o nombre de usuario</label>

          <div class="input-group">
            <i class="fa-solid fa-envelope"></i>

            <input
              type="text"
              id="email"
              name="identificador"
              class="form-control"
              placeholder="Correo o usuario"
              autocomplete="username"
              required>
          </div>

          <span id="emailError" class="js-error">
            Ingresa tu correo o nombre de usuario.
          </span>
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <div class="input-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
          </div>
          <span id="passwordError" class="js-error">La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.</span>
        </div>

        <button type="submit" class="btn-submit">Ingresar</button>
      </form>

      <div class="login-footer">
        <p>¿No tienes una cuenta? <a href="<?= url('/register') ?>">Regístrate aquí</a></p>
      </div>
    </div>
  </div>
<script src="<?= url('/js/login.js') ?>" defer></script>
</body>

