<?php
$error = $error ?? null;
$mensaje = $mensaje ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/inicio_sesion.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>
    <a href="index.php?action=registrarse" class="btn-link">Registrarse</a>
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

      <form id="loginForm" action="index.php?action=login_process" method="POST" novalidate>

        <div class="form-group">
          <label for="email">Correo Electrónico</label>
          <div class="input-group">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
          </div>
          <span id="emailError" class="js-error">Ingresa un correo electrónico válido.</span>
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
        <p>¿No tienes una cuenta? <a href="index.php?action=register">Regístrate aquí</a></p>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> kdemy. Todos los derechos reservados.</p>
  </footer>

  <script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
      let isValid = true;

      const email = document.getElementById('email');
      const password = document.getElementById('password');

      const emailError = document.getElementById('emailError');
      const passwordError = document.getElementById('passwordError');
      const jsAlert = document.getElementById('jsAlert');
      const jsAlertMessage = document.getElementById('jsAlertMessage');

      // Ocultar mensajes previos
      emailError.style.display = 'none';
      passwordError.style.display = 'none';
      jsAlert.style.display = 'none';

      // Validar Correo Electrónico
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value.trim() || !emailRegex.test(email.value)) {
        emailError.style.display = 'block';
        isValid = false;
      }

      // Validar Contraseña: Mínimo 8 caracteres, al menos 1 mayúscula, 1 número y 1 carácter especial
      const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?¡¿]).{8,}$/;
      if (!passwordRegex.test(password.value)) {
        passwordError.style.display = 'block';
        isValid = false;
      }

      // Si hay errores de validación, cancelar el envío del formulario
      if (!isValid) {
        event.preventDefault();
        jsAlertMessage.textContent = 'Por favor, corrige los errores resaltados antes de continuar.';
        jsAlert.style.display = 'flex';
      }
    });
  </script>
</body>

</html>