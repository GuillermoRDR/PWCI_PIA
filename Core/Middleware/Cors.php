<?php

namespace Core\Middleware;

class Cors
{
  public function handle(): void
  {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $permitidos = [
      'http://localhost',
      'http://127.0.0.1',
      'http://localhost:3000',
      'http://127.0.0.1:3000',
    ];

    if ($origin !== '' && !in_array($origin, $permitidos, true)) {
      http_response_code(403);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['error' => 'Origen no autorizado.']);
      exit;
    }

    if ($origin !== '') {
      header('Access-Control-Allow-Origin: ' . $origin);
      header('Access-Control-Allow-Credentials: true');
      header('Vary: Origin');
    }

    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Accept, X-CSRF-Token');
    header('Access-Control-Max-Age: 600');
  }
}
