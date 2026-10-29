
<?php

use Models\Comentario;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
  // La ruta será /api/v1/cursos/{id}/comentarios
  $ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  if (!preg_match(
    '#/api/v1/cursos/([1-9][0-9]*)/comentarios/?$#',
    $ruta,
    $coincidencias
  )) {
    http_response_code(400);
    echo json_encode([
      'error' => 'Ruta o ID de curso inválido.'
    ]);
    return;
  }

  $cursoId = (int)$coincidencias[1];

  $modelo = new Comentario();

  if (!$modelo->existeCurso($cursoId)) {
    http_response_code(404);
    echo json_encode([
      'error' => 'Curso no encontrado.'
    ]);
    return;
  }

  $comentarios = $modelo->obtenerPorCurso($cursoId);
  $estadisticas = $modelo->obtenerPromedio($cursoId);

  foreach ($comentarios as &$comentario) {
    $comentario['comentario_id'] =
      (int)$comentario['comentario_id'];
    $comentario['calificacion'] =
      (int)$comentario['calificacion'];
  }
  unset($comentario);

  http_response_code(200);

  echo json_encode([
    'curso_id' => $cursoId,
    'total_comentarios' => (int)$estadisticas['total'],
    'promedio_calificacion' =>
    (float)($estadisticas['promedio'] ?? 0),
    'comentarios' => $comentarios
  ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  error_log($e->getMessage());

  http_response_code(500);
  echo json_encode([
    'error' => 'Error interno del servidor.'
  ]);
}
