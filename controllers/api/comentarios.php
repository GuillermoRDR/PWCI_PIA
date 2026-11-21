<?php

use Models\Comentario;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
  // La ruta será /api/v1/cursos/{id}/comentarios
  $ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  if (
    !preg_match(
      '#/api/v1/cursos/([1-9][0-9]*)/comentarios/?$#',
      $ruta,
      $coincidencias
    )
  ) {
    http_response_code(400);
    echo json_encode([
      'error' => 'Ruta o ID de curso inválido.'
    ]);
    return;
  }

  $cursoId = (int) $coincidencias[1];

  $modelo = new Comentario();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = !empty($_SESSION['user_id']) ? (new \Models\User())->buscarSesion((int) $_SESSION['user_id']) : false;
    if (!$usuario || $usuario['estado'] !== 'activo') {
      http_response_code(401);
      echo json_encode(['error' => 'Inicia sesión.']);
      return;
    }
    if ($usuario['rol'] !== 'alumno') {
      http_response_code(403);
      echo json_encode(['error' => 'Acceso denegado.']);
      return;
    }
    $token = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
      http_response_code(403);
      echo json_encode(['error' => 'Solicitud inválida.']);
      return;
    }
    if (!str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
      http_response_code(400);
      echo json_encode(['error' => 'Se requiere JSON.']);
      return;
    }
    $datos = json_decode(file_get_contents('php://input'), true);
    if (!is_array($datos) || !is_int($datos['calificacion'] ?? null) || !is_string($datos['comentario'] ?? null)) {
      http_response_code(400);
      echo json_encode(['error' => 'Datos inválidos.']);
      return;
    }
    if (!$modelo->existeCurso($cursoId)) {
      http_response_code(404);
      echo json_encode(['error' => 'Curso no encontrado.']);
      return;
    }
    $yaExiste = $modelo->existeDelAlumno($cursoId, (int) $_SESSION['user_id']);
    try {
      (new \Models\Portal())->comentar((int) $_SESSION['user_id'], $cursoId, $datos['calificacion'], trim($datos['comentario']));
    } catch (\RuntimeException $e) {
      http_response_code(400);
      echo json_encode(['error' => 'Comentario inválido.']);
      return;
    }
    http_response_code($yaExiste ? 200 : 201);
    echo json_encode(['ok' => true, 'mensaje' => 'Guardado.'], JSON_UNESCAPED_UNICODE);
    return;
  }

  if (!$modelo->existeCurso($cursoId)) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Curso no encontrado.'
    ]);
    return;
  }

  $comentarios = $modelo->obtenerPorCurso($cursoId);
  $estadisticas = $modelo->obtenerPromedio($cursoId);

  $storage = null;
  foreach ($comentarios as &$comentario) {
    if (!empty($comentario['avatar']) && !filter_var($comentario['avatar'], FILTER_VALIDATE_URL)) {
      try {
        $storage ??= new \Services\FirebaseStorage();
        $comentario['avatar'] = $storage->getUrl($comentario['avatar']);
      } catch (Throwable $e) {
        $comentario['avatar'] = null;
      }
    }
    $comentario['comentario_id'] =
      (int) $comentario['comentario_id'];
    $comentario['calificacion'] =
      $comentario['calificacion'] === null ? null : (int) $comentario['calificacion'];
  }
  unset($comentario);

  http_response_code(200);

  echo json_encode([
    'curso_id' => $cursoId,
    'total_comentarios' => (int) $estadisticas['total'],
    'promedio_calificacion' =>
      (float) ($estadisticas['promedio'] ?? 0),
    'comentarios' => $comentarios
  ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  error_log($e->getMessage());

  http_response_code(500);
  echo json_encode([
    'error' => 'Error interno del servidor.'
  ]);
}
