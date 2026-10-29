<?php

namespace Core\Middleware;

use Models\User;

class Auth
{
  public function handle(?string $rolRequerido = null): void
  {
    if (empty($_SESSION['user_id'])) {
      header('Location: ' . \url('/login'), true, 303);
      exit;
    }

    $usuario = (new User())->buscarSesion(
      (int) $_SESSION['user_id']
    );

    if (!$usuario || $usuario['estado'] !== 'activo') {
      $_SESSION = [];
      session_regenerate_id(true);

      header('Location: ' . \url('/login'), true, 303);
      exit;
    }

    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['nombre'] = $usuario['nombre_completo'];

    if (
      $rolRequerido !== null &&
      $usuario['rol'] !== $rolRequerido
    ) {
      http_response_code(403);
      echo 'No tienes permisos para acceder a esta página.';
      exit;
    }
  }
}
