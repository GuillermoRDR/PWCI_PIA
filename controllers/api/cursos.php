<?php

use Models\CursoCatalogo;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
  $cursos = (new CursoCatalogo())->recientes(50);

  echo json_encode([
    'ok' => true,
    'total' => count($cursos),
    'cursos' => $cursos,
  ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  error_log($e->getMessage());

  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'mensaje' => 'Error interno del servidor.'
  ], JSON_UNESCAPED_UNICODE);
}
