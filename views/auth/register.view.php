<?php

$old ??= [];
$errors ??= [];

function oldValue(string $key): string
{
  global $old;
  return htmlspecialchars($old[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= url('/css/registro.css') ?>">
</head>

<body>
  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>
    <a href="<?= url('/login') ?>" class="btn-link">Iniciar Sesión</a>
  </header>

  <main class="register-wrapper">
    <section class="register-card">

      <h2>
        <i class="fa-solid fa-user-plus"></i>
        Crear Cuenta
      </h2>

      <p class="register-subtitle">
        Únete a Kdemy y comienza a aprender o enseñar.
      </p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
          <i class="fa-solid fa-circle-exclamation"></i>

          <ul>
            <?php foreach ($errors as $error): ?>
              <li>
                <?= htmlspecialchars(
                  $error,
                  ENT_QUOTES,
                  'UTF-8'
                ) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div
        id="jsAlert"
        class="alert alert-danger"
        style="display: none;"
        role="alert"
        aria-live="polite">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span id="jsAlertMessage"></span>
      </div>

      <form id="registerForm" action="<?= url('/register') ?>" method="POST" enctype="multipart/form-data" novalidate>
        <input
          type="hidden"
          name="csrf_token"
          value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '',  ENT_QUOTES,  'UTF-8') ?>">

        <div class="form-grid">

          <div class="form-group full-width">
            <label for="nombre_completo">
              Nombre Completo
            </label>

            <div class="input-group">
              <i class="fa-solid fa-user"></i>

              <input type="text" id="nombre_completo" name="nombre_completo"
                class="form-control" placeholder="Ej. Juan Pérez González" maxlength="150"
                autocomplete="name" value="<?= oldValue('nombre_completo') ?>" required>
            </div>

            <span id="nombreError" class="js-error">
              Ingresa un nombre de 3 a 150 caracteres.
            </span>
          </div>

          <div class="form-group full-width">
            <label for="nombre_usuario">
              Nombre de Usuario
            </label>

            <div class="input-group">
              <i class="fa-solid fa-at"></i>

              <input type="text" id="nombre_usuario" name="nombre_usuario"
                class="form-control" placeholder="Ej. juan_perez" minlength="3" maxlength="50"
                pattern="[a-zA-Z0-9_]{3,50}" autocomplete="username" value="<?= oldValue('nombre_usuario') ?>" required>
            </div>

            <span id="usuarioError" class="js-error">
              Usa de 3 a 50 caracteres: letras, números
              o guion bajo.
            </span>
          </div>

          <div class="form-group full-width">
            <label for="correo">
              Correo Electrónico
            </label>

            <div class="input-group">
              <i class="fa-solid fa-envelope"></i>

              <input type="email" id="correo" name="correo" class="form-control"
                placeholder="ejemplo@correo.com" maxlength="254" autocomplete="email" value="<?= oldValue('correo') ?>" required>
            </div>

            <span id="emailError" class="js-error">
              Ingresa un correo electrónico válido.
            </span>
          </div>

          <div class="form-group">
            <label for="genero">
              Género
            </label>

            <div class="input-group">
              <i class="fa-solid fa-venus-mars"></i>

              <select
                id="genero" name="genero" class="form-control" required>
                <option value="">
                  Selecciona...
                </option>

                <?php foreach (
                  [
                    'Masculino',
                    'Femenino',
                    'Otro',
                    'Prefiero no decirlo'
                  ] as $genero
                ): ?>

                  <option
                    value="<?= htmlspecialchars($genero) ?>"
                    <?= ($old['genero'] ?? '') === $genero
                      ? 'selected'
                      : '' ?>>
                    <?= htmlspecialchars($genero) ?>
                  </option>

                <?php endforeach; ?>
              </select>
            </div>

            <span id="generoError" class="js-error">
              Selecciona tu género.
            </span>
          </div>

          <div class="form-group">
            <label for="fecha_nacimiento">
              Fecha de Nacimiento
            </label>

            <div class="input-group">
              <i class="fa-solid fa-calendar-days"></i>

              <input
                type="date"
                id="fecha_nacimiento"
                name="fecha_nacimiento"
                class="form-control"
                max="<?= date('Y-m-d') ?>"
                value="<?= oldValue('fecha_nacimiento') ?>"
                required>
            </div>

            <span id="fechaError" class="js-error">
              Selecciona una fecha válida.
            </span>
          </div>

          <div class="form-group full-width">
            <label for="rol">
              Registrarme como
            </label>

            <div class="input-group">
              <i class="fa-solid fa-user-gear"></i>

              <select
                id="rol"
                name="rol"
                class="form-control"
                required>
                <option value="">
                  Selecciona un rol
                </option>

                <option
                  value="alumno"
                  <?= ($old['rol'] ?? '') === 'alumno'
                    ? 'selected'
                    : '' ?>>
                  Estudiante
                </option>

                <option
                  value="profesor"
                  <?= ($old['rol'] ?? '') === 'profesor'
                    ? 'selected'
                    : '' ?>>
                  Profesor / Instructor
                </option>
              </select>
            </div>

            <span id="rolError" class="js-error">
              Selecciona cómo deseas registrarte.
            </span>
          </div>

          <div class="form-group full-width">
            <label for="avatar">
              Foto de Perfil / Avatar
            </label>

            <div class="avatar-preview-container">
              <div class="avatar-placeholder">
                <img
                  id="previewImg"
                  class="avatar-preview"
                  alt="Vista previa del avatar"
                  style="display: none;">

                <i
                  id="avatarPlaceholder"
                  class="fa-solid fa-circle-user"
                  aria-hidden="true"></i>
              </div>

              <input
                type="file"
                id="avatar"
                name="avatar"
                accept="image/jpeg,image/png,image/webp"
                class="form-control"
                required>
            </div>

            <small class="form-hint">
              Formatos permitidos: JPG, PNG y WebP.
              Tamaño máximo: 2 MB.
            </small>

            <span id="avatarError" class="js-error">
              Selecciona una imagen válida de máximo 2 MB.
            </span>
          </div>

          <div class="form-group">
            <label for="password">
              Contraseña
            </label>

            <div class="input-group">
              <i class="fa-solid fa-lock"></i>

              <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="••••••••"
                minlength="8"
                autocomplete="new-password"
                required>
            </div>

            <span id="passwordError" class="js-error">
              Mínimo 8 caracteres, una mayúscula,
              un número y un carácter especial.
            </span>
          </div>

          <div class="form-group">
            <label for="confirmar_password">
              Confirmar Contraseña
            </label>

            <div class="input-group">
              <i class="fa-solid fa-shield-halved"></i>

              <input
                type="password"
                id="confirmar_password"
                name="confirmar_password"
                class="form-control"
                placeholder="••••••••"
                autocomplete="new-password"
                required>
            </div>

            <span
              id="confirmPasswordError"
              class="js-error">
              Las contraseñas no coinciden.
            </span>
          </div>

        </div>

        <button type="submit" class="btn-submit">
          <i class="fa-solid fa-user-check"></i>
          Completar Registro
        </button>

      </form>

      <div class="register-footer">
        <p>
          ¿Ya tienes una cuenta?
          <a href="<?= url('/login') ?>">
            Inicia sesión aquí
          </a>
        </p>
      </div>

    </section>
  </main>
</body>

<?php require base_path('views/partials/footer.php'); ?>