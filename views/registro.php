<?php
$error = $error ?? null;
$mensaje = $mensaje ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/registro.css">
</head>

<body>

  <!-- Encabezado / Navbar -->
  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>
    <a href="index.php?action=logearse" class="btn-link">Iniciar Sesión</a>
  </header>

  <!-- Contenido del Registro -->
  <div class="register-wrapper">
    <div class="register-card">
      <h2><i class="fa-solid fa-user-plus"></i> Crear Cuenta</h2>

      <!-- Mensajes servidor -->
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

      <form id="registerForm" action="index.php?action=register_process" method="POST" enctype="multipart/form-data" novalidate>

        <div class="form-grid">

          <div class="form-group full-width">
            <label for="nombre">Nombre Completo</label>
            <div class="input-group">
              <i class="fa-solid fa-user"></i>
              <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej. Juan Pérez González" required>
            </div>
            <span id="nombreError" class="js-error">Ingresa tu nombre completo.</span>
          </div>

          <div class="form-group full-width">
            <label for="email">Correo Electrónico</label>
            <div class="input-group">
              <i class="fa-solid fa-envelope"></i>
              <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
            </div>
            <span id="emailError" class="js-error">Ingresa un correo electrónico válido.</span>
          </div>

          <div class="form-group">
            <label for="genero">Género</label>
            <div class="input-group">
              <i class="fa-solid fa-venus-mars"></i>
              <select id="genero" name="genero" class="form-control" required>
                <option value="">Selecciona...</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="O">Otro</option>
              </select>
            </div>
            <span id="generoError" class="js-error">Selecciona tu género.</span>
          </div>

          <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <div class="input-group">
              <i class="fa-solid fa-calendar-days"></i>
              <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
            </div>
            <span id="fechaError" class="js-error">Selecciona tu fecha de nacimiento.</span>
          </div>

          <div class="form-group full-width">
            <label for="rol">Registrarme como</label>
            <div class="input-group">
              <i class="fa-solid fa-user-gear"></i>
              <select id="rol" name="rol" class="form-control" required>
                <option value="estudiante">Estudiante</option>
                <option value="profesor">Profesor</option>
              </select>
            </div>
          </div>

          <div class="form-group full-width">
            <label for="avatar">Foto de Perfil / Avatar</label>
            <div class="avatar-preview-container">
              <img id="previewImg" class="avatar-preview" src="https://placehold.co/400x400/ffffff/D9A05B" alt="Vista previa avatar">
              <input type="file" id="avatar" name="avatar" accept="image/*" class="form-control" style="padding-left: 12px;" required>
            </div>
            <span id="avatarError" class="js-error">Debes seleccionar una imagen válida (JPG, PNG).</span>
          </div>

          <!-- Contraseña -->
          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="input-group">
              <i class="fa-solid fa-lock"></i>
              <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <span id="passwordError" class="js-error">Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.</span>
          </div>

          <!-- Confirmar Contraseña -->
          <div class="form-group">
            <label for="confirm_password">Confirmar Contraseña</label>
            <div class="input-group">
              <i class="fa-solid fa-shield-halved"></i>
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>
            <span id="confirmPasswordError" class="js-error">Las contraseñas no coinciden.</span>
          </div>

        </div>

        <button type="submit" class="btn-submit">Completar Registro</button>
      </form>

      <div class="register-footer">
        <p>¿Ya tienes una cuenta? <a href="index.php?action=logearse">Inicia sesión aquí</a></p>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy. Todos los derechos reservados.</p>
  </footer>

  <script>
    // Vista previa inmediata de la foto de perfil subida
    document.getElementById('avatar').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('previewImg').src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });

    // Validación del formulario en el cliente
    document.getElementById('registerForm').addEventListener('submit', function(event) {
      let isValid = true;

      const nombre = document.getElementById('nombre');
      const email = document.getElementById('email');
      const genero = document.getElementById('genero');
      const fechaNacimiento = document.getElementById('fecha_nacimiento');
      const avatar = document.getElementById('avatar');
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirm_password');

      // Span de errores
      const errors = {
        nombre: document.getElementById('nombreError'),
        email: document.getElementById('emailError'),
        genero: document.getElementById('generoError'),
        fecha: document.getElementById('fechaError'),
        avatar: document.getElementById('avatarError'),
        password: document.getElementById('passwordError'),
        confirmPassword: document.getElementById('confirmPasswordError')
      };

      const jsAlert = document.getElementById('jsAlert');
      const jsAlertMessage = document.getElementById('jsAlertMessage');

      // Ocultar mensajes previos
      Object.values(errors).forEach(el => el.style.display = 'none');
      jsAlert.style.display = 'none';

      // Validar Nombre
      if (!nombre.value.trim()) {
        errors.nombre.style.display = 'block';
        isValid = false;
      }

      // Validar Correo
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value.trim() || !emailRegex.test(email.value)) {
        errors.email.style.display = 'block';
        isValid = false;
      }

      // Validar Género
      if (!genero.value) {
        errors.genero.style.display = 'block';
        isValid = false;
      }

      // Validar Fecha de Nacimiento
      if (!fechaNacimiento.value) {
        errors.fecha.style.display = 'block';
        isValid = false;
      }

      // Validar Avatar
      if (avatar.files.length === 0) {
        errors.avatar.style.display = 'block';
        isValid = false;
      }

      // Validar Contraseña (Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial)
      const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?¡¿]).{8,}$/;
      if (!passwordRegex.test(password.value)) {
        errors.password.style.display = 'block';
        isValid = false;
      }

      // Validar Confirmación de Contraseña
      if (password.value !== confirmPassword.value) {
        errors.confirmPassword.style.display = 'block';
        isValid = false;
      }

      // Si hay errores, prevenir envío
      if (!isValid) {
        event.preventDefault();
        jsAlertMessage.textContent = 'Por favor, completa correctamente todos los campos obligatorios.';
        jsAlert.style.display = 'flex';
      }
    });
  </script>
</body>

</html>