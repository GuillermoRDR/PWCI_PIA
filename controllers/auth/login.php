
<?php

use Models\User;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

  if (isset($_SESSION['user_id'])) {
    header('Location: ' . url('/'), true, 303);
    exit;
  }

  view('auth/login.view.php');
  return;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
  $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

  if (!str_contains($contentType, 'application/json')) {
    http_response_code(415);
    echo json_encode([
      'ok' => false,
      'mensaje' => 'Se requiere application/json.'
    ]);
    return;
  }

  $datos = json_decode(file_get_contents('php://input'), true);

  if (!is_array($datos)) {
    http_response_code(400);
    echo json_encode([
      'ok' => false,
      'mensaje' => 'JSON inválido.'
    ]);
    return;
  }

  $identificador = $datos['identificador'] ?? '';
  $password = $datos['password'] ?? '';

  if (!is_string($identificador) || !is_string($password)) {
    http_response_code(400);
    echo json_encode([
      'ok' => false,
      'mensaje' => 'Los campos deben ser texto.'
    ]);
    return;
  }

  $identificador = trim($identificador);

  if (
    $identificador === '' || $password === ''
    || strlen($identificador) > 254
    || strlen($password) > 1024
  ) {

    http_response_code(400);
    echo json_encode([
      'ok' => false,
      'mensaje' => 'Introduce usuario y contraseña válidos.'
    ]);
    return;
  }

  $resultado = (new User())->autenticar($identificador, $password);

  if (!$resultado['ok']) {
    http_response_code($resultado['codigo']);
    echo json_encode($resultado);
    return;
  }

  session_regenerate_id(true);

  $_SESSION['user_id'] = $resultado['usuario']['id'];
  $_SESSION['rol'] = $resultado['usuario']['rol'];
  $_SESSION['nombre'] = $resultado['usuario']['nombre'];

  $destinos = [
    'administrador' => '/admin',
    'profesor' => '/profesor',
    'alumno' => '/alumno'
  ];

  $rol = $resultado['usuario']['rol'];

  echo json_encode([
    'ok' => true,
    'mensaje' => 'Inicio de sesión exitoso.',
    'usuario' => $resultado['usuario'],
    'redirect' => url($destinos[$rol] ?? '/')
  ]);
} catch (Throwable $e) {
  error_log($e->getMessage());

  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'mensaje' => 'Error interno del servidor.'
  ]);
}
