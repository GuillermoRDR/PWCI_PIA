
<?php

use Services\FirebaseStorage;

$userModel = new \Models\User();
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $old = [
    'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
    'nombre_usuario' => trim($_POST['nombre_usuario'] ?? ''),
    'correo' => trim($_POST['correo'] ?? ''),
    'genero' => trim($_POST['genero'] ?? ''),
    'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
    'rol' => trim($_POST['rol'] ?? '')
  ];

  $password = $_POST['password'] ?? '';
  $confirmar = $_POST['confirmar_password'] ?? '';

  if (
    !hash_equals(
      $_SESSION['csrf_token'] ?? '',
      $_POST['csrf_token'] ?? ''
    ) ||
    empty($_SESSION['csrf_token'])
  ) {
    $errors[] = 'Solicitud inválida. Recarga la página.';
  }

  if (mb_strlen($old['nombre_completo']) < 3 || mb_strlen($old['nombre_completo']) > 150) {
    $errors[] = 'El nombre debe tener entre 3 y 150 caracteres.';
  }

  if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $old['nombre_usuario'])) {
    $errors[] = 'El usuario debe tener de 3 a 50 caracteres: letras, números o guion bajo.';
  }

  if (!filter_var($old['correo'], FILTER_VALIDATE_EMAIL) || strlen($old['correo']) > 254) {
    $errors[] = 'Introduce un correo electrónico válido.';
  }

  if (!in_array($old['genero'], ['Masculino', 'Femenino', 'Otro', 'Prefiero no decirlo'], true)) {
    $errors[] = 'Selecciona un género válido.';
  }

  $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $old['fecha_nacimiento']);

  if (!$fecha || $fecha->format('Y-m-d') !== $old['fecha_nacimiento'] || $fecha > new DateTimeImmutable('today')) {
    $errors[] = 'La fecha de nacimiento no es válida.';
  }

  if (!in_array($old['rol'], ['alumno', 'profesor'], true)) {
    $errors[] = 'Selecciona un rol válido.';
  }

  if (
    strlen($password) < 8 || !preg_match('/[A-Z]/', $password)
    || !preg_match('/[0-9]/', $password)
    || !preg_match('/[^a-zA-Z0-9]/', $password)
  ) {
    $errors[] = 'La contraseña requiere 8 caracteres, una mayúscula, un número y un símbolo.';
  }

  if ($password !== $confirmar) {
    $errors[] = 'Las contraseñas no coinciden.';
  }

  if (strlen($password) > 72) {
    $errors[] = 'La contraseña no puede superar 72 bytes.';
  }

  $avatar = $_FILES['avatar'] ?? null;

  if (!$avatar || $avatar['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Debes seleccionar una imagen de perfil.';
  } elseif ($avatar['size'] > 2 * 1024 * 1024) {
    $errors[] = 'El avatar no puede superar 2 MB.';
  }

  $mime = null;
  $extension = null;

  if ($avatar && $avatar['error'] === UPLOAD_ERR_OK && $avatar['size'] <= 2 * 1024 * 1024) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($avatar['tmp_name']);

    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    if (!isset($tipos[$mime])) {
      $errors[] = 'El avatar debe ser JPG, PNG o WebP.';
    } elseif (@getimagesize($avatar['tmp_name']) === false) {
      $errors[] = 'El archivo no es una imagen válida.';
    } else {
      $extension = $tipos[$mime];
    }
  }

  if (!$errors) {
    try {
      if ($userModel->exists(
        $old['correo'],
        $old['nombre_usuario']
      )) {
        $errors[] = 'El correo o nombre de usuario ya está registrado.';
      } else {

        $fotoUrl = null;
        $storage = new FirebaseStorage();
        try {

          $fotoUrl = $storage->uploadAvatar(
            $avatar['tmp_name'],
            $extension,
            $mime
          );

          // 2. Crear usuario en MySQL
          $userModel->create([
            'nombre_completo' => $old['nombre_completo'],
            'nombre_usuario' => $old['nombre_usuario'],
            'correo' => $old['correo'],
            'genero' => $old['genero'],
            'fecha_nacimiento' => $old['fecha_nacimiento'],
            'rol' => $old['rol'],
            'foto_url' => $fotoUrl,
            'password_hash' => password_hash(
              $password,
              PASSWORD_BCRYPT,
              ['cost' => 12]
            )
          ]);

          // 3. Registro exitoso
          $_SESSION['registro_exitoso'] = true;

          header('Location: ' . url('/login'), true, 303);
          exit;
        } catch (Throwable $e) {

          // Eliminar el avatar si ya se subió
          if ($fotoUrl !== null) {
            try {
              $storage->delete($fotoUrl);
            } catch (Throwable $cleanupError) {
              error_log(
                '[FIREBASE CLEANUP] ' .
                  $cleanupError->getMessage()
              );
            }
          }

          error_log(
            '[REGISTRO] ' . get_class($e) .
              ' | ' . $e->getMessage() .
              ' | ' . $e->getFile() .
              ':' . $e->getLine()
          );

          $errors[] = 'No se pudo completar el registro. '
            . 'Revisa el registro de errores de Apache.';
        }
      }
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        $errors[] = 'El correo o nombre de usuario ya está registrado.';
      } else {
        error_log($e->getMessage());
        $errors[] = 'No fue posible completar el registro.';
      }
    } catch (Throwable $e) {
      error_log($e->getMessage());
      $errors[] = 'Ocurrió un error al registrar la cuenta.';
    }
  }
}

$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

require base_path('views/auth/register.view.php');
