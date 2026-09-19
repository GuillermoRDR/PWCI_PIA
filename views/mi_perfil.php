<?php
$usuario = $usuario ?? [
  'nombre' => 'Juan Pérez González',
  'email' => 'juan.perez@ejemplo.com',
  'genero' => 'M',
  'fecha_nacimiento' => '1995-05-15',
  'avatar' => 'https://via.placeholder.com/150',
  'rol' => 'Estudiante',
  'fecha_registro' => '2024-01-10 14:30:00',
  'fecha_ultimo_cambio' => '2024-02-01 09:15:00'
];

$error = $error ?? null;
$mensaje = $mensaje ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/mi_perfil.css">
</head>

<body>

  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> kdemy
    </a>
    <div class="nav-links">
      <a href="index.php?action=home" class="btn-link"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="index.php?action=home" class="btn-link"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </div>
  </header>

  <div class="profile-wrapper">
    <div class="profile-card">
      <h2><i class="fa-solid fa-user-gear"></i> Mi Perfil</h2>

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

      <div class="avatar-section">
        <div class="avatar-wrapper">
          <img id="avatarPreview" class="avatar-img" src="https://placehold.co/400x400/ffffff/D9A05B" alt="Avatar del Usuario">
        </div>

        <div class="meta-info">
          <div class="meta-item">
            <i class="fa-solid fa-user-shield"></i> Rol: <?= htmlspecialchars($usuario['rol']) ?>
          </div>
          <div class="meta-item">
            <i class="fa-solid fa-calendar-check"></i> Registro: <?= htmlspecialchars($usuario['fecha_registro']) ?>
          </div>
          <div class="meta-item">
            <i class="fa-solid fa-clock-rotate-left"></i> Último Cambio: <?= htmlspecialchars($usuario['fecha_ultimo_cambio']) ?>
          </div>
        </div>
      </div>

      <form id="profileForm" action="index.php?action=update_profile" method="POST" enctype="multipart/form-data" novalidate>
        <div class="form-grid">

          <div class="form-group full-width">
            <label for="avatar">Actualizar Foto de Perfil / Avatar</label>
            <div class="input-group">
              <i class="fa-solid fa-image"></i>
              <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
            </div>
            <span id="avatarError" class="js-error">Selecciona un archivo de imagen válido.</span>
          </div>

          <div class="form-group full-width">
            <label for="nombre">Nombre Completo</label>
            <div class="input-group">
              <i class="fa-solid fa-user"></i>
              <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            <span id="nombreError" class="js-error">El nombre no puede estar vacío.</span>
          </div>

          <div class="form-group full-width">
            <label for="email">Correo Electrónico</label>
            <div class="input-group">
              <i class="fa-solid fa-envelope"></i>
              <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="genero">Género</label>
            <div class="input-group">
              <i class="fa-solid fa-venus-mars"></i>
              <select id="genero" name="genero" class="form-control" required>
                <option value="M" <?= $usuario['genero'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= $usuario['genero'] === 'F' ? 'selected' : '' ?>>Femenino</option>
                <option value="O" <?= $usuario['genero'] === 'O' ? 'selected' : '' ?>>Otro</option>
              </select>
            </div>
            <span id="generoError" class="js-error">Selecciona un género.</span>
          </div>

          <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <div class="input-group">
              <i class="fa-solid fa-calendar-days"></i>
              <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>" required>
            </div>
            <span id="fechaError" class="js-error">Selecciona una fecha válida.</span>
          </div>

          <div class="form-group">
            <label for="password">Nueva Contraseña (Opcional)</label>
            <div class="input-group">
              <i class="fa-solid fa-lock"></i>
              <input type="password" id="password" name="password" class="form-control" placeholder="Deja en blanco para conservar">
            </div>
            <span id="passwordError" class="js-error">Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.</span>
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirmar Nueva Contraseña</label>
            <div class="input-group">
              <i class="fa-solid fa-shield-halved"></i>
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Repite la nueva contraseña">
            </div>
            <span id="confirmPasswordError" class="js-error">Las contraseñas no coinciden.</span>
          </div>

          <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy. Todos los derechos reservados.</p>
  </footer>

  <script>
    document.getElementById('avatar').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });

    document.getElementById('profileForm').addEventListener('submit', function(event) {
      let isValid = true;

      const nombre = document.getElementById('nombre');
      const genero = document.getElementById('genero');
      const fechaNacimiento = document.getElementById('fecha_nacimiento');
      const password = document.getElementById('password');
      const confirmPassword = document.getElementById('confirm_password');

      const errors = {
        nombre: document.getElementById('nombreError'),
        genero: document.getElementById('generoError'),
        fecha: document.getElementById('fechaError'),
        password: document.getElementById('passwordError'),
        confirmPassword: document.getElementById('confirmPasswordError')
      };

      const jsAlert = document.getElementById('jsAlert');
      const jsAlertMessage = document.getElementById('jsAlertMessage');

      Object.values(errors).forEach(el => el.style.display = 'none');
      jsAlert.style.display = 'none';

      if (!nombre.value.trim()) {
        errors.nombre.style.display = 'block';
        isValid = false;
      }

      if (!genero.value) {
        errors.genero.style.display = 'block';
        isValid = false;
      }

      if (!fechaNacimiento.value) {
        errors.fecha.style.display = 'block';
        isValid = false;
      }

      if (password.value.trim() !== "") {
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?¡¿]).{8,}$/;
        if (!passwordRegex.test(password.value)) {
          errors.password.style.display = 'block';
          isValid = false;
        }

        if (password.value !== confirmPassword.value) {
          errors.confirmPassword.style.display = 'block';
          isValid = false;
        }
      }

      if (!isValid) {
        event.preventDefault();
        jsAlertMessage.textContent = 'Por favor, verifica los campos del formulario.';
        jsAlert.style.display = 'flex';
      }
    });
  </script>
</body>

</html>